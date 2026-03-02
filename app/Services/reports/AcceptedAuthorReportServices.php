<?php

namespace App\Services\reports;

use App\Models\DesignationsModel;
use App\Models\PaperAuthorsModel;
use App\Models\RemovedPaperAuthorModel;
use App\Models\UserModel;
use App\Models\UsersProfileModel;
use App\Models\UserOrganizationsModel;
use App\Services\InstitutionServices;
use App\Services\UserServices;
use CodeIgniter\Config\BaseService;


class AcceptedAuthorReportServices extends BaseService
{
    function __construct()
    {
        helper('array');
    }


    public function all_accepted_authors_disclosures_report()
    {
        $authorOrder = [
            'column' => 'id',
            'direction' => 'asc'
        ];
        $authors = (new PaperAuthorsModel())
            ->getAuthorsAcceptedByAdmin(null, $authorOrder);

        $filteredAuthors = array_filter($authors, function ($author) {
            return empty($author['is_removed']) || $author['is_removed'] == 0;
        });

        $authorsIds = array_unique(array_column($filteredAuthors, 'author_id'));
        $authorsIds = array_filter($authorsIds, function ($id) {
            return $id != 0; // skip author_id = 0
        });


        if (empty($authorsIds)) return 'empty ids';

// 2. Fetch Details and Profiles only for the authors we need
        $userDetails = (new UserModel())
            ->whereIn('id', $authorsIds)
            ->findAll();
        $userProfiles = (new UsersProfileModel())
            ->whereIn('author_id', $authorsIds)
            ->findAll();

        $PaperAuthorsModel = (new PaperAuthorsModel());
        $RemovedPaperAuthorsModel = (new RemovedPaperAuthorModel());
// 3. Fetch ALL relevant papers in ONE query instead of inside the loop
        $allAssignedPapers = $PaperAuthorsModel
            ->select('paper_authors.*, p.*') // specify columns to avoid ID collisions
            ->join('papers p', 'paper_authors.paper_id = p.id', 'left')
            ->whereIn('author_id', $authorsIds)
            ->whereNotIn($PaperAuthorsModel->table . '.id', function ($builder) use ($RemovedPaperAuthorsModel) {
                $builder->select('paper_author_id')->from($RemovedPaperAuthorsModel->table);
            })
            ->findAll();

        $institutionIds = array_column($userProfiles, 'institution_id');

// 4. Map the data for easy access
        $detailsMap = array_column($userDetails, null, 'id');
        $profileMap = array_column($userProfiles, null, 'author_id');

        $institutionIds = array_filter($institutionIds);
        $userInstitutionMap = (new InstitutionServices())->getInstitutionsWithAddresses($institutionIds);
        $userInstitutionMap = (array_column($userInstitutionMap, null, 'id'));

//        print_r($userInstitutionMap);exit;
        $designations = (new DesignationsModel())->findAll();
        $designationsColumn = (array_column($designations, 'name', 'id'));
        $userDesignationsArray = [];
        if (!empty($profileMap)) {
            foreach ($profileMap as $profile) {
                $profileDesignationsArray = json_decode($profile['designations']);
                if (empty($profileDesignationsArray)) {
                    $profile['designationArray'][] = [];
                } else {
                    foreach ($profileDesignationsArray as $profDes) {
                        if (strtolower($designationsColumn[$profDes]) === 'other') {
                            $userDesignationsArray[$profile['id']]['designations'][] = $profile['other_designation'];
                        } else {
                            $userDesignationsArray[$profile['id']]['designations'][] = $designationsColumn[$profDes];
                        }
                    }
                }
            }
        }

// Group papers by author_id manually
        $papersMap = [];
        foreach ($allAssignedPapers as $paper) {
            $papersMap[$paper['author_id']][] = $paper;
        }


        $excel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $excel->getProperties()
            ->setCreator("Your Name")
            ->setTitle("AP All Data Export")
            ->setDescription("Exported data from AP");
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('Abstract All Data Export', true);

        $headers = [
            'Assigned ID', 'Abstract ID', 'First Name', 'Middle Initial',
            'Last Name', 'Email', 'Degree', 'Full Name', 'Author’s Institution',
            'Country', 'Disclosure Status', 'Disclosure'
        ];

        $colHeader = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($colHeader . '1', $header);
            $colHeader++;
        }
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);

