<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

//App landing page
//$routes->get('/', 'Home::index');

$routes->group('admin',['filter' => 'authGuard'], function ($routes) {

    $routes->get('login', 'admin\Abstracts\AdminLogin::index');
    $routes->get('logout', 'admin\Abstracts\AdminLogin::logout/$1');
    $routes->get('papers_list', 'admin\Abstracts\AbstractController::papers_list');
    $routes->get('panels_list', 'admin\Abstracts\AbstractController::panels_list');
    $routes->get('getUsers', 'admin\Abstracts\AbstractController::getUsers/$1');
    $routes->get('getAllPanels', 'admin\Abstracts\AbstractController::getAllPanels');
    $routes->get('getAbstractAuthorsJson', 'admin\Abstracts\AbstractController::getAbstractAuthorsJson/$1');
    $routes->get('assign_reviewer_view/(:any)', 'admin\Abstracts\AbstractController::assign_reviewer_view/$1');
    $routes->post('delete_abstract', 'admin\Abstracts\AbstractController::delete_abstract/$1');
    $routes->get('reviewer_list', 'admin\Abstracts\AbstractController::reviewer_list');
    $routes->get('getReviewerList', 'admin\Abstracts\AbstractController::getReviewerList');
    $routes->get('exportScores', 'admin\Abstracts\AbstractController::exportScores/$1');
    $routes->get('view_abstract/(:any)', 'admin\Abstracts\AbstractController::view_abstract/$1');

    $routes->post('getUserById', 'admin\Abstracts\AbstractController::getUserById');
    $routes->post('getAllPapers', 'admin\Abstracts\AbstractController::getAllPapers');
    $routes->post('getAllPanels', 'admin\Abstracts\AbstractController::getAllPanels');
    $routes->post('getAllPanelsWithId', 'admin\Abstracts\AbstractController::getAllPanelsWithId');
    $routes->get('getDivisions', 'admin\Abstracts\AbstractController::getDivisions');
    $routes->post('assign_reviewer', 'admin\Abstracts\AbstractController::assign_reviewer');

    $routes->get('abstract_acceptance_view/(:any)', 'admin\Abstracts\AbstractController::abstract_acceptance_view/$1');
    $routes->get('edit_papers_submission/(:any)', 'admin\Abstracts\AbstractController::edit_papers_submission/$1');
    $routes->get('authors_and_copyright/(:any)', 'admin\Abstracts\AbstractController::authors_and_copyright/$1');
    $routes->get('panelist/(:any)', 'admin\Abstracts\AbstractController::panelist/$1');
    $routes->get('permissions/(:any)', 'admin\Abstracts\AbstractController::permissions/$1');
    $routes->post('save_admin_acceptance', 'admin\Abstracts\AbstractController::save_admin_acceptance/$1');
    $routes->post('update_abstract_ajax', 'admin\Abstracts\AbstractController::update_abstract_ajax');

    $routes->get('view_abstract_panel/(:any)', 'admin\Abstracts\AbstractController::view_abstract_panel/$1');
    $routes->get('view_individual_panel/(:any)', 'admin\Abstracts\AbstractController::view_individual_panel/$1');
    $routes->get('panel_coordinators/(:any)', 'admin\Abstracts\AbstractController::panel_coordinators/$1');
    $routes->get('panelist/(:any)', 'admin\Abstracts\AbstractController::panelist/$1');
    $routes->get('view_presentation_upload/(:any)', 'admin\Abstracts\AbstractController::view_presentation_upload/$1');
    $routes->get('view_paper_presentation_upload/(:any)', 'admin\Abstracts\AbstractController::view_paper_presentation_upload/$1');
    $routes->post('get_paper_uploads', 'admin\Abstracts\AbstractController::get_paper_uploads');
    $routes->post('paper_presentation_do_upload', 'admin\Abstracts\AbstractController::paper_presentation_do_upload');
    $routes->post('presentation_do_upload', 'admin\Abstracts\AbstractController::presentation_do_upload'); //admin edit upload presentations
    $routes->post('getIndividualUploads', 'admin\Abstracts\AbstractController::getIndividualUploads'); //admin edit upload presentations

    $routes->get('edit_panel_submission/(:any)', 'admin\Abstracts\AbstractController::edit_panel_submission/$1');
    $routes->get('edit_individual_panel_submission/(:any)', 'admin\Abstracts\AbstractController::edit_individual_panel_submission/$1');
    $routes->post('update_individual_panel_ajax', 'admin\Abstracts\AbstractController::update_individual_panel_ajax');
    $routes->post('saveIndividualPanelAdminAcceptance', 'admin\Abstracts\AbstractController::saveIndividualPanelAdminAcceptance');
    $routes->post('saveIndividualPanelComment', 'admin\Abstracts\AbstractController::saveIndividualPanelComment');
    $routes->post('getRegularReviewersByDivision', 'admin\Abstracts\AbstractController::getRegularReviewersByDivision');
    $routes->post('searchUser', 'admin\Abstracts\AbstractController::searchUser');

    $routes->post('assignPapersToProgramChair', 'admin\Abstracts\AbstractController::assignPapersToProgramChair');
    $routes->post('saveAdminAcceptance', 'admin\Abstracts\AbstractController::saveAdminAcceptance');
    $routes->post('emailAdminAcceptance', 'admin\Abstracts\AbstractController::emailAdminAcceptance');
    $routes->post('saveAdminCommentOnPaper', 'admin\Abstracts\AbstractController::saveAdminCommentOnPaper');
    $routes->post('savePaperTracks', 'admin\Abstracts\AbstractController::savePaperTracks');
    $routes->post('delete_presentation_upload', 'admin\Abstracts\AbstractController::delete_presentation_upload');
    $routes->post('assignPaperToRegularReviewer', 'admin\Abstracts\AbstractController::assignPaperToRegularReviewer');

    $routes->get('email_logs/(:any)', 'admin\EmailController::email_logs/$1');
    $routes->get('group_email_logs', 'admin\EmailController::group_email_logs');
    $routes->get('mass_mailer', 'admin\EmailController::mass_mailer');
    $routes->get('email_templates', 'admin\EmailController::email_templates');
    $routes->get('get_email_templates/(:any)', 'admin\EmailController::get_email_templates/$1');
    $routes->get('get_all_email_templates', 'admin\EmailController::get_all_email_templates');
    $routes->post('get_all_users_filtered', 'admin\EmailController::get_all_users_filtered');
    $routes->post('get_preview_email', 'admin\EmailController::get_preview_email');
    $routes->post('getAllEmailLogs/(:any)', 'admin\EmailController::getAllEmailLogs/$1');
    $routes->post('getGroupEmailLogs', 'admin\EmailController::getGroupEmailLogs');
    $routes->post('save_email_template', 'admin\EmailController::save_email_template');

    $routes->get('excelExport/(:any)', 'ExcelController::export/$1'); //Excel
    $routes->get('exportSample', 'ExcelController::exportSample/$1'); //Excel

    $routes->post('importReviewers', 'admin\UserManagerController::importReviewers');
    $routes->post('user/create_user', 'admin\UserManagerController::createUser');
    $routes->post('user/update_user', 'admin\UserManagerController::updateUser');

//    $routes->get('getAllAbstracts', 'admin\Abstracts\AbstractController::getAllAbstracts');

    $routes->get('report/all_abstract_data', 'admin\Reports::all_abstract_data');
    $routes->get('report/printAll', 'admin\Reports::printAll');

    $routes->get('scheduler', 'admin\Abstracts\SchedulerController::index/$1');
    $routes->get('addSchedule', 'admin\Abstracts\SchedulerController::add/$1');
    $routes->post('scheduler/getAllPapers', 'admin\Abstracts\AbstractController::getAllPapers');
    $routes->post('scheduler/getAllRooms', 'admin\Abstracts\AbstractController::getAllRooms');
    $routes->post('scheduler/getAllSessionChair', 'admin\Abstracts\AbstractController::getAllSessionChair');
    $routes->post('scheduler/getAllPaperType', 'admin\Abstracts\AbstractController::getAllPaperType');
    $routes->post('scheduler/getSchedulerAllowedDate', 'admin\Abstracts\SchedulerController::getSchedulerAllowedDate');

    $routes->post('scheduler/create', 'admin\Abstracts\SchedulerController::create');
    $routes->get('scheduler/get', 'admin\Abstracts\SchedulerController::get');
    $routes->get('scheduler/get_one/(:any)', 'admin\Abstracts\SchedulerController::get_one/$1');
    $routes->get('scheduler/get_one_json/(:any)', 'admin\Abstracts\SchedulerController::get_one_json/$1');
    $routes->get('scheduler/render_talks/(:any)', 'admin\Abstracts\SchedulerController::render_talks/$1');
    $routes->get('scheduler/get_scheduled_events/(:any)', 'admin\Abstracts\SchedulerController::get_scheduled_events/$1');
    $routes->get('scheduler/get_paper/(:any)', 'admin\Abstracts\SchedulerController::get_paper/$1');
    $routes->post('scheduler/move', 'admin\Abstracts\SchedulerController::move');
    $routes->post('scheduler/delete/(:any)', 'admin\Abstracts\SchedulerController::delete/$1');

    $routes->post('talks/create', 'admin\Abstracts\SessionTalksController::create');
    $routes->get('talks', 'admin\Abstracts\SessionTalksController::get');
    $routes->get('talks/scheduled/(:any)', 'admin\Abstracts\SessionTalksController::talk_scheduled/$1');



});

