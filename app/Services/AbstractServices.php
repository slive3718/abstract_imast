<?php

namespace App\Services;

use App\Models\PaperAuthorsModel;
use App\Models\PapersModel;
use App\Models\RemovedPaperAuthorModel;
use App\Models\SiteSettingModel;
use App\Models\UserModel;
use App\Models\UsersProfileModel;

class AbstractServices extends CoreServices
{
    public function __construct() {

        $this->request = \Config\Services::request();
        $this->initializeModels();
    }

    function topics_column($topics_array){
        $abstract_topics_array = [];
        foreach ($topics_array as $primary_topic) {
            $topic_model = new AbstractTopicsModel();
            $abstract_topics_array[] = $topic_model->getTopicsColumn($primary_topic);  // Use $topic_id, not fixed primary_topic
        }
        return $abstract_topics_array;
    }

    function view_abstract_data($paper_id){
        $post = $this->request->getPost();

        $paper = $this->papersModel->asArray()->find($paper_id);
        $mergePaperData = $this->mergePaperData($paper);
        $userInfo = $this->userModel->find($post['user_id'] ?? session('user_id'));
        $paper_uploads = $this->paperAuthorsModel->where('paper_id', $paper_id)->orderBy('id', 'desc')->findAll();
        $paper_reviewer_uploads = $this->reviewerPaperUploadsModel->where('paper_id', $paper_id)->findAll();

        $authorIds = $this->paperAuthorsModel
            ->select('author_id')
            ->where('paper_id', $paper_id)
            ->whereNotIn('id', function ($builder) {
                $builder->select('paper_author_id')->from('removed_paper_authors');
            })
            ->findAll();

        if (!empty($authorIds)) {
            $authors = $this->mergeAuthorData($authorIds, $paper_id);
        }

        $deputy_acceptance = $this->papersDeputyAcceptanceModel->where('paper_id', $paper_id)->findAll();
        $admin_acceptance = $this->adminAcceptanceModel->where(['user_id'=>session('user_id'), 'abstract_id'=>$paper_id])->first();
        $paperUploads = $this->paperUploadsModel->where('paper_id', $paper_id)->findAll();
        $email_templates = $this->emailTemplatesModel->findAll();

        $adminComment = $this->adminAbstractCommentModel->where(['paper_id'=>$paper_id, 'admin_id'=>session('user_id')])->first();
        $designations = $this->designationsModel->get_array_column();

        $data = [
            'abstract'=> $mergePaperData,
            'paper_type'=> $mergePaperData['type_name'],
            'abstract_id'=> $paper_id,
            'userInfo'=> $userInfo,
            'paper_uploads' => $paperUploads,
            'deputy_acceptance' => $deputy_acceptance,
            'authors'=>$authors,
            'review_details'=>$reviewDetails ?? [],
            'email_templates'=>$email_templates,
            'admin_acceptance'=>$admin_acceptance,
            'adminComment' => $adminComment,
            'paper_reviewer_uploads'=>$paper_reviewer_uploads,
            'paper_types' => $this->paperTypeModel->asArray()->findAll(),
            'designations' =>$designations,
            'current_disclosure_date' => date( 'Y-m-d', strtotime($this->siteSettingModel->where(['name' => 'disclosure_current_date'])->first()['value'])),
        ];

        return $data;
    }

