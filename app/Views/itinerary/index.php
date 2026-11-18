<?php

use App\Models\DesignationsModel;

?>
<style>
    .nav-link {
        height: 120px;
        font-family: 'Calibri', Helvetica Neue, Helvetica, sans-serif;
        font-size: 17px;
        border-bottom-right-radius : 4px;
        border-bottom-left-radius : 4px;
    }
    .nav-link.active{
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important

    }

    .eventDiv {
        width: 900px;
    }

    .scheduleTab .schedule-day {
        color: #805000;
    }

    .color-yellow{
        color: #805000;
    }

    .scheduleTab p:first-child:active {
        color: white;
    }

    .scheduleTabNav .nav-link:active {
        color: black;
    }

    #main-banner {
        width: 100% !important;
        object-fit: cover;
        mix-blend-mode: multiply;
    }

    .card {
        margin-bottom: 20px;
    }

    .glass-container{
        background: rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .text-primary{
        color: #0549a0 !important;
    }

    .scheduleContent .text-success,
    .scheduleContent .schedule-time {
        color: #0f5132 !important;
    }

    .bg-primary{
        background-color: #0549a0 !important;
        color: white
    }
    .bg-primary:after{
        color: white;
        border-radius: 3px;
        background-color: white;
    }

</style>

<div class="row mt-5">
    <div class="col-md-12 text-center mt-md-4">
        <img id="main-banner" src="<?= base_url('main_banner.png') ?>" class="figure-img" alt="Main Banner">
    </div>
    <hr aria-hidden="true">
</div>

<main id="main-content" class="light-white" tabindex="-1">
    <h1 class="visually-hidden">IMAST 2027 Schedule</h1>
    <div class="container shadow-lg glass-container">
        <nav aria-label="Schedule by day">
            <div class="nav nav-tabs scheduleTabNav" id="nav-tab" role="tablist">
                <div class="row g-3 text-center m-auto eventDiv sha" role="presentation">
                    <?php if (!empty($schedules)): ?>
                        <?php foreach ($schedules as $scheduleIndex => $schedule): ?>
                            <div class="col" role="presentation">
                                <button type="button" class="nav-link text-primary<?= $scheduleIndex === 0 ? ' active' : '' ?>"
                                        id="nav-<?= htmlspecialchars($schedule['id'], ENT_QUOTES, 'UTF-8') ?>"
                                        data-bs-toggle="tab"
                                        data-bs-target="#tab-<?= htmlspecialchars($schedule['id'], ENT_QUOTES, 'UTF-8') ?>"
                                        role="tab"
                                        aria-controls="tab-<?= htmlspecialchars($schedule['id'], ENT_QUOTES, 'UTF-8') ?>"
                                        aria-selected="<?= $scheduleIndex === 0 ? 'true' : 'false' ?>"
                                        tabindex="<?= $scheduleIndex === 0 ? '0' : '-1' ?>">
                                    <span class="scheduleTab d-block">
                                        <span class="schedule-day fw-bolder h3 d-block"><?= date('l', strtotime($schedule['date'])) ?></span>
                                        <span class="fw-bolder d-block"><?= htmlspecialchars($schedule['date'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </span>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </nav>

        <div class="tab-content scheduleContent" id="nav-tabContent">
            <?php if (!empty($schedules)): ?>
                <?php foreach ($schedules as $scheduleIndex => $schedule): ?>
                    <div class="tab-pane fade<?= $scheduleIndex === 0 ? ' show active' : '' ?>"
                         id="tab-<?= htmlspecialchars($schedule['id'], ENT_QUOTES, 'UTF-8') ?>"
                         role="tabpanel"
                         aria-labelledby="nav-<?= htmlspecialchars($schedule['id'], ENT_QUOTES, 'UTF-8') ?>"
                         tabindex="0">

                        <div class="card col mb-3 p-3 text-center shadow">
                            <div class="scheduleTab">
                                <h2 class="fw-bolder text-primary mt-4"><?= htmlspecialchars(date('l, M. d, Y', strtotime($schedule['date'])), ENT_QUOTES, 'UTF-8') ?></h2>
                                <p class="fw-bolder">34th Scoliosis Research Society (SRS) <br>
                                    International Meeting on Advanced Spine Techniques (IMAST) <br>
                                    Copenhagen, Denmark,
                                    April 7-9, 2027</p>
                            </div>
                        </div>

                        <?php if(!empty($schedule['events'] )): foreach ($schedule['events'] as $event): ?>
                            <div class="card text-start shadow">
                                <div class="card-header bg-none">  <hr aria-hidden="true" style="height: 3px; border-width: 0; color: #805000; background-color: #805000; max-width: 130px; opacity:100%"/>
                                    <h3 class="fw-bolder text-primary"><?= htmlspecialchars(ucfirst($event['session_title']), ENT_QUOTES, 'UTF-8') ?> <span class="float-end fw-normal" style="font-size: 16px" ><?=$event['track']['name'] ?? ''?></span></h3>
                                    <p class="schedule-time fw-bolder fs-5">
                                        <?= htmlspecialchars(date('h:i a', strtotime($event['session_start_time'])), ENT_QUOTES, 'UTF-8') ?> -
                                        <?= htmlspecialchars(date('h:i a', strtotime($event['session_end_time'])), ENT_QUOTES, 'UTF-8') ?>  CEST
                                    </p>
                                    <?php if(!empty($event['content_tag_name'])):?>
                                    <div class="fw-bolder text-success p-2 border-success border alert alert-success d-inline-block" style="font-size: 14px">
                                        <?= htmlspecialchars($event['content_tag_name'], ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                    <?php endif;?>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="chairDiv">

                                                <?php if (!empty($event['moderators'])): ?>
                                                    <h4 class="text-primary fw-bolder">Session Moderator(s):</h4>
                                                    <?php foreach ($event['moderators'] as $moderator): ?>
                                                        <?php
                                                        $moderatorDesignationText = '';
                                                        if (!empty($moderator['designation']) && is_array($moderator['designation'])) {
                                                            $moderatorDesignationText = implode(', ', array_filter($moderator['designation']));
                                                        }
                                                        ?>
                                                        <div>
                                                            <span class="fw-bolder"><?= htmlspecialchars($moderator['name'] . ' '. ($moderator['middle_name'] ? $moderator['middle_name'] . '. ' : '') . $moderator['surname'], ENT_QUOTES, 'UTF-8') ?><?= $moderatorDesignationText !== '' ? ', ' . htmlspecialchars($moderatorDesignationText, ENT_QUOTES, 'UTF-8') : '' ?>
                                                            </span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                                <?php if (!empty($event['table_moderators'])): ?>
                                                    <h4 class="text-primary fw-bolder mt-3">Table Moderator(s):</h4>
                                                    <?php foreach ($event['table_moderators'] as $tableModerator): ?>
                                                        <?php
                                                        $tableModeratorDesignationText = '';
                                                        if (!empty($tableModerator['designation']) && is_array($tableModerator['designation'])) {
                                                            $tableModeratorDesignationText = implode(', ', array_filter($tableModerator['designation']));
                                                        }
                                                        ?>
                                                        <div>
                                                            <span class="fw-bolder"><?= htmlspecialchars($tableModerator['name'] . ' ' . (!empty($tableModerator['middle_name']) ? $tableModerator['middle_name'] . '. ' : '') . $tableModerator['surname'], ENT_QUOTES, 'UTF-8') ?><?= $tableModeratorDesignationText !== '' ? ', ' . htmlspecialchars($tableModeratorDesignationText, ENT_QUOTES, 'UTF-8') : '' ?></span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="col-9" >
                                            <div class="accordion border-0 talkCollapseItem" id="scheduleAccordion-<?=$event['id']?>" >
                                                <div class="accordion-item border-0">
                                                    <?php if (!empty($event['talks'])): ?>
                                                        <h4 class="accordion-header">
                                                            <button id="talkToggle-<?= htmlspecialchars($event['id'], ENT_QUOTES, 'UTF-8') ?>" class="btn-outline-primary btn-sm accordion-button collapsed fw-bolder" type="button" data-bs-toggle="collapse" data-bs-target="#talkCollapse-<?= htmlspecialchars($event['id'], ENT_QUOTES, 'UTF-8') ?>" aria-controls="talkCollapse-<?= htmlspecialchars($event['id'], ENT_QUOTES, 'UTF-8') ?>" aria-expanded="false">
                                                                <span class="me-2 toggle-icon" aria-hidden="true">+</span> View Full Session Schedule
                                                            </button>
                                                        </h4>
                                                        <div id="talkCollapse-<?= htmlspecialchars($event['id'], ENT_QUOTES, 'UTF-8') ?>" class="accordion-collapse collapse" aria-labelledby="talkToggle-<?= htmlspecialchars($event['id'], ENT_QUOTES, 'UTF-8') ?>">
                                                        <div class="accordion-body p-0">
                                                            <?php if (!empty($event['talks'])): ?>
                                                                <table class="table table-bordered">
                                                                    <caption class="visually-hidden">Session schedule and presenters</caption>
                                                                    <thead class="visually-hidden">
                                                                        <tr><th scope="col">Time</th><th scope="col">Session details</th></tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <?php foreach ($event['talks'] as $talk): ?>
                                                                        <?php
                                                                        $isHeading = strip_tags($talk['item_type']) === 'heading';
                                                                        $hasAbstract = !empty($talk['abstract_id']);
                                                                        ?>

                                                                        <tr class="table-secondary">
                                                                            <?php if (!$isHeading): ?>
                                                                                <td class="text-nowrap" style="width: 80px; vertical-align: top;">
                                                                                    <?= htmlspecialchars(date('h:i a', strtotime($talk['time_start'])), ENT_QUOTES, 'UTF-8') ?>
                                                                                    -
                                                                                    <?= htmlspecialchars(date('h:i a', strtotime($talk['time_end'])), ENT_QUOTES, 'UTF-8') ?>
                                                                                </td>
                                                                                <td style="vertical-align: top;">
                                                                                    <span class="fw-bolder">
                                                                                        <?php if ($hasAbstract): ?>
                                                                                            <?= strip_tags($talk['title']) ?>
                                                                                        <?php else: ?>
                                                                                            <?= strip_tags($talk['custom_abstract_desc']) ?>
                                                                                        <?php endif; ?>
                                                                                    </span>
                                                                                </td>
                                                                            <?php else: ?>
                                                                                <th scope="row" colspan="2" class="fw-bolder text-primary">
                                                                                    <?= strip_tags($talk['item_title']) ?>
                                                                                </th>
                                                                            <?php endif; ?>
                                                                        </tr>
                                                                        <?php if (!empty($talk['presenters']) || !empty($talk['panelist'])): ?>
                                                                            <tr style="background-color:white !important;">
                                                                                <td colspan="2" class="p-0">
                                                                                    <ul class="ps-0 list-group">
                                                                                        <?php if ($talk['submission_type'] === 'panel' && !empty($talk['panelist'])): ?>
                                                                                            <li class="list-group-item " style="list-style: none;">
                                                                                                <span class="fw-bolder"><?= htmlspecialchars($talk['paper_sub']['custom_id'], ENT_QUOTES, 'UTF-8') ?></span>
                                                                                                <?= $talk['paper_sub']['individual_panel_title']
                                                                                                        ? '<span class="fw-bolder">' . strip_tags($talk['paper_sub']['individual_panel_title']) . '</span><br>'
                                                                                                        : '' ?>
                                                                                                <?= trim($talk['panelist']['user_name'] . ' ' . $talk['panelist']['user_surname']) ?>
                                                                                                <?= !empty($talk['panelist']['deg']) ? ', ' . htmlspecialchars($talk['panelist']['deg'], ENT_QUOTES, 'UTF-8') : '' ?>
                                                                                            </li>
                                                                                        <?php elseif (!empty($talk['presenters'])): ?>
                                                                                            <?php foreach ($talk['presenters'] as $presenter): ?>
                                                                                                <!--                                                                                                       Designations-->
                                                                                                <?php
                                                                                                $presenterDesignationText = '';
                                                                                                if (!empty($presenter['details']['designation']) && is_array($presenter['details']['designation'])) {
                                                                                                    $presenterDesignationText = implode(', ', array_filter($presenter['details']['designation']));
                                                                                                }
                                                                                                ?>

                                                                                                <li class="list-group-item" style="list-style: none;">
                                                                                                    <?= trim($presenter['user_name'] . ' ' .$presenter['middle_name']. ' ' . $presenter['user_surname']). (!empty($presenterDesignationText) ? ', ' . htmlspecialchars($presenterDesignationText, ENT_QUOTES, 'UTF-8') : '') ?>
                                                                                                </li>
                                                                                            <?php endforeach; ?>
                                                                                        <?php endif; ?>
                                                                                    </ul>
                                                                                </td>
                                                                            </tr>
                                                                        <?php endif; ?>
                                                                    <?php endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <?php endif; ?>

                                                </div>

                                                <?php if($event['description']):?>
                                                    <div class="accordion-item border-0">
                                                        <h4 class="accordion-header">
                                                            <button id="descriptionToggle-<?= htmlspecialchars($event['id'], ENT_QUOTES, 'UTF-8') ?>" class="btn-primary btn-sm accordion-button collapsed fw-bolder" type="button" data-bs-toggle="collapse" data-bs-target="#descriptionCollapse-<?= htmlspecialchars($event['id'], ENT_QUOTES, 'UTF-8') ?>" aria-controls="descriptionCollapse-<?= htmlspecialchars($event['id'], ENT_QUOTES, 'UTF-8') ?>" aria-expanded="false">
                                                                <span class="me-2 toggle-icon" aria-hidden="true">+</span> Session Description
                                                            </button>
                                                        </h4>
                                                        <div id="descriptionCollapse-<?= htmlspecialchars($event['id'], ENT_QUOTES, 'UTF-8') ?>" class="accordion-collapse collapse" aria-labelledby="descriptionToggle-<?= htmlspecialchars($event['id'], ENT_QUOTES, 'UTF-8') ?>">
                                                            <div class="accordion-body p-0">
                                                                <?= htmlspecialchars($event['description'], ENT_QUOTES, 'UTF-8') ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif ?>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.accordion-button').forEach(button => {
            button.addEventListener('click', function () {
                const icon = this.querySelector('.toggle-icon');
                icon.textContent = this.classList.contains('collapsed') ? "+" : "-";
            });
        });

    });
</script>