$routes->group('reviewer', function ($routes) {

    $routes->get('login', 'reviewer\ReviewerLogin::index');
    $routes->get('logout', 'reviewer\ReviewerLogin::logout');
    $routes->post('authenticate', 'reviewer\ReviewerLogin::authenticate');
    $routes->get('abstract_list', 'reviewer\ReviewerController::index');
    $routes->post('getAllReviewerAbstracts', 'reviewer\ReviewerController::getAllReviewerAbstracts');
    $routes->get('reviewAbstract/(:any)', 'reviewer\ReviewerController::reviewAbstract/$1');
    $routes->post('addReviewData', 'reviewer\ReviewerController::addReviewData');
    $routes->post('uploadReviewerFile', 'reviewer\ReviewerController::uploadReviewerFile');
    $routes->get('getAbstractReview/(:any)', 'reviewer\ReviewerController::getAbstractReview/$1');
    $routes->get('getNextReviewAbstract/(:any)', 'reviewer\ReviewerController::getNextReviewAbstract/$1');
    $routes->post('declineReviewerAbstract', 'reviewer\ReviewerController::declineReviewerAbstract');
    $routes->post('checkAbstractReviewsCount', 'reviewer\ReviewerController::checkAbstractReviewsCount');

});

$routes->group('deputy', function ($routes) {

    $routes->get('login', 'deputy\ReviewerLogin::index');
    $routes->get('logout', 'deputy\ReviewerLogin::logout');
    $routes->post('authenticate', 'deputy\ReviewerLogin::authenticate');
    $routes->get('menu', 'deputy\ReviewerController::index');
    $routes->get('papers_list', 'deputy\ReviewerController::papers_list');
    $routes->get('panels_list', 'deputy\ReviewerController::panels_list');
    $routes->get('reviewers_and_progress', 'deputy\ReviewerController::reviewers_and_progress');
    $routes->post('getAllReviewerPapers', 'deputy\ReviewerController::getAllReviewerPapers');
    $routes->post('getAllReviewerPanels', 'deputy\ReviewerController::getAllReviewerPanels');

    $routes->post('getAllDeputyReviewerPapersByDivision', 'deputy\ReviewerController::getAllDeputyReviewerPapersByDivision');
    $routes->post('getAllDeputyReviewerPanelsByDivision', 'deputy\ReviewerController::getAllDeputyReviewerPanelsByDivision');

    $routes->get('reviewAbstract/(:any)', 'deputy\ReviewerController::reviewAbstract/$1');
    $routes->post('addReviewData', 'deputy\ReviewerController::addReviewData');
    $routes->get('getAbstractReview/(:any)', 'deputy\ReviewerController::getAbstractReview/$1');
    $routes->get('getNextReviewAbstract/(:any)', 'deputy\ReviewerController::getNextReviewAbstract/$1');

    $routes->post('getRegularReviewersByDivision', 'deputy\ReviewerController::getRegularReviewersByDivision');
    $routes->post('assignPaperToRegularReviewer', 'deputy\ReviewerController::assignPaperToRegularReviewer');
    $routes->post('addPaperUploadViews', 'deputy\ReviewerController::addPaperUploadViews');
    $routes->get('review_details/(:any)', 'deputy\ReviewerController::review_details/$1');
    $routes->post('acceptance', 'deputy\ReviewerController::acceptance');
    $routes->get('allPaperDetails/(:any)', 'deputy\ReviewerController::allPaperDetails/$1');
    $routes->post('updatePaperSuitableStatus', 'deputy\ReviewerController::updatePaperSuitableStatus');
    $routes->get('getAcceptanceDetails/(:any)', 'deputy\ReviewerController::getAcceptanceDetails/$1');
    $routes->post('updateReviewerComments', 'deputy\ReviewerController::updateReviewerComments');
    $routes->post('displayReview', 'deputy\ReviewerController::displayReview');
    $routes->post('saveDeadline', 'deputy\ReviewerController::saveDeadline');
    $routes->post('sendReReviewEmailToReviewers', 'deputy\ReviewerController::sendReReviewEmailToReviewers');
    $routes->post('emailDeadlineToSubmitter', 'deputy\ReviewerController::emailDeadlineToSubmitter');

    $routes->get('logout', 'deputy\ReviewerLogin::logout');
});