    function process_update_paper($post){

        // Ensure paper_id is provided
        if (empty($post['paper_id'])) {
            return (['status' => 400, 'msg' => "Paper ID is required", 'data' => '']);
        }

        $papersModel = new PapersModel();

        // Fetch existing data to prevent overwriting with null
        $existingPaper = $papersModel->asArray()->find($post['paper_id']);
        if (!$existingPaper) {
            return (['status' => 404, 'msg' => "Paper not found", 'data' => '']);
        }

        // Prepare the update array
        $update_array = [
            'type_id'            => isset($post['paper_type']) ? trim($post['paper_type']) : $existingPaper['type_id'],
            'previous_presentation'  => isset($post['previous_presentation']) ? trim($post['previous_presentation']) : $existingPaper['previous_presentation'],
            'basic_science_format'   => isset($post['basic_science_format']) ? trim($post['basic_science_format']) : $existingPaper['basic_science_format'],
            'abstract_category'      => isset($post['abstract_category']) ? trim($post['abstract_category']) : $existingPaper['abstract_category'],
            'abstract_subcategories'  => isset($post['abstract_subcategories']) ? json_encode(($post['abstract_subcategories'])) : $existingPaper['abstract_subcategories'],
            'title'                  => isset($post['abstract_title']) ? trim(preg_replace('/\.$/', '', $post['abstract_title'])) : $existingPaper['title'],
            'hypothesis'             => isset($post['hypothesis']) ? trim($post['hypothesis']) : $existingPaper['hypothesis'],
            'study_design'           => isset($post['study_design']) ? trim($post['study_design']) : $existingPaper['study_design'],
            'introduction'           => isset($post['introduction']) ? trim($post['introduction']) : $existingPaper['introduction'],
            'methods'                => isset($post['methods']) ? trim($post['methods']) : $existingPaper['methods'],
            'results'                => isset($post['results']) ? trim($post['results']) : $existingPaper['results'],
            'conclusions'            => isset($post['conclusions']) ? trim($post['conclusions']) : $existingPaper['conclusions'],
            'additional_notes'       => isset($post['additional_notes']) ? trim($post['additional_notes']) : $existingPaper['additional_notes'],
            'abstract_body_count'      => isset($post['abstract_body_count']) ? trim($post['abstract_body_count']) : $existingPaper['abstract_body_count'],
            'min_follow_up_period'   => isset($post['min_follow_up_period']) ? trim($post['min_follow_up_period']) : $existingPaper['min_follow_up_period'],
            'is_srs_funded'          => isset($post['is_srs_funded']) ? trim($post['is_srs_funded']) : $existingPaper['is_srs_funded'],
            'primary_investigator'   => isset($post['primary_investigator']) ? trim($post['primary_investigator']) : $existingPaper['primary_investigator'],
            'grant_year'             => isset($post['grant_year']) ? trim($post['grant_year']) : $existingPaper['grant_year'],
            'image_caption'          => isset($post['image_caption']) ? trim($post['image_caption']) : $existingPaper['image_caption'],
            'author_q_1'          => isset($post['author_q_1']) ? trim($post['author_q_1']) : $existingPaper['author_q_1'],
            'author_q_2'          => isset($post['author_q_2']) ? trim($post['author_q_2']) : $existingPaper['author_q_2'],
            'image_upload_finished'  => isset($post['image_upload_finished']) ? trim($post['image_upload_finished']) : $existingPaper['image_upload_finished'],
        ];


        // Remove fields that haven't changed
        $update_array = array_diff_assoc($update_array, $existingPaper);

        // If no changes, return a message
        if (empty($update_array)) {
            return (['status' => 200, 'msg' => "No changes made", 'data' => ['abstract_id' => $post['paper_id']]]);
        }

        // Perform the update operation
        try {
            $updated = $papersModel->update($post['paper_id'], $update_array);

            if ($updated) {
                return ([
                    'status' => 200,
                    'msg' => "Paper updated successfully",
                    'data' => ['abstract_id' => $post['paper_id']]
                ]);
            } else {
                return (['status' => 500, 'msg' => "Update failed", 'data' => '']);
            }
        } catch (\Exception $e) {
            return ([
                'status' => 500,
                'msg' => "Paper update failed: " . $e->getMessage(),
                'data' => ''
            ]);
        }
    }

