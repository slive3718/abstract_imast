<?php

namespace App\Services;

use App\Models\AbstractCategoriesModel;
use App\Models\DesignationsModel;
use App\Models\PaperAuthorsModel;
use App\Models\PapersModel;
use App\Models\PaperTypeModel;
use App\Models\SchedulerModel;
use App\Models\SchedulerSessionTalksModel;
use App\Models\UserModel;
use App\Models\UsersProfileModel;
use CodeIgniter\Config\BaseService;

class SRRReportSerice extends BaseService
{

    private $request;

    public function __construct()
    {

        $this->request = \Config\Services::request();
    }


    public function generateReport()
    {
        // Implement report generation logic here
       $reportData = $this->processReportBySchedule();

        $excel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('Abstract All Data Export', true);

        // Apply header styling
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 12],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => '8fce00']
            ],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];

        $dataStyle = [
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ];

        if(empty($reportData))
            throw new \Exception('No data found for the report.');


        $ssrHeaders = [
            "Session Date",
            "Session Title",
            "Session Time",
            "Presentation Start time",
            "Room",

            "First Name",
            "Middle Name",
            "Last Name",
            "Credentials",
            "Email Address",

            "Presentation Title",
            "Presentation Type",

            "Type",
            "Role",

            "Poster Topic",
            "Poster Number",

            "AbstractID"
        ];


        $sheet->fromArray($ssrHeaders, null, 'A1');
        $sheet->fromArray($reportData, null, 'A2');

        // Get the highest column with data
        $highestColumn = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();
// Apply header style dynamically for all columns from A to the highest column
        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray($headerStyle);

// Apply data style for all columns from A2 to the last row dynamically
        $sheet->getStyle('A2:' . $highestColumn . $sheet->getHighestRow())->applyFromArray($dataStyle);

