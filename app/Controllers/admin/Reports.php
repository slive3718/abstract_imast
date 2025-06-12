<?php

namespace App\Controllers\admin;

use App\Controllers\admin\Abstracts\AbstractController;
use App\Models\SchedulerModel;
use App\Models\SchedulerSessionTalksModel;

class Reports extends AbstractController
{

    function all_abstract_data()
    {

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

        $papers = $this->getAllPapersArray('paper');
        $exportHeader = $this->exportHeader();
        if (!empty($papers)) {
            foreach ($papers as $index => $paper) {

                $authorList = '';
                $presentingAuthors = [];

                // Extract presenting authors and co-authors
                foreach ($paper->authors as $author) {
                    if ($author) {
                        if ($author->is_presenting_author == 'Yes') {
                            $authorList .= "Presenting Author: " . $author->user_name . ' ' . $author->user_surname . ' ';
                            $presentingAuthors[] = $author;
                        } elseif ($author->is_coauthor == 'Yes') {
                            $authorList .= "Co-Author: " . $author->user_name . ' ' . $author->user_surname . ' ';
                        }
                    }
                }

                // Handle uploads
                $uploads = '';
                if ($paper->uploads) {
                    $upload_names = array_map(function ($upload) {
                        return $upload['file_preview_name'];
                    }, $paper->uploads);

                    // Remove duplicates and join names into a string
                    $upload_names = array_unique($upload_names);
                    $uploads = implode(',', $upload_names);
                }

                // Handle admin acceptance and presentation preferences
                $adminAcceptance = '';
                $adminPresentationPref = '';
                if ($paper->adminOption) {
                    if ($paper->adminOption['acceptance_confirmation'] == 1) {
                        $adminAcceptance = "Accepted";
                        switch ($paper->adminOption['presentation_preference']) {
                            case 1: $adminPresentationPref = 'Presentation Only'; break;
                            case 2: $adminPresentationPref = 'Publication Only'; break;
                            case 3: $adminPresentationPref = 'Presentation and Publication'; break;
                        }
                    } elseif ($paper->adminOption['acceptance_confirmation'] == 2) {
                        $adminAcceptance = "Rejected";
                    } elseif ($paper->adminOption['acceptance_confirmation'] == 3) {
                        $adminAcceptance = "Suggested Revision";
                    } elseif ($paper->adminOption['acceptance_confirmation'] == 4) {
                        $adminAcceptance = "Required Revision";
                    } elseif ($paper->adminOption['acceptance_confirmation'] == 5) {
                        $adminAcceptance = "Declined/Withdrawn for Participation";
                    }
                }

                $paperType = '';
                switch ($paper->type_id){
                    case 1: $paperType = 'Presentation Only'; break;
                    case 2: $paperType = 'Publication Only'; break;
                    case 3: $paperType = 'Presentation and Publication'; break;
                }

                $ijmcStatus = '';
                switch ($paper->is_ijmc_interested){
                    case 0: $ijmcStatus = 'I am NOT interested in submitting this paper to IJMC'; break;
                    case 1: $ijmcStatus = 'I am interested in submitting this paper to IJMC'; break;
                    case 2: $ijmcStatus = 'I have already submitted this paper to IJMC'; break;
                }

                // Add paper data to the export
                $exportData[$index] = [
                    strip_tags($paper->custom_id),
                    strip_tags($paper->submission_type),
                    strip_tags($paper->title),
                    strip_tags($paper->summary),
                    strip_tags($ijmcStatus),
                    strip_tags($paper->tracks),
                    $authorList,
                    $paperType,
                    $paper->division->name,
                    $uploads,
                    $adminAcceptance .($adminPresentationPref ? " (" . $adminPresentationPref . ")":''),
                    $paper->adminComment ? $paper->adminComment['comment'] : '',
                    $paper->adminComment ? $paper->adminComment['comment'] : '',
                    $paper->adminComment ? $paper->user_name. ' '. $paper->user_surname  : '',
                    $paper->adminComment ? $paper->user_email : '',
                ];

                $talkScheduleData = [
                    !empty($paper->schedule['session_date']) ? date('Y-m-d', strtotime($paper->schedule['session_date'])) : '',
                    !empty($paper->schedule['session_start_time']) ? date('h:i a', strtotime($paper->schedule['session_start_time'])) : '',
                    !empty($paper->schedule['session_end_time']) ? date('h:i a', strtotime($paper->schedule['session_end_time'])) : '',
                    !empty($paper->schedule['time_start']) ? date('h:i a', strtotime($paper->schedule['time_start'])) : '',
                    !empty($paper->schedule['time_end']) ? date('h:i a', strtotime($paper->schedule['time_end'])) : '',
                ];
                $exportData[$index] = array_merge($exportData[$index], $talkScheduleData);
                // Loop through additional presenting authors
                for ($i = 0; $i < count($presentingAuthors); $i++) {
//                    print_r($presentingAuthors);exit;
                    $exportData[$index][] = isset($presentingAuthors[$i]) ? $presentingAuthors[$i]->user_name : '';
                    $exportData[$index][] = isset($presentingAuthors[$i]) ? $presentingAuthors[$i]->user_middle : '';
                    $exportData[$index][] = isset($presentingAuthors[$i]) ? $presentingAuthors[$i]->user_surname : '';
                    $exportData[$index][] = isset($presentingAuthors[$i]) ? $presentingAuthors[$i]->details['deg'] : '';
                    $exportData[$index][] = isset($presentingAuthors[$i]) ? $presentingAuthors[$i]->user_email : '';
                    $exportData[$index][] = isset($presentingAuthors[$i]) ? $presentingAuthors[$i]->details['address'] : '';
                    $exportData[$index][] = isset($presentingAuthors[$i]) ? $presentingAuthors[$i]->details['city'] : '';
                    $exportData[$index][] = isset($presentingAuthors[$i]) ? $presentingAuthors[$i]->details['province'] : '';
                    $exportData[$index][] = isset($presentingAuthors[$i]) ? $presentingAuthors[$i]->details['country'] : '';
                    $exportData[$index][] = isset($presentingAuthors[$i]) ? $presentingAuthors[$i]->details['zipcode'] : '';
                    $exportData[$index][] = isset($presentingAuthors[$i]) ? $presentingAuthors[$i]->details['institution'] : '';
                    $exportData[$index][] = isset($presentingAuthors[$i]) ? $presentingAuthors[$i]->details['phone'] : '';
                    $exportData[$index][] = isset($presentingAuthors[$i]) ? $presentingAuthors[$i]->acceptance ? $presentingAuthors[$i]->acceptance->author_bio : '' : '';
                    $exportData[$index][] = isset($presentingAuthors[$i]) ? $presentingAuthors[$i]->acceptance ?  $presentingAuthors[$i]->acceptance->breakfast_attendance : '' : '';
                    $exportData[$index][] = isset($presentingAuthors[$i]) ? $presentingAuthors[$i]->acceptance && $presentingAuthors[$i]->acceptance->presentation_file_path ?  base_url().$presentingAuthors[$i]->acceptance->presentation_file_path.'/'.$presentingAuthors[$i]->acceptance->presentation_saved_name : '' : '';
                    $exportData[$index][] = '';
                }



            }
        }
//        print_r($exportData);exit;
        // Output the export data into the sheet
        $sheet->fromArray($exportHeader, null, 'A1');
        $sheet->fromArray($exportData, null, 'A2');


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
        header('Content-Disposition: attachment;filename="AFS_All_Data_Export_' . date('Y-m-d') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $xlsxWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($excel, 'Xlsx');
        $xlsxWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($excel);

        exit($xlsxWriter->save('php://output'));
    }

    function printAll(){

        $papers = $this->getAllPapersArray('paper');
        print_r(json_encode($papers));exit;
    }


    function exportHeader(){
        $exportHeader =  [[
            'AbstractID',
            'Submission Status',
            'Title',
            'Summary',
            'Submit to IJMC?',
            'Track(s)',
            'Authors List',
            'Type',
            'Division',
            'Formal Upload',
            'Acceptance Status',
            'Comments to Submitter',
            'Admin comments',
            'Submitter Name',
            'Submitter Email',

        ]];

        $scheduleHeader = [
            'Session Date',
            'Session Start',
            'Session End',
            'Talk Start',
            'Talk End',
        ];

        $exportHeader[0]  = array_merge($exportHeader[0], $scheduleHeader);

       for($i=1; $i<6; $i++){
           $additionalHeader = [
               'Presenting Author'.$i.' Firstname',
               'Presenting Author'.$i.' MiddleName',
               'Presenting Author'.$i.' Lastname',
               'Presenting Author'.$i.' Degree',
               'Presenting Author'.$i.' Email',
               'Presenting Author'.$i.' Address',
               'Presenting Author'.$i.' City',
               'Presenting Author'.$i.' State',
               'Presenting Author'.$i.' Country',
               'Presenting Author'.$i.' Postal Code',
               'Presenting Author'.$i.' Institution',
               'Presenting Author'.$i.' Work Phone',
               'Presenting Author'.$i.' Biography',
               'Presenting Author'.$i.' Breakfast Attendance',
               'Presenting Author'.$i.' Acceptance Upload',
           ];
           $exportHeader[0] = array_merge($exportHeader[0], $additionalHeader);
       }




        return $exportHeader;
    }

}