    function proccess_assign_abstract_author($post){
        $message = array();
        $duplicate = 0;
        $duplicateAuthor = [];
        $author_type = (isset($post['author_type']) ? $post['author_type'] : null);
        try {
            if (!empty($post['author_ids']) && $post['paper_id'] !== '') {
                $post['author_ids'] = array_filter($post['author_ids'], function ($value) {
                    return $value !== '';
                });
                foreach ($post['author_ids'] as $index => $author_id) {
                    $checkAbstractAuthor = $this->checkAbstractAuthor($author_id, $post['paper_id'], $author_type); //Todo: fix cant add multiple author at once

                    if (empty($checkAbstractAuthor)) {

                        $assignedAuthorExceed = $this->checkAssignedAuthorExceed($post['paper_id']);

                        if (!$assignedAuthorExceed) {
                            $resultID = $this->assignToPaper($author_id, $post['paper_id'], $author_type);
                            if (is_int($resultID)) {
                            } else {
                                $message[] = json_encode(['status' => '500', 'message' => 'Error assigning to paper', 'data' => $resultID]);
                            }
                        } else {
                            $message[] = json_encode(['status' => '500', 'message' => 'Author count exceeds or database setting not configured', 'data' => '']);
                        }
                    }
                    else {
                        $duplicate = $duplicate + 1;
                        foreach ($checkAbstractAuthor as $val){
                            $duplicateAuthor[] = (new UserModel())->find($val['author_id']);;
                        }
                    }
                }
            } else {
                $message[] = json_encode(['status' => '500', 'message' => 'Error: Empty author ID or paper ID', 'data' => '']);
            }
        } catch (\Exception $e) {
            return json_encode(['status' => '500', 'message' => $e->getMessage(), 'data' => '']);
        }

        if($message)
            return json_encode($message);
        else
            return json_encode(['status' => '200', 'message' => 'Success', 'data' => ['duplicate'=>$duplicateAuthor]]);
    }

    function checkAbstractAuthor($author_id, $paper_id, $author_type = null){

        $PaperAuthorsModel = (new PaperAuthorsModel());
        $RemovedPaperAuthorsModel = (new RemovedPaperAuthorModel());

        if(empty($author_type)){
            $author_type = 'author';
        }
        try{
            $result = $PaperAuthorsModel
                ->select('*')
                ->where('author_id', $author_id)
                ->where('paper_id', $paper_id)
                ->where('author_type', $author_type)
                ->findAll();

            if($result){
                foreach ($result as $val){
                    $RemovedPaperAuthorsModel
                        ->where('paper_author_id', $val['id'])
                        ->delete();
                }
            }

            return $result;
        } catch (\Exception $e) {
            return  $e->getMessage();
        }
    }

    private function checkAssignedAuthorExceed($paper_id){
        $SiteSettingsModel = (new SiteSettingModel());
        $siteSetting = $SiteSettingsModel->where('name', 'number_of_authors')->first();

        $PaperAuthorsModel = (new PaperAuthorsModel());
        $paperAuthorsCount = ($PaperAuthorsModel
            ->where('paper_id', $paper_id)
            ->whereNotIn('paper_authors.id', function ($builder) {
                $builder->select('paper_author_id')->from('removed_paper_authors');
            })
            ->findAll());

        if(count($siteSetting) > 0){
            if(count($paperAuthorsCount) >= $siteSetting['value']){
                return true;
            }else{
                return false;
            }
        }
        return true;
    }

    private function assignToPaper($author_id, $paper_id , $author_type = null)
    {
        helper('text');
        $paperAuthorsModel = (new PaperAuthorsModel());
        $insertArray = [
            'paper_id'=> $paper_id,
            'author_id'=> $author_id,
            'date_time'=> date('Y-m-d H:i:s')
        ];



        if(!empty($author_type)){
            $insertArray['author_type'] = $author_type;
        }

        try {
            $paperAuthors = $paperAuthorsModel->set($insertArray)->insert();
            if($paperAuthors){
                return $paperAuthorsModel->getInsertID();
            }else{
                return '';
            }
        }catch (\Exception $e) {
            return json_encode(array('status'=>'500', 'message'=> $e->getMessage(),'data'=>''));
        }
    }

