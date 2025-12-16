<?php
require_once 'config.php';

try {
    $db = getDB();
    
    $db->exec('CREATE TABLE IF NOT EXISTS posts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        content TEXT NOT NULL,
        excerpt TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )');
    
    $db->exec('CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL
    )');
    
    $stmt = $db->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
    $stmt->execute([ADMIN_USERNAME]);
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        $hashedPassword = password_hash(ADMIN_PASSWORD, PASSWORD_DEFAULT);
        $stmt = $db->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
        $stmt->execute([ADMIN_USERNAME, $hashedPassword]);
    }
    
    echo "<div style='text-align: center; padding: 50px; font-family: Tahoma;'>
            <h1>پایگاه داده با موفقیت ایجاد شد</h1>
            <p style='margin: 20px 0;'>
                <a href='index.php' style='display: inline-block; background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 10px;'>
                    مشاهده وبلاگ
                </a>
                <a href='admin.php' style='display: inline-block; background: #2196F3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 10px;'>
                    ورود به پنل مدیریت
                </a>
            </p>
            <p>نام کاربری: <strong>admin</strong></p>
            <p>رمز عبور: <strong>123456</strong></p>
        </div>";
    
} catch(PDOException $e) {
    die('خطا در ایجاد پایگاه داده: ' . $e->getMessage());
}
?>