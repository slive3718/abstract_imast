
<div class="card">
    <div class="card-body">
        <p><strong>Session Title:</strong>
            <?= $scheduler_data ? $scheduler_data['session_title'] : ''?>
        </p>
        <p><strong>Session Date and Time:</strong>
            <?= $scheduler_data ?  date('F d, Y', strtotime($scheduler_data['session_date'])) : '' ?>
            <?= $scheduler_data ? date('H:i', strtotime($scheduler_data['session_start_time'])) : '' ?>-<?= $scheduler_data? date('H:i', strtotime($scheduler_data['session_end_time'])) : '' ?>
        </p>
    </div>
</div>
