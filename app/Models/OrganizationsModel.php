<?php
namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;

class OrganizationsModel extends Model
{
    protected $table = 'organizations';
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

    public function get_selected_org($user_id){
        $UserOrganizationsModel = new UserOrganizationsModel(); // New model to handle user affiliations
        // Get saved affiliations for the user
        $savedOrganizations = $UserOrganizationsModel
            ->where('user_id', $user_id)
            ->orderBy('id', 'asc') // <-- Order by insertion order
            ->findAll();

        // Map saved affiliations to an easy-to-use array
        $selectedOrganizations = [];
        if (!empty($savedOrganizations)) {
            foreach ($savedOrganizations as $org) {
                $selectedOrganizations[$org['id']] = [
                    'organization_id' => $org['organization_id'], // Fixed ID to match organization_id
                    'affiliations' => json_decode($org['affiliation'], true) ?? [],
                    'custom_organization' => $org['custom_organization'] ?? null,
                    'relationship_ended' => $org['relationship_ended'] ?? null,
                    'other_affiliation' => $org['other_affiliation'] ?? null
                ];
            }
        }

        return $selectedOrganizations;
    }

    public function getUserOrganization(){
        $UserOrganizationsModel = new UserOrganizationsModel(); // New model to handle user affiliations
        // Get saved affiliations for the user
        $savedOrganizations = $UserOrganizationsModel
            ->orderBy('id', 'asc') // <-- Order by insertion order
            ->findAll();

        $OrganizationChoices = (new OrganizationsModel())->findAll();
        $OrganizationChoices = array_column($OrganizationChoices, 'name', 'id');

        $affiliations = (new AffiliationsModel())->findAll();
        $affiliationsTable = array_column($affiliations, 'name', 'id');
        // Map saved affiliations to an easy-to-use array
        $userOrganization = [];
        if (!empty($savedOrganizations)) {
            foreach ($savedOrganizations as $org) {
                $userOrganization[$org['user_id']][] = [
                    'organization_id' => $org['organization_id'], // Fixed ID to match organization_id
                    'organization_name'=>$OrganizationChoices[$org['organization_id']] ?? 'Unknown Organization',
                    'affiliations' => array_map(function($affiliation) use ($affiliationsTable) {
                        $affiliationName = $affiliationsTable[$affiliation] ?? 'Unknown Affiliation';
                        return $affiliationName;
                    },json_decode($org['affiliation'])),

                    'custom_organization' => $org['custom_organization'] ?? null,
                    'relationship_ended' => $org['relationship_ended'] ?? null
                ];
            }
        }

        return $userOrganization;
    }
}