$routes->group('acceptance', function ($routes) {

    $routes->get('login', 'acceptance\AcceptanceLogin::index');
    $routes->get('logout', 'acceptance\AcceptanceLogin::logout');
    $routes->post('authenticate', 'acceptance\AcceptanceLogin::authenticate');
    $routes->get('abstract_list', 'acceptance\AcceptanceController::index');
    $routes->post('get_accepted_abstracts', 'acceptance\AcceptanceController::get_accepted_abstracts');
    $routes->get('acceptance_menu/(:any)', 'acceptance\AcceptanceController::acceptance_menu/$1');
    $routes->get('speaker_acceptance/(:any)', 'acceptance\AcceptanceController::speaker_acceptance/$1');
    $routes->get('speaker_acceptance_finalize/(:any)', 'acceptance\AcceptanceController::speaker_acceptance_finalize/$1');
    $routes->get('presentation_data_view/(:any)', 'acceptance\AcceptanceController::presentation_data_view/$1');
    $routes->post('save_finalized_acceptance/(:any)', 'acceptance\AcceptanceController::save_finalized_acceptance/$1');

    $routes->post('save_acceptance_confirmation', 'acceptance\AcceptanceController::save_acceptance_confirmation');
    $routes->post('send_acceptance_confirmation/(:any)', 'acceptance\AcceptanceController::send_acceptance_confirmation');
    $routes->get('acceptance_agree', 'acceptance\AcceptanceController::acceptance_agree');

    $routes->get('breakfast_attendance/(:any)', 'acceptance\AcceptanceController::breakfast_attendance/$1');
    $routes->get('biography/(:any)', 'acceptance\AcceptanceController::biography/$1');
    $routes->post('update_acceptance', 'acceptance\AcceptanceController::update_acceptance');

    $routes->get('moderator/get/(:any)', 'acceptance\ModeratorAcceptanceController::get/$1');
    $routes->get('moderator/acceptance/(:any)', 'acceptance\ModeratorAcceptanceController::acceptance/$1');
    $routes->get('moderator/schedules', 'acceptance\ModeratorAcceptanceController::schedules');
    $routes->post('moderator/save', 'acceptance\ModeratorAcceptanceController::save');
    $routes->get('moderator/breakfast_attendance/(:any)', 'acceptance\ModeratorAcceptanceController::breakfast_attendance/$1');
    $routes->post('moderator/update', 'acceptance\ModeratorAcceptanceController::update_acceptance');
    $routes->get('moderator/session_details/(:any)', 'acceptance\ModeratorAcceptanceController::session_details/$1');
    $routes->get('moderator/finalize/(:any)', 'acceptance\ModeratorAcceptanceController::finalize/$1');
    $routes->post('moderator/check_finalize_acceptance/(:any)', 'acceptance\ModeratorAcceptanceController::check_finalize_acceptance/$1');
    $routes->get('moderator/acceptance_menu/(:any)', 'acceptance\ModeratorAcceptanceController::acceptance_menu/$1');
    $routes->get('moderator/acceptance_data/(:any)', 'acceptance\ModeratorAcceptanceController::acceptance_data/$1');


//    $routes->get('curriculumVitaeUpload/(:any)', 'acceptance\AcceptanceController::curriculumVitaeUpload');
//    $routes->post('curriculumVitaeDoUpload', 'acceptance\AcceptanceController::curriculumVitaeDoUpload');
//    $routes->get('acceptance_disclosure/(:any)', 'acceptance\AcceptanceController::acceptance_disclosure');
//    $routes->post('saveAcceptanceDisclosure/(:any)', 'acceptance\AcceptanceController::saveAcceptanceDisclosure');
    $routes->get('presentation_upload/(:any)', 'acceptance\AcceptanceController::presentation_upload/$1');
    $routes->post('presentation_do_upload', 'acceptance\AcceptanceController::presentation_do_upload');
    $routes->post('presentation_upload_delete', 'acceptance\AcceptanceController::presentation_upload_delete');

    $routes->post('check_finalize_acceptance/(:any)', 'acceptance\AcceptanceController::check_finalize_acceptance/$1');
    // for testing

    // $routes->get('getAcceptedAbstracts', 'acceptance\AcceptanceController::getAcceptedAbstracts/$1');
    $routes->post('getAuthorAcceptance/(:any)', 'acceptance\AcceptanceController::getAuthorAcceptance/$1');
});


