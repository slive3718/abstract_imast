<?php

namespace App\Services;

use App\Controllers\User;
use App\Models\AppDisclosureModel;
use App\Models\CitiesModel;
use App\Models\CountriesModel;
use App\Models\InstitutionModel;
use App\Models\LogsModel;
use App\Models\PaperAuthorsModel;
use App\Models\UserModel;
use App\Models\UsersProfileModel;
use CodeIgniter\Config\BaseService;
use CodeIgniter\HTTP\RequestInterface;

class PaperAuthorsServices extends BaseService
{
    protected PaperAuthorsModel $model;

    // Optional: inject model (better for testing)
    public function __construct()
    {

    }

    public function getActiveAuthors(): array
    {
        return model(PaperAuthorsModel::class)
            ->getActivePaperAuthors()
            ->findAll();
    }

    // Example with real business logic
    public function getActiveAuthorsWithStats(): array
    {
        $authors = $this->model->getAuthors()->findAll();

        // pretend we do something more complex
        foreach ($authors as $author) {
            $author->paper_count = $this->model->getPaperCountForAuthor($author->id);
        }

        return $authors;
    }

    public function getPresenters(array $authors): array
    {
        return array_filter($authors, function($author) {
            return $this->isPresenter($author);
        });
    }

    public function isPresenter(array $author): bool
    {
        $value = $author['is_presenting_author'] ?? '';
        return strtolower(trim((string)$value)) === 'yes';
    }

    public function mappedPaperAuthors(): array{
        $authors = $this->getActiveAuthors();
        $mappedAuthorsToPaper = [];
        array_map(function($author) use (&$mappedAuthorsToPaper){
            $mappedAuthorsToPaper[$author['paper_id']][] = $author;
        },$authors);

        return $mappedAuthorsToPaper;
    }

    public function get_paper_authors($post, $user_id){
        $LogsModel = (new LogsModel());
        if(!empty($post['author_type']))
            $author_type = trim($post['author_type']);
        else
            $author_type = 'author';
        $UsersModel = (new UserModel());
        $UsersProfileModel = (new UsersProfileModel());
        $PaperAuthorsModel= (new PaperAuthorsModel());
        $AppDisclosureModel = (new AppDisclosureModel());
        $appDisclosure = $AppDisclosureModel->findAll();
        $appDisclosure = array_column(
            array_map(function($item) {
                $item['updated_at'] = !empty($item['updated_at']) ? $item['updated_at'] : $item['created_at'];
                return $item;
            }, $appDisclosure),
            'updated_at',
            'author_id'
        );

        $query = $PaperAuthorsModel->select('*, paper_authors.id as id, users.id as author_id, users.name, users.surname, users.middle_name, ad.updated_at as disclosure_updated_date')
            ->join($UsersModel->db->database.'.users', 'paper_authors.author_id = users.id', 'left')
            ->join($UsersProfileModel->db->database.'.users_profile', 'users.id = users_profile.author_id', 'left')
            ->join('app_disclosures ad', 'users.id = ad.author_id', 'left')
            ->join('removed_paper_authors', 'paper_authors.id = removed_paper_authors.paper_author_id', 'left')
            ->where('paper_authors.paper_id', $post['paper_id'])
            ->where('author_type', $author_type)
            ->whereNotIn('paper_authors.id', function ($builder) {
                $builder->select('paper_author_id')->from('removed_paper_authors');
            })
            ->orderBy('paper_authors.author_order', 'asc')
            ->orderBy('paper_authors.id', 'asc');

// Execute the query
        $paperAuthors = $query->findAll();

        $paperAuthorsArray = array();
        foreach ($paperAuthors as $author){
            $mailLogs = $LogsModel
                ->where('ref_1', $author['author_id'])
                ->where('user_id', $user_id)
                ->where('context', 'copyright')
                ->where('action', 'email')
                ->where('message', 'sent')
                ->orderBy('id', 'desc')
                ->first();

            $author['mailLogs'] = $mailLogs;
            $author['app_disclosure'] = $appDisclosure[$author['author_id']] ?? null;

            $paperAuthorsArray[] = $author;
        }

        return $paperAuthorsArray;
    }

    public function update_paper_authors($post){

        $sendToId = array();

        try {
            // Begin transaction
            $this->model->db->transBegin();

            // Validate paper_id
            if (empty($post['paper_id'])) {
                throw new \Exception('Paper ID is missing');
            }

            // Update all authors' roles to 'No' and 'null'
            $this->model
                ->set('is_presenting_author', 'No')
                ->set('is_correspondent', 'No')
                ->set('is_senior_author', 'No')
                ->set('author_order', null)
                ->set('is_confirm_attending', 0)
                ->where('paper_id', $post['paper_id'])
                ->update();

            // Update author orders
            if (isset($post['author_orders'])) {
                foreach ($post['author_orders'] as $index => $author_order) {
                    if ($author_order !== '') {
                        $this->model
                            ->set('author_order', $index + 1)
                            ->where('author_id', $author_order)
                            ->where('paper_id', $post['paper_id'])
                            ->update();
                    }
                }
            }

            // Update Senior Author
            if (isset($post['senior_author_id']) && !empty($post['senior_author_id'])) {
                $this->model
                    ->set('is_senior_author', 'Yes')
                    ->set('update_date_time', date('Y-m-d H:i:s'))
                    ->where('author_id', $post['senior_author_id'])
                    ->where('paper_id', $post['paper_id'])
                    ->update();
            }


            // Update correspondents
            if (isset($post['selectedCorrespondents']) && !empty($post['selectedCorrespondents'])) {
                foreach ($post['selectedCorrespondents'] as $selectedCorrespondent) {

                    $this->model
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
                    $this->model
                        ->set('is_presenting_author', 'Yes')
                        ->set('is_confirm_attending', 1)
                        ->set('update_date_time', date('Y-m-d H:i:s'))
                        ->where('author_id', $presenting_author)
                        ->where('paper_id', $post['paper_id'])
                        ->update();
                }
            }


            // Check transaction status
            if ($this->model->db->transStatus() === false) {
                $this->model->db->transRollback();
                throw new \Exception('Transaction status is false');
            } else {
                $this->model->db->transCommit();
                return (array('status' => '200', 'message' => 'Success', 'data' => ''));

            }
        } catch (\Exception $e) {
            // Rollback transaction and log the error
            $this->model->db->transRollback();
            error_log('Transaction failed: ' . $e->getMessage());
            return (array('status' => '500', 'message' => 'Transaction failed: ' . $e->getMessage(), 'data' => $e->getMessage()));
        }
    }
}