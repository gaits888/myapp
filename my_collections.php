<?php 
include_once 'loaduser.php';
include_once 'config.php';
$pageTitle = "我的收藏";
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="author" content="<?php echo $webname; ?>" />
<meta name="keywords" content="我的收藏_<?php echo $webname; ?>">
<meta name="description" content="我的收藏_<?php echo $webname; ?>">
<title>我的收藏_<?php echo $webname; ?></title>
<link rel="stylesheet" href="/css/comm.css">
<link rel="stylesheet" href="/css/my_collections.css">
</head>
<body>
<?php include_once 'comm/header.php'; ?>

  <!-- 大分类标签 -->
  <div class="main-category-tabs">
    <div class="main-category-btn active" data-category="forum">论坛</div>
    <div class="main-category-btn" data-category="highend">高端</div>
    <div class="main-category-btn" data-category="travel">伴游</div>
    <div class="main-category-btn" data-category="friend">包养</div>
  </div>

  <!-- 收藏卡片容器 -->
  <div class="collections-container">
    <div class="cards-grid" id="cardsGrid">
      <!-- 加载中提示 -->
      <div class="empty-state">
        <div class="empty-state-icon">
          <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
          </svg>
        </div>
        <div class="empty-state-text">加载中...</div>
      </div>
    </div>

    <!-- 分页 -->
    <div class="pagination" id="pagination" style="display: none;">
    </div>
  </div>

  <!-- 底部导航 -->
  <?php include_once 'comm/footer.php'; ?>
  <script src="/js/my_collections.js"></script>
</body>
</html>
