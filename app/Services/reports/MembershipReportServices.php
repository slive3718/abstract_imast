<?php

namespace App\Services\reports;

use App\Models\AbstractCategoriesModel;
use App\Models\AdminAcceptanceModel;
use App\Models\AuthorAcceptanceModel;
use App\Models\DesignationsModel;
use App\Models\InstitutionModel;
use App\Models\PaperAuthorsModel;
use App\Models\PapersModel;
use App\Models\PaperTypeModel;
use App\Models\SchedulerSessionTalksModel;
use App\Models\UserModel;
use App\Services\AbstractReviewsServices;
use App\Services\InstitutionServices;
use CodeIgniter\Config\BaseService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MembershipReportServices extends BaseService
{
    private array $paperTypes = [];
    private array $adminPresentationPreference = [];
    private array $categories = [];
    private array $adminAcceptance = [];
    private array $institutions = [];
    private array $reviewsByPaper         = [];
    private array $users = [];
    private array $schedules = [];
    private array $allAcceptance = [];

    public function __construct()
    {
        helper('array');
        $this->paperTypes = array_column((new PaperTypeModel())->findAll(), 'name', 'id');
        $this->adminPresentationPreference = array_column((new AdminAcceptanceModel())->findAll(), null, 'abstract_id');
        $this->categories = array_column((new AbstractCategoriesModel())->findAll(), 'name', 'id');
        $this->institutions = array_column((new InstitutionServices())->getInstitutionQuery()->findAll(), null, 'id');
        $this->reviewsByPaper = (new AbstractReviewsServices())->getReviewsMappedByPaper();
        $this->users = array_column((new UserModel())->findAll(), null, 'id');
        $this->schedules = array_column((new SchedulerSessionTalksModel())->getAbstractSchedules()->get()->getResultArray(), null, 'abstract_id');
//        $this->allAcceptance = $this->getAllAuthorAcceptanceByAuthorPaper();
    }

    public function membership_report($exportName = '', $excludeInvitedFaculty = false)
    {
        $papersModel = new PapersModel();
        $allPapers = $papersModel->asArray()->findAll();

        if ($excludeInvitedFaculty) {
            $excludeTypes = ['invited_faculty', 'speaker'];
            $allPapers = array_filter($allPapers, function ($paper) use ($excludeTypes) {
                return !in_array(strtolower($paper['acceptance_type'] ?? ''), $excludeTypes);
            });
        }

        $paperIds = array_column($allPapers, 'id');

        $paperAuthorsMapped = [];
        if (!empty($paperIds)) {
            $allAuthors = (new PaperAuthorsModel())->getAuthorsWithProfiles($paperIds);
            foreach ($allAuthors as $author) {
                $paperAuthorsMapped[$author['paper_id']][] = $author;
            }
        }

        $designations = (new DesignationsModel())->findAll();
        $designationsColumn = array_column($designations, 'name', 'id');

        // Build rows - One row per author per paper
        $rows = [];
        foreach ($allPapers as $paper) {
            $authors = $paperAuthorsMapped[$paper['id']] ?? [];
            $reviewsData = $this->reviewsByPaper[$paper['id']] ?? [];
            [$reviewsText, $totalScore, $avgScore, $olympicScore] = $this->processReviews($reviewsData);
            if (empty($authors)) {
                $rows[] = $this->buildRowData($paper, null, $designationsColumn, [], null, null, null, null);
                continue;
            }

            foreach ($authors as $author) {
                if (($author['is_removed'] ?? '0') !== '0') {
                    continue;
                }
                $rows[] = $this->buildRowData($paper, $author, $designationsColumn, $authors, $reviewsText, $totalScore, $avgScore, $olympicScore);
            }



        }

        // ====================== Create Excel ======================
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Exact headers from your screenshot
        $headers = [
            'AbstractID',
            'Assigned ID',
            'Accepted Session Type',
            'Author Name',
            'First Name',
            'Last Name',
            'Degree',
            'Author Email',
            'Role',
            'Author Institution',
            'Country Institution',
            'Abstract Title',
            'Authors List',
            'Presentation Preference',
            'Basic Science Proposal Format',
            'Hypothesis',
            'Category',
            'Introduction',
            'Methods',
            'Results',
            'Conclusions',
            'Study Design',
            'Image Caption',
            'Image Link',
            'Minimum Time Period for Follow up',
            'Funded by SRS Grant?',
            'TOTAL SCORE',
            'AVERAGE SCORE',
            'Scores and Comments-short',
            'IMAST Olympic',
            'Submitter First Name',
            'Submitter Last Name',
            'Submitter Email',
            'Session Title',
            'Session Date',
            'Session Start Time',
            'Session End Time',
            'Presentation Start Time',
            'Presentation End Time',
            'Participation Status'
        ];

        // Set headers
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        // Header style
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ];
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray($headerStyle);

        // Fill data rows
        $rowNumber = 2;
        foreach ($rows as $rowData) {
            $col = 'A';
            foreach ($headers as $header) {
                $value = $rowData[$header] ?? '';

                if (is_array($value)) {
                    $value = implode('; ', $value);
                }

                $sheet->setCellValue($col . $rowNumber, $value);
                $col++;
            }
            $rowNumber++;
        }

        // Auto-size all columns
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Output Excel
        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Membership_Report_' . date('Y-m-d_His') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Build row data - one row per author
     */
    private function buildRowData($paper, $currentAuthor, $designationsColumn, array $allAuthorsForPaper, $reviewsText, $totalScore, $avgScore, $olympicScore)
    {
//        print_r($currentAuthor);exit;
        // Find presenting author (for institution/country)
        $presenting = null;
        foreach ($allAuthorsForPaper as $a) {
            if (($a['is_presenting_author'] ?? '') === 'Yes') {
                $presenting = $a;
                break;
            }
        }

        // Current author details (this changes per row)
        $authorName   = '';
        $firstName    = '';
        $lastName     = '';
        $degree       = '';
        $authorEmail  = '';
        $authorInstitution = '';
        $countryInstitution = '';

        if ($currentAuthor) {
            $firstName    = $currentAuthor['user_name'] ?? '';
            $lastName     = $currentAuthor['user_surname'] ?? '';
            $authorName   = trim($firstName . ' ' . $lastName);
            $degree       = $this->getAuthorDegrees($currentAuthor, $designationsColumn);
            $authorEmail  = $currentAuthor['user_email'] ?? '';
            $authorInstitution = $this->institutions[$currentAuthor['institution_id']]['institution_name'] ?? '';
            $countryInstitution = $this->institutions[$currentAuthor['institution_id']]['institution_country'] ?? '';
        }

        // Role: Presenter or Co-Author
        $role = 'Co-Author';
        if ($currentAuthor && ($currentAuthor['is_presenting_author'] ?? '') === 'Yes') {
            $role = 'Presenter';
        }

        // Full Authors List (numbered like your example)
        $authorsList = [];
        $i = 1;
        foreach ($allAuthorsForPaper as $a) {
            if (($a['is_removed'] ?? '0') !== '0') continue;
            $name = trim(($a['user_name'] ?? '') . ' ' . ($a['user_surname'] ?? ''));
            if ($name) {
                // Build the institution and country part
                $institutionPart = '';
                if (!empty($authorInstitution) && !empty($countryInstitution)) {
                    $institutionPart = ', ' . $authorInstitution . ', ' . $countryInstitution;
                } elseif (!empty($authorInstitution)) {
                    $institutionPart = ', ' . $authorInstitution;
                }

                $authorsList[] = $i . '. ' . $name . $institutionPart;
                $i++;
            }
        }

        // Presenting author institution (if needed for consistency)
        $presentingInstitution = $presenting['institution'] ?? $authorInstitution;
        $presentingCountry     = $presenting['country_institution'] ?? $countryInstitution;

        // === Presentation Preference from admin acceptance ===
        $adminPref = $this->adminPresentationPreference[$paper['id']] ?? null;
        $adminAcceptancePreference = '';
        if (!empty($adminPref) && isset($adminPref['presentation_preference'])) {
            $prefKey = $adminPref['presentation_preference'];
            $adminAcceptancePreference = $this->paperTypes[$prefKey] ?? $prefKey;
        }

//        $acceptanceStatus = $this->allAcceptance[$currentAuthor['author_id'] . '_' . $paper['id']]['is_finalized'] === 1 ? '' ?? null;
        return [
            'AbstractID'                        => $paper['custom_id'] ?? '',
            'Assigned ID'                       => $paper['assigned_id'] ?? '',
            'Accepted Session Type'             => $adminAcceptancePreference ?? '',
            'Author Name'                       => $authorName,
            'First Name'                        => $firstName,
            'Last Name'                         => $lastName,
            'Degree'                            => $degree,
            'Author Email'                      => $authorEmail,
            'Role'                              => $role,
            'Author Institution'                => $authorInstitution,      // Current author
            'Country Institution'               => $countryInstitution,
            'Abstract Title'                    => $paper['title'] ?? '',
            'Authors List'                      => $authorsList,
            'Presentation Preference'           => $this->paperTypes[$paper['type_id']] ?? '',
            'Basic Science Proposal Format'     => $paper['basic_science_format'] ?? '',
            'Hypothesis'                        => $paper['hypothesis'] ?? '',
            'Category'                          => $this->categories[$paper['abstract_category']] ?? '',
            'Introduction'                      => $paper['introduction'] ?? '',
            'Methods'                           => $paper['methods'] ?? '',
            'Results'                           => $paper['results'] ?? '',
            'Conclusions'                       => $paper['conclusions'] ?? '',
            'Study Design'                      => $paper['study_design'] ?? '',
            'Image Caption'                     => $paper['image_caption'] ?? '',
            'Image Link'                        => $paper['image_link'] ?? '',
            'Minimum Time Period for Follow up' => $paper['min_follow_up_period'] ?? '',
            'Funded by SRS Grant?'              => $paper['is_srs_funded'] ?? '',
            'TOTAL SCORE'                       => $totalScore ?? '',
            'AVERAGE SCORE'                     => $avgScore ?? '',
            'Scores and Comments-short'         => $reviewsText ?? '',
            'IMAST Olympic'                     => $olympicScore ?? '',
            'Submitter First Name'              => $this->users[$paper['user_id']]['name']  ?? '',
            'Submitter Last Name'               => $this->users[$paper['user_id']]['surname']  ?? '',
            'Submitter Email'                   => $this->users[$paper['user_id']]['email'] ?? '',
            'Session Title'                     => $this->schedules[$paper['id']]['session_title'] ?? '',
            'Session Date'                      => $this->schedules[$paper['id']]['session_date'] ?? '',
            'Session Start Time'                => $this->schedules[$paper['id']]['session_start_time'] ?? '',
            'Session End Time'                  => $this->schedules[$paper['id']]['session_end_time'] ?? '',
            'Presentation Start Time'           => $this->schedules[$paper['id']]['time_start'] ?? '',
            'Presentation End Time'             => $this->schedules[$paper['id']]['time_end'] ?? '',
            'Participation Status'              => $this->schedules[$paper['id']]['title'] ?? '',
        ];
    }

    private function getAuthorDegrees($author, $designationsColumn)
    {
        if (!$author || empty($author['designations'])) {
            return '';
        }

        $ids = json_decode($author['designations'], true) ?: [];
        $degrees = [];

        foreach ($ids as $id) {
            if (isset($designationsColumn[$id])) {
                $name = $designationsColumn[$id];
                if (strtolower($name) === 'other') {
                    $degrees[] = $author['other_designation'] ?? '';
                } elseif (strtolower($name) !== 'none') {
                    $degrees[] = $name;
                }
            }
        }
        return implode(', ', $degrees);
    }

    /**
     * Calculate Olympic score (remove highest and lowest, average the rest)
     *
     * @param array $scores Array of numeric scores
     * @param int $precision Number of decimal places for rounding (default: 2)
     * @return float|null Returns null if not enough valid scores
     */
    private function calculateOlympicScore(array $scores, int $precision = 2): ?float
    {
        // Keep only numeric scores (exclude 'coi', null, strings, etc.)
        $validScores = array_filter($scores, function($score) {
            return is_numeric($score);
        });

        // Need at least 3 valid numeric scores for Olympic scoring
        if (count($validScores) < 3) {
            return null;
        }

        $sortedScores = array_values($validScores); // re-index
        sort($sortedScores);

        // Remove highest and lowest
        $middleScores = array_slice($sortedScores, 1, -1);

        $average = array_sum($middleScores) / count($middleScores);

        return round($average, $precision);
    }

    private function processReviews(array $reviews): array
    {
        if (empty($reviews)) {
            return ['No reviews yet', 0, 0, 0];
        }

        $lines = [];
        $allNumericScores = [];   // Only for Olympic
        $total = 0.0;
        $count = 0;               // Count of numeric scores only

        foreach ($reviews as $index => $review) {
            $reviewerId = $review['reviewer_id'] ?? null;
            $reviewer = $reviewerId ? ($this->usersById[$reviewerId] ?? null) : null;

            $reviewerName = $reviewer
                ? trim(($reviewer['name'] ?? '') . ' ' . ($reviewer['surname'] ?? ''))
                : 'Reviewer #' . ($index + 1);

            $scoreRaw = $review['total_score'] ?? '';

            // Determine what to display
            if ($scoreRaw === 'coi' || $scoreRaw === '' || $scoreRaw === null) {
                $displayScore = $scoreRaw === 'coi' ? 'coi' : '—';
                $numericScore = null;
            } else {
                $numericScore = (float)$scoreRaw;
                $displayScore = $numericScore;
            }

            // Only numeric scores contribute to totals and Olympic
            if ($numericScore !== null) {
                $total += $numericScore;
                $count++;
                $allNumericScores[] = $numericScore;
            }

            $comment = trim($review['reviewer_comment'] ?? '');

            // Build the line exactly in the format you showed
            $line = $reviewerName . "\n";
            $line .= "Score: " . $displayScore . "\n";

            if (!empty($comment)) {
                $line .= "Comments: " . $comment . "\n";
            }

            $lines[] = rtrim($line);

            // Add separator line between reviewers (but not after the last one)
            if ($index < count($reviews) - 1) {
                $lines[] = "";
            }
        }

        $olympicScore = $this->calculateOlympicScore($allNumericScores);

        $simpleAverage = ($count > 0) ? round($total / $count, 2) : 0;

        return [
            implode("\n", $lines),
            $total,
            $simpleAverage,
            $olympicScore ?? 0   // or null if you prefer
        ];
    }

    function getAllAuthorAcceptanceByAuthorPaper(){
        $allAcceptance = (new AuthorAcceptanceModel())->asArray()->findAll();

        $mapped = [];
        foreach($allAcceptance as $acceptance){
            $mapped[$acceptance['author_id'].'_'.$acceptance['abstract_id']] = $acceptance;
        }
        return $mapped;
    }
}