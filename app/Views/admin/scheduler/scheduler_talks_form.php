
<?php //print_R($panels); exit;?>
<div class="session-details">
    <ul class="session-list">
        <li class="session-title">Session: <?= htmlspecialchars($scheduled_event['session_title'] ?? '') ?></li>
        <li class="session-date" data-value="<?= date("Y-m-d", strtotime($scheduled_event['session_date'])) ?>">Session Date: <?= date("Y-m-d", strtotime($scheduled_event['session_date'])) ?? '' ?></li>
        <li class="session-start-time">Session Length: <?= date('h:i a', strtotime($scheduled_event['start_time'])) ?? '' ?> to <?= date('h:i a', strtotime($scheduled_event['end_time'])) ?? '' ?></li>
        <li class="session-duration" data-value="<?= htmlspecialchars($scheduled_event['duration_in_minutes'] ?? '') ?>">Set Session Duration: <?= $scheduled_event['duration_in_minutes'] ?? '' ?> minutes</li>
        <li class="session-talk-duration" data-value="<?= htmlspecialchars($scheduled_event['talk_duration'] ?? '') ?>">Set Talk Duration: <?= $scheduled_event['talk_duration'] ?? '' ?> minutes</li>
        <li class="session-break-duration" data-value="<?= htmlspecialchars($scheduled_event['break_duration'] ?? '') ?>">Break Duration: <?= $scheduled_event['break_duration'] ?? '' ?> minutes</li>
    </ul>
</div>

<hr style="border: 3px solid black" />

