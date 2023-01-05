<?php 

function convert_time ( $rcv_time_raw ) {

    $time_ago = (time() - strtotime($rcv_time_raw));

    if ($time_ago >= 31536000) {
        $time = intval($time_ago / 31536000);
        if($time == 1) {return "1 year ago";}
        return $time." years ago";
    } elseif ($time_ago >= 2419200) {
        $time = intval($time_ago / 2419200);
        if($time == 1) {return "1 month ago";}
        return $time." months ago";
    } elseif ($time_ago >= 86400) {
        $time = intval($time_ago / 86400);
        if($time == 1) {return "1 day ago";}
        return $time." days ago";
    } elseif ($time_ago >= 3600) {
        $time = intval($time_ago / 3600);
        if($time == 1) {return "1 hour ago";}
        return $time." hours ago";
    } elseif ($time_ago >= 60) {
        $time = intval($time_ago / 60);
        if($time == 1) {return "1 minute ago";}
        return $time." minutes ago";
    } else {
        return "less than a minute ago";
    }
}