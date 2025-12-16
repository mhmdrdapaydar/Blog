<?php
require_once 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id = intval($_GET['id']);
$db = getDB();
$post = null;

try {
    $stmt = $db->prepare('SELECT * FROM posts WHERE id = ?');
    $stmt->execute([$id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$post) {
        header('Location: index.php');
        exit();
    }
} catch(PDOException $e) {
    die('خطا در دریافت پست: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo escape($post['title']); ?> - <?php echo escape(SITE_TITLE); ?></title>
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
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
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
        
        .back-btn {
            background-color: rgba(255, 255, 255, 0.2);
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
        
        .back-btn:hover {
            background-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }
        
        main {
            padding: 40px 0;
            min-height: calc(100vh - 200px);
        }
        
        .post-detail {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            padding: 40px;
            margin-bottom: 40px;
        }
        
        .post-header {
            margin-bottom: 30px;
            text-align: center;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 30px;
        }
        
        .post-title {
            font-size: 32px;
            color: var(--dark-color);
            margin-bottom: 20px;
            line-height: 1.4;
        }
        
        .post-meta {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            color: var(--gray-color);
            font-size: 15px;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .post-content {
            font-size: 18px;
            line-height: 1.9;
            color: #444;
            margin-bottom: 40px;
        }
        
        .post-content p {
            margin-bottom: 25px;
        }
        
        .post-footer {
            display: flex;
            justify-content: space-between;
            padding-top: 30px;
            border-top: 1px solid #eee;
            margin-top: 40px;
        }
        
        .share-buttons {
            display: flex;
            gap: 10px;
        }
        
        .share-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: #f0f0f0;
            color: var(--dark-color);
            border-radius: 50%;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .share-btn:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-3px);
        }
        
        .related-posts {
            margin-top: 50px;
        }
        
        .section-title {
            font-size: 24px;
            color: var(--secondary-color);
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .related-post {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }
        
        .related-post:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }
        
        .related-post h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: var(--dark-color);
        }
        
        .related-post a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            display: inline-block;
            margin-top: 10px;
        }
        
        .related-post a:hover {
            text-decoration: underline;
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
            
            .post-detail {
                padding: 25px;
            }
            
            .post-title {
                font-size: 24px;
            }
            
            .post-content {
                font-size: 16px;
            }
            
            .post-meta {
                flex-direction: column;
                align-items: center;
                gap: 15px;
            }
            
            .post-footer {
                flex-direction: column;
                gap: 20px;
            }
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
            
            <a href="index.php" class="back-btn">
                <i class="fas fa-arrow-right"></i>
                بازگشت به صفحه اصلی
            </a>
        </div>
    </header>
    
    <main>
        <div class="container">
            <article class="post-detail">
                <div class="post-header">
                    <h1 class="post-title"><?php echo escape($post['title']); ?></h1>
                    
                    <div class="post-meta">
                        <div class="meta-item">
                            <i class="far fa-calendar-alt"></i>
                            <span>تاریخ انتشار: <?php echo persianDate($post['created_at']); ?></span>
                        </div>
                        
                        <div class="meta-item">
                            <i class="far fa-clock"></i>
                            <span>زمان مطالعه: ۵ دقیقه</span>
                        </div>
                        
                        <div class="meta-item">
                            <i class="far fa-user"></i>
                            <span>نویسنده: مدیر وبلاگ</span>
                        </div>
                    </div>
                </div>
                
                <div class="post-content">
                    <?php 
                    $content = nl2br(escape($post['content']));
                    echo $content;
                    ?>
                </div>
                
                <div class="post-footer">
                    <div class="share-buttons">
                        <span style="margin-left: 10px;">اشتراک گذاری:</span>
                        <a href="#" class="share-btn" title="تلگرام">
                            <i class="fab fa-telegram"></i>
                        </a>
                        <a href="#" class="share-btn" title="توئیتر">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="share-btn" title="اینستاگرام">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                    
                    <a href="index.php" class="back-btn">
                        <i class="fas fa-arrow-right"></i>
                        بازگشت
                    </a>
                </div>
            </article>
            
            <?php
            try {
                $stmt = $db->prepare('SELECT id, title FROM posts WHERE id != ? ORDER BY created_at DESC LIMIT 3');
                $stmt->execute([$id]);
                $relatedPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (count($relatedPosts) > 0):
            ?>
            <div class="related-posts">
                <h2 class="section-title">پست‌های مرتبط</h2>
                <div class="related-grid">
                    <?php foreach($relatedPosts as $related): ?>
                    <div class="related-post">
                        <h3><?php echo escape($related['title']); ?></h3>
                        <a href="single.php?id=<?php echo $related['id']; ?>">
                            مطالعه پست
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php 
                endif;
            } catch(PDOException $e) {
            }
            ?>
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
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>تماس با ما</h3>
                    <p>سوالات و پیشنهادات خود را با ما در میان بگذارید</p>
                    <p style="margin-top: 10px;">
                        <i class="fas fa-envelope"></i>
                        ایمیل: mhmdrdapaydar@gmail.com
                    </p>
                </div>
            </div>
            
            <div class="copyright">
                <p>طراح/محمدرضا پایداره</p>
            </div>
        </div>
    </footer>
</body>
</html>