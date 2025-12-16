<?php
function jdate($format, $timestamp = null) {
    if ($timestamp === null) {
        $timestamp = time();
    }
    
    $gregorianDate = date('Y-m-d H:i:s', $timestamp);
    
    return $gregorianDate;
}
?>