<?php

namespace App\Services;

use App\Models\AppDisclosureModel;
use App\Models\PaperAuthorsModel;
use App\Models\SiteSettingModel;
use App\Models\UserModel;

class AppDisclosureServices
{
    protected $disclosureModel;
    protected $usersModel;
    protected $validation;

    private $currentDisclosureData;
    public function __construct(
        AppDisclosureModel $disclosureModel = null,
        UserModel $usersModel = null,
        SiteSettingModel $siteSettingModel = null
    ) {
        $this->disclosureModel = $disclosureModel ?? new AppDisclosureModel();
        $this->usersModel = $usersModel ?? new UserModel();
        $this->siteSettingModel = $siteSettingModel ?? new SiteSettingModel();
        $this->validation = \Config\Services::validation();
    }
    /**
     * Submit or update disclosure
     */
    public function submitDisclosure(array $data, int $authorId): array
    {
        // Merge author ID with form data
        $data['author_id'] = $authorId;

        // Validate the data
        $validationResult = $this->validateDisclosureData($data);
        if (!$validationResult['success']) {
            return $validationResult;
        }

        try {
            // Check if author already has a disclosure
            $existing = $this->disclosureModel->findByAuthorId($authorId);

            if ($existing) {
                // Update existing disclosure
                if ($this->disclosureModel->update($existing['id'], $data)) {
                    return [
                        'success' => true,
                        'action' => 'updated',
                        'message' => 'Disclosure updated successfully',
                        'disclosure_id' => $existing['id']
                    ];
                }
            } else {
                // Insert new disclosure
                $disclosureId = $this->disclosureModel->insert($data);

                if ($disclosureId) {
                    return [
                        'success' => true,
                        'action' => 'submitted',
                        'message' => 'Disclosure submitted successfully',
                        'disclosure_id' => $disclosureId
                    ];
                }
            }

            return [
                'success' => false,
                'errors' => ['Failed to save disclosure. Please try again.']
            ];

        } catch (\Exception $e) {
            log_message('error', 'Disclosure submission failed: ' . $e->getMessage());
            return [
                'success' => false,
                'errors' => ['An error occurred while saving your disclosure.']
            ];
        }
    }

    /**
     * Validate disclosure form data
     */
    public function validateDisclosureData(array $data): array
    {
        // Basic validation rules
        $rules = [
            'author_id' => 'required|integer',
            'financial_relationship' => 'required|in_list[yes,no]',
            'disclosure_support' => 'required|in_list[yes,no]',
            'disclosure_discussed' => 'required|in_list[yes,no]',
            'disclosure_relationship' => 'required|in_list[yes,no]',
            'disclosure_signature' => 'required|in_list[yes,no]'
        ];

        // Custom messages
        $messages = [
            'financial_relationship' => [
                'required' => 'Please indicate if you have any financial relationships to disclose',
                'in_list' => 'Please select Yes or No'
            ],
            'disclosure_support' => [
                'required' => 'Please indicate if you received support for this work',
                'in_list' => 'Please select Yes or No'
            ],
            'disclosure_discussed' => [
                'required' => 'Please indicate if potential conflicts were discussed',
                'in_list' => 'Please select Yes or No'
            ],
            'disclosure_relationship' => [
                'required' => 'Please indicate if you have relationships to disclose',
                'in_list' => 'Please select Yes or No'
            ],
            'disclosure_signature' => [
                'required' => 'Please confirm your signature',
                'in_list' => 'Please select Yes or No'
            ]
        ];

        // Set rules and validate
        $this->validation->setRules($rules, $messages);

        if (!$this->validation->run($data)) {
            return [
                'success' => false,
                'errors' => $this->validation->getErrors()
            ];
        }

        return ['success' => true];
    }


    /**
     * Check if author has a valid disclosure
     */
    public function hasValidDisclosure(int $authorId): bool
    {
        $disclosure = $this->getDisclosure($authorId);

        if (!$disclosure) {
            return false;
        }

        if(!$this->isDisclosureValidDate($disclosure, $this->siteSettingModel->get_current_disclosure_date()))
            return false;

        // These fields should be 1 (true) for valid disclosure
        $requiredFields = [
            'financial_relationship',
            'disclosure_support',
            'disclosure_discussed',
            'disclosure_relationship',
            'disclosure_signature'
        ];

        foreach ($requiredFields as $field) {
            if (empty($disclosure[$field])) {
                return false;
            }
        }

        return true;
    }


    /**
     * Get disclosure for author
     */
    public function getDisclosure(int $authorId): ?array
    {
        return $this->disclosureModel->findByAuthorId($authorId);
    }

    public function getDisclosures(array $authorIds): ?array
    {
        return $this->disclosureModel->whereIn('author_id', $authorIds)->findAll();
    }


    /**
     * Check if author needs to submit disclosure
     */
    public function needsDisclosure(int $authorId): bool
    {
        return !$this->disclosureModel->hasSubmitted($authorId);
    }

