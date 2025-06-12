<?php

namespace App\Controllers;

use App\Models\AbstractReviewModel;
use App\Models\Core\Api;
use App\Models\AbstractEventsModel;
use App\Models\PaperAuthorsModel;
use App\Models\PapersModel;
use App\Models\PaperTypeModel;
use App\Models\RemovedPaperAuthorModel;
use App\Models\ReviewerPaperUploadsModel;
use App\Models\UserModel;

class Home extends BaseController
{


    public function __construct()
    {
        if(empty(session('email')) || session('email') == ''){
            print_r('User must login to continue');
            exit;
        }
    }

    public function index(): string
    {

//        print_r('test');exit;

        $event = (new AbstractEventsModel())->first();

        $PaperAuthorsModel = (new PaperAuthorsModel());

        if(!$event){
            return "Missing Even on Database";
        }

        $header_data = [
            'title' => "My Submissions"
        ];
        $data = [
            'event'=> $event
        ];
        $user_id = $_SESSION['user_id'];
        $PaperTypeModel = new PaperTypeModel();
        $papersModel =  (new PapersModel());
        $AbstractReviewModel = (new AbstractReviewModel());
        $UserModel = new UserModel();
        $ReviewerPaperUploadsModel = (new ReviewerPaperUploadsModel());
        $RemovedPaperAuthorsModel = (new RemovedPaperAuthorModel());
        
        $papers = $papersModel
            ->select("*, ".$papersModel->getTable().".id as id")
            ->join($PaperTypeModel->getTable(), $papersModel->getTable(). '.type_id = '.$PaperTypeModel->getTable().'.type', 'left')
            ->join($UserModel->getTable(), $papersModel->getTable(). '.user_id = '.$UserModel->getTable().'.id', 'left')
            ->where("user_id", $user_id)
            ->orderBy($papersModel->getTable().'.id', 'asc')
            ->findAll();


        foreach ($papers as $paper) {
            // Fetch authors for the paper
            $paper->authors = $PaperAuthorsModel
                ->select($UserModel->getTable() . '.*,'.$PaperAuthorsModel->getTable().'.is_copyright_agreement_accepted') // Select all fields from the UserModel
                ->join($UserModel->getTable(), $PaperAuthorsModel->getTable() . '.author_id =' . $UserModel->getTable() . '.id', 'left')
                ->where(['paper_id' => $paper->id, 'author_type' => 'author'])
                ->whereNotIn('paper_authors.id', function ($builder) use ($RemovedPaperAuthorsModel) {
                    $builder->select('paper_author_id')->from($RemovedPaperAuthorsModel->getTable());
                })
                ->findAll();

            // Fetch reviewers for the paper
            $paper->reviewers = $AbstractReviewModel
                ->where(['abstract_id' => $paper->id])
                ->findAll();

            // Fetch uploads for each reviewer
            foreach ($paper->reviewers as &$reviewer) { // Using "&" to make $reviewer mutable
                $reviewer['uploads'] = $ReviewerPaperUploadsModel
                    ->where(['paper_id'=>$paper->id, 'reviewer_id' => $reviewer['reviewer_id']])
                    ->first(); // Assuming each reviewer has only one upload, so using "first()"

            }

            $paper->panelist = $PaperAuthorsModel
                ->select($UserModel->getTable() . '.*,'.$PaperAuthorsModel->getTable().'.is_copyright_agreement_accepted') // Select all fields from the UserModel
                ->join($UserModel->getTable(), $PaperAuthorsModel->getTable() . '.author_id =' . $UserModel->getTable() . '.id', 'left')
                ->where(['paper_id' => $paper->id, 'author_type' => 'panelist'])
                ->findAll();
        }
//        $reviewer['uploads'] = 'test';
//        print_r($papers);exit;
        $data['papers'] = $papers;

        return
            view('event/common/header', $header_data).
            view('event/submission',$data).
            view('event/common/footer')
            ;
    }

}
