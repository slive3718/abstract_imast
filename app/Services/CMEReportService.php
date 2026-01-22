<?php

namespace App\Services;

use App\Controllers\Disclosure;
use App\Models\AbstractCategoriesModel;
use App\Models\AdminAcceptanceModel;
use App\Models\AffiliationsModel;
use App\Models\CMEReviewsModel;
use App\Models\DesignationsModel;
use App\Models\OrganizationsModel;
use App\Models\PaperAuthorsModel;
use App\Models\PapersModel;
use App\Models\PaperTypeModel;
use App\Models\SchedulerModel;
use App\Models\SchedulerSessionTalksModel;
use App\Models\SiteSettingModel;
use App\Models\UserModel;
use App\Models\UsersProfileModel;
use CodeIgniter\Config\BaseService;

class CMEReportService extends BaseService
{

    private $request;

    public function __construct()
    {

        $this->request = \Config\Services::request();
    }


    public function generateCMEReport()
    {
        $this->loadCmeReviews();
        // Implement report generation logic here
        $reportData = $this->processReport();

        $excel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('Abstract All Data Export', true);

        // Apply header styling
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 12],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => '8fce00']
            ],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];

        $dataStyle = [
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ];

        if(empty($reportData))
            throw new \Exception('No data found for the report.');


        $headers = [
            "Reviewer",
            "Abstract ID",
            "Assigned ID",
            "Presenting Author first and last name",
            "Disclosures",
            "Commercial Bias",
            "Content Validity",
            "Mitigation",
            "Request",
            "Signature",
            "Date",
        ];


        $sheet->fromArray($headers, null, 'A1');
        $sheet->fromArray($reportData, null, 'A2');

        // Get the highest column with data
        $highestColumn = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();
// Apply header style dynamically for all columns from A to the highest column
        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray($headerStyle);

// Apply data style for all columns from A2 to the last row dynamically
        $sheet->getStyle('A2:' . $highestColumn . $sheet->getHighestRow())->applyFromArray($dataStyle);

