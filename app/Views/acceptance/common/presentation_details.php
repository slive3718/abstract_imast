
<div class="card">
    <div class="card-body">
        <p><strong>ID:</strong> # <?= !empty($abstract_details) ? $abstract_details['custom_id'] : ''?>, <?= !empty($abstract_details) ? strip_tags($abstract_details['title']) : ''?></p>
        <p><strong>Presenting Author(s):</strong>
            <?php if($authors): ?>
                <?php
                $first = true; // Add a flag to check the first item
                foreach ($authors as $index => $author):
                    if($author['is_presenting_author'] == 'Yes'):
                        if (!$first) {
                            echo ", "; // Add a comma before each subsequent name
                        }
                        echo $author['user']['name'] .' '. $author['user']['surname'];
                        $first = false; // Set flag to false after the first author
                    endif;
                endforeach; ?>
            <?php endif; ?>
        </p>
        <p><strong>Date and Time:</strong>
            <?php if($abstract_schedule): ?>
                <?= date('F-d-Y', strtotime($abstract_schedule['event']['session_date']))?>,
                <?= date('h:i a', strtotime($abstract_schedule['time_start']))?> -
                <?= date('h:i a', strtotime($abstract_schedule['time_end']))?>
            <?php endif ?>
        </p>
        <p><strong>Session Name:</strong> <?= $abstract_schedule ? $abstract_schedule['event']['session_title'] : ''?> </p>
        <p><strong>Room:</strong> <?=$abstract_schedule ?  $abstract_schedule['room']['name'] : ''?></p>
    </div>
</div>
