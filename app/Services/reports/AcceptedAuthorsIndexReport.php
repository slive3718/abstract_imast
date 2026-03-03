<?php

namespace App\Services\reports;

use App\Models\PaperAuthorsModel;
use App\Models\UserModel;
use CodeIgniter\Config\BaseService;


class AcceptedAuthorsIndexReport extends BaseService
{
    function __construct()
    {
        helper('array');
    }


    public function all_accepted_authors_index_report_word(){

        $order = [
            'column' => 'user_surname',
            'direction' => 'asc'
        ];
        $authors = (new PaperAuthorsModel())->getAuthorsAcceptedByAdmin(null , $order);

        $validAuthors = array_values(array_filter($authors, fn($author) => empty($author['is_removed'])));
        $authorsIds = array_unique(array_column($validAuthors, 'author_id'));
        $authorsIds = array_filter($authorsIds, function($id) {
            return $id != 0; // skip author_id = 0
        });


        if (empty($authorsIds)) return 'empty ids';

        $userDetails = (new UserModel())
            ->whereIn('id', $authorsIds)
            ->findAll();

        $detailsMap = array_column($userDetails, null, 'id');

        $allAssignedPapers = (new PaperAuthorsModel())->getByAuthors($authorsIds);
        $validAssignedPapers = array_values(array_filter($allAssignedPapers, fn($assignedPaper) => empty($assignedPaper['is_removed'])));

        $papersMap = [];
        foreach ($validAssignedPapers as $paper) {
            $papersMap[$paper['author_id']][] = $paper;
        }


        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();

        foreach ($authorsIds as $authorId) {
            if(empty($detailsMap[$authorId]))
                continue;

            $assignedIdsArray = array_filter(array_column($papersMap[$authorId], 'assigned_id'));
            asort($assignedIdsArray);
            $assignedIds = !empty($assignedIdsArray) ? ' '. implode(', ', $assignedIdsArray) : '';

            $middleName = $detailsMap[$authorId]['middle_name'] ?? '';
            $middleInitial = !empty($middleName) ? ' '.strtoupper(substr(trim($middleName), 0, 1)). '.': '';
            $authorArrayData = [
                $detailsMap[$authorId]['surname'],
                $detailsMap[$authorId]['name']. $middleInitial. $assignedIds,
            ];
            $authorText =  implode(', ', $authorArrayData);
            $section->addText(
                $authorText
            );
        }

        \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');

        // ✅ Save ONLY to temp file — remove the Agenda.docx save
        $tempFile = tempnam(sys_get_temp_dir(), 'WordReport') . '.docx';
        $objWriter->save($tempFile);

        // ✅ Clear any accidental output buffer before sending headers
        if (ob_get_length()) ob_clean();

        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="All Accepted Authors Index Report.docx"');
        header('Content-Length: ' . filesize($tempFile));
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

        readfile($tempFile);
        unlink($tempFile);
    }
}
