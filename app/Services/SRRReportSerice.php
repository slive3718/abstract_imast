<?php

namespace App\Services;

use App\Models\AbstractCategoriesModel;
use App\Models\AdminAcceptanceModel;
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
       $reportData = $this->processReportAllPapers();

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

//    public function processReportBySchedule()
//    {
//        $schedules = $this->loadSchedules() ?? [];
//        if (empty($schedules)) {
//            return [];
//        }
//
//        $scheduleById = array_column($schedules, null, 'id');
//        $scheduleIds = array_keys($scheduleById);
//
//        // Load dependencies
//        $talks = (new SchedulerSessionTalksModel())
//            ->join('papers p', 'scheduler_session_talks.abstract_id = p.id', 'right')
//            ->join('admin_abstract_acceptance aaa', 'p.id = aaa.abstract_id', 'left')
////            ->whereIn('scheduler_event_id', $scheduleIds)
//            ->where('aaa.acceptance_confirmation', 1) // Only accepted abstracts
//            ->groupBy('p.id')
//            ->findAll();
//
////        $talks = (new AdminAcceptanceModel())
////            ->join('scheduler_session_talks sst', 'sst.abstract_id = admin_abstract_acceptance.abstract_id', 'left')
////            ->join('papers p', 'sst.abstract_id = p.id', 'right')
////            ->where('admin_abstract_acceptance.acceptance_confirmation', 1) // Only accepted abstracts
////            ->groupBy('p.id')
////            ->findAll();
//
//
//
////        $acceptedPapers = (new AdminAcceptanceModel())->findAll();
////        $acceptedPaperIds = array_column($acceptedPapers, 'abstract_id');
//
////        $formattedPapers = [];
////        foreach ($acceptedPaperIds as $acceptedPaperId){
////            $formattedPapers[$acceptedPaperId]['talkData'] =
////        }
//
//        print_r($talks);exit;
//
//        $users = (new UserModel())
//            ->select('id, name, middle_name, surname, email')
//            ->findAll();
//        $usersById = array_column($users, null, 'id');
//
//        $usersProfile = (new UsersProfileModel())->findAll();
//        $usersProfileById = array_column($usersProfile, null, 'author_id');
//
//        $allCategories = (new AbstractCategoriesModel())->findAll();
//        $allCategories = array_column($allCategories, 'name', 'id');
//
//        $paperIds = array_column(
//            (new PapersModel())->select('id')->findAll(),
//            'id'
//        );
//
//        $paperTypes = (new PaperTypeModel())->findAll();
//        $paperTypes = array_column($paperTypes, 'name', 'id');
//
//        $presenters = $this->loadPresentingAuthorsByPaperIds($paperIds);
//        $presentersByPaperId = array_column($presenters, null, 'paper_id');
//
//        $rows = [];
//
//        foreach ($scheduleById as $event) {
//            $sessionDate  = $event['session_date']   ?? '1970-01-01';
//            $sessionStart = $event['session_start_time'] ?? '00:00:00';
//            $roomId       = $event['room_id']        ?? 'ZZZ_NO_ROOM';
//
//            // Sorting key components – date → room → session time
//            $datePart = date('Ymd', strtotime($sessionDate));
//            $roomPart = str_pad((string)$roomId, 6, '0', STR_PAD_LEFT);
//            $timePart = $this->makeSortableDatetime($sessionDate, $sessionStart);
//
//            $sessionBaseKey = $datePart . '_' . $roomPart . '_' . $timePart;
//
//            // 1. Moderators – always first
//            $moderatorIds = json_decode($event['session_chair_ids'] ?? '[]', true) ?: [];
//
//            foreach ($moderatorIds as $index => $userId) {
//                $user = $usersById[$userId] ?? null;
//                if (!$user) {
//                    continue;
//                }
//
//                $sortKey = $sessionBaseKey . '_000' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
//
//                $rows[] = [
//                    'Session Date'            => date('Y-m-d', strtotime($sessionDate)),
//                    'Session Title'           => $event['session_title'] ?? '',
//                    'Session Start Time'      => date('H:i', strtotime($sessionStart)),
//                    'Presentation Start time' => '',
//                    'Room'                    => $event['room_id'] ?? '',
//                    'First Name'     => $user['name'] ?? '',
//                    'Middle Name'    => $user['middle_name'] ?? '',
//                    'Last Name'      => $user['surname'] ?? '',
//                    'Credentials'    => !empty($user['designations']['data'] ) ? implode(', ', $user['designations']['data'] ) : '',
//                    'Email Address'  => $user['email'] ?? '',
//                    'Presentation Title' => '',
//                    'Presentation Type'  => '',
//                    'Type' => 'Moderator',
//                    'Role' => 'Moderator',
//                    'Poster Topic'  => '',
//                    'Poster Number' => '',
//                    'AbstractID'         => '',
//                    '_sort_key' => $sortKey,
//                ];
//            }
//
//            // 2. Talks / Presenters – after moderators, ordered by time
//            foreach ($talks as $talk) {
//
////                print_r($talk);exit;
//                if ($talk['scheduler_event_id'] !== $event['id']) {
//                    continue;
//                }
//
//                $presenter = null;
//                if (!empty($talk['abstract_id']) && isset($presentersByPaperId[$talk['abstract_id']])) {
//                    $presenter = $presentersByPaperId[$talk['abstract_id']];
//                }
//
//                $user = $presenter ? ($usersById[$presenter['author_id']] ?? null) : null;
//                $user['profile'] = $presenter ? ($usersProfileById[$presenter['author_id']] ?? null) : null;
//                $user['designations'] = $presenter ? (new UserServices())->get_designations($user['profile']['designations'], $user['profile']['other_designation']) : null;
//
//                $talkTime = $talk['time_start'] ?? '23:59:59';
//                $talkKey  = $this->makeSortableDatetime($sessionDate, $talkTime);
//
//                // FIXED LINE – keep room & date in the key!
//                $sortKey = $sessionBaseKey . '_1_' . substr($talkKey, -14);
//
//                $rows[] = [
//                    'Session Date'            => date('Y-m-d', strtotime($sessionDate)),
//                    'Session Title'           => $event['session_title'] ?? '',
//                    'Session Start Time'      => date('H:i a', strtotime($sessionStart)),
//                    'Presentation Start time' => date('H:i a', strtotime($talkTime)),
//                    'Room'                    => $event['room_id'] ?? '',
//                    'First Name'     => $user['name'] ?? '',
//                    'Middle Name'    => $user['middle_name'] ?? '',
//                    'Last Name'      => $user['surname'] ?? '',
//                    'Credentials'    => !empty($user['designations']['data'] ) ? implode(', ', $user['designations']['data'] ) : '',
//                    'Email Address'  => $user['email'] ?? '',
//                    'Presentation Title' => ($talk['type_id'] == '1' ? $talk['assigned_id']:'') .' '. $talk['title'] ?? '',
//                    'Presentation Type'  => $paperTypes[$talk['presentation_preference']] ?? 'Unknown',
//                    'Type' => '',
//                    'Role' => 'Presenter',
//                    'Poster Topic'  => $allCategories[$talk['abstract_category']] ?? '',
//                    'Poster Number' => $talk['assigned_id'] ?? '',
//                     'AbstractID'         => $talk['abstract_id'] ?? '',
//                    '_sort_key' => $sortKey,
//                ];
//            }
//        }
//
//        // Sort everything chronologically (now includes room)
//        usort($rows, static fn($a, $b) => strcmp($a['_sort_key'], $b['_sort_key']));
//
//        // Remove internal sort field
//        return array_map(static fn($row) => array_diff_key($row, ['_sort_key' => true]), $rows);
//    }

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


    public function processReportAllPapers()
    {
        $schedules = $this->loadSchedules() ?? [];

        $scheduleById = array_column($schedules, null, 'id');
        $scheduleIds  = array_keys($scheduleById);

        $acceptedPapers = (new PapersModel())
            ->select('*, papers.id as id')
            ->join('admin_abstract_acceptance aaa', 'papers.id = aaa.abstract_id', 'left')
            ->where('aaa.acceptance_confirmation', 1)
            ->asArray()
            ->findAll();

        if (empty($acceptedPapers)) {
            return [];
        }

        $acceptedPaperIds = array_column($acceptedPapers, 'id');

        // Preloads
        $users            = (new UserModel())->select('id, name, middle_name, surname, email')->findAll();
        $usersById        = array_column($users, null, 'id');

        $usersProfile     = (new UsersProfileModel())->findAll();
        $usersProfileById = array_column($usersProfile, null, 'author_id');

        $allCategories    = (new AbstractCategoriesModel())->findAll();
        $categoriesById   = array_column($allCategories, 'name', 'id');

        $paperTypes       = (new PaperTypeModel())->findAll();
        $paperTypesById   = array_column($paperTypes, 'name', 'id');

        $presenters       = $this->loadPresentingAuthorsByPaperIds($acceptedPaperIds);
        $presentersByPaperId = array_column($presenters, null, 'paper_id');

        $paperById = array_column($acceptedPapers, null, 'id');

        // ─── 1. Collect all scheduled talks grouped by event ───────────────────────
        $talksByEvent = [];
        $talkModel = new SchedulerSessionTalksModel();
        $allTalks = $talkModel
            ->whereIn('scheduler_event_id', $scheduleIds)
            ->whereIn('abstract_id', $acceptedPaperIds)
            ->groupBy('abstract_id')
            ->findAll();

        foreach ($allTalks as $talk) {
            $eventId = $talk['scheduler_event_id'];
            $talksByEvent[$eventId][] = $talk;
        }

        $rows = [];

        // Process scheduled sessions
        foreach ($talksByEvent as $eventId => $talksInSession) {
            $event = $scheduleById[$eventId] ?? null;
//            if (!$event) continue;

            $sessionDate  = $event['session_date']   ?? '';
            $sessionStart = $event['session_start_time'] ?? '';
            $roomId       = $event['room_id']        ?? '—';

//            if (empty($sessionDate)) continue;

            $datePart = date('Ymd', strtotime($sessionDate));
            $roomPart = str_pad((string)$roomId, 6, '0', STR_PAD_LEFT);
            $timePart = $this->makeSortableDatetime($sessionDate, $sessionStart ?: '23:59:59');
            $sessionBaseKey = $datePart . '_' . $roomPart . '_' . $timePart;

            // Moderators - once per session
            $moderatorIds = json_decode($event['session_chair_ids'] ?? '[]', true) ?: [];
            foreach ($moderatorIds as $index => $modId) {
                if (!isset($usersById[$modId])) continue;

                $modUser = $usersById[$modId];
                $modProfile = $usersProfileById[$modId] ?? null;

                $modDesignations = '';
                if ($modProfile && !empty($modProfile['designations'])) {
                    $desData = (new UserServices())->get_designations(
                        $modProfile['designations'],
                        $modProfile['other_designation'] ?? ''
                    );
                    $modDesignations = !empty($desData['data'])
                        ? implode(', ', $desData['data'])
                        : '';
                }

                $modSortKey = $sessionBaseKey . '_000' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

                $rows[] = [
                    'Session Date'            => date('Y-m-d', strtotime($sessionDate)),
                    'Session Title'           => $event['session_title'] ?? '',
                    'Session Start Time'      => $sessionStart ? date('h:i a', strtotime($sessionStart)) : '',
                    'Presentation Start time' => '',
                    'Room'                    => $roomId === 'ZZZ_NO_ROOM' ? '' : $roomId,

                    'First Name'              => $modUser['name'] ?? '',
                    'Middle Name'             => $modUser['middle_name'] ?? '',
                    'Last Name'               => $modUser['surname'] ?? '',
                    'Credentials'             => $modDesignations,
                    'Email Address'           => $modUser['email'] ?? '',

                    'Presentation Title'      => '',
                    'Presentation Type'       => '',
                    'Type'                    => '',
                    'Role'                    => 'Moderator',

                    'Poster Topic'            => '',
                    'Poster Number'           => '',
                    'AbstractID'              => '',
                    'acceptance_preference'   => '',

                    '_sort_key'               => $modSortKey,
                ];
            }

            // Presenters in this session
            foreach ($talksInSession as $talk) {
                $paperId = $talk['abstract_id'];
                $paper = $paperById[$paperId] ?? null;
                if (!$paper) continue;

                $presenter = $presentersByPaperId[$paperId] ?? null;
                $user      = $presenter ? ($usersById[$presenter['author_id']] ?? null) : null;
                $profile   = $presenter ? ($usersProfileById[$presenter['author_id']] ?? null) : null;

                $designations = '';
                if ($profile && !empty($profile['designations'])) {
                    $desData = (new UserServices())->get_designations(
                        $profile['designations'],
                        $profile['other_designation'] ?? ''
                    );
                    $designations = !empty($desData['data'])
                        ? implode(', ', $desData['data'])
                        : '';
                }

                $talkTime = $talk['time_start'] ?? '23:59:59';
                $talkKey  = $this->makeSortableDatetime($sessionDate, $talkTime);
                $presenterSortKey = $sessionBaseKey . '_1_' . substr($talkKey, -14);

                $rows[] = [
                    'Session Date'            => date('Y-m-d', strtotime($sessionDate)),
                    'Session Title'           => $event['session_title'] ?? '',
                    'Session Start Time'      => $sessionStart ? date('h:i a', strtotime($sessionStart)) : '',
                    'Presentation Start time' => $talkTime !== '23:59:59' ? date('h:i a', strtotime($talkTime)) : '',
                    'Room'                    => $roomId === 'ZZZ_NO_ROOM' ? '' : $roomId,

                    'First Name'              => $user['name']        ?? '',
                    'Middle Name'             => $user['middle_name'] ?? '',
                    'Last Name'               => $user['surname']     ?? '',
                    'Credentials'             => $designations,
                    'Email Address'           => $user['email']       ?? '',

                    'Presentation Title'      => ($paper['presentation_preference'] == '1' ? ($paper['assigned_id'] ?? '') . ' ' : '') . ($paper['title'] ?? ''),
                    'Presentation Type'       => $paperTypesById[$paper['presentation_preference']] ?? ($talk['presentation_preference'] ?? '—'),
                    'Type'                    => '',
                    'Role'                    => 'Presenter',

                    'Poster Topic'            => $categoriesById[$paper['abstract_category'] ?? $talk['abstract_category'] ?? ''] ?? '',
                    'Poster Number'           => $talk['assigned_id'] ?? $paper['assigned_id'] ?? '',
                    'AbstractID'              => $paper['id'] ?? '',
                    'acceptance_preference'   => $paper['presentation_preference'] ?? '',

                    '_sort_key'               => $presenterSortKey,
                ];
            }
        }

        // ─── 2. Add all NOT scheduled accepted papers at the end ───────────────────
        $scheduledPaperIds = array_unique(array_column($allTalks, 'abstract_id'));

        foreach ($acceptedPapers as $paper) {
            if (in_array($paper['id'], $scheduledPaperIds)) {
                continue;
            }

            $presenter = $presentersByPaperId[$paper['id']] ?? null;
            $user      = $presenter ? ($usersById[$presenter['author_id']] ?? null) : null;
            $profile   = $presenter ? ($usersProfileById[$presenter['author_id']] ?? null) : null;

            $designations = '';
            if ($profile && !empty($profile['designations'])) {
                $desData = (new UserServices())->get_designations(
                    $profile['designations'],
                    $profile['other_designation'] ?? ''
                );
                $designations = !empty($desData['data'])
                    ? implode(', ', $desData['data'])
                    : '';
            }

            $rows[] = [
                'Session Date'            => '',
                'Session Title'           => '',
                'Session Start Time'      => '',
                'Presentation Start time' => '',
                'Room'                    => '',

                'First Name'              => $user['name']        ?? '',
                'Middle Name'             => $user['middle_name'] ?? '',
                'Last Name'               => $user['surname']     ?? '',
                'Credentials'             => $designations,
                'Email Address'           => $user['email']       ?? '',

                'Presentation Title'      => ($paper['type_id'] == '1' ? ($paper['assigned_id'] ?? '') . ' ' : '') . ($paper['title'] ?? ''),
                'Presentation Type'       => $paperTypesById[$paper['presentation_preference']] ?? '—',
                'Type'                    => '',
                'Role'                    => 'Accepted (Not Scheduled)',

                'Poster Topic'            => $categoriesById[$paper['abstract_category'] ?? ''] ?? '',
                'Poster Number'           => $paper['assigned_id'] ?? '',
                'AbstractID'              => $paper['id'] ?? '',
                'acceptance_preference'   => $paper['presentation_preference'] ?? '',

                '_sort_key'               => '99991231_999999_99999999_9_99999999999999',
            ];
        }

        // Final sort
        usort($rows, function ($a, $b) {
            return strcmp($a['_sort_key'], $b['_sort_key']);
        });

        // Remove internal sort field
        return array_map(static fn($row) => array_diff_key($row, ['_sort_key' => true]), $rows);

    }
}
