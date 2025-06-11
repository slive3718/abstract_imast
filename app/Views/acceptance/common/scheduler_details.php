
<div class="card">
    <div class="card-body">
        <p><strong>Session Title:</strong>
            <?= $scheduler_data ? $scheduler_data['session_title'] : ''?>
        </p>
        <p><strong>Session Date and Time:</strong>
            <?= $scheduler_data?  date('Y-m-d', strtotime($scheduler_data['session_date'])) : '' ?>
            <?= $scheduler_data? date('h:i a', strtotime($scheduler_data['session_start_time'])) : '' ?>
            <?= $scheduler_data? date('h:i a', strtotime($scheduler_data['session_end_time'])) : '' ?>
        </p>
        <p><strong>Room:</strong> <?= $room ?  $room['name'] : ''?></p>
    </div>
</div>
