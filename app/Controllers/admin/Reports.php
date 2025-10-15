<?php

namespace App\Controllers\admin;

use App\Controllers\admin\Abstracts\AbstractController;
use App\Controllers\ItineraryController;
use App\Models\SchedulerDatesModel;
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
                    $paper->talkSchedule['session_date'] ?? '',
                    $paper->talkSchedule['session_start_time'] ?? '',
                    $paper->talkSchedule['session_end_time'] ?? '',
                    $paper->talkSchedule['time_start'] ?? '',
                    $paper->talkSchedule['time_end'] ?? '',
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

    public function agendaToWord(){
        $scheduledDates = (new SchedulerDatesModel())->findAll();
        $schedules = (new ItineraryController())->getItinerary($scheduledDates);

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();

        $font1 = 'Calibri';
        $fontSize1 = 12;
        $fontSize2 = 14;
        $fontStyle1 = 'bold';

        if($schedules){
//            print_r($schedules);exit;
            foreach ($schedules as $schedule) {

                if($schedule['description']){
                    $section->addText(
                        date('l, F d, Y', strtotime($schedule['date'])),
                        array('name' => 'Tahoma', 'size' => $fontSize1, $fontStyle1 => true)
                    );
                }

                foreach ($schedule['events'] as $event) {
                    if($event['session_start_time'] && $event['session_end_time']) {
                        $section->addText(
                            date('H:i', strtotime($event['session_start_time'])) .'-'. date('H:i', strtotime($event['session_end_time'])),
                            array('name' => $font1, 'size' => $fontSize2, $fontStyle1 => true)
                        );
                    }

                    $section->addText(
                        $event['session_title'],
                        array('name' => $font1, 'size' => $fontSize2, $fontStyle1 => true)
                    );

                    if($event['moderators']) {
                        $moderatorsJoined = '';
                        foreach ($event['moderators'] as $index => $moderator) {
                            $designations = '';
                            $designationsIds = ($moderator['designations']) ? json_decode($moderator['designations']) : [];
                            if($designationsIds) {
                                $designations = (new \App\Models\DesignationsModel())->whereIn('id', $designationsIds)->findAll();
                                $newDesignations = array_map(function ($designation) use ($moderator) {
                                    if(strtolower(trim($designation['name'])) == 'other') {
                                        $ret = $moderator['other_designation'];
                                    } elseif (strtolower(trim($designation['name'])) == 'none') {
                                        $ret = '';
                                    } else {
                                        $ret = $designation['name'];
                                    }
                                    return $ret;
                                }, $designations);
                                $designations = implode(', ', $newDesignations);
                            }
                            $moderatorsJoined .= (!empty($moderatorsJoined) ? " & " : '') . $moderator['name'] . ($moderator['middle_name'] ? ' ' .$moderator['middle_name'].'.' : ''). ' ' . $moderator['surname']. ($designations ? ', '. $designations: '');
                        }

                        $section->addText(
                            'Moderators:'."\t" . $moderatorsJoined,
                            array('name' => $font1, 'size' => 12, 'italic' => true),
                        );
                    }


                    foreach ($event['talks'] as $talk){

                        $talkAcceptanceConfirmation = $talk['admin_acceptance']['acceptance_confirmation'] ?? null;
                        $talkAcceptancePreference = $talk['admin_acceptance']['presentation_preference'] ?? null;
                        $acceptedAsPodium = ($talkAcceptanceConfirmation && $talkAcceptancePreference == 1);
                        $textRun = $section->addTextRun(array(
                            'spaceAfter' => 0,
                            'keepNext' => true,
                            'indentation' => array(
                                'left' => 1440,      // Total left margin
                                'hanging' => 1440    // Hanging indent (first line will be at left:0, subsequent lines at left:1440)
                            )
                        ));

                        $textRun->addText(
                            date('H:i', strtotime($talk['time_start'])) . ' - ' . date('H:i', strtotime($talk['time_end'])) . "\t",
                            array('name' => $font1, 'size' => 12) // Regular font
                        );

                        $textRun->addText(
                            ($talk['assigned_id'] && $acceptedAsPodium ? 'Paper#'. $talk['assigned_id'] . ': ': '').($talk['title'] == '' ? $talk['custom_abstract_desc'] : $talk['title']),
                            array('name' => $font1, 'size' => 12, 'bold' => true), // Bold font for title
                        );

//                        $presenterJoined = '';
//
//                        foreach ($talk['presenters'] as $index => $presenter) {
//                            $presenterDesignations = '';
//                            $designationsIds = ($presenter['details']['designations']) ? json_decode($presenter['details']['designations']) : [];
//                            if($designationsIds) {
//                                $designations = (new \App\Models\DesignationsModel())->whereIn('id', $designationsIds)->findAll();
//                                $designations = array_map(function ($designation) {
//                                    return $designation['name'];
//                                }, $designations);
//                                $presenterDesignations = implode(', ', $designations);
//                            }
//                            $presenterJoined .= (!empty($presenterJoined) ? "; " : '') . $presenter['user_name'] . ' ' .($presenter['user_middle'] ? $presenter['user_middle'].'.' : ''). ' ' . $presenter['user_surname']. ($presenterDesignations ? ', '. $presenterDesignations: '');
//                        }

//                        Authors lists
                        $authorsJoined = '';
                        $filteredAuthorsArray = array_values(array_filter($talk['authors'], fn($author) => empty($author['is_removed'])));

                        foreach ($filteredAuthorsArray as $index => $author) {
                            $authorDesignations = '';
                            $designationsIds = !empty($author['details']['designations']) ? json_decode($author['details']['designations']) : [];
                            if ($designationsIds) {
                                $designations = (new \App\Models\DesignationsModel())->whereIn('id', $designationsIds)->findAll();
                                $newDesignations = array_map(function ($designation) use ($author) {
                                    if(strtolower(trim($designation['name'])) == 'other') {
                                        $ret = $author['details']['other_designation'];
                                    } elseif (strtolower(trim($designation['name'])) == 'none') {
                                        $ret = '';
                                    } else {
                                        $ret = $designation['name'];
                                    }
                                    return $ret;
                                }, $designations);
                                $authorDesignations = implode(', ', $newDesignations);
                            }
                            $authorFullName = $author['user_name'] . ' ' . ($author['user_middle'] ? $author['user_middle'] . '. ' : '') . $author['user_surname'];
                            $authorsJoined .= (!empty($authorsJoined) ? "; " : '') . trim($authorFullName) . ($authorDesignations ? ', ' . $authorDesignations : '');
                        }

//                        // Create a text run to allow different styles for each author
                        if(!$talk['custom_abstract_desc'])
                            $textRun = $section->addTextRun([
                                'indentation' => ['left' => 1440]
                            ]);

                        // Split authorsJoined by semicolon to style each author
                        $authorsArray = explode('; ', $authorsJoined);

                        foreach ($filteredAuthorsArray as $index => $author) {
                            $fontStyle = [
                                'name' => $font1,
                                'size' => 12,
                                'italic' => $author['is_presenting_author'] == 'Yes'
                            ];

                            if(count($authorsArray) > 1) {
                                $fontStyle['underline'] = ($author['is_presenting_author'] == 'Yes') ? 'single' : 'none';
                            }
                            $textRun->addText($authorsArray[$index] . (isset($authorsArray[$index + 1]) ? '; ' : ''), $fontStyle);
                        }
                    }
                }
                $section->addTextBreak(1);
            }
        }
        \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save('Agenda.docx');

        // Save to a temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'Agenda') . '.docx';
        $objWriter->save($tempFile);

        // Send headers for download
//        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
//        header('Content-Disposition: attachment; filename="Agenda.docx"');
//        header('Content-Length: ' . filesize($tempFile));
//        header('Cache-Control: no-cache, must-revalidate');
//        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
//
//// Output the file
//        readfile($tempFile);
//
//// Clean up the temporary file
//        unlink($tempFile);

    }

}