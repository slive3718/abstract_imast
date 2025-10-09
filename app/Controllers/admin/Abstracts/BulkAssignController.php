<?php

namespace App\Controllers\admin\Abstracts;

use App\Models\PaperAssignedReviewerModel;
use App\Models\UserModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class BulkAssignController extends Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function bulk_assign()
    {

        // If no password provided, return HTML with simple popup
        $providedPassword = $this->request->getPost('password');

        if (!$providedPassword) {
            $html = '
        <html>
        <body>
            <script>
                var password = prompt("Bulk Assign Reviewers\\n\\nEnter password:");
                if (password !== null) {
                    var formData = new FormData();
                    formData.append("password", password);
                    
                    fetch(window.location.href, {
                        method: "POST",
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("Success!\\nInserted: " + (data.inserted || 0) + 
                                  "\\nUpdated: " + (data.updated || 0) + 
                                  "\\nSkipped: " + (data.skipped || 0) + 
                                  "\\nTotal: " + (data.total_processed || 0));
                        } else {
                            alert("Error: " + data.error);
                        }
                    })
                    .catch(error => {
                        alert("Request failed: " + error);
                    });
                }
            </script>
        </body>
        </html>';

            return $this->response->setBody($html);
        }


        // Password verification
        $requiredPassword = 'IMAST2026#!';
        if ($providedPassword !== $requiredPassword) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Invalid password'
            ]);
        }


        // Assume the file is uploaded or placed at a specific path
        // For demo, hardcode path; in production, handle upload via $this->request->getFile('file')
        $filePath = FCPATH.'assets/documents/imports/reviewer_assignments.txt';

        if (!file_exists($filePath)) {
            return $this->response->setJSON(['error' => 'File not found'])->setStatusCode(404);
        }

        $this->db->transStart();

        try {
            // Read and parse the file
            $handle = fopen($filePath, 'r');
            $assignments = [];
            $emails = [];
            $abstractIds = [];

            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    $line = trim($line);
                    if (empty($line)) continue;

                    $parts = explode("\t", $line);
                    if (count($parts) !== 2) continue;

                    $fullId = trim($parts[0]);
                    $email = trim($parts[1]);

                    // Remove '2026-' prefix and convert to int
                    $abstractId = (int) str_replace('2026-', '', $fullId);

                    if ($abstractId > 0) {
                        $assignments[] = [
                            'abstract_id' => $abstractId,
                            'email' => $email
                        ];
                        $emails[] = $email; // Collect unique emails
                        $abstractIds[] = $abstractId; // Collect abstract IDs
                    }
                }

                fclose($handle);
            }

            if (empty($assignments)) {
                return $this->response->setJSON(['error' => 'No valid data found'])->setStatusCode(400);
            }

            // Get unique emails and fetch user_ids in one query
            $uniqueEmails = array_unique($emails);
            $emailPlaceholders = str_repeat('?,', count($uniqueEmails) - 1) . '?';

            $userQuery = (new UserModel())->select('id, email')->whereIn('email', $uniqueEmails);
            $users = $userQuery->asArray()->findAll();

            // Map email to user_id
            $emailToUserId = [];
            foreach ($users as $user) {
                $emailToUserId[$user['email']] = $user['id'];
            }

            // Get existing assignments to avoid duplicates
            $paperAssignedModel = new PaperAssignedReviewerModel();
            $uniqueAbstractIds = array_unique($abstractIds);

            $existingAssignments = $paperAssignedModel
                ->whereIn('paper_id', $uniqueAbstractIds)
                ->asArray()
                ->findAll();

            // Create a map of existing assignments for quick lookup
            $existingMap = [];
            foreach ($existingAssignments as $existing) {
                $key = $existing['paper_id'] . '_' . $existing['reviewer_id'];
                $existingMap[$key] = $existing;
            }

            // Prepare data for insert/update
            $insertData = [];
            $updateData = [];
            $skippedCount = 0;

            foreach ($assignments as $assignment) {
                if (!isset($emailToUserId[$assignment['email']])) {
                    $skippedCount++; // No user found for this email
                    continue;
                }

                $reviewerId = $emailToUserId[$assignment['email']];
                $paperId = $assignment['abstract_id'];

                $key = $paperId . '_' . $reviewerId;

                if (isset($existingMap[$key])) {
                    // Assignment already exists, prepare for update if needed
                    $existingId = $existingMap[$key]['id'];
                    $updateData[] = [
                        'id' => $existingId,
                        'paper_id' => $paperId,
                        'reviewer_id' => $reviewerId,
                        'updated_at' => date('Y-m-d H:i:s') // if you have timestamps
                    ];
                } else {
                    // New assignment, prepare for insert
                    $insertData[] = [
                        'reviewer_id' => $reviewerId,
                        'paper_id' => $paperId,
                        'created_at' => date('Y-m-d H:i:s') // if you have timestamps
                    ];
                }
            }

            $insertedCount = 0;
            $updatedCount = 0;

            // Process inserts
            if (!empty($insertData)) {
                $chunks = array_chunk($insertData, 1000);
                foreach ($chunks as $chunk) {
                    $paperAssignedModel->insertBatch($chunk);
                    $insertedCount += count($chunk);
                }
            }

            // Process updates
            if (!empty($updateData)) {
                $chunks = array_chunk($updateData, 1000);
                foreach ($chunks as $chunk) {
                    $paperAssignedModel->updateBatch($chunk, 'id');
                    $updatedCount += count($chunk);
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                // Get detailed error information
                $error = $this->db->error();
                echo "Database Error: ";
                print_r($error);

                // Backtrace to see where the error occurred
                echo "Backtrace:\n";
                debug_print_backtrace();

                throw new \Exception('Transaction failed: ' . $error['message']);
            }

            return $this->response->setJSON([
                'success' => true,
                'inserted' => $insertedCount,
                'updated' => $updatedCount,
                'skipped' => $skippedCount,
                'total_processed' => count($assignments),
                'message' => 'Bulk assignment completed'
            ]);

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', $e->getMessage());
            return $this->response->setJSON(['error' => $e->getMessage()])->setStatusCode(500);
        }
    }
}