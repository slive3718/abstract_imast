<?php

namespace App\Services;

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