// Loop through each column from A to the highest column
        foreach (range('A', $highestColumn) as $columnID) {
            $maxLength = 0;

            // Loop through each row for the current column
            for ($row = 1; $row <= $highestRow; $row++) {
                $cellValue = $sheet->getCell($columnID . $row)->getValue();

                // Check the length of the cell content and keep track of the maximum length
                if ($cellValue !== null) {
                    $cellLength = strlen($cellValue);
                    if ($cellLength > $maxLength) {
                        $maxLength = $cellLength;
                    }
                }
            }

            // Set column width based on the maximum length of content
            $sheet->getColumnDimension($columnID)->setWidth($maxLength + 2); // Add padding
        }
        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="SRR_Report' . date('Y-m-d') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $xlsxWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($excel, 'Xlsx');
        $xlsxWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($excel);

        exit($xlsxWriter->save('php://output'));
    }

    private function loadPresentingAuthorsByPaperIds(array $paperIds): array
    {
        if (empty($paperIds)) return [];

        return (new PaperAuthorsModel())
            ->whereIn('paper_id', $paperIds)
            ->where('LOWER(is_presenting_author)', 'yes')
            ->findAll();
    }

    public function processReportBySchedule()
    {
        $schedules = $this->loadSchedules() ?? [];
        if (empty($schedules)) {
            return [];
        }

        $scheduleById = array_column($schedules, null, 'id');
        $scheduleIds = array_keys($scheduleById);

        // Load dependencies
        $talks = (new SchedulerSessionTalksModel())
            ->join('papers p', 'scheduler_session_talks.abstract_id = p.id', 'left')
            ->whereIn('scheduler_event_id', $scheduleIds)
            ->findAll();

        $users = (new UserModel())
            ->select('id, name, middle_name, surname, email')
            ->findAll();
        $usersById = array_column($users, null, 'id');

        $usersProfile = (new UsersProfileModel())->findAll();
        $usersProfileById = array_column($usersProfile, null, 'author_id');

        $allCategories = (new AbstractCategoriesModel())->findAll();
        $allCategories = array_column($allCategories, 'name', 'id');

        $paperIds = array_column(
            (new PapersModel())->select('id')->findAll(),
            'id'
        );

        $paperTypes = (new PaperTypeModel())->findAll();
        $paperTypes = array_column($paperTypes, 'name', 'id');

        $presenters = $this->loadPresentingAuthorsByPaperIds($paperIds);
        $presentersByPaperId = array_column($presenters, null, 'paper_id');

        $rows = [];

        foreach ($scheduleById as $event) {
            $sessionDate  = $event['session_date']   ?? '1970-01-01';
            $sessionStart = $event['session_start_time'] ?? '00:00:00';
            $roomId       = $event['room_id']        ?? 'ZZZ_NO_ROOM';

            // Sorting key components – date → room → session time
            $datePart = date('Ymd', strtotime($sessionDate));
            $roomPart = str_pad((string)$roomId, 6, '0', STR_PAD_LEFT);
            $timePart = $this->makeSortableDatetime($sessionDate, $sessionStart);

            $sessionBaseKey = $datePart . '_' . $roomPart . '_' . $timePart;

            // 1. Moderators – always first
            $moderatorIds = json_decode($event['session_chair_ids'] ?? '[]', true) ?: [];

            foreach ($moderatorIds as $index => $userId) {
                $user = $usersById[$userId] ?? null;
                if (!$user) {
                    continue;
                }

                $sortKey = $sessionBaseKey . '_000' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

                $rows[] = [
                    'Session Date'            => date('Y-m-d', strtotime($sessionDate)),
                    'Session Title'           => $event['session_title'] ?? '',
                    'Session Start Time'      => date('H:i', strtotime($sessionStart)),
                    'Presentation Start time' => '',
                    'Room'                    => $event['room_id'] ?? '',
                    'First Name'     => $user['name'] ?? '',
                    'Middle Name'    => $user['middle_name'] ?? '',
                    'Last Name'      => $user['surname'] ?? '',
                    'Credentials'    => '',
                    'Email Address'  => $user['email'] ?? '',
                    'Presentation Title' => '',
                    'Presentation Type'  => '',
                    'Type' => 'Moderator',
                    'Role' => 'Moderator',
                    'Poster Topic'  => '',
                    'Poster Number' => '',
                    'AbstractID'         => '',
                    '_sort_key' => $sortKey,
                ];
            }

            // 2. Talks / Presenters – after moderators, ordered by time
            foreach ($talks as $talk) {
                
                if ($talk['scheduler_event_id'] !== $event['id']) {
                    continue;
                }

                $presenter = null;
                if (!empty($talk['abstract_id']) && isset($presentersByPaperId[$talk['abstract_id']])) {
                    $presenter = $presentersByPaperId[$talk['abstract_id']];
                }

                $user = $presenter ? ($usersById[$presenter['author_id']] ?? null) : null;
                $user['profile'] = $presenter ? ($usersProfileById[$presenter['author_id']] ?? null) : null;
                $user['designations'] = $presenter ? (new UserServices())->get_designations($user['profile']['designations'], $user['profile']['other_designation']) : null;

                $talkTime = $talk['time_start'] ?? '23:59:59';
                $talkKey  = $this->makeSortableDatetime($sessionDate, $talkTime);

                // FIXED LINE – keep room & date in the key!
                $sortKey = $sessionBaseKey . '_1_' . substr($talkKey, -14);

                $rows[] = [
                    'Session Date'            => date('Y-m-d', strtotime($sessionDate)),
                    'Session Title'           => $event['session_title'] ?? '',
                    'Session Start Time'      => date('H:i a', strtotime($sessionStart)),
                    'Presentation Start time' => date('H:i a', strtotime($talkTime)),
                    'Room'                    => $event['room_id'] ?? '',
                    'First Name'     => $user['name'] ?? '',
                    'Middle Name'    => $user['middle_name'] ?? '',
                    'Last Name'      => $user['surname'] ?? '',
                    'Credentials'    => !empty($user['designations']['data'] ) ? implode(', ', $user['designations']['data'] ) : '',
                    'Email Address'  => $user['email'] ?? '',
                    'Presentation Title' => ($talk['type_id'] == '1' ? $talk['assigned_id']:'') .' '. $talk['title'] ?? '',
                    'Presentation Type'  => $paperTypes[$talk['type_id']] ?? 'Unknown',
                    'Type' => '',
                    'Role' => 'Presenter',
                    'Poster Topic'  => $allCategories[$talk['abstract_category']] ?? '',
                    'Poster Number' => $talk['assigned_id'] ?? '',
                     'AbstractID'         => $talk['abstract_id'] ?? '',
                    '_sort_key' => $sortKey,
                ];
            }
        }

        // Sort everything chronologically (now includes room)
        usort($rows, static fn($a, $b) => strcmp($a['_sort_key'], $b['_sort_key']));

        // Remove internal sort field
        return array_map(static fn($row) => array_diff_key($row, ['_sort_key' => true]), $rows);
    }

    private function makeSortableDatetime(?string $datePart, ?string $timePart): string
    {
        if (!$datePart || !$timePart) {
            return '9999-12-31 23:59:59'; // push invalid to the very end
        }

        // Handle different possible date/time formats safely
        $date = date('Y-m-d', strtotime($datePart));
        $time = date('H:i:s', strtotime($timePart));

        return "$date $time";  // ← this format sorts perfectly with strcmp
    }

    private function loadSchedules(){
        $SchedulerModel = (new SchedulerModel());
        return $SchedulerModel->db->table('scheduler_events se')
            ->select('*')
            ->orderBy('se.session_date', 'asc')
            ->where('is_deleted', 0)
            ->get()->getResultArray();
    }

    private function mapDesignations($profile): string {
        $designations = json_decode($profile['designations'] ?? '[]', true);
        $designationsOptions = (new DesignationsModel())->findAll();
        $designationsOptions = array_column($designationsOptions, 'name','id');

        if(empty($designations))
            return '';

        $designationAssigned= [];
        foreach ($designations as $designationId){
            if(strtolower($designationsOptions[$designationId]) === 'other'){
                $designationAssigned[] = $profile['other_designation'] ?: 'None';
            }else{
                $designationAssigned[] = $designationsOptions[$designationId];
            }

        }

        return implode(', ', $designationAssigned);
    }

}
