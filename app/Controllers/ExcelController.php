<?php
namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;


use App\Models\UserModel;
use App\Models\PapersModel;
use App\Models\PaperAuthorModel;
use App\Models\ReviewerModel;
use App\Models\AbstractTopicsModel;
use App\Models\PopulationModel;
use App\Models\AbstractReviewModel;
use App\Models\InstitutionModel;
use App\Models\AuthorDetailsModel;
use App\Models\LearningObjectivesModel;

class ExcelController extends BaseController
{
    public function export($event_uri, $data)
    {    

    // $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    // $sheet = $spreadsheet->getActiveSheet();

    $excel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $excel->getActiveSheet();
    // $sheet->setTitle('This is a test', true);

    // Set headers
    $sheet->setCellValue('A1', 'ID');
    $sheet->setCellValue('B1', 'LEAD PRESENTER');
    $sheet->setCellValue('C1', 'ABSTRACT TITLE');
    $sheet->setCellValue('D1', 'AUTHOR LIST');
    $sheet->setCellValue('E1', 'Primary Topic');
    $sheet->setCellValue('F1', 'Secondary Topic');
    $sheet->setCellValue('G1', 'REVIEWERS TOTAL SCORES');
    $sheet->setCellValue('H1', 'REVIEWERS AVERAGE TOTAL SCORES');
    $sheet->setCellValue('I1', 'OVERALL VOTE TOTAL SCORES');
    $sheet->setCellValue('J1', 'OVERALL VOTE SCORE AVERAGE');
    $sheet->setCellValue('K1', 'COMMENTS');
    $row = 2;
    
    $authorDetails = '';
    // Populate data
    // echo '<pre>';
    
    foreach ($data as $abstract) {
        $authorDetails = '';
        $primary_topic_text= '';
        $secondary_topic_text= '';
        // print_r($abstract);
        // $abstractReviewerTotalScore = 0;
        $sheet->setCellValue('A' . $row, $abstract->id);
        if(isset($abstract->lead_presenter[0]['name'])){
            $sheet->setCellValue('B' . $row, trim(($abstract->lead_presenter[0]['name'].' '. $abstract->lead_presenter[0]['surname'])));
        }
        $sheet->setCellValue('C' . $row, trim(($abstract->title)));
        if(isset($abstract->author)){
            foreach ($abstract->author as $author){
                $authorDetails .= ($author['details']['name']." ".$author['details']['surname']."\n");
            }

              }
        
        if(isset($abstract->primary_topic)){
            foreach(json_decode($abstract->primary_topic) as $primary_topic){
                $topic = (new AbstractTopicsModel())->where('id',$primary_topic)->first();
                // print_r($topic);exit;
                $primary_topic_text .= $topic['name']. "\n";
            }
        }


        if(isset($abstract->secondary_topic)){
            foreach(json_decode($abstract->secondary_topic) as $secondary_topic){
                $topic = (new AbstractTopicsModel())->where('id',$secondary_topic)->first();
                $secondary_topic_text .= $topic['name']. "\n";
            }
        }

        // print_R($substanceAreaText);
        //  print_R($populationText);
        $authorDetails = rtrim($authorDetails, "\n");
        $sheet->setCellValue('D' . $row,($authorDetails));
       
        // print_R($abstract->reviewersTotalScore);exit;
        
        if(isset($abstract->reviewersTotalScore)){
            $sum = 0;
            $overAllReviews = 0;
            foreach ($abstract->reviewersTotalScore as $review) {
                $reviewerModel = (new AbstractReviewModel())->where(['reviewer_id'=> $review['reviewer_id'], 'abstract_id'=>$abstract->id])->first();
                if($reviewerModel['with_conflict_of_interest'] == 0){
                    $sum += $review['total_score'];
                    $overAllReviews = $overAllReviews + 1;
                }
            }

            if($overAllReviews > 0){
                $averageTotalScores = ($sum/$overAllReviews);
            }else{
                $averageTotalScores = 0;
            }
        }

        if(isset($abstract->overallVote)){
            $overallVoteSum = 0;
            $overAllReviewers = 0;
            // foreach ($abstract->overallVote as $review) {
            //     $reviewerModel = (new AbstractReviewModel())->where(['reviewer_id'=> $review['reviewer_id'], 'abstract_id'=>$abstract->id])->first();
            //     if($reviewerModel['conflict_of_interest'] == 'No'){
            //         $overallVoteSum += $review['overall_vote'];
            //         $overAllReviewers = $overAllReviewers+1;
            //     }
            // }

            // if($overAllReviewers > 0){
            //     $averageOverallVote = ($overallVoteSum/$overAllReviewers);
            // }else{
            //     $averageOverallVote = 0;
            // }
        }

        if(isset($abstract->reviewComments) ){
            
            $reviewCommentText = '';
            foreach($abstract->reviewComments as $reviewComment){
                if(!empty($reviewComment)){
                    // print_r($reviewComment);
                    if(isset($reviewComment['userDetails'])){
                         $reviewCommentText.=  "Reviewer: ".$reviewComment['userDetails'][0]['name'].' '.$reviewComment['userDetails'][0]['surname']."\n";
                    }
               
                $reviewCommentText.= "Methodology/Hypothesis: ".$reviewComment['methodology_score']."\n";
                $reviewCommentText.= "Data Analysis: ".$reviewComment['data_analysis_score']."\n";
                $reviewCommentText.= "Discovery/Interpretation: ".$reviewComment['interpretation_score']."\n";
                $reviewCommentText.= "Clarity of Writing/Presentation: ".$reviewComment['clarity_score']."\n";
                $reviewCommentText.= "Relevance/Significance: ".$reviewComment['significance_score']."\n";
                $reviewCommentText.= "Originality: ".$reviewComment['originality_score']."\n";
                $reviewCommentText.=  "Total Score: ".$reviewComment['total_score']."\n";
                $reviewCommentText.= "Topic1 Suggestion: ".$this->getTopic($reviewComment['opinion_topic_selected'])."\n";
                $reviewCommentText.= "Topic2 Suggestion: ".$this->getTopic($reviewComment['opinion_topic_selected2'])."\n";

                if($reviewComment['is_case_report'] == 1){
                     $reviewCommentText.= "Case: Yes \n";
                }else{
                     $reviewCommentText.= "Case: No \n";
                }
                
                if($reviewComment['with_conflict_of_interest'] == 1){
                     $reviewCommentText.= "COI: Yes \n";
                }else{
                     $reviewCommentText.= "COI: No \n";
                }

                if($reviewComment['is_requirements_meet'] == 1){
                     $reviewCommentText.= "Relevant to PRiSM: Yes \n";
                }else{
                     $reviewCommentText.= "Relevant to PRiSM: No \n";
                }

                if($reviewComment['is_abstract_qualified'] == 1){
                     $reviewCommentText.= "Eligibility for Diversity: Yes \n";
                }else{
                     $reviewCommentText.= "Eligibility for Diversity: No \n";
                }
               
                $reviewCommentText.= "Comments for the committee: ".$reviewComment['comments_for_committee']."\n\n";
                }
            }
        }
// exit;
        $sheet->setCellValue('E' . $row,($primary_topic_text));
        $sheet->setCellValue('F' . $row,($secondary_topic_text));
        $sheet->setCellValue('G' . $row,($sum));
        $sheet->setCellValue('H' . $row,($averageTotalScores));
        // $sheet->setCellValue('I' . $row,($overallVoteSum));
        // $sheet->setCellValue('J' . $row,($averageOverallVote));
        $sheet->setCellValue('K' . $row,($reviewCommentText));

        if($row%2){
             $sheet->getStyle('A'.$row.':K'.$row)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('0000');
        }

        if($abstract->active_status == 0){
            $sheet->getStyle('A'.$row.':K'.$row)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('ffffe0');
        }

        $sheet->getStyle('A1:K'.$row)
        ->getBorders()
        ->getAllBorders()
        ->setBorderStyle(Border::BORDER_THIN);
        //  print_r($sum.'<br>');

         $row++;
    }

    
    $sheet->getColumnDimension('A')->setWidth(5);
    $sheet->getColumnDimension('B')->setWidth(30);
    $sheet->getColumnDimension('C')->setWidth(50);
    $sheet->getColumnDimension('D')->setWidth(50);
    $sheet->getColumnDimension('E')->setAutoSize(true);
    $sheet->getColumnDimension('F')->setAutoSize(true);
    $sheet->getColumnDimension('G')->setAutoSize(true);
    $sheet->getColumnDimension('H')->setAutoSize(true);

    $sheet->getColumnDimension('I')->setWidth(50);
    $sheet->getColumnDimension('J')->setWidth(50);
    $sheet->getColumnDimension('K')->setWidth(50);
    $sheet->getStyle('D')->getAlignment()->setWrapText(true);
    $sheet->getStyle('C')->getAlignment()->setWrapText(true);
    $sheet->getStyle('E')->getAlignment()->setWrapText(true);
    $sheet->getStyle('F')->getAlignment()->setWrapText(true);
    $sheet->getStyle('I')->getAlignment()->setWrapText(true);
    $sheet->getStyle('J')->getAlignment()->setWrapText(true);
    $sheet->getStyle('K')->getAlignment()->setWrapText(true);
    $sheet->getStyle('A1:K1')->getFont()->setSize(12);
    $sheet->getStyle('A1:K1')->getFont()->setBold(true);



    $filename = 'PRISM_Export_Scores_';

      ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.time() . '.xlsx"');
        header('Cache-Control: max-age=0');