// 5. Build the final array
        $excelRow = 2;
        $authorsIdsUnique = array_unique($authorsIds);


        foreach ($authorsIdsUnique as $authorId) {
            if ($authorId == 0) continue;
            if (!isset($detailsMap[$authorId])) continue;

            $disclosureStatus = (new UserServices())->is_incomplete_disclosure($authorId);
            $disclosureArray = [];
            $outputParts = [];
            if (strtolower($disclosureStatus) === 'complete') {
                $disclosureArray[] = (new UserOrganizationsModel())->getFullOrganizationsWithAffiliation($authorId);

                if (!empty($disclosureArray[0])) {
                    foreach ($disclosureArray[0] as $entry) {
// 1. Determine the Organization Name
// Use custom_organization if organization_id is 'Other'
                        $orgName = ($entry['organization_id'] === 'Other')
                            ? trim($entry['custom_organization'])
                            : $entry['organization_id'];

                        $affiliationsMapped = array_map(function ($affiliation) {
                            return substr(trim($affiliation), 0, 1);
                        }, $entry['affiliations']);

                        $affiliationsText = implode(', ', $affiliationsMapped);
                        $outputParts[] = $orgName . " (" . $affiliationsText . ")";
                    }
                }
            }

            if (!empty($outputParts) && strtolower($disclosureStatus) === 'complete')
                $disclosureText = implode('; ', $outputParts);
            elseif (empty($outputParts) && strtolower($disclosureStatus) === 'complete')
                $disclosureText = 'No Relationship';
            else {
                $disclosureText = '';
            }
            
            $middleName = $detailsMap[$authorId]['middle_name'] ?? '';
            $middleInitial = !empty($middleName) ? ' ' . strtoupper(substr($middleName, 0, 1)) . '.' : '';
            $institution = $userInstitutionMap[$profileMap[$authorId]['institution_id']] ?? [];
            $designationsText = isset($userDesignationsArray[$authorId]['designations']) ? implode(', ', $userDesignationsArray[$authorId]['designations']) : '';
            $fullNameWithDegree = $detailsMap[$authorId]['name'] . ' ' . $detailsMap[$authorId]['surname'] . $middleInitial . ($designationsText ? ', ' . $designationsText : '');

            $assignedIds = implode(', ', array_filter(array_column($papersMap[$authorId], 'assigned_id')));
            $abstractIds = implode(', ', array_filter(array_column($papersMap[$authorId], 'paper_id')));
            $sheet->setCellValue('A' . $excelRow, $assignedIds); // Assigned ID
            $sheet->setCellValue('B' . $excelRow, $abstractIds);          // Abstract ID
            $sheet->setCellValue('C' . $excelRow, $detailsMap[$authorId]['name']);              // First Name
            $sheet->setCellValue('D' . $excelRow, $middleInitial);                   // Middle Initial (add if exists)
            $sheet->setCellValue('E' . $excelRow, $detailsMap[$authorId]['surname']);           // Last Name
            $sheet->setCellValue('F' . $excelRow, $detailsMap[$authorId]['email']);           // Email
            $sheet->setCellValue('G' . $excelRow, $designationsText);      // Degree
            $sheet->setCellValue('H' . $excelRow, $fullNameWithDegree); // Full Name
            $sheet->setCellValue('I' . $excelRow, $institution['name'] ?? '');       // Institution
            $sheet->setCellValue('J' . $excelRow, $institution['country'] ?? '');     // Country
            $sheet->setCellValue('K' . $excelRow, $disclosureStatus);
            $sheet->setCellValue('L' . $excelRow, $disclosureText);

            $excelRow++;

        }


        $textColumns = ['A', 'B'];
        foreach ($textColumns as $column) {
// Set as text format (prefix with single quote to force text)
            $sheet->getStyle($column)
                ->getNumberFormat()
                ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
        }


// Set print settings
        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="All_accepted_authors_disclosures' . date('Y-m-d') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $xlsxWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($excel);
        $xlsxWriter->save('php://output');
        exit;
    }
}