    public function needsDisclosures(): array
    {
        $authorsIds = (new PaperAuthorsModel())->getUniqueAuthorsIds();
        $authorIdsWithValidDisclosure = $this->getMappedAuthorsWithDisclosure('valid');

        // Extract just the author IDs from valid disclosures
        $validDisclosureAuthorIds = array_column($authorIdsWithValidDisclosure, 'author_id');

        // Convert all to integers for consistent comparison
        $authorsIds = array_map('intval', $authorsIds);
        $validDisclosureAuthorIds = array_map('intval', $validDisclosureAuthorIds);

        // Use array_diff to find the difference
        $authorsWithoutValidDisclosure = array_diff($authorsIds, $validDisclosureAuthorIds);

        // Reset array keys
        $authorsWithoutValidDisclosure = array_values($authorsWithoutValidDisclosure);

//        // Debug output
//        echo "DEBUG:\n";
//        echo "Total authors: " . count($authorsIds) . "\n";
//        echo "With valid disclosure: " . count($validDisclosureAuthorIds) . "\n";
//        echo "Without valid disclosure: " . count($authorsWithoutValidDisclosure) . "\n\n";

//        if (!empty($authorsWithoutValidDisclosure)) {
//            echo "Missing authors (sample):\n";
//            print_r(array_slice($authorsWithoutValidDisclosure, 0, 20));
//        }

//        Debug End #######


        return $authorsWithoutValidDisclosure;
    }

    /**
     * Get disclosure status
     */
    public function getStatus(int $authorId): string
    {
        if (!$this->disclosureModel->hasSubmitted($authorId)) {
            return 'none';
        }


        return $this->hasValidDisclosure($authorId) ? 'valid' : 'incomplete';
    }

    public function getBatchStatus(array $authorIds): array
    {
        $statuses = [];

        foreach ($authorIds as $authorId) {
            $statuses[$authorId] = $this->getStatus($authorId);
        }

        return $statuses;
    }


    public function getAuthorsWithDisclosures(): array
    {
        // Get all disclosures that aren't deleted
        $disclosures = $this->disclosureModel
            ->where('deleted_at', null)
            ->findAll();

        // Apply business rules to determine "valid"
        $authors = [];

        foreach ($disclosures as $disclosure) {
            $authors[] = [
                'author_id' => $disclosure['author_id'],
                'disclosure' => $disclosure,
                'valid_since' => $disclosure['updated_at'] ?? $disclosure['created_at'] ?? null
            ];
        }

        return $authors;
    }

    public function getAuthorsWithValidDisclosures(): array
    {
        // Get all disclosures that aren't deleted
        $disclosures = $this->disclosureModel
            ->where('deleted_at', null)
            ->findAll();

        // Apply business rules to determine "valid"
        $validAuthors = [];

        $currentDisclosureDate = (new SiteSettingModel())->get_current_disclosure_date();
        foreach ($disclosures as $disclosure) {
            if ($this->isDisclosureValidDate($disclosure, $currentDisclosureDate)) {
                $validAuthors[] = [
                    'author_id' => $disclosure['author_id'],
                    'disclosure' => $disclosure,
                    'valid_since' => $disclosure['updated_at'] ?? $disclosure['created_at'] ?? null
                ];
            }
        }

        return $validAuthors;
    }

    public function getMappedAuthorsWithDisclosure($search = null): array
    {
        if($search == 'valid')
            $disclosures = $this->getAuthorsWithValidDisclosures();

        $disclosures = $this->getAuthorsWithDisclosures();
        $authorsWithDisclosureMapped = [];
        array_map(function($val) use (&$authorsWithDisclosureMapped) {
            $authorsWithDisclosureMapped[$val['author_id']] = $val;
        }, $disclosures);

        return $authorsWithDisclosureMapped;
    }

    /**
     * Check if a single disclosure is valid (business rules)
     */
    public function isDisclosureValidDate(array $disclosure, string $currentDisclosureDate): bool
    {
        $disclosureDate = $this->getLatestDisclosureDate($disclosure);
        if(strtotime($disclosureDate) >= strtotime($currentDisclosureDate)){
            return true;
        }

        return false;
    }

    public function getLatestDisclosureDate($disclosure)
    {
        if (!empty($disclosure['updated_at'])) {
            return $disclosure['updated_at'];
        }

        if (!empty($disclosure['created_at'])) {
            return $disclosure['created_at'];
        }
        // Return a default or null if no date is found
        return null;
    }


    /**
     * Get authors WITHOUT valid disclosures
     */
    public function getAuthorsWithoutValidDisclosures(): array
    {
        // If you have an Author model
        $users = $this->usersModel->findAll();
        $authorsWithValid = $this->getAuthorsWithValidDisclosures();

        $authorIdsWithValid = array_column($authorsWithValid, 'author_id');

        return array_filter($users, function($user) use ($authorIdsWithValid) {
            return !in_array($user['id'], $authorIdsWithValid);
        });
    }

    /**
     * Get valid disclosure count for dashboard/reporting
     */
    public function getValidDisclosureStats(): array
    {
        $disclosures = $this->disclosureModel
            ->where('deleted_at', null)
            ->findAll();

        $total = count($disclosures);
        $valid = 0;
        $invalid = 0;

        foreach ($disclosures as $disclosure) {
            if ($this->isDisclosureValidDate($disclosure)) {
                $valid++;
            } else {
                $invalid++;
            }
        }

        return [
            'total' => $total,
            'valid' => $valid,
            'invalid' => $invalid,
            'valid_percentage' => $total > 0 ? round(($valid / $total) * 100, 2) : 0
        ];
    }


}