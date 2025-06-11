<style>
    .nav-link {
        height: 120px;
        font-family: 'Calibri', Helvetica Neue, Helvetica, sans-serif;
        font-size: 17px;
        border-bottom-right-radius : 4px;
        border-bottom-left-radius : 4px;
    }
    .card > .nav-link {
        height: 150px;
        font-family: 'Calibri', Helvetica Neue, Helvetica, sans-serif;
        font-size: 17px;
    }

    .nav-link.active{
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important

    }

    .eventDiv {
        width: 900px;
    }

    .scheduleTab p:first-child {
        color: #FBA61C;
    }

    .color-yellow{
        color: #FBA61C;
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
    <div class="col-md-12 text-center mt-md-4" style="width: 60%; margin:auto">
        <img id="main-banner" src="https://afs.owpm2.com/main_banner.webp" class="figure-img" alt="Main Banner">
    </div>
    <hr>
</div>

<main class="light-white">
    <div class="container shadow-lg glass-container">
        <nav>
            <div class="nav nav-tabs scheduleTabNav" id="nav-tab" role="tablist">
                <div class="row g-3 text-center m-auto eventDiv sha">
                    <?php if (!empty($schedules)): ?>
                        <?php foreach ($schedules as $schedule): ?>
                            <div class="col">
                                <div class="nav-link text-primary"
                                     id="nav-<?= htmlspecialchars($schedule['id'], ENT_QUOTES, 'UTF-8') ?>"
                                     data-bs-toggle="tab"
                                     data-bs-target="#tab-<?= htmlspecialchars($schedule['id'], ENT_QUOTES, 'UTF-8') ?>"
                                     role="tab"
                                     aria-controls="tab-<?= htmlspecialchars($schedule['id'], ENT_QUOTES, 'UTF-8') ?>">
                                    <div class="scheduleTab">
                                        <p class="fw-bolder">Annual Meeting</p>
                                        <p class="fw-bolder"><?= htmlspecialchars($schedule['date'], ENT_QUOTES, 'UTF-8') ?></p>

                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </nav>

        <div class="tab-content scheduleContent" id="nav-tabContent">
            <?php if (!empty($schedules)): ?>
                <?php foreach ($schedules as $schedule): ?>
                    <div class="tab-pane fade"
                         id="tab-<?= htmlspecialchars($schedule['id'], ENT_QUOTES, 'UTF-8') ?>"
                         role="tabpanel"
                         aria-labelledby="nav-<?= htmlspecialchars($schedule['id'], ENT_QUOTES, 'UTF-8') ?>">

                        <div class="card col mb-3 p-3 text-center shadow">
                            <div class="nav-link"
                                 id="nav-<?= htmlspecialchars($schedule['id'], ENT_QUOTES, 'UTF-8') ?>"
                                 data-bs-toggle="tab"
                                 data-bs-target="#tab-<?= htmlspecialchars($schedule['id'], ENT_QUOTES, 'UTF-8') ?>"
                                 role="tab"
                                 aria-controls="tab-<?= htmlspecialchars($schedule['id'], ENT_QUOTES, 'UTF-8') ?>">
                                <div class="scheduleTab ">
                                    <h3 class="fw-bolder text-primary mt-4"><?= htmlspecialchars(date('l, M. d, Y', strtotime($schedule['date'])), ENT_QUOTES, 'UTF-8') ?></h3>
                                    <h4 class="fw-bolder"> CASTEXPO 2025 / Presenting the WFO Technical Forum <br>
                                        Georgia World Congress Center Atlanta, GA</h4>
                                </div>
                            </div>
                        </div>

                        <?php foreach ($schedule['events'] as $event): ?>
                            <div class="card text-start shadow">
                                <div class="card-header bg-none">  <hr style="height: 3px; border-width: 0; color: #FBA61C; background-color: #FBA61C; max-width: 130px; opacity:100%"/>
                                    <h4 class="fw-bolder text-primary"><?= htmlspecialchars(ucfirst($event['session_title']), ENT_QUOTES, 'UTF-8') ?> <span class="float-end fw-normal" style="font-size: 16px" ><?=$event['track']['name'] ?? ''?></span></h4>
                                    <h5 class="text-success">
                                        <?= htmlspecialchars(date('h:i a', strtotime($event['session_start_time'])), ENT_QUOTES, 'UTF-8') ?> -
                                        <?= htmlspecialchars(date('h:i a', strtotime($event['session_end_time'])), ENT_QUOTES, 'UTF-8') ?>  EST |
                                        <?= htmlspecialchars($event['room_name'], ENT_QUOTES, 'UTF-8') ?>
                                    </h5>
                                </div>
                                <div class="card-body">

                                    <div class="row">
                                        <div class="col-3">
                                            <div class="chairDiv">

                                                <?php if (!empty($event['moderators'])): ?>
                                                    <h5 class="text-success fw-bolder">Session Moderator(s):</h5>
                                                    <?php foreach ($event['moderators'] as $moderator): ?>
                                                        <div>
                                                            <span class="fw-bolder"><?= htmlspecialchars($moderator['name'] . ' ' . $moderator['surname'], ENT_QUOTES, 'UTF-8') ?><?= $moderator['deg'] ? ', '.htmlspecialchars($moderator['deg'], ENT_QUOTES, 'UTF-8') : ''?>
                                                            </span>
                                                            <i><?= $moderator['institution'] ? '<br>'.htmlspecialchars($moderator['institution'], ENT_QUOTES, 'UTF-8') : ''?></i>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="col-9" >



                                            <div class="accordion border-0 talkCollapseItem" id="scheduleAccordion-<?=$event['id']?>" >
                                                <div class="accordion-item border-0">
                                                    <?php if (!empty($event['talks'])): ?>
                                                    <h2 class="accordion-header">
                                                        <button class="btn-outline-primary btn-sm accordion-button collapsed fw-bolder" type="button" data-bs-toggle="collapse" data-bs-target="#talkCollapse-<?= htmlspecialchars($event['id'], ENT_QUOTES, 'UTF-8') ?>" aria-expanded="false">
                                                            <span class="me-2 toggle-icon">+</span> View Full Session Schedule
                                                        </button>
                                                    </h2>
                                                    <?php endif ?>
                                                    <div id="talkCollapse-<?= htmlspecialchars($event['id'], ENT_QUOTES, 'UTF-8') ?>" class="accordion-collapse collapse">
                                                        <div class="accordion-body">
                                                            <?php if (!empty($event['talks'])): ?>
                                                                <table class="table table-striped table-bordered">
                                                                    <?php foreach ($event['talks'] as $talk): ?>
                                                                        <tr>
                                                                            <td class="text-nowrap" style="width:80px; vertical-align:top;">
                                                                                <?= htmlspecialchars(date('h:i a', strtotime($talk['time_start'])), ENT_QUOTES, 'UTF-8') ?> -
                                                                                <?= htmlspecialchars(date('h:i a', strtotime($talk['time_end'])), ENT_QUOTES, 'UTF-8') ?>
                                                                            </td>
                                                                            <td style="vertical-align:top;">
                                                                                <?php if ($talk['abstract_id']): ?>
                                                                                    <?= htmlspecialchars($talk['custom_id'], ENT_QUOTES, 'UTF-8') ?>
                                                                                    <span class="fw-bolder">
                                                                                        <?= strip_tags($talk['title']) ?>
                                                                                    </span>
                                                                                <?php else: ?>
                                                                                    <?= strip_tags($talk['custom_abstract_desc']) ?>
                                                                                <?php endif; ?>
                                                                            </td>
                                                                        </tr>
                                                                        <?php if (!empty($talk['presenters']) || !empty($talk['panelist'])): ?>
                                                                            <tr>
                                                                                <td>
                                                                                    <p class="fw-bolder">
                                                                                        <?= $talk['submission_type'] === 'panel' ? 'Panelist(s)' : 'Presenter(s)' ?>
                                                                                    </p>
                                                                                </td>
                                                                                <td class="p-0">
                                                                                    <ul class="ps-0 list-group">
                                                                                        <?php if ($talk['submission_type'] === 'panel' && !empty($talk['panelist'])): ?>
                                                                                            <li class="list-group-item" style="list-style: none;">
                                                                                                <span class="fw-bolder"><?= htmlspecialchars($talk['paper_sub']['custom_id'], ENT_QUOTES, 'UTF-8') ?></span>
                                                                                                <?= $talk['paper_sub']['individual_panel_title']
                                                                                                    ? '<span class="fw-bolder">' . strip_tags($talk['paper_sub']['individual_panel_title']) . '</span><br>'
                                                                                                    : '' ?>
                                                                                                <?= trim($talk['panelist']['user_name'] . ' ' . $talk['panelist']['user_surname']) ?>
                                                                                                <?= !empty($talk['panelist']['deg']) ? ', ' . htmlspecialchars($talk['panelist']['deg'], ENT_QUOTES, 'UTF-8') : '' ?>
                                                                                                <i><?= !empty($talk['panelist']['institution']) ? '<br>' . htmlspecialchars($talk['panelist']['institution'], ENT_QUOTES, 'UTF-8') : '' ?></i>
                                                                                                <?php if (!empty($event['talks'])): ?>
                                                                                                    <div class="accordion mt-2" id="accordionExample">
                                                                                                        <div class="accordion-item">
                                                                                                            <h6 class="accordion-header">
                                                                                                                <button class="accordion-button p-1 bg-primary text-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= htmlspecialchars($talk['paper_sub']['custom_id'], ENT_QUOTES, 'UTF-8') ?>" aria-expanded="true">
                                                                                                                    View Paper Summary
                                                                                                                </button>
                                                                                                            </h6>
                                                                                                            <div id="collapse-<?= htmlspecialchars($talk['paper_sub']['custom_id'], ENT_QUOTES, 'UTF-8') ?>" class="accordion-collapse collapse">
                                                                                                                <div class="accordion-body">
                                                                                                                    <?= strip_tags($talk['summary']) ?>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                <?php endif; ?>
                                                                                            </li>
                                                                                        <?php elseif (!empty($talk['presenters'])): ?>
                                                                                            <?php foreach ($talk['presenters'] as $presenter): ?>
                                                                                                <li class="list-group-item" style="list-style: none;">
                                                                                                    <?= trim($presenter->user_name . ' ' . $presenter->user_surname) ?>
                                                                                                    <?php if (!empty($presenter->details)): ?>
                                                                                                        <?= !empty($presenter->details['deg']) ? ', ' . htmlspecialchars($presenter->details['deg'], ENT_QUOTES, 'UTF-8') : '' ?>
                                                                                                        <i><?= !empty($presenter->details['institution']) ? '<br>' . htmlspecialchars($presenter->details['institution'], ENT_QUOTES, 'UTF-8') : '' ?></i>
                                                                                                        <?php if (!empty($event['talks'])): ?>
                                                                                                            <div class="accordion mt-2" id="accordionExample">
                                                                                                                <div class="accordion-item">
                                                                                                                    <h6 class="accordion-header">
                                                                                                                        <button class="accordion-button p-1 bg-primary text-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= htmlspecialchars($presenter->id, ENT_QUOTES, 'UTF-8') ?>" aria-expanded="true">
                                                                                                                            View Paper Summary
                                                                                                                        </button>
                                                                                                                    </h6>
                                                                                                                    <div id="collapse-<?= htmlspecialchars($presenter->id, ENT_QUOTES, 'UTF-8') ?>" class="accordion-collapse collapse">
                                                                                                                        <div class="accordion-body">
                                                                                                                            <?= strip_tags($talk['summary']) ?>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        <?php endif; ?>
                                                                                                    <?php endif; ?>
                                                                                                </li>
                                                                                            <?php endforeach; ?>
                                                                                        <?php endif; ?>
                                                                                    </ul>
                                                                                </td>
                                                                            </tr>
                                                                        <?php endif; ?>
                                                                    <?php endforeach; ?>
                                                                </table>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>

                                                </div>

                                                <?php if($event['description']):?>
                                                <div class="accordion-item border-0">
                                                    <h2 class="accordion-header">
                                                        <button class="btn-primary btn-sm accordion-button collapsed fw-bolder" type="button" data-bs-toggle="collapse" data-bs-target="#descriptionCollapse-<?= htmlspecialchars($event['id'], ENT_QUOTES, 'UTF-8') ?>" aria-expanded="false">
                                                            <span class="me-2 toggle-icon">+</span> Session Description
                                                        </button>
                                                    </h2>
                                                    <div id="descriptionCollapse-<?= htmlspecialchars($event['id'], ENT_QUOTES, 'UTF-8') ?>" class="accordion-collapse collapse">
                                                        <div class="accordion-body ">
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

        const firstTabLink = document.querySelector('.scheduleTabNav .nav-link');
        if (firstTabLink) {
            firstTabLink.classList.add('active');
        }

        const firstTabPane = document.querySelector('.tab-content .tab-pane');
        if (firstTabPane) {
            firstTabPane.classList.add('show', 'active');
        }
    });
</script>