// Loop through each column from A to the highest column
        foreach (range('A', $highestColumn) as $columnID) {
            $maxLength = 0;

            // Loop through each row for the current column
            for ($row = 1; $row <= $highestRow; $row++) {
                $cellValue = $sheet->getCell($columnID . $row)->getValue();

                // Check the length of the cell content and keep track of the maximum length
                if ($cellValue !== null) {
                    $cellLength = strlen($cellValue);
                    if ($cellLength > $maxLength) {
                        $maxLength = $cellLength;
                    }
                }
            }

            // Set column width based on the maximum length of content
            $sheet->getColumnDimension($columnID)->setWidth($maxLength + 2); // Add padding
        }
        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="CME_report' . date('Y-m-d') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $xlsxWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($excel, 'Xlsx');
        $xlsxWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($excel);

        exit($xlsxWriter->save('php://output'));
    }

    private function processReport(){

        $cmeReviews = $this->loadCmeReviews();
        $users = (new UserModel())->findAll();
        $papers = (new PapersModel())->asArray()->findAll();
        $authors = (new PaperAuthorsModel())->getPresentingAuthors()->findALl();

        //Note: this will only work because we have a single presenter per paper!
        $presenter = array_column($authors, null, 'paper_id');
        $profiles = (new UserProfilesServices())->getProfileWithDisclosureData();

        $reportRow[] = [];
        foreach ($cmeReviews as $review){
            if(empty($review))
                continue;

            $presenterName = $presenter[$review['paper_id']]['user_name'] . ' ' . $presenter[$review['paper_id']]['user_surname'];
            $presenterDisclosure = $this->processDisclosures($profiles[$presenter[$review['paper_id']]['author_id']]);
            $reportRow[] = [
//                'Review ID' => $review['cme_reviewer_id'],
                'Reviewer Full Name' => isset($users[$review['cme_reviewer_id']]) ? $users[$review['cme_reviewer_id']]['name'] . ' ' . $users[$review['cme_reviewer_id']]['surname'] : 'Unknown',
                'Abstract ID' => $review['paper_id'],
                'Assigned ID' => $papers[$review['paper_id']]['assigned_id'] ?? 'Unknown',
                'Presenter' => $presenterName,
                'Disclosures' => $presenterDisclosure ?: 'Incomplete',
                'Content Validity' => $this->processReviewsContentValidity($review),
                'Evidence Basis' => $this->processReviewEvidenceBasis($review),
                'Mitigation' => $this->mitigationChoices($review['answer12']),
                'Request' => $review['request_text'],
                'Signature' => $review['e_signature'],
                'Date' => $review['updated_at'],
            ];
        }

        return $reportRow ?? [];

    }

    private function processReviewsContentValidity($reviews){

        if(!$reviews)
            return '';

        $processedReview = [];
        $allValidityYes = true;
        for($a=1; $a<=5; $a++){
            if( $reviews['answer'.$a] === 'no' || $reviews['answer'.$a] === 'na'){
                $allValidityYes = false;
                $processedReview['content_validity'][] = ucFirst($reviews['answer'.$a]).' '.$this->loadChoices('1.'.$a);
            }
        }
        return $allValidityYes ? 'All Yes' : implode("\n", $processedReview['content_validity']);
    }

    private function processReviewEvidenceBasis($reviews){
        if(!$reviews)
            return '';

        $b = 0;
        $allEvidenceYes = true;
        for($a=6; $a<=11; $a++){
            $b++;
            if($reviews['answer'.$a] === 'no' || $reviews['answer'.$a] === 'na'){
                $allEvidenceYes = false;
                $processedReview['evidence_basis'][] = ucFirst($reviews['answer'.$a]).' '.$this->loadChoices('2.'.$b);
            }
        }
        return $allEvidenceYes ? 'All Yes' : implode("\n", $processedReview['evidence_basis']);
    }

    private function loadCmeReviews(){
        $cmeReviews = (new CMEReviewsModel())->findAll();
        return $cmeReviews ?? [];
    }

    private function loadChoices($choiceIds){
        switch($choiceIds){
            case '1.1':
                return "1.1: The content is relevant to products or business lines of commercial interests that have been disclosed.";

            case '1.2':
                return "1.2: The content or format promotes improvements or quality in healthcare and not a specific proprietary business interest of an ineligible company.";

            case '1.3':
                return "1.3: The content uses generic names of products and/or comparable trade names from several companies rather than one single company.";

            case '1.4':
                return "1.4: The content is balanced on its discussion of therapeutic options and products.";

            case '1.5':
                return "1.5: The content is free from commercial bias.";

            case '2.1':
                return "2.1: Recommendations for patient care are based on current science, evidence and clinical reasoning.";

            case '2.2':
                return "2.2: Content has a fair and balanced view of diagnostic and therapeutic options.";

            case '2.3':
                return "2.3: Scientific research conforms to the generally accepted standards of experimental design, data collection, analysis, and interpretation.";

            case '2.4':
                return "2.4: New and evolving topics for which there is a lower (or absent) evidence base, are clearly identified as such within the education and individual presentations.";

            case '2.5':
                return "2.5: The content avoids advocating for, or promoting, practices that are not, or not yet, adequately based on current science, evidence, and clinical reasoning.";

            case '2.6':
                return "2.6: The content excludes any advocacy for, or promotion of, unscientific approaches to diagnosis or therapy, or recommendations, treatment, or manners of practicing healthcare that are determined to have risks or dangers that outweigh the benefits or are known to be ineffective in the treatment of patients.";

            default:
                return "";
        }
    }

    private function mitigationChoices($answer){
        switch ($answer):
            case 1:
                return "I, the reviewer, have a financial relationship that is relevant to the content of this abstract and/or a conflict of interest with this content, and it should be reviewed by someone who is not conflicted. ";
            case 2:
                return "No further action is recommended – the financial relationships disclosed are not relevant to the content submitted. ";
            case 3:
                return "Request the speaker to make the following revisions to the content. ";
            case 4:
                return "Request the speaker to submit presentation materials for further review. ";
            default:
                return "";
        endswitch;
    }

    private function processDisclosures($disclosures):string
    {

        $parts = [];

//        print_R($disclosures);exit;
        if($disclosures['profile']['disclosure_relationship'] === '0')
            return 'No Relationship';

        foreach ($disclosures['organizations'] ?? [] as $org) {
            // Determine the display name (priority order)
            $name = trim(
                $org['organization_name'] ??
                $org['custom_organization'] ??
                'Unknown Organization'
            );

            if($name === 'Other')
                $name = $org['custom_organization'];

            // Collect all unique letters from affiliations
            $letters = [];
            foreach ($org['affiliations'] ?? [] as $aff) {
                if (preg_match('/^([a-zA-Z]) -/', $aff, $matches)) {
                    $letter = strtolower($matches[1]);
                    if ($letter && !in_array($letter, $letters)) {
                        $letters[] = $letter;
                    }
                }
            }

            if (empty($letters)) {
                // Rare case: no valid affiliation letter → skip or handle differently
                continue;
                // or: $letters = ['?'];
            }

            // Build the suffix: (b) or (a,b) or (a,b,c)
            $letterStr = implode(',', $letters);
            $suffix = " ($letterStr)";

            $parts[] = trim("$name$suffix");
        }

        $imploded = implode('; ', $parts);

        return $imploded;   // ← most likely what you want in a private processing method
    }

}
