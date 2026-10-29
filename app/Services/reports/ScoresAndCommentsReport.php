<?php

namespace App\Services\reports;

use App\Models\AdminAcceptanceModel;
use App\Models\InstitutionModel;
use App\Models\PapersModel;
use App\Models\PaperTypeModel;
use App\Models\PaperUploadsModel;
use App\Models\UserModel;
use App\Models\UsersProfileModel;
use App\Services\AbstractReviewsServices;
use App\Services\AbstractServices;
use App\Services\InstitutionServices;
use App\Services\PaperAuthorsServices;
//use App\Services\PapersServices;
use CodeIgniter\Config\BaseService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ScoresAndCommentsReport extends BaseService
{
    private array $papersById         = [];
    private array $usersById          = [];
    private array $profilesByUserId   = [];
    private array $institutionsById   = [];
    private array $institutionsData = [];
    private array $paperTypesById     = [];
    private array $adminAcceptanceByPaper = [];
    private array $mappedAuthorsByPaper   = [];
    private array $categoriesByPaper      = [];
    private array $reviewsByPaper         = [];
    private array $finalizedPaperIds      = [];
    private array $paperUploads = [];

    private array $institutionData = [];


    public function generate(): void
    {
        $this->loadReferenceData();

        $activePaperIds = array_diff(
            array_keys($this->papersById),
            $this->getDeletedPaperIds()
        );

        $exportRows = [];

        foreach ($activePaperIds as $paperId) {
            $paper = $this->papersById[$paperId];

            $row = $this->buildExportRow($paper);
            if ($row === null) {
                continue;
            }

            $exportRows[] = $row;
        }

        $this->exportToExcel($exportRows);
    }

    private function loadReferenceData(): void
    {
        // Papers (all – we filter deleted later)
        $papers = model(PapersModel::class)->asArray()->findAll();
        $this->papersById = array_column($papers, null, 'id');

        // Users + profiles
        $profiles = model(UsersProfileModel::class)->asArray()->findAll();
        $this->profilesByUserId = array_column($profiles, null, 'author_id');

        $users = model(UserModel::class)->asArray()->findAll();
        foreach ($users as $user) {
            $uid = $user['id'];
            $this->usersById[$uid] = $user;
            $this->usersById[$uid]['profile'] = $this->profilesByUserId[$uid] ?? null;
        }

        // Other lookups
        $this->institutionsById   = array_column(model(InstitutionModel::class)->findAll(), null, 'id');
        $this->paperTypesById     = array_column((new PaperTypeModel())->findAll(), 'name', 'id');

        $this->adminAcceptanceByPaper = array_column(
            (new AdminAcceptanceModel())->getAllWithDetails(['status', 'user_id', 'acceptance_type', 'presentation_preference']),
            null,
            'abstract_id'
        );

        $this->mappedAuthorsByPaper = (new PaperAuthorsServices)->mappedPaperAuthors();
        $this->categoriesByPaper    = (new AbstractServices())->processMappedCategories();
        $this->reviewsByPaper       = (new AbstractReviewsServices())->getReviewsMappedByPaper();
        $this->finalizedPaperIds    = model(PapersModel::class)->getFinalizedPaperIds();
//        $this->paperUploads         = (new AbstractServices())->mappedUploads();
//        $this->institutionsData     = (new InstitutionServices())->getAllInstitutionWithAddress();

//        print_r($this->institutionsData  );exit;
    }

    private function getDeletedPaperIds(): array
    {
        $deleted = model(PapersModel::class)->getInActivePapers()->select('id')->asArray()->findAll();
        return array_column($deleted, 'id');
    }

    private function buildExportRow(array $paper): ?array
    {
        $paperId = $paper['id'];

        $submitter = $this->usersById[$paper['user_id']] ?? null;
        $submitterName = $submitter
            ? trim(($submitter['name'] ?? '') . ' ' . ($submitter['surname'] ?? ''))
            : '';

        $acceptance = $this->adminAcceptanceByPaper[$paperId] ?? null;
        $presentationPref = $acceptance && isset($this->paperTypesById[$acceptance['presentation_preference']])
            ? $this->paperTypesById[$acceptance['presentation_preference']]
            : '';

        $authorsData = $this->mappedAuthorsByPaper[$paperId] ?? [];
        [$authorsFormatted, $presentingIndices, $presentingAuthor] = $this->formatAuthors($authorsData);

        $reviewsData = $this->reviewsByPaper[$paperId] ?? [];

        [$reviewsText, $totalScore, $avgScore, $olympicScore] = $this->processReviews($reviewsData);

        $uploadString = $this->processUploads();
//        print_r($this->formatPresenter($presentingAuthor));exit;
        $presenterInfo = $this->formatPresenter($presentingAuthor);

        return [
            'id'                    => $paperId,
            'submitter'             => $submitterName,
            'status'                => in_array($paperId, $this->finalizedPaperIds, true) ? 'Finalized' : '',
            'authors'               => $authorsFormatted,
            'presenting_indices'    => $presentingIndices,
            'title'                 => $paper['title'] ?? '',
            'presentation_pref'     => $presentationPref,
            'category'              => $this->categoriesByPaper[$paperId] ?? '',
            'reviews'               => $reviewsText,
            'total_score'           => $totalScore,
            'average_score'         => $avgScore,
            'olympic_score'         => $olympicScore,
            'previous_presentation' => $paper['previous_presentation'] ?? '',
            'basic_science'         => $paper['basic_science_format'] ?? '',
//            'is_theme_aligned'      => $paper['is_theme_aligned'] == '' ? 'Yes' : 'No',
            'hypothesis'            => $paper['hypothesis'] ?? '',
            'study_design'          => $paper['study_design'] ?? '',
            'introduction'          => $paper['introduction'] ?? '',
            'methods'               => $paper['methods'] ?? '',
            'results'               => $paper['results'] ?? '',
            'conclusions'            => $paper['conclusions'] ?? '',
            'uploads'                => $uploadString,
            'image_caption'                => $paper['image_caption'] ?? '' ,
            'minimum_followup'              => $paper['min_follow_up_period'] ?? '' ,
            'is_srs_funded'                 => $paper['is_srs_funded'] ? 'Yes' : 'No', '',
            'fda_unapproved_uses'           => $paper['fda_unapproved_uses'] ? 'Yes' : 'No',
            'fda_discuss_product_name'      => $paper['fda_discuss_product_name'] ? 'Yes' : 'No',
            'fda_unapproved_explanation'    => $paper['fda_unapproved_explanation'] ?? '',
            'fda_product_name_explanation'  => $paper['fda_product_name_explanation'] ?? '',
            'is_fda_accepted'               => $paper['is_fda_accepted'] ? 'Yes' : 'No',
            'presenter_info'               => $presenterInfo,
        ];
    }

    private function formatAuthors(array $authorsData): array
    {
        $lines = [];
        $presenting = [];

        foreach ($authorsData as $idx => $author) {
            $user = $this->usersById[$author['author_id']] ?? null;
            if (!$user) {
                continue;
            }

            $nameParts = array_filter([
                $user['name'] ?? '',
                $author['user_middle'] ?? '',
                $user['surname'] ?? ''
            ]);

            $fullName = trim(implode(' ', $nameParts));
            if ($fullName === '') {
                continue;
            }

            $instId = $user['profile']['institution_id'] ?? null;
            $institution = $instId && !empty($this->institutionsData[$instId])
                ? $this->institutionsData[$instId]['institution_name']
                : '';

            $institutionData = $instId && !empty($this->institutionsData[$instId]) ? $this->institutionsData[$instId] : [];

            $line = ($idx + 1) . '. ' . $fullName;
            if ($institution !== '') {
                $line .= ', ' . $institution;
            }

            $lines[] = $line;

            if (!empty($author['is_presenting_author']) && strcasecmp($author['is_presenting_author'], 'yes') === 0) {
                $presenting[] = $idx;
                $presentingAuthor = [
                    'name' =>    $user['name'] ?? '',
                    'surname' => $user['surname'] ?? '',
                    'middle_name' => $user['middle_name'] ?? '',
                    'institution' => $institutionData,
                ];
            }
        }

        return [implode("\n", $lines), $presenting, $presentingAuthor ?? null];
    }

    private function processReviews(array $reviews): array
    {
        if (empty($reviews)) {
            // Return 4 elements to match the destructuring
            return ['No reviews yet', 0, 0, 0];
        }

        $lines = [];
        $total = 0.0;
        $count = 0;
        $scoresArray = [];

        foreach ($reviews as $review) {
            $reviewerId = $review['reviewer_id'] ?? null;
            $reviewer = $reviewerId ? ($this->usersById[$reviewerId] ?? null) : null;

            $reviewerName = $reviewer
                ? trim(($reviewer['name'] ?? '') . ' ' . ($reviewer['surname'] ?? ''))
                : 'Reviewer #' . ($reviewerId ?? '?');

            $scoreRaw = $review['total_score'] ?? '';
            $score = ($scoreRaw !== '' && $scoreRaw !== 'coi' && $scoreRaw !== 'na') ? (float)$scoreRaw : null;

            $scoresArray[] = $score;

            if ($score !== null) {
                $total += $score;
                $count++;
            }

            $comment = ($review['reviewer_comment'] ?? '');

            $lines[] = $reviewerName
                . "\nScore: " . ($score ?? strtoupper($scoreRaw) ?: '—')
                . "\nComments: " . ($comment ?: '—')
                . "\n";
        }

        $olympicScore = $this->calculateOlympicScore($scoresArray, $total, $count);

        $avg = $count > 0 ? round($total / $count, 2) : 0;


        return [implode("\n", $lines), $total, $avg, $olympicScore];
    }

    private function processUploads(): string{
            $lines = [];
            foreach ($this->paperUploads as $upload) {
                $lines[] = $upload['file_path'] . $upload['file_name'];
            }
            return implode("\n", $lines);
    }

    private function formatPresenter($presentingAuthor): string{
        if($presentingAuthor === null){
            return '';
        }
        $parts = [
            trim($presentingAuthor['name'] . ' ' . $presentingAuthor['surname']),
            $presentingAuthor['institution']['institution_name'] ?? null,
            $presentingAuthor['institution']['institution_city'] ?? null,
            $presentingAuthor['institution']['institution_state'] ?? null,
            $presentingAuthor['institution']['institution_country'] ?? null
        ];

        $parts = array_filter($parts, function($value) {
            return !empty($value) && trim($value) !== '';
        });

        return implode(', ', $parts);
    }

    private function calculateOlympicScore(array $scoresArray, float $total, int $count): float
    {
        $validScores = array_filter($scoresArray, function($score) {
            return $score !== null;
        });


        if (count($validScores) >= 3) {
            // Sort while preserving keys if needed
            asort($validScores);
            $trimmedScores = array_slice($validScores, 1, -1, true);
            $olympicScore = round(array_sum($trimmedScores) / count($trimmedScores), 2);
        }

        return $olympicScore ?? 0;
    }

    private function exportToExcel(array $rows)
    {
        $excel = new Spreadsheet();
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('Abstract Scores & Comments');

        $headers = [
            'Abstract ID',
            'Submitter',
            'Submission Status',
            'Authors',
            'Title',
            'Category',
            'Reviews',
            'Total Score',
            'Average Score',
            'Olympic Score',
            'Previous Presentation',
            'Basic Science Format',
            'Is Theme Aligned',
            'Hypothesis',
            'Study Design',
            'Introduction',
            'Methods',
            'Results',
            'Conclusion',
            'Uploads',
            'Image Caption',
            'Minimum Follow-up Period',
            'Is SRS Funded',
            'FDA Unapproved Uses',
            'FDA Discuss Product Name',
            'FDA Unapproved Explanation',
            'FDA Product Name Explanation',
            'Is FDA Accepted',
            'Presenter Info',
        ];

        $sheet->fromArray($headers, null, 'A1');

        $rowNum = 2;
        foreach ($rows as $data) {
            $col = 'A';

            $sheet->setCellValue($col++ . $rowNum, $data['id']);
            $sheet->setCellValue($col++ . $rowNum, $data['submitter']);
            $sheet->setCellValue($col++ . $rowNum, $data['status']);

            // Authors with bold presenting authors
            $authorsCell = $col++ . $rowNum;
            $authorsText = $data['authors'];
            $presenting = $data['presenting_indices'];


            if (empty($presenting)) {
                $sheet->setCellValue($authorsCell, $authorsText);
            } else {
                $rich = new RichText();
                $parts = explode("\n", $authorsText);

                foreach ($parts as $i => $line) {
                    if ($i > 0) {
                        $rich->createText("\n");
                    }
                    $run = $rich->createTextRun($line);
                    if (in_array($i, $presenting, true)) {
                        $run->getFont()->setBold(true);
                    }
                }
                $sheet->setCellValue($authorsCell, $rich);
            }

            $sheet->setCellValue($col++ . $rowNum, $data['title']);
            $sheet->setCellValue($col++ . $rowNum, $data['category']);
            $sheet->setCellValue($col++ . $rowNum, $data['reviews']);
            $sheet->setCellValue($col++ . $rowNum, $data['total_score']);
            $sheet->setCellValue($col++ . $rowNum, $data['average_score']);
            $sheet->setCellValue($col++ . $rowNum, $data['olympic_score']);
            $sheet->setCellValue($col++ . $rowNum, $data['previous_presentation']);
            $sheet->setCellValue($col++ . $rowNum, $data['basic_science']);
//            $sheet->setCellValue($col++ . $rowNum, $data['is_theme_aligned']);
            $sheet->setCellValue($col++ . $rowNum, $data['hypothesis']);
            $sheet->setCellValue($col++ . $rowNum, $data['study_design']);
            $sheet->setCellValue($col++ . $rowNum, $data['introduction']);
            $sheet->setCellValue($col++ . $rowNum, $data['methods']);
            $sheet->setCellValue($col++ . $rowNum, $data['results']);
            $sheet->setCellValue($col++ . $rowNum, $data['conclusions']);
            $sheet->setCellValue($col++ . $rowNum, $data['uploads']);
            $sheet->setCellValue($col++ . $rowNum, $data['image_caption']);
            $sheet->setCellValue($col++ . $rowNum, $data['minimum_followup']);
            $sheet->setCellValue($col++ . $rowNum, $data['is_srs_funded']);
            $sheet->setCellValue($col++ . $rowNum, $data['fda_unapproved_uses']);
            $sheet->setCellValue($col++ . $rowNum, $data['fda_discuss_product_name']);
            $sheet->setCellValue($col++ . $rowNum, $data['fda_unapproved_explanation']);
            $sheet->setCellValue($col++ . $rowNum, $data['fda_product_name_explanation']);
            $sheet->setCellValue($col++ . $rowNum, $data['is_fda_accepted']);
            $sheet->setCellValue($col++ . $rowNum, $data['presenter_info'] ?? '');
            $rowNum++;
        }

        // Styling & formatting
        $lastCol = $sheet->getHighestDataColumn();
        $lastRow = $sheet->getHighestRow();

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getStyle("A1:{$lastCol}1")->getFont()->setBold(true);

        $sheet->getStyle("A2:{$lastCol}{$lastRow}")
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_TEXT);

        // Output
        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.env('app.name').'_Scores_and_Comments_' . date('Y-m-d') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($excel);
        $writer->save('php://output');
        exit;
    }

}