<form class="my-4" id="talksForm">
    <h5>Search Criteria</h5>
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="presentationPreference" class="form-label">Presentation Preference</label>
            <select id="presentationPreference" class="form-select" onchange="searchCriteria()">
                <option value="">-- Select --</option>
                <?php if (!empty($presentation_preferences)): ?>
                    <?php foreach ($presentation_preferences as $preference): ?>
                        <option value="<?= htmlspecialchars($preference['id']) ?>"><?= htmlspecialchars($preference['acronym']) ?></option>
                    <?php endforeach; ?>
                <?php endif ?>
            </select>
        </div>
        <div class="col-md-4">
            <label for="keywords" class="form-label">Tracks</label>
            <select id="keywords" class="form-select" onchange="searchCriteria()">
                <option value="">-- Select --</option>
                <?php if (!empty($tracks)): ?>
                    <?php foreach ($tracks as $track): ?>
                        <option value="<?= htmlspecialchars($track['id']) ?>"><?= htmlspecialchars($track['name']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label for="statusAccepted" class="form-label">Status Accepted</label>
            <select id="statusAccepted" class="form-select" onchange="searchCriteria()">
                <option value="">-- Select --</option>
                <option value="1">Accepted</option>
                <option value="2">Suggested Revision</option>
                <option value="3">Required Revision</option>
            </select>
        </div>
    </div>

    <h6>Results:</h6>
    <div class="table-responsive mb-3" style="max-height: 500px; overflow-y: auto;">
        <table class="table table-bordered table-sm" id="abstractTable">
            <thead>
            <tr>
                <th scope="col">Select</th>
                <th scope="col">Presenter</th>
                <th scope="col">Abstract ID</th>
                <th scope="col">Title</th>
                <th scope="col">Preference</th>
                <th scope="col">Tracks</th>
                <th scope="col">Status</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($abstracts)): ?>
                <?php foreach ($abstracts as $abstract):

                    $display = '';
                    if (in_array($abstract['abstract_id'], array_column($talks, 'abstract_id'))) {
                        $display = 'display: none;';
                    }

                    $mappedAuthors = array_map(function ($author) {
                        return $author['name'] . ' ' . $author['surname'];
                    }, $abstract['authors']);
                    $authorsString = implode('<br> ', $mappedAuthors);
                    ?>
                    <tr style="<?= $display ?>">
                        <td><input type="checkbox" class="row-select" data-abstract-id="<?= $abstract['abstract_id'] ?>" data-abstract-type="paper"></td>
                        <td class="text-nowrap"><?= $authorsString ?></td>
                        <td><?= ($abstract['details']->custom_id) ?></td>
                        <td><?= (strip_tags($abstract['details']->title)) ?></td>
                        <td><?= ($abstract['presentation_preference']) ?></td>
                        <td><?= ($abstract['details']->tracks) ?></td>
                        <td><?= ($abstract['acceptance_confirmation']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif ?>

            <?php if(!empty($admin_accepted_panels)) :  foreach ($admin_accepted_panels as $admin_accepted_panel):   ?>

            <?php $display = '';
                    if (in_array($admin_accepted_panel['id'], array_column($talks, 'abstract_id'))) {
                        $display = 'display: none;';
                    } ?>
                <tr  style="<?= $display ?>">
                    <td><input type="checkbox" class="row-select" data-abstract-id="<?=$admin_accepted_panel['id'] ?>" data-abstract-type="panel"></td>
                    <td>Submitter: <?=$admin_accepted_panel['user_name']. '' . $admin_accepted_panel['user_surname']?></td>
                    <td>
                        Panel #<?= htmlspecialchars($admin_accepted_panel['custom_id']) ?>
                        <ol>
                            <?php if (!empty($admin_accepted_panel['panelist_abstract']) && is_array($admin_accepted_panel['panelist_abstract'])): ?>
                                <?php foreach ($admin_accepted_panel['panelist_abstract'] as $subPaper):?>
                                    <li class="fw-normal">
                                        <span class="text-muted"><?= htmlspecialchars($subPaper['custom_id']) ?></span>
                                            <?= htmlspecialchars($subPaper['name'] ?? '') . ' ' . htmlspecialchars($subPaper['surname'] ?? '') ?>
                                        <?php if (!empty($subPaper['individual_panel_title'])): ?>
                                            (<?= htmlspecialchars(strip_tags($subPaper['individual_panel_title'])) ?>)
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="fw-normal text-muted">No subpanels available.</li>
                            <?php endif; ?>
                        </ol>
                    </td>
                    <td><?= htmlspecialchars(strip_tags($admin_accepted_panel['title'])) ?></td>
                    <td><?= htmlspecialchars($abstract['presentation_preference']) ?></td>
                    <td><?= htmlspecialchars($abstract['details']->tracks) ?></td>
                    <td><?= htmlspecialchars($abstract['acceptance_confirmation']) ?></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <button type="button" class="btn btn-secondary" id="addAbstractBtn">Add Abstract(s)</button>
    <button type="button" class="btn btn-secondary ms-2" id="addCustomEventBtn">Add Custom Event</button>
</form>

<hr style="border: 3px solid black" />

<!-- Added Abstracts Table -->
<div class="mb-4">
    <h5>Added Abstracts</h5>
    <table class="table table-bordered" id="tableAddedAbstract">
        <thead>
        <tr>
            <th style="width: 100px;">Time</th>
            <th style="width:50px">Talk Duration</th>
            <th>Presenter</th>
            <th>Abstract Details</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <button type="button" class="btn btn-success" id="save-session-talks">Save</button>
</div>

<script>
    $(function() {
        // Initialize DataTable with settings
        let table = $('#abstractTable').DataTable({
            lengthMenu: [
                [25, 50, 100, 200, -1],
                [25, 50, 100, 200, "All"]
            ],
            iDisplayLength: -1,
            autoWidth: false,
            columnDefs: [
                { targets: [4,5,6], visible: false }
            ],
            language: {
                info: "",
                infoEmpty: "No records available",
                infoFiltered: ""
            }
        });
        table.draw();
    });

    function searchCriteria() {
        let statusAccepted = $('#statusAccepted').val();
        let keywords = $('#keywords').val();
        let presentationPreference = $('#presentationPreference').val();

        let table = $('#abstractTable').DataTable();
        table.column(4).search(statusAccepted || '', true, false);
        table.column(5).search(keywords || '', true, false);
        table.column(6).search(presentationPreference || '', true, false);

        table.draw();
    }
</script>
