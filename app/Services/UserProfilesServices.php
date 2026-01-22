<?php

namespace App\Services;

use App\Models\OrganizationsModel;
use App\Models\SiteSettingModel;
use App\Models\UsersProfileModel;
use CodeIgniter\Config\BaseService;

class UserProfilesServices extends BaseService
{

    private $request;
    public function __construct() {

        $this->request = \Config\Services::request();
    }

    public function getProfileWithDisclosureData(): array{
        $profiles = (new UsersProfileModel())->findAll();
        $currentDisclosureDate = (new SiteSettingModel())->get_current_disclosure_date('disclosure_current_date');

        if(empty($currentDisclosureDate) || empty($profiles))
            return [];

        $usersOrganizations = (new OrganizationsModel())->getUserOrganization();

        $profilesWithDisclosureStatus = [];
        foreach ($profiles as $profile) {
            if(!$profile['signature_signed_date'])
                $signatureStatus = 'Incomplete';
            elseif(strtotime($profile['signature_signed_date']) < strtotime($currentDisclosureDate))
                $signatureStatus = 'Expired';
            else
                $signatureStatus = 'Complete';

            $profilesWithDisclosureStatus[$profile['author_id']] = [
                'profile' => $profile,
                'signature_status' => $signatureStatus,
                'organizations' => $usersOrganizations[$profile['author_id']] ?? []
            ];
        }

        return $profilesWithDisclosureStatus;
    }
}