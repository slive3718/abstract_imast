<?php declare(strict_types=1);

namespace App\Services;

use App\Models\CitiesModel;
use App\Models\CountriesModel;
use App\Models\InstitutionModel;
use App\Models\StatesModel;
use App\Models\UsersProfileModel;
use CodeIgniter\Config\BaseService;
use CodeIgniter\Model;
use Exception;

/**
 * Service class for Institution-related operations, handling data retrieval
 * and address component mapping using dependency injection.
 */
class InstitutionServices extends BaseService
{
    // 1. Define properties to hold the injected models (models are typically models, not services)
    protected InstitutionModel $institutionModel;
    protected CitiesModel $citiesModel;
    protected CountriesModel $countriesModel;
    protected StatesModel $statesModel;

    protected \CodeIgniter\Database\BaseConnection $defaultDB;
    protected \CodeIgniter\Database\BaseConnection $sharedDB;
    /**
     * 2. Use Dependency Injection in the constructor.
     */
    public function __construct() {

        $this->institutionModel = (new InstitutionModel());
        $this->citiesModel = (new CitiesModel());
        $this->countriesModel = (new CountriesModel());
        $this->statesModel = (new StatesModel());

        $this->defaultDB = \Config\Database::connect();
        $this->sharedDB = \Config\Database::connect('shared');
    }

    /**
     * Safely retrieves the name field from an address component model by ID.
     * Assumes address component models (City/State/Country) have a 'name' field.
     *
     * @param Model $model The CodeIgniter model instance (e.g., CitiesModel).
     * @param int|null $id The ID of the record to find.
     * @param string $default The default string to return if the ID is null or the record is not found.
     * @return string The name of the component, or the default string.
     */
    private function getAddressComponentName(Model $model, ?int $id, string $default): string
    {
        if ($id === null || $id <= 0) {
            return $default;
        }

        // Use find() to fetch the record. Assumes models return an array or null.
        $record = $model->find($id);

        // Check if the record exists and has a 'name' key
        return is_array($record) && isset($record['name'])
            ? $record['name']
            : $default;
    }

    /**
     * Retrieves institution details along with full address names.
     *
     * @param int|string $id The ID of the institution.
     * @return array|null Returns an array of institution details on success, or null on failure.
     */
    public function getInstitutionWithAddress(int|string $id): ?array
    {
        // Ensure ID is treated as an integer internally
        $institutionId = (int)$id;

        try {
            // Validate ID
            if ($institutionId <= 0) {
                log_message('warning', "Invalid institution ID provided: " . $id);
                return null;
            }

            $institution = $this->institutionModel->find($institutionId);

            if (!$institution) {
                log_message('warning', "Institution not found with ID: " . $id);
                return null;
            }

            // Extract IDs, ensuring they are cast to integers or null
            $cityId = $institution['city_id'] ? (int)$institution['city_id'] : null;
            $countryId = $institution['country_id'] ? (int)$institution['country_id'] : null;
            $stateId = $institution['state_id'] ? (int)$institution['state_id'] : null;

            // Use the robust helper to get address components
            $cityName = $this->getAddressComponentName($this->citiesModel, $cityId, 'Unknown City');
            $countryName = $this->getAddressComponentName($this->countriesModel, $countryId, 'Unknown Country');
            $stateName = $this->getAddressComponentName($this->statesModel, $stateId, 'Unknown State');

            return [
                'id' => $institution['id'] ?? $institutionId,
                'name' => $institution['name'] ?? 'Institution Name Missing',
                'city' => $cityName,
                'country' => $countryName,
                'state' => $stateName,
            ];

        } catch (Exception $e) {
            log_message('error', 'Error in getInstitutionWithAddress: ' . $e->getMessage() . ' for ID: ' . $id);
            return null;
        }
    }

    // Removed the private helper methods: getCity, getCountry, getState, as they were redundant/incorrectly implemented.

    /**
     * Alternative method to get institutions with addresses in bulk.
     * This method calls the single-record method in a loop, which can lead to N+1 queries.
     * For high performance, consider using Query Builder joins in the Model layer.
     *
     * @param array<int> $ids Array of institution IDs.
     * @return array<array> Returns an array of processed institution records.
     */
    public function getInstitutionsWithAddresses(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $result = [];
        // Iterate through IDs and use the single-record retrieval method
        foreach ($ids as $id) {
            $institution = $this->getInstitutionWithAddress($id);
            if ($institution !== null) {
                $result[] = $institution;
            }
        }

        return $result;
    }

    public function getInstitutionQuery($institution_id){

        $query = (new InstitutionModel());
        $query->select('
        institution.name as institution_name,
        ci.name as institution_city,
        co.name as institution_country,
       ')
            ->join($this->defaultDB->database. '.cities ci', 'institution.city_id = ci.id', 'left')
            ->join($this->defaultDB->database. '.countries co', 'ci.country_id = co.id', 'left')
            ->where('institution.id', $institution_id);

        $result = $query->first();
        return $result;

    }
}