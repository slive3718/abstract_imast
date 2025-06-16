<?php

namespace App\Controllers;

use Config\Database;

class DBController extends BaseController
{
    public function truncate($password)
    {
        $db = Database::connect();
        $dbName = $db->query('SELECT DATABASE() AS db')->getRow()->db;

        if ($password !== $dbName) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Wrong database. Truncate canceled.']);
        }

        $tables = [
            'logs', 'mail_logs', 'moderator_acceptance', 'papers',
            'paper_assigned_reviewer', 'paper_authors', 'paper_deputy_acceptance',
            'paper_uploads', 'paper_upload_views', 'removed_paper_authors',
            'admin_abstract_acceptance', 'admin_abstract_comment',
            'author_abstract_acceptance', 'author_organization',
            'author_organization_affiliations', 'author_presentation_upload',
            'institution', 'users', 'users_profile', 'user_organizations', 'institution'
        ];
//this is moved to the shared users. No longer be able to truncate
//        foreach ($tables as $table) {
//            if (in_array($table, ['users', 'users_profile'])) {
//                // Delete all except id = 1
//                $db->query("DELETE FROM `$table` WHERE `id` != 1");
//                // Reset AUTO_INCREMENT to 2
//                $db->query("ALTER TABLE `$table` AUTO_INCREMENT = 2");
//            } else {
//                // Truncate other tables
//                $db->query("TRUNCATE TABLE `$table`");
//            }
//        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Tables truncated. Users and Users_profile keep id=1, auto_increment reset.']);
    }
}