$routes->group('schedules', function ($routes) {
    $routes->get('/', 'ItineraryController::/index');
});



$routes->group('logs', function ($routes)
{
    $routes->post('add_seen_logs', 'LogsController::add_seen_logs');
    $routes->post('get_logs', 'LogsController::getLogs');
});

//Other event routes
$routes->get('login', 'Login::index');
$routes->get('login/backdoor', 'Login::backdoor/$1');
$routes->get('logout', 'Login::logout/$1');
$routes->get('account', 'Account::index/$1');
$routes->post('register', 'Account::register');
$routes->post('user/send_support_mail', 'User::send_support_mail');
$routes->post('account/reset_password', 'Account::reset_password');

$routes->get('tracksJson', 'SessionTracks::getJson');

$routes->group('',['filter' => 'authGuard'], function ($routes)
{

    $routes->post('account/update_password', 'Account::update_password/$1');
    
    $routes->get('user/papers_submission', 'User::papers_submission');
    $routes->get('user/edit_papers_submission/(:any)', 'User::edit_papers_submission/$1');

    $routes->get('user/authors_and_copyright/(:any)', 'User::authors_and_copyright/$1');

    $routes->get('user/submission_menu/(:any)', 'User::submission_menu/$1');
    $routes->get('user', 'User::index/$1');
    $routes->get('user/finalize_paper/(:any)', 'User::finalize_paper/$1');
    $routes->post('user/save_finalize_paper', 'User::save_finalize_paper');

    $routes->post('user/search_author_ajax', 'User::search_author_ajax');
    $routes->post('user/get_paper_authors', 'User::get_paper_authors');
    $routes->post('user/submit_paper_ajax', 'User::submit_paper_ajax/$1');
    $routes->post('user/update_paper_ajax', 'User::update_paper_ajax');
    $routes->post('user/assign_abstract_author', 'User::assign_abstract_author');
    $routes->post('user/add_author_ajax', 'User::add_author_ajax/$1');
    $routes->get('user/update_author_details', 'User::update_author_details');
    $routes->post('user/update_paper_authors', 'User::update_paper_authors/$1');
    $routes->post('user/get_institution', 'User::get_institution');
    $routes->post('user/add_new_institution', 'User::add_new_institution/$1');
    $routes->post('user/resend_disclosure_email', 'User::resend_disclosure_email/$1');
    $routes->post('user/get_author_info', 'User::get_author_info/$1');

    $routes->post('user/remove_paper_author', 'User::remove_paper_author/$1');
    $routes->post('user/get', 'User::get/$1');
    $routes->post('user/get_user_info', 'User::get_user_info');
    $routes->post('user/update_user_info', 'User::update_user_info/$1');
    $routes->post('user/quick_add_author', 'User::quick_add_author');
    $routes->post('user/uploadHeadShot', 'User::uploadHeadShot/$1');
    $routes->post('user/update_abstract_permission', 'User::update_abstract_permission/$1');



    $routes->get('user/presentation_upload/(:num)', 'User::presentation_upload/$1');
    $routes->post('user/presentation_do_upload/(:num)', 'User::presentation_do_upload/$1');
    $routes->post('user/getPaperUploads', 'User::getPaperUploads');

    $routes->post('user/saveCommentToUpload', 'User::saveCommentToUpload');
//    $routes->get('image_upload/view/(:any)', 'Image_upload::view/$1');
//    $routes->get('image_upload/get_image', 'Image_upload::get_image/$1');
//
//    $routes->post('image_upload/get', 'Image_upload::get/$1');
//    $routes->post('image_upload/abstract_file_upload', 'Image_upload::abstract_file_upload/$1');
//    $routes->post('image_upload/submit', 'Image_upload::submit/$1');
//    $routes->post('image_upload/delete_uploaded', 'Image_upload::delete_uploaded/$1');
//    $routes->post('image_upload/no_upload', 'Image_upload::no_upload/$1');

    $routes->post('locations/get_country_states', 'Locations::get_country_states/$1');
    $routes->post('locations/get_countries', 'Locations::get_countries/$1');
    $routes->post('locations/get_state_cities', 'Locations::get_state_cities/$1');
    $routes->post('locations/get_all_cities', 'Locations::get_all_cities/$1');

    $routes->post('disclosures/cv_upload', 'User::cv_upload/$1');
    $routes->get('disclosures[/(:any)]', 'Disclosures::index/$1');
    $routes->get('disclosures/(:any)', 'Disclosures::index/$1');
    $routes->get('disclosures', 'Disclosures::index/$1');

    $routes->get('disclosure', 'Disclosure::index/$1');

    $routes->get('fda/(:any)', 'FDADisclosure::view_fda/$1');
    $routes->post('save_fda_disclosure', 'FDADisclosure::save_fda_disclosure/$1');

    $routes->get('permissions/(:any)', 'User::view_permissions/$1');

    $routes->get('author/login', 'Author::validateLogin');
    $routes->get('author/view_copyright', 'Author::view_copyright/$1');
    $routes->get('author/profile', 'Author::profile/$1');
    $routes->get('author/copyright_of_publication_agreement/(:num)', 'Author::copyright_of_publication_agreement/$1');
    $routes->get('author/review', 'Author::conflict_of_interest_disclosure_review/$1');
    $routes->get('author/finalize', 'Author::finalize_disclosure/$1');
    $routes->get('author/finalize_success', 'Author::finalize_success/$1');
    $routes->post('author/confirm_copyright_ajax', 'Author::confirm_copyright_ajax');

    //Event landing page

    //Panels

    $routes->get('user/panel_coordinators/(:any)', 'User::panel_coordinators/$1');
    $routes->get('user/panel_coordinators', 'User::panel_coordinators');
    $routes->post('user/edit_assigned_panel_coordinators/(:any)', 'User::edit_assigned_panel_coordinators/$1');
    $routes->post('user/assign_panel_coordinators', 'User::assign_panel_coordinators');
    $routes->post('user/edit_panel_coordinators/(:any)', 'User::panel_submission_menu/$1');

    $routes->get('user/panel_submission', 'User::panel_submission/');
    $routes->get('user/panel_menu/(:any)', 'User::panel_menu/$1');
    $routes->get('user/panel_submission_menu/(:any)', 'User::panel_submission_menu/$1');
    $routes->get('user/panel_overview/(:any)', 'User::panel_overview/$1');
    $routes->post('user/update_panelist_submitted', 'User::update_panelist_submitted');

    $routes->get('user/panelist/(:any)', 'User::panelist/$1');
    $routes->post('user/assign_panelist', 'User::assign_panelist');

    $routes->get('user/finalize_panel/(:num)', 'User::finalize_panel/$1');
    $routes->post('user/save_finalize_panel', 'User::save_finalize_panel');
});

$routes->get('author', 'Author::index');
$routes->get('phpInfo', 'PhpInfo::index');

$routes->post('login', 'Login::index');
//$routes->post('login', 'Login::validateLogin');
//$routes->post('login/validateLogin', 'Login::validateLogin');
$routes->get('home', 'Home::index');
$routes->get('admin', 'admin\Abstracts\AdminLogin::index');
$routes->get('reviewer', 'reviewer\ReviewerLogin::index');
$routes->get('deputy', 'deputy\ReviewerLogin::index');
$routes->get('reviewers_instruction_pdf', 'PDFController::show_pdf/');
$routes->get('acceptance', 'acceptance\AcceptanceLogin::index');
//Event landing page
$routes->get('submission_guidelines', 'Event::submissionGuidelines');
$routes->get('afs', 'Event::index');
$routes->get('/', 'Event::index');

$routes->get('testMail', 'User::testMail');

$routes->get('(.+)', 'BaseController::show404');
/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . 'Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . 'Routes.php';
}
