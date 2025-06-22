<?php

namespace App\Controllers;

use App\Models\AbstractReviewModel;
use App\Models\PaperAuthorsModel;
use App\Models\PapersModel;
use App\Models\PaperTypeModel;
use App\Models\RemovedPaperAuthorModel;
use App\Models\ReviewerPaperUploadsModel;
use App\Models\UserModel;
use App\Models\UsersProfileModel;

class Home extends BaseController
{
    public function __construct()
    {
        if (empty(session('email')) || session('email') == '') {
            print_r('User must login to continue');
            exit;
        }
    }

    public function index(): string
    {
        $header_data = [
            'title' => "My Submissions"
        ];

        $user_id = $_SESSION['user_id'];

        // Fetch papers
        $papersQuery = "
            SELECT p.*, p.id AS id, pt.type AS paper_type, u.name AS user_name
            FROM papers p
            LEFT JOIN paper_type pt ON p.type_id = pt.id
            LEFT JOIN {$this->shared_db_name }.users u ON p.user_id = u.id
            WHERE p.user_id = ?
            ORDER BY p.id ASC
        ";
        $papers = $this->default_db->query($papersQuery, [$user_id])->getResult();

        foreach ($papers as $paper) {
            // Fetch authors for the paper
            $authorsQuery = "
                SELECT u.*, up.signature_signed_date
                FROM paper_authors pa
                LEFT JOIN {$this->shared_db_name }.users u ON pa.author_id = u.id
                LEFT JOIN {$this->shared_db_name }.users_profile up ON pa.author_id = up.author_id
                WHERE pa.paper_id = ? AND pa.author_type = 'author'
                AND pa.id NOT IN (
                    SELECT paper_author_id FROM removed_paper_authors
                )
            ";
            $paper->authors = $this->default_db->query($authorsQuery, [$paper->id])->getResultArray();

            // Fetch reviewers for the paper
            $reviewersQuery = "
                SELECT * FROM abstract_review
                WHERE abstract_id = ?
            ";
            $paper->reviewers = $this->default_db->query($reviewersQuery, [$paper->id])->getResult();

            // Fetch uploads for each reviewer
            foreach ($paper->reviewers as &$reviewer) {
                $uploadsQuery = "
                    SELECT * FROM reviewer_paper_uploads
                    WHERE paper_id = ? AND reviewer_id = ?
                    LIMIT 1
                ";
                $reviewer->uploads = $this->default_db->query($uploadsQuery, [$paper->id, $reviewer->reviewer_id])->getRow();
            }

            // Fetch panelists for the paper
            $panelistsQuery = "
                SELECT u.*, pa.is_copyright_agreement_accepted
                FROM paper_authors pa
                LEFT JOIN {$this->shared_db_name }.users u ON pa.author_id = u.id
                WHERE pa.paper_id = ? AND pa.author_type = 'panelist'
            ";
            $paper->panelist = $this->default_db->query($panelistsQuery, [$paper->id])->getResult();
        }

        $data['papers'] = $papers;

        return
            view('event/common/header', $header_data) .
            view('event/submission', $data) .
            view('event/common/footer');
    }
}