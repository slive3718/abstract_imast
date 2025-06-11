
<?php
if (!function_exists('presentation_preferences')) {
    function presentation_preferences() : array
    {
        return [
           1 => 'Presentation Only',
           2 => 'Publication Only',
           3 => 'Presentation and Publication',
           4 => 'Event',
        ];
    }
}

function confirmation_preferences($id){
    return [
        1 => 'Accepted',
        2 => 'Rejected',
        3 => 'Suggested',
        4 => 'Required',
        5 => 'Declined',
    ];
}
?>