        $xlsxWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($excel, 'Xlsx');
        $xlsxWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($excel);
        
        exit($xlsxWriter->save('php://output'));

    }

    public function exportAcceptedAbstracts($event_uri, $data)
    {


        $excel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $excel->getActiveSheet();
        // $sheet->setTitle('This is a test', true);

        // Set headers
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'LEAD PRESENTER');
        $sheet->setCellValue('C1', 'ABSTRACT TITLE');
        $sheet->setCellValue('D1', 'AUTHOR LIST');
        $sheet->setCellValue('E1', 'Primary Topic');
        $sheet->setCellValue('F1', 'Secondary Topic');
        $sheet->setCellValue('G1', 'REVIEWERS TOTAL SCORES');
        $sheet->setCellValue('H1', 'REVIEWERS AVERAGE TOTAL SCORES');
        $sheet->setCellValue('I1', 'OVERALL VOTE TOTAL SCORES');
        $sheet->setCellValue('J1', 'OVERALL VOTE SCORE AVERAGE');
        $sheet->setCellValue('K1', 'COMMENTS');
        $row = 2;

        $authorDetails = '';
        // Populate data
        // echo '<pre>';

        foreach ($data as $abstract) {
            $authorDetails = '';
            $primary_topic_text= '';
            $secondary_topic_text= '';
            // print_r($abstract);
            // $abstractReviewerTotalScore = 0;
            $sheet->setCellValue('A' . $row, $abstract->id);
            if(isset($abstract->lead_presenter[0]['name'])){
                $sheet->setCellValue('B' . $row, trim(($abstract->lead_presenter[0]['name'].' '. $abstract->lead_presenter[0]['surname'])));
            }
            $sheet->setCellValue('C' . $row, trim(($abstract->title)));
            if(isset($abstract->author)){
                foreach ($abstract->author as $author){
                    $authorDetails .= ($author['details']['name']." ".$author['details']['surname']."\n");
                }

            }

            if(isset($abstract->primary_topic)){
                foreach(json_decode($abstract->primary_topic) as $primary_topic){
                    $topic = (new AbstractTopicsModel())->where('id',$primary_topic)->first();
                    // print_r($topic);exit;
                    $primary_topic_text .= $topic['name']. "\n";
                }
            }


            if(isset($abstract->secondary_topic)){
                foreach(json_decode($abstract->secondary_topic) as $secondary_topic){
                    $topic = (new AbstractTopicsModel())->where('id',$secondary_topic)->first();
                    $secondary_topic_text .= $topic['name']. "\n";
                }
            }

            // print_R($substanceAreaText);
            //  print_R($populationText);
            $authorDetails = rtrim($authorDetails, "\n");
            $sheet->setCellValue('D' . $row,($authorDetails));

            // print_R($abstract->reviewersTotalScore);exit;

            if(isset($abstract->reviewersTotalScore)){
                $sum = 0;
                $overAllReviews = 0;
                foreach ($abstract->reviewersTotalScore as $review) {
                    $reviewerModel = (new AbstractReviewModel())->where(['reviewer_id'=> $review['reviewer_id'], 'abstract_id'=>$abstract->id])->first();
                    if($reviewerModel['with_conflict_of_interest'] == 0){
                        $sum += $review['total_score'];
                        $overAllReviews = $overAllReviews + 1;
                    }
                }

                if($overAllReviews > 0){
                    $averageTotalScores = ($sum/$overAllReviews);
                }else{
                    $averageTotalScores = 0;
                }
            }

            if(isset($abstract->overallVote)){

            }

            if(isset($abstract->reviewComments) ){

                $reviewCommentText = '';
                foreach($abstract->reviewComments as $reviewComment){
                    if(!empty($reviewComment)){
                        // print_r($reviewComment);
                        if(isset($reviewComment['userDetails'])){
                            $reviewCommentText.=  "Reviewer: ".$reviewComment['userDetails'][0]['name'].' '.$reviewComment['userDetails'][0]['surname']."\n";
                        }

                        $reviewCommentText.= "Methodology/Hypothesis: ".$reviewComment['methodology_score']."\n";
                        $reviewCommentText.= "Data Analysis: ".$reviewComment['data_analysis_score']."\n";
                        $reviewCommentText.= "Discovery/Interpretation: ".$reviewComment['interpretation_score']."\n";
                        $reviewCommentText.= "Clarity of Writing/Presentation: ".$reviewComment['clarity_score']."\n";
                        $reviewCommentText.= "Relevance/Significance: ".$reviewComment['significance_score']."\n";
                        $reviewCommentText.= "Originality: ".$reviewComment['originality_score']."\n";
                        $reviewCommentText.=  "Total Score: ".$reviewComment['total_score']."\n";
                        $reviewCommentText.= "Topic1 Suggestion: ".$this->getTopic($reviewComment['opinion_topic_selected'])."\n";
                        $reviewCommentText.= "Topic2 Suggestion: ".$this->getTopic($reviewComment['opinion_topic_selected2'])."\n";

                        if($reviewComment['is_case_report'] == 1){
                            $reviewCommentText.= "Case: Yes \n";
                        }else{
                            $reviewCommentText.= "Case: No \n";
                        }

                        if($reviewComment['with_conflict_of_interest'] == 1){
                            $reviewCommentText.= "COI: Yes \n";
                        }else{
                            $reviewCommentText.= "COI: No \n";
                        }

                        if($reviewComment['is_requirements_meet'] == 1){
                            $reviewCommentText.= "Relevant to PRiSM: Yes \n";
                        }else{
                            $reviewCommentText.= "Relevant to PRiSM: No \n";
                        }

                        if($reviewComment['is_abstract_qualified'] == 1){
                            $reviewCommentText.= "Eligibility for Diversity: Yes \n";
                        }else{
                            $reviewCommentText.= "Eligibility for Diversity: No \n";
                        }

                        $reviewCommentText.= "Comments for the committee: ".$reviewComment['comments_for_committee']."\n\n";
                    }
                }
            }
// exit;
            $sheet->setCellValue('E' . $row,($primary_topic_text));
            $sheet->setCellValue('F' . $row,($secondary_topic_text));
            $sheet->setCellValue('G' . $row,($sum));
            $sheet->setCellValue('H' . $row,($averageTotalScores));
            // $sheet->setCellValue('I' . $row,($overallVoteSum));
            // $sheet->setCellValue('J' . $row,($averageOverallVote));
            $sheet->setCellValue('K' . $row,($reviewCommentText));

            if($row%2){
                $sheet->getStyle('A'.$row.':K'.$row)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('0000');
            }

            if($abstract->active_status == 0){
                $sheet->getStyle('A'.$row.':K'.$row)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('ffffe0');
            }

            $sheet->getStyle('A1:K'.$row)
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
            //  print_r($sum.'<br>');

            $row++;
        }


        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(50);
        $sheet->getColumnDimension('D')->setWidth(50);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);

        $sheet->getColumnDimension('I')->setWidth(50);
        $sheet->getColumnDimension('J')->setWidth(50);
        $sheet->getColumnDimension('K')->setWidth(50);
        $sheet->getStyle('D')->getAlignment()->setWrapText(true);
        $sheet->getStyle('C')->getAlignment()->setWrapText(true);
        $sheet->getStyle('E')->getAlignment()->setWrapText(true);
        $sheet->getStyle('F')->getAlignment()->setWrapText(true);
        $sheet->getStyle('I')->getAlignment()->setWrapText(true);
        $sheet->getStyle('J')->getAlignment()->setWrapText(true);
        $sheet->getStyle('K')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A1:K1')->getFont()->setSize(12);
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);



        $filename = 'PRISM_Export_Scores_';

        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.time() . '.xlsx"');
        header('Cache-Control: max-age=0');

        $xlsxWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($excel, 'Xlsx');
        $xlsxWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($excel);

        exit($xlsxWriter->save('php://output'));

    }

    function getTopic($id){
       
        $topics = (new AbstractTopicsModel());
        $topic = ($topics->find($id));
        if ($topic)
        return $topics->find($id)['name'];
        else return '';
    }
    public function exportSample() // for testing purpose only
    {

        $excel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('This is a test', true);
      
        // Set the data
        $exportData = [
            ['Name', 'Email'],
            ['John Doe', 'john@example.com'],
            ['Jane Smith', 'jane@example.com'],
            ['Bob Johnson', 'bob@example.com'],
        ];
        $sheet->fromArray($exportData, null, 'A1');

   
        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="filename_' . time() . '.xlsx"');
        header('Cache-Control: max-age=0');

        $xlsxWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($excel, 'Xlsx');
        $xlsxWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($excel);
        
        exit($xlsxWriter->save('php://output'));
    }
}