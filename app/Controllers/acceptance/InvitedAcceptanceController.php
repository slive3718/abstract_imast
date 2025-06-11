<?php

namespace App\Controllers\acceptance;
use App\Models\AdminAcceptanceModel;
use App\Models\PaperAuthorsModel;
use App\Models\RoomsModel;
use App\Models\SchedulerModel;
use App\Models\SchedulerSessionTalksModel;
use App\Models\UsersProfileModel;
use App\Models\UserModel;
use App\Models\PapersModel;
use App\Models\AuthorAcceptanceModel;
use App\Models\RemovedPaperAuthorModel;

use App\Controllers\admin\Abstracts\AbstractController;
class InvitedAcceptanceController extends AcceptanceController
{

    public function invited_speaker_acceptance($abstract_id)
    {
        if (!$this->validate_abstract_id($abstract_id))
            exit;

        $acceptanceDetails = (new AuthorAcceptanceModel())->where(['abstract_id' => $abstract_id, 'author_id' => session('user_id')])->asArray()->first();
        $header_data = [
            'title' => 'Invited Speaker/Faculty Acceptance '
        ];

        $abstract_schedule = (new SchedulerSessionTalksModel())
            ->where('abstract_id', $abstract_id)->first();

        if($abstract_schedule){
            $abstract_schedule['event'] = (new SchedulerModel())->find($abstract_schedule['scheduler_event_id']) ?? [];
            $abstract_schedule['room']  = (new RoomsModel())->find($abstract_schedule['event']['room_id']);
            foreach (json_decode($abstract_schedule['event']['session_chair_ids']) as &$moderator){
                $abstract_schedule['moderators'][] = (new UserModel())->find($moderator);
            }

        }

        $abstract_details = (new PapersModel())->find($abstract_id);

//        print_R($abstract_schedule);exit;

        $data = [
            'abstract_id' => $abstract_id,
            'acceptanceDetails' => $acceptanceDetails,
            'abstract_preference' => presentation_preferences(),
            'presentation_data_view' => $this->invited_presentation_data_view($abstract_id),
            'abstract_schedule' => $abstract_schedule,
            'abstract_details' => $abstract_details
        ];
        return
            view('acceptance/common/header', $header_data) .
            view('acceptance/invited/speaker_acceptance', $data) .
            view('acceptance/common/footer');
    }

    public function invited_speaker_travel_expense($abstract_id)
    {
        if (!$this->validate_abstract_id($abstract_id))
            exit;

        $acceptanceDetails = (new AuthorAcceptanceModel())->where(['abstract_id' => $abstract_id, 'author_id' => session('user_id')])->asArray()->first();
        $header_data = [
            'title' => 'Invited Speaker/Faculty Acceptance '
        ];
        $data = [
            'abstract_id' => $abstract_id,
            'acceptanceDetails' => $acceptanceDetails,
            'abstract_preference' => presentation_preferences(),
            'presentation_data_view' => $this->invited_presentation_data_view($abstract_id)
        ];
        return
            view('acceptance/common/header', $header_data) .
            view('acceptance/invited/travel_and_expense', $data) .
            view('acceptance/common/footer');
    }




    public function invited_acceptance_menu($abstract_id)
    {
        $removed_author = (new RemovedPaperAuthorModel())->get();

        $removed_author_ids = array();
        if (!empty($removed_author)) {
            foreach ($removed_author as $removed) {
                $removed_author_ids[] = $removed['paper_author_id'];
            }
        }

        $authorsQuery = (new PaperAuthorsModel());
            if(!empty($removed_author_ids)){
                $authorsQuery->whereNotIn('id', $removed_author_ids);
            }
            $authorsQuery->where('paper_id', $abstract_id)
                ->orderBy('author_order', 'asc')
                ->orderBy('date_time', 'asc');
           $authors = $authorsQuery->asArray()->findALl();

        foreach ($authors as $index => &$author) {
            $removed_author = (new RemovedPaperAuthorModel())->where('paper_author_id', $author['id'])->first();
            if ($removed_author == null) {
                $author['info'] = (new UserModel())->find($author['author_id']);
                $author['profile'] = (new UsersProfileModel())->where('author_id', $author['author_id'])->first();
            }
        }


        $abstract_details = (new PapersModel())->find($abstract_id);
        $author_acceptance = (new AuthorAcceptanceModel())->where(['abstract_id' => $abstract_id, 'author_id' => session('user_id')])->first();
        $abstract_preference = (new AdminAcceptanceModel())->where('abstract_id', $abstract_id)->first();
        $header_data = [
            'title' => 'Acceptance Finalize'
        ];
// print_R($abstract_details);exit;
        $header_data = [
            'title' => 'Acceptance Menu'
        ];

        $userProfile =  (new UsersProfileModel())->where('author_id', session('user_id'))->asObject()->first();
        $data = [
            'abstract_id' => $abstract_id,
            'author_acceptance' => $author_acceptance,
            'authors' => $authors,
            'abstract_details' => $abstract_details,
            'abstract_preference' => $abstract_preference,
            'presentation_data_view' => $this->invited_presentation_data_view($abstract_id),
            'userProfile'=>$userProfile
        ];


        return
            view('acceptance/common/header', $header_data) .
            view('acceptance/invited/acceptance_menu', $data) .
            view('acceptance/common/footer');
    }

    public function invited_presentation_data_view($abstract_id)
    {
//        print_R($abstract_id);exit;
        $removed_author = (new RemovedPaperAuthorModel())->get();
        $removed_paper_author_ids = array();
        if (!empty($removed_author)) {
            foreach ($removed_author as $removed) {
                $removed_paper_author_ids[] = $removed['paper_author_id'];
            }
        }

        $removed_paper_author_ids = array();
        if (!empty($removed_author)) {
            foreach ($removed_author as $removed) {
                $removed_paper_author_ids[] = $removed['paper_author_id'];
            }
        }

        // Get paper authors query
        $authorsQuery = (new PaperAuthorsModel())
            ->where('paper_id', $abstract_id)
            ->orderBy('author_order', 'asc')
            ->orderBy('date_time', 'asc');

        // Only add whereNotIn if there are removed authors
        if (!empty($removed_paper_author_ids)) {
            $authorsQuery->whereNotIn('id', $removed_paper_author_ids);
        }

        $authors = $authorsQuery->findAll();

        foreach ($authors as &$item) {
            $item['user'] = (new UserModel())->find($item['author_id']);
            $item['user']['profile'] = (new UsersProfileModel())->where('author_id', $item['author_id'])->first();
        }

        $abstract_details = (new PapersModel())->asArray()->find($abstract_id);
        $abstract_schedule = (new SchedulerSessionTalksModel())
            ->where('abstract_id', $abstract_id)->first();


//        print_R($abstract_schedule);exit;
        if ($abstract_schedule) {
            $abstract_schedule['event'] = (new SchedulerModel())->find($abstract_schedule['scheduler_event_id']) ?? [];
            $abstract_schedule['room'] = (new RoomsModel())->find($abstract_schedule['event']['room_id']);
        }

        $data = [
            'abstract_id' => $abstract_id,
            'abstract_details' => $abstract_details,
            'abstract_preference' => presentation_preferences(),
            'authors' => $authors,
            'abstract_schedule' => $abstract_schedule
        ];

        return view('acceptance/common/invited_presentation_details', $data);

    }

}