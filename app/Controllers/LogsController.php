<?php

namespace App\Controllers;

use App\Models\LogsModel;
use App\Models\LogsSeenModel;
use CodeIgniter\Debug\Toolbar\Collectors\Logs;

class LogsController extends BaseController
{

    public function __construct()
    {

    }


    public function add_seen_logs()
    {
        $post = $this->request->getPost();
        $validateFields = ['paper_id']; // Fields to validate

        try {
            // Validate required fields
            foreach ($validateFields as $field) {
                if (!isset($post[$field])) {  // Using isset() is better than empty() for validation
                    return $this->response->setJSON([
                        'status' => 400,
                        'message' => 'Missing required field: ' . $field
                    ]);
                }
            }

            $logs = $this->getLogs($post);

            if (empty($logs)) {
                return $this->response->setJSON([
                    'status' => 404,
                    'message' => 'No logs found to mark as seen'
                ]);
            }

            $logsSeenModel = new LogsSeenModel();
            $userId = session('user_id');
            $results = [];
            $successCount = 0;
            $errorCount = 0;

            foreach ($logs as $log) {
                try {
                    $logsRecord = $logsSeenModel->where([
                        'log_id' => $log['id'],
                        'user_id' => $userId
                    ])->first();

                    if ($logsRecord) {
                        // Record exists - update (though you may not need to update anything)
                        $result = $logsSeenModel->update($logsRecord['id'], [
                            'updated_at' => date('Y-m-d H:i:s') // Example field to update
                        ]);
                    } else {
                        // Record doesn't exist - insert
                        $result = $logsSeenModel->insert([
                            'log_id' => $log['id'],
                            'user_id' => $userId,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    }

                    if ($result) {
                        $successCount++;
                        $results[] = ['log_id' => $log['id'], 'status' => 'success'];
                    } else {
                        $errorCount++;
                        $results[] = ['log_id' => $log['id'], 'status' => 'failed'];
                    }

                } catch (\Exception $e) {
                    $errorCount++;
                    $results[] = [
                        'log_id' => $log['id'],
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ];
                }
            }

            return $this->response->setJSON([
                'status' => 200,
                'message' => "Processed {$successCount} logs successfully, {$errorCount} failed",
                'data' => $results
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 500,
                'message' => 'Error processing request',
                'error' => $e->getMessage()
            ]);
        }
    }


    function getLogs($post){
        return (new LogsModel())
            ->where(['ref_2'=>$post['paper_id']])
            ->whereIn('context', [
                'submitter_comment',
                'upload_comment',
                'upload_presentation',
                'suggested_revision_comment_added',
                'review',
                'decline_assigned_abstract',
                're_review_comment_added',
                'upload'
            ])
            ->findAll();
    }
}
