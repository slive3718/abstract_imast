<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\AbstractReviewModel;
use App\Models\CMEAssignedPapersModel;
use App\Models\CMEReviewersModel;
use App\Models\CMEReviewsModel;
use App\Models\PaperAssignedReviewerModel;


class CMEReviewersController extends BaseController
{

    protected $helpers = ['form', 'general_helpers'];
    private $db;
    private $model;

    public function __construct()
    {

        $this->db = \Config\Database::connect();
        $this->model = (new CMEReviewersModel());
    }

    public function getCMEReviewerList(): array{
        $CMEReviewers = $this->model->getCMEReviewers();

        if(empty($CMEReviewers)) {
            return [];
        }

        $CMEAssignedPaper = new CMEAssignedPapersModel();
        $CMEReviews = new CMEReviewsModel();

        foreach ($CMEReviewers as $key => $reviewer) {
            $totalAssignePerReviewed = count($CMEAssignedPaper
                ->where('cme_reviewer_id', $reviewer['user_id'])
                ->findAll());

            $totalAssignedPerReviwer = count($CMEReviews
                ->where('cme_reviewer_id', $reviewer['user_id'])
                ->findAll());

            $reviewer['total_assigned'] = $totalAssignePerReviewed;
            $reviewer['total_reviewed'] = $totalAssignedPerReviwer;
            $reviewer_array[] = $reviewer;
        }



       return $reviewer_array?? [];
    }

}
