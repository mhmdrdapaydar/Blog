<?php
require_once 'config.php';

$loggedIn = false;
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    $loggedIn = true;
}

if (isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    try {
        $db = getDB();
        $stmt = $db->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $loggedIn = true;
            $success = 'با موفقیت وارد شدید!';
        } else {
            $error = 'نام کاربری یا رمز عبور اشتباه است';
        }
    } catch(PDOException $e) {
        $error = 'خطا در سیستم احراز هویت';
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit();
}

if ($loggedIn && isset($_POST['add_post'])) {
    $title = trim($_POST['title']);
    $excerpt = trim($_POST['excerpt']);
    $content = trim($_POST['content']);
    
    if (!empty($title) && !empty($content)) {
        try {
            $db = getDB();
            $stmt = $db->prepare('INSERT INTO posts (title, excerpt, content) VALUES (?, ?, ?)');
            $stmt->execute([$title, $excerpt, $content]);
            $success = 'پست با موفقیت اضافه شد!';
            
            $title = $excerpt = $content = '';
        } catch(PDOException $e) {
            $error = 'خطا در ذخیره پست: ' . $e->getMessage();
        }
    } else {
        $error = 'عنوان و محتوا باید پر شوند';
    }
}

$posts = [];
if ($loggedIn) {
    try {
        $db = getDB();
        $stmt = $db->query('SELECT id, title, created_at FROM posts ORDER BY created_at DESC');
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $error = 'خطا در دریافت پست‌ها';
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پنل مدیریت - <?php echo escape(SITE_TITLE); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --accent-color: #4cc9f0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --gray-color: #6c757d;
            --success-color: #4CAF50;
            --danger-color: #f72585;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Vazirmatn', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7ff;
            color: var(--dark-color);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .admin-header {
            background: linear-gradient(135deg, var(--dark-color), #2d3436);
            color: white;
            padding: 20px 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 24px;
            font-weight: 700;
            text-decoration: none;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo i {
            font-size: 28px;
        }
        
        .admin-nav {
            display: flex;
            gap: 15px;
        }
        
        .nav-btn {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .nav-btn:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        
        main {
            flex-grow: 1;
            padding: 40px 0;
        }
        
        .page-title {
            text-align: center;
            margin-bottom: 40px;
            color: var(--secondary-color);
            font-size: 32px;
            position: relative;
            padding-bottom: 15px;
        }
        
        .page-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(to right, var(--primary-color), var(--accent-color));
            border-radius: 2px;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border-right: 4px solid #2e7d32;
        }
        
        .alert-error {
            background-color: #ffebee;
            color: #c62828;
            border-right: 4px solid #c62828;
        }
        
        .form-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 50px;
        }
        
        @media (max-width: 992px) {
            .form-container {
                grid-template-columns: 1fr;
            }
        }
        
        .login-form, .post-form {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        }
        
        .form-title {
            font-size: 22px;
            margin-bottom: 25px;
            color: var(--dark-color);
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }
        
        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }
        
        #content {
            min-height: 250px;
        }
        
        .btn {
            display: inline-block;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            text-align: center;
        }
        
        .btn:hover {
            background: linear-gradient(to right, var(--secondary-color), var(--primary-color));
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }
        
        .btn-logout {
            background: linear-gradient(to right, #f72585, #b5179e);
        }
        
        .btn-logout:hover {
            background: linear-gradient(to right, #b5179e, #f72585);
        }
        

        .posts-table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            margin-top: 40px;
        }
        
        .table-header {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 20px;
            font-size: 18px;
            font-weight: 600;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: right;
            border-bottom: 1px solid #eee;
            color: var(--dark-color);
            font-weight: 600;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        tr:hover {
            background-color: #f8f9fa;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn-edit, .btn-delete {
            padding: 6px 12px;
            border-radius: 5px;
            font-size: 14px;
            text-decoration: none;
        }
        
        .btn-edit {
            background-color: #4CAF50;
            color: white;
        }
        
        .btn-delete {
            background-color: #f44336;
            color: white;
        }
        
        .btn-edit:hover, .btn-delete:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        
        .empty-posts {
            text-align: center;
            padding: 40px;
            color: var(--gray-color);
        }
        
        footer {
            background-color: var(--dark-color);
            color: white;
            padding: 30px 0 20px;
            margin-top: 60px;
        }
        
        .copyright {
            text-align: center;
            color: #888;
            font-size: 14px;
        }
        
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }
            
            .admin-nav {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            th, td {
                padding: 10px;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="container header-content">
            <a href="index.php" class="logo">
                <i class="fas fa-user-shield"></i>
                پنل مدیریت
            </a>
            
            <div class="admin-nav">
                <a href="index.php" class="nav-btn">
                    <i class="fas fa-home"></i>
                    صفحه اصلی
                </a>
                
                <?php if ($loggedIn): ?>
                <a href="?logout=1" class="nav-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    خروج
                </a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    
    <main>
        <div class="container">
            <h1 class="page-title">پنل مدیریت وبلاگ</h1>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <div class="form-container">
                <?php if (!$loggedIn): ?>
                <div class="login-form">
                    <h2 class="form-title">ورود به پنل مدیریت</h2>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="username" class="form-label">نام کاربری</label>
                            <input type="text" id="username" name="username" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="password" class="form-label">رمز عبور</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" name="login" class="btn">ورود</button>
                    </form>
                    
                    <div style="margin-top: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 8px;">
                        <p style="margin: 0; font-size: 14px; color: var(--gray-color);">
                            <strong>اطلاعات ورود پیش‌فرض:</strong><br>
                            نام کاربری: <strong>admin</strong><br>
                            رمز عبور: <strong>123456</strong>
                        </p>
                    </div>
                </div>
                <?php else: ?>
                <div class="post-form">
                    <h2 class="form-title">افزودن پست جدید</h2>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="title" class="form-label">عنوان پست</label>
                            <input type="text" id="title" name="title" class="form-control" value="<?php echo isset($title) ? escape($title) : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="excerpt" class="form-label">خلاصه پست (اختیاری)</label>
                            <textarea id="excerpt" name="excerpt" class="form-control"><?php echo isset($excerpt) ? escape($excerpt) : ''; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="content" class="form-label">محتوا</label>
                            <textarea id="content" name="content" class="form-control" required><?php echo isset($content) ? escape($content) : ''; ?></textarea>
                        </div>
                        <button type="submit" name="add_post" class="btn">انتشار پست</button>
                    </form>
                </div>
                <?php endif; ?>
                
                <?php if ($loggedIn): ?>
                <div class="login-form">
                    <h2 class="form-title">اطلاعات پنل</h2>
                    <div style="margin-bottom: 20px;">
                        <p><strong>وضعیت:</strong> <span style="color: #4CAF50;">وارد شده</span></p>
                        <p><strong>تعداد پست‌ها:</strong> <?php echo count($posts); ?></p>
                        <p><strong>آخرین ورود:</strong> امروز</p>
                    </div>
                    
                    <div style="margin-top: 30px;">
                        <a href="index.php" class="btn" style="display: block; text-align: center; margin-bottom: 10px;">
                            <i class="fas fa-eye"></i>
                            مشاهده وبلاگ
                        </a>
                        <a href="?logout=1" class="btn btn-logout" style="display: block; text-align: center;">
                            <i class="fas fa-sign-out-alt"></i>
                            خروج از سیستم
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if ($loggedIn && count($posts) > 0): ?>
            <div class="posts-table">
                <div class="table-header">
                    <i class="fas fa-list"></i>
                    پست‌های منتشر شده
                </div>
                <table>
                    <thead>
                        <tr>
                            <th width="60">ردیف</th>
                            <th>عنوان پست</th>
                            <th width="150">تاریخ انتشار</th>
                            <th width="120">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($posts as $index => $post): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo escape($post['title']); ?></td>
                            <td><?php echo persianDate($post['created_at']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="single.php?id=<?php echo $post['id']; ?>" class="btn-edit" target="_blank">
                                        <i class="fas fa-eye"></i>
                                        مشاهده
                                    </a>
                                    <a href="#" class="btn-delete">
                                        <i class="fas fa-trash"></i>
                                        حذف(اضافش نکردم)
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php elseif ($loggedIn): ?>
            <div class="empty-posts">
                <i class="fas fa-newspaper" style="font-size: 48px; color: #ddd; margin-bottom: 15px;"></i>
                <h3>هنوز هیچ پستی منتشر نکرده‌اید</h3>
                <p>اولین پست خود را از طریق فرم بالا منتشر کنید.</p>
            </div>
            <?php endif; ?>
        </div>
    </main>
    <footer>
        <div class="container">
            <div class="copyright">
                <p>محمدرضا پایداره/طراح</p>
            </div>
        </div>
    </footer>
</body>
</html>