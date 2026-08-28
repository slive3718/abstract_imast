
<?php
if (!function_exists('presentation_preferences')) {
    function presentation_preferences() : array
    {
        return [
            1 => 'Podium Presentation',
            2 => 'E-Point Presentation',
            3 => 'Podium or E-Point Presentation',
            4 => 'Invited Faculty',
            5 => 'Invited Speaker',
            6 => 'Invited Presenter',
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

if(!function_exists('strict_email')){
    function strict_email(string $str = null): bool
    {
        if ($str === null) {
            return false;
        }

        // Basic format check
        if (!filter_var($str, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Check for @ symbol and proper domain format
        $parts = explode('@', $str);
        if (count($parts) !== 2) {
            return false;
        }

        $local = $parts[0];
        $domain = $parts[1];

        // Validate local part (before @)
        if (empty($local) || preg_match('/[\\x00-\\x1F\\x7F-\\xFF]/', $local)) {
            return false;
        }

        // Validate domain part (after @)
        if (empty($domain) || !preg_match('/^[a-zA-Z0-9.-]+$/', $domain)) {
            return false;
        }

        // Check for at least one dot in domain
        if (strpos($domain, '.') === false) {
            return false;
        }

        return true;
    }
}

if(!function_exists('getFormattedDesignations')){
    // Helper function to get formatted designations
    function getFormattedDesignations($author, $designations) {
        $author_designations_arr = json_decode($author['designations'] ?? '[]', true);
        return array_map(function ($value) use ($designations) {
            return $designations[$value] ?? '';
        }, $author_designations_arr);
    }
}


if(!function_exists('getAuthorTypeBadge')) {
// Helper function to get author type badge
    function getAuthorTypeBadge($author, $index)
    {
        $badge = '';
        if ($author['is_presenting_author'] == "Yes") {
            $badge = '<span class="badge bg-primary">Presenting Author</span>';
        } else {
            $badge = '<span class="badge bg-secondary">Co-Author</span>';
        }

        if ($author['is_senior_author'] == "Yes") {
            $badge .= ' <span class="badge bg-warning text-dark">Senior Author</span>';
        }

        return $badge . ' (' . ($index + 1) . ')';
    }
}
?>