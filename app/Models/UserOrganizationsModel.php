<?php
namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;

class UserOrganizationsModel extends Model
{
    protected $table = 'user_organizations';
    protected $primaryKey = 'id';
    protected $allowedFields ;

    function __construct(ConnectionInterface $db = null, ValidationInterface $validation = null)
    {
        parent::__construct();
        $this->db = $db ?? db_connect();
        $this->validation = $validation;

        $this->initializeAllowedFields();
    }

    protected function initializeAllowedFields(): void
    {
        $this->allowedFields = $this->db->getFieldNames($this->table);

        // Optionally, you can filter or manipulate the allowed fields array
        $excludedFields = ['id', 'created_at', 'updated_at'];
        $this->allowedFields = array_diff($this->allowedFields, $excludedFields);
    }

    protected function excludeDeletedRecords(array $data){
        if (isset($data['builder']) && empty($data['method'])) {
            $data['builder']->where('deleted', 0);
        }
        return $data;
    }

    function getFullOrganizationsWithAffiliation($user_id){
        $organizations = (new OrganizationsModel())->findAll();
        $affiliations = (new AffiliationsModel())->findAll();

        $organizations = array_column($organizations, 'name', 'id');
        $affiliationsTable = array_column($affiliations, 'name', 'id');
        // Get saved affiliations for the user
        $savedOrganizations = $this
            ->where('user_id', $user_id)
            ->orderBy('id', 'asc') // <-- Order by insertion order
            ->findAll();

        // Map saved affiliations to an easy-to-use array
        $selectedOrganizations = [];
        if (!empty($savedOrganizations)) {
            foreach ($savedOrganizations as $org) {
                $selectedOrganizations[$org['id']] = [
                    'organization_id' => $organizations[$org['organization_id']], // Fixed ID to match organization_id
                    'affiliations' => array_map(function($affiliation) use ($affiliationsTable) {
                        $affiliationName = $affiliationsTable[$affiliation] ?? 'Unknown Affiliation';
                        return $affiliationName;
                    },json_decode($org['affiliation'])),
                    'affiliations_stocks' => json_decode($org['affiliations_stocks'] ?? '[]', true) ?? [],
                    'other_affiliation' => $org['other_affiliation'] ?? null,
                    'custom_organization' => $org['custom_organization'] ?? null
                ];
            }
        }

        return  $selectedOrganizations;
    }
}