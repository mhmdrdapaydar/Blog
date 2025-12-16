<?php
require_once 'config.php';

$db = getDB();
$posts = [];

try {
    $stmt = $db->query('SELECT id, title, content, excerpt, created_at FROM posts ORDER BY created_at DESC');
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "خطا در دریافت پست‌ها";
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo escape(SITE_TITLE); ?> - صفحه اصلی</title>
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
            line-height: 1.8;
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 20px 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
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
        
        nav ul {
            display: flex;
            list-style: none;
            gap: 25px;
        }
        
        nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            padding: 5px 0;
            position: relative;
        }
        
        nav a:hover {
            color: var(--accent-color);
        }
        
        nav a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--accent-color);
            transition: width 0.3s;
        }
        
        nav a:hover::after {
            width: 100%;
        }
        
        .admin-btn {
            background-color: var(--accent-color);
            color: white;
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .admin-btn:hover {
            background-color: #2aa8d0;
            transform: translateY(-2px);
        }
        
        main {
            padding: 40px 0;
            min-height: calc(100vh - 200px);
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
        
        .posts-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }
        
        .post-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .post-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        }
        
        .post-image {
            height: 200px;
            background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
        }
        
        .post-content {
            padding: 25px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .post-title {
            font-size: 20px;
            margin-bottom: 15px;
            color: var(--dark-color);
            line-height: 1.4;
        }
        
        .post-excerpt {
            color: var(--gray-color);
            margin-bottom: 20px;
            flex-grow: 1;
        }
        
        .post-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            font-size: 14px;
            color: var(--gray-color);
        }
        
        .post-date {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .read-more {
            display: inline-block;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
            font-weight: 500;
            transition: all 0.3s;
            align-self: flex-start;
        }
        
        .read-more:hover {
            background: linear-gradient(to right, var(--secondary-color), var(--primary-color));
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            grid-column: 1 / -1;
        }
        
        .empty-state i {
            font-size: 64px;
            color: var(--primary-color);
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            font-size: 24px;
            color: var(--dark-color);
            margin-bottom: 10px;
        }
        
        .empty-state p {
            color: var(--gray-color);
            margin-bottom: 20px;
        }
        
        footer {
            background-color: var(--dark-color);
            color: white;
            padding: 40px 0 20px;
            margin-top: 60px;
        }
        
        .footer-content {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 40px;
            margin-bottom: 30px;
        }
        
        .footer-section {
            flex: 1;
            min-width: 250px;
        }
        
        .footer-section h3 {
            font-size: 18px;
            margin-bottom: 20px;
            color: var(--accent-color);
            position: relative;
            padding-bottom: 10px;
        }
        
        .footer-section h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 50px;
            height: 2px;
            background-color: var(--accent-color);
        }
        
        .footer-section p {
            color: #aaa;
            line-height: 1.7;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: #333;
            color: white;
            border-radius: 50%;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .social-links a:hover {
            background-color: var(--primary-color);
            transform: translateY(-3px);
        }
        
        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #444;
            color: #888;
            font-size: 14px;
        }
        
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }
            
            nav ul {
                gap: 15px;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .posts-container {
                grid-template-columns: 1fr;
            }
            
            .page-title {
                font-size: 26px;
            }
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-weight: 500;
        }
        
        .alert-error {
            background-color: #ffebee;
            color: #c62828;
            border-right: 4px solid #c62828;
        }
        
        .alert-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border-right: 4px solid #2e7d32;
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-content">
            <a href="index.php" class="logo">
                <i class="fas fa-blog"></i>
                <?php echo escape(SITE_TITLE); ?>
            </a>
            
            <nav>
                <ul>
                    <li><a href="index.php">خانه</a></li>
                    <li><a href="index.php#posts">پست‌ها</a></li>
                    <li><a href="admin.php">پنل مدیریت</a></li>
                </ul>
            </nav>
            
            <a href="admin.php" class="admin-btn">
                <i class="fas fa-user-shield"></i>
                ورود ادمین
            </a>
        </div>
    </header>
    
    <main>
        <div class="container">
            <h1 class="page-title">به وبلاگ ما خوش آمدید</h1>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo escape($error); ?>
                </div>
            <?php endif; ?>
            
            <div id="posts" class="posts-container">
                <?php if (count($posts) > 0): ?>
                    <?php foreach($posts as $post): ?>
                        <article class="post-card">
                            <div class="post-image">
                                <i class="fas fa-newspaper"></i>
                            </div>
                            
                            <div class="post-content">
                                <h2 class="post-title"><?php echo escape($post['title']); ?></h2>
                                
                                <p class="post-excerpt">
                                    <?php 
                                    if (!empty($post['excerpt'])) {
                                        echo escape($post['excerpt']);
                                    } else {
                                        echo getExcerpt($post['content'], 120);
                                    }
                                    ?>
                                </p>
                                
                                <a href="single.php?id=<?php echo $post['id']; ?>" class="read-more">
                                    <i class="fas fa-book-open"></i>
                                    مطالعه ادامه مطلب
                                </a>
                                
                                <div class="post-meta">
                                    <div class="post-date">
                                        <i class="far fa-calendar-alt"></i>
                                        <span><?php echo persianDate($post['created_at']); ?></span>
                                    </div>
                                    <div class="post-comments">
                                        <i class="far fa-comment"></i>
                                        <span>۰ دیدگاه</span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-newspaper"></i>
                        <h3>هنوز هیچ پستی منتشر نشده است</h3>
                        <p>اولین پست را از طریق پنل مدیریت منتشر کنید.</p>
                        <a href="admin.php" class="read-more">ورود به پنل مدیریت</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>درباره ما</h3>
                    <p>
                        این وبلاگ ساده با PHP و SQLite ساخته شده است. 
                        شما می‌توانید پست‌های خود را در این وبلاگ منتشر کنید 
                        و آن را با دیگران به اشتراک بگذارید.
                    </p>
                </div>
                
                <div class="footer-section">
                    <h3>لینک‌های سریع</h3>
                    <ul style="list-style: none; color: #aaa;">
                        <li style="margin-bottom: 10px;"><a href="index.php" style="color: #aaa; text-decoration: none;">صفحه اصلی</a></li>
                        <li style="margin-bottom: 10px;"><a href="admin.php" style="color: #aaa; text-decoration: none;">پنل مدیریت</a></li>
                        <li style="margin-bottom: 10px;"><a href="init.php" style="color: #aaa; text-decoration: none;">نصب مجدد</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>شبکه‌های اجتماعی</h3>
                    <p>ما را در شبکه‌های اجتماعی دنبال کنید</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-telegram"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="copyright">
                <p>طراح:محمدرضا پایداره</p>
            </div>
        </div>
    </footer>
</body>
</html>