<?php
namespace App\Services;

use App\Models\AbstractCategoriesModel;
use App\Models\AbstractSubCategoriesModel;
use App\Models\AdminAbstractCommentModel;
use App\Models\AdminAcceptanceModel;
use App\Models\AuthorAcceptanceModel;
use App\Models\CMEReviewersModel;
use App\Models\CMEReviewsModel;
use App\Models\DesignationsModel;
use App\Models\DivisionsModel;
use App\Models\EmailLogsModel;
use App\Models\EmailTemplatesModel;
use App\Models\InstitutionModel;
use App\Models\LogsModel;
use App\Models\PaperAuthorsModel;
use App\Models\PapersDeputyAcceptanceModel;
use App\Models\PapersModel;
use App\Models\PaperTypeModel;
use App\Models\PaperUploadsModel;
use App\Models\ReviewerPaperUploadsModel;
use App\Models\SiteSettingModel;
use App\Models\UserModel;
use App\Models\UsersProfileModel;
use CodeIgniter\Config\BaseService;
use CodeIgniter\Database\Config;
use CodeIgniter\HTTP\RequestInterface;
use Config\Services;

class CoreServices extends BaseService
{
    protected RequestInterface $request;
    protected PaperAuthorsModel $paperAuthorsModel;
    protected PapersModel $papersModel;
    protected PaperTypeModel $paperTypeModel;
    protected DivisionsModel $divisionsModel;
    protected AbstractCategoriesModel $abstractCategoriesModel;
    protected AbstractSubCategoriesModel $abstractSubCategoriesModel;
    protected AdminAbstractCommentModel $adminAbstractCommentModel;
    protected AdminAcceptanceModel $adminAcceptanceModel;
    protected AuthorAcceptanceModel $authorAcceptanceModel;
    protected DesignationsModel $designationsModel;
    protected EmailLogsModel $emailLogsModel;
    protected EmailTemplatesModel $emailTemplatesModel;
    protected LogsModel $logsModel;
    protected PapersDeputyAcceptanceModel $papersDeputyAcceptanceModel;
    protected PaperUploadsModel $paperUploadsModel;
    protected ReviewerPaperUploadsModel $reviewerPaperUploadsModel;
    protected CMEReviewersModel $CMEReviewersModel;
    protected CMEReviewsModel $CMEReviewsModel;
    protected SiteSettingModel $siteSettingModel;
    protected UserModel $userModel;
    protected UsersProfileModel $usersProfileModel;
    protected InstitutionModel $institutionModel;

    public function __construct()
    {
        $this->request = Services::request();
    }

    protected function initializeModels(): void
    {
        $this->paperAuthorsModel = new PaperAuthorsModel();
        $this->papersModel = new PapersModel();
        $this->paperTypeModel = new PaperTypeModel();
        $this->divisionsModel = new DivisionsModel();
        $this->abstractCategoriesModel = new AbstractCategoriesModel();
        $this->abstractSubCategoriesModel = new AbstractSubCategoriesModel();
        $this->adminAbstractCommentModel = new AdminAbstractCommentModel();
        $this->adminAcceptanceModel = new AdminAcceptanceModel();
        $this->authorAcceptanceModel = new AuthorAcceptanceModel();
        $this->designationsModel = new DesignationsModel();
        $this->emailLogsModel = new EmailLogsModel();
        $this->emailTemplatesModel = new EmailTemplatesModel();
        $this->logsModel = new LogsModel();
        $this->papersDeputyAcceptanceModel = new PapersDeputyAcceptanceModel();
        $this->paperUploadsModel = new PaperUploadsModel();
        $this->reviewerPaperUploadsModel = new ReviewerPaperUploadsModel();
        $this->siteSettingModel = new SiteSettingModel();
        $this->userModel = new UserModel();
        $this->usersProfileModel = new UsersProfileModel();
        $this->institutionModel = new InstitutionModel();
    }
}
