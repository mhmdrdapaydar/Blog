<?php
session_start();
define('DB_PATH', 'blog.db');
define('SITE_TITLE', 'وبلاگ من');
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', '123456');


function getDB() {
    try {
        $db = new PDO('sqlite:' . DB_PATH);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->exec('PRAGMA encoding = "UTF-8";');
        return $db;
    } catch(PDOException $e) {
        die('خطا در اتصال به پایگاه داده: ' . $e->getMessage());
    }
}


function escape($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}


function persianDate($timestamp) {
    $date = date('Y/m/d H:i', strtotime($timestamp));
    return $date;
}


function getExcerpt($text, $length = 150) {
    $text = strip_tags($text);
    if (mb_strlen($text) > $length) {
        $text = mb_substr($text, 0, $length) . '...';
    }
    return $text;
}
?>