    function process_update_paper_author($post){

        $PaperAuthorsModel = (new PaperAuthorsModel());
        $sendToId = array();
        try {
            // Begin transaction
            $PaperAuthorsModel->db->transBegin();

            // Validate paper_id
            if (empty($post['paper_id'])) {
                throw new \Exception('Paper ID is missing');
            }

            // Update all authors' roles to 'No' and 'null'
            $PaperAuthorsModel
                ->set('is_presenting_author', 'No')
                ->set('is_correspondent', 'No')
                ->set('is_senior_author', 'No')
                ->set('author_order', null)
                ->where('paper_id', $post['paper_id'])
                ->update();

            // Update author orders
            if (isset($post['author_orders'])) {
                foreach ($post['author_orders'] as $index => $author_order) {
                    if ($author_order !== '') {
                        $PaperAuthorsModel
                            ->set('author_order', $index + 1)
                            ->where('author_id', $author_order)
                            ->where('paper_id', $post['paper_id'])
                            ->update();
                    }
                }
            }

            // Update Senior Author
            if (isset($post['senior_author_id']) && !empty($post['senior_author_id'])) {
                $PaperAuthorsModel
                    ->set('is_senior_author', 'Yes')
                    ->set('update_date_time', date('Y-m-d H:i:s'))
                    ->where('author_id', $post['senior_author_id'])
                    ->where('paper_id', $post['paper_id'])
                    ->update();
            }


            // Update correspondents
            if (isset($post['selectedCorrespondents']) && !empty($post['selectedCorrespondents'])) {
                foreach ($post['selectedCorrespondents'] as $selectedCorrespondent) {
                    $PaperAuthorsModel
                        ->set('is_correspondent', 'Yes')
                        ->set('update_date_time', date('Y-m-d H:i:s'))
                        ->where('author_id', $selectedCorrespondent)
                        ->where('paper_id', $post['paper_id'])
                        ->update();
                }
            }

            // Update presenting authors
            if (!empty($post['presenting_authors'])) {
                foreach ($post['presenting_authors'] as $presenting_author) {
                    $PaperAuthorsModel
                        ->set('is_presenting_author', 'Yes')
                        ->set('update_date_time', date('Y-m-d H:i:s'))
                        ->where('author_id', $presenting_author)
                        ->where('paper_id', $post['paper_id'])
                        ->update();
                }
            }


            if(!$this->process_update_paper($post)){
                throw new \Exception('Error updating paper');
            }


            // Check transaction status
            if ($PaperAuthorsModel->db->transStatus() === false) {
                // Rollback and throw exception to catch block
                $PaperAuthorsModel->db->transRollback();
                throw new \Exception('Transaction status is false');
            } else {
                // Commit transaction
                $PaperAuthorsModel->db->transCommit();
                return json_encode(array('status' => '200', 'message' => 'Success', 'data' => ''));

            }
        } catch (\Exception $e) {
            // Rollback transaction and log the error
            $PaperAuthorsModel->db->transRollback();
            error_log('Transaction failed: ' . $e->getMessage());
            return json_encode(array('status' => '500', 'message' => 'Transaction failed: ' . $e->getMessage(), 'data' => $e->getMessage()));
        }
    }

    function process_add_author($post){
        $UserModel = (new UserModel());
        $UsersProfileModel = (new UsersProfileModel());
        $users = $UserModel->where('email', $post['authorEmail'])->first();

        if(!empty($users) ){
            return json_encode(array('status'=>'400', 'message'=>'User Already Exist','data'=>$users));
        }else{
            $insertUsersArray = [
                'name' => $post['authorFName'],
                'surname' => $post['authorLName'],
                'middle_name' => $post['authorMName'],
                'author_type'=> $post['author_type'] ?? 'author',
                'email' => $post['authorEmail'],
                'is_study_group' => isset($post['is_study_group']) ? 1: 0
            ];

            try {
                $db = db_connect();
                $db->transBegin();

                $userResult = $UserModel->set($insertUsersArray)->insert();

                if ($userResult) {
                    $insertAuthorDetailsArray = [
                        'phone' => $post['authorPhone']?:'',
                        'cellphone' => $post['cellphone']?:'',
                        'institution' => $post['authorInstitution']?:'',
                        'institution_id' => $post['authorInstitutionId']?:'',
                        'author_id' => $userResult,
                        'designations' => !empty($post['designations']) ? json_encode($post['designations']) : '',
                        'other_designation' => $post['other_designation'] ?? '',
                    ];

                    $UsersProfileModel->set($insertAuthorDetailsArray)->insert();
                }

                $db->transCommit();
                return json_encode(array('status'=>'200', 'message'=>'Author Added Successfully','data'=>$userResult));
            } catch (\Exception $e) {
                // Handle the database error
                $db->transRollback();
                return json_encode(array('status'=>'500', 'message'=>'Error:','data'=>$e->getMessage()));
            }
        }
    }

    public function process_quick_add_author($post){

        $RemovedPaperAuthor = (new RemovedPaperAuthorModel());
        $removedAuthor = $RemovedPaperAuthor
            ->join('paper_authors pa', 'removed_paper_authors.paper_author_id = pa.id', 'left')
            ->where('pa.paper_id', $post['paper_id'])
            ->where('paper_author_id', $post['author_id'])
            ->where('author_type', 'author')
            ->first();

        try {
            if($removedAuthor) {
                $removeResult = $RemovedPaperAuthor->where('paper_author_id', $removedAuthor['id'])->delete();
                if ($removeResult) {
                    return (['status' => 200, 'message' => 'success', 'data' => '']);
                }
            }else{
                $PaperAuthorsModel = (new PaperAuthorsModel());
                $recentAuthorInfo = $PaperAuthorsModel
                    ->find($post['author_id']);

                if(!$recentAuthorInfo)
                    return (['status' => 500, 'message' => 'error', 'data' => 'Author not found']);

                if(!$this->checkAbstractAuthor($recentAuthorInfo['author_id'], $post['paper_id'], 'author')){
                    $result = $PaperAuthorsModel->insert(
                        [
                            'paper_id' => $post['paper_id'],
                            'author_id' => $recentAuthorInfo['author_id'],
                            'author_type' => 'author',
                            'is_correspondent' => 'No',
                            'is_presenting_author' => 'No',
                            'created_at' => date('Y-m-d H:i:s'),
                            'update_date_time' => date('Y-m-d H:i:s')
                        ]
                    );
                }else{
                    return (['status' => 200, 'message' => 'success', 'data' => '']);
                }

                if($result){
                    return (['status' => 200, 'message' => 'success', 'data' => '']);
                }
                return (['status' => 500, 'message' => 'error', 'data' => '']);
            }
        } catch (\Exception $e) {
            return (['status' => 500, 'message' => 'error: ' . $e->getMessage(), 'data' => '']);
        }
        return (['status' => 500, 'message' => 'error', 'data' => '']);
    }
    public function mergePaperData($paper): array{
        if ($paper) {
            $typeData = $this->paperTypeModel->getPaperTypeName($paper['type_id']);
            $divisionData = $this->divisionsModel->getDivisionName($paper['division_id']);
            $categories = $this->abstractCategoriesModel->get_array_column();
            $subCategories = $this->getSubCategories($paper['id']);
            $sub_categories_names =$subCategories ? array_column($subCategories, 'name') : [];
            $abstracts = [
                'paper' => $paper,
                'type_name' => $typeData['name'] ?? null,
                'division_name' => $divisionData['name'] ?? null,
                'category' => $categories[$paper['abstract_category']] ?? null,
                'sub_categories' => $sub_categories_names
            ];
            return $abstracts;
        }
        return [];
    }

    public function getSubCategories($paper_id = null){
        $sub_categories = [];
        if($paper_id){
            $paper = $this->papersModel->asArray()->find($paper_id);
            $sub_categories_ids = json_decode($paper['abstract_subcategories'] ?? '[]', true);
            foreach ($sub_categories_ids as $sub_category_id) {
                $sub_categories[] = $this->abstractSubCategoriesModel->find($sub_category_id);
            }
        }
        return $sub_categories;
    }

    private function mergeAuthorData(array $authorIds, int $paper_id): array
    {
        $filteredIds = array_unique(array_column($authorIds, 'author_id'));
        $userProfiles = $this->getUserProfiles($filteredIds);
        $users = $this->getUsers($filteredIds);
        $acceptance = $this->getAuthorAcceptance($filteredIds, $paper_id);

        $userProfilesMap = array_column($userProfiles, null, 'author_id');
        $usersMap = array_column($users, null, 'id');
        $acceptanceMap = array_column($acceptance, null, 'author_id');

        return array_map(function($paperAuthor) use ($userProfilesMap, $usersMap, $acceptanceMap, $paper_id) {
//            print_R($paperAuthor);exit;
            $authorId = $paperAuthor['author_id'] ?? $paperAuthor->author_id;
            $userInstitution = $userProfilesMap[$authorId]['institution_id'] ? (new InstitutionServices())->getInstitutionWithAddress($userProfilesMap[$authorId]['institution_id']) : [];
            if($userInstitution){
                unset($userInstitution['id']);
            }

            // Return as array
            return [
                'author_id' => $authorId,
                'name' => $usersMap[$authorId]['name'] ?? [],
                'surname' => $usersMap[$authorId]['surname'] ?? [],
                'email' => $usersMap[$authorId]['email'] ?? [],
                'profile_data' => $userProfilesMap[$authorId] ?? [],
                'created_at' => $paperAuthor['created_at'] ?? NULL,
                'institution'=> $userInstitution,
                'acceptance' => $acceptanceMap[$authorId] ?? [],
                'designations' => $this->getUserDesignations($userProfilesMap[$authorId]['designations']),
                'assigned_paper' => $this->getAuthorAssignedPaper([$authorId], $paper_id),
                // Add other fields as needed
            ];
        }, $authorIds);
    }

    private function getUserProfiles($ids) : array{
        return $this->usersProfileModel
            ->whereIn('author_id', $ids)
            ->findAll();
    }
    private function getUsers($ids) : array{
        return $this->userModel
            ->whereIn('id', $ids)
            ->findAll();
    }
    private function getAuthorAcceptance($ids, $paper_id) : array{
        return $this->authorAcceptanceModel
            ->whereIn('author_id', $ids)
            ->where('abstract_id', $paper_id)
            ->findAll();
    }

    private function getAuthorAssignedPaper($id, $paper_id){
        return $this->paperAuthorsModel
            ->where('author_id', $id)
            ->where('paper_id', $paper_id)
            ->first();
    }

    public function getUserDesignations($userDesignations){
        $designations = $this->designationsModel->get_array_column();
        $userDesignations = json_decode($userDesignations);
        !empty($userDesignations) ? $designations = array_intersect_key($designations, array_flip($userDesignations)) : $designations = [];
        return $designations;
    }

    function processEntities($authors){
        if (empty($authors))
            return [];
        $newAuthorEntities = [];
        // get the status of disclosure
        $authorsIds = array_column($authors, 'author_id');
        $authorsIds = array_unique($authorsIds);
        $authorsDisclosureStatus = (new AppDisclosureServices())->getBatchStatus($authorsIds);
        foreach ($authors as &$author){
            $newAuthorEntities[$author['author_id']] = $author;
            $newAuthorEntities[$author['author_id']]['disclosureStatus'] = $authorsDisclosureStatus[$author['author_id']] ?? 'none';
        }
        return $newAuthorEntities;
    }

}