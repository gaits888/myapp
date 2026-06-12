<?php
include_once 'config.php';

// SEO 城市落地页：/city.php?name=北京  或伪静态 /city/北京
$cityName = isset($_GET['name']) ? trim($_GET['name']) : '';

// 兼容传 areab 的 id
if ($cityName === '' && isset($_GET['id'])) {
    $cityRow = db('areab')->where('id', intval($_GET['id']))->find();
    if (!empty($cityRow)) {
        $cityName = isset($cityRow['fullname']) ? $cityRow['fullname'] : $cityRow['name'];
    }
}

// 未指定城市 -> 城市索引页（列出所有省份/热门城市，利于收录）
$isIndex = ($cityName === '');

if ($isIndex) {
    // 省份列表（pid=0）
    $provinces = db('areab')->where('pid', 0)->field('id,name,fullname')->select();
} else {
    // 该城市下的活动
    $hdList = db('fl_huodong')
        ->where('status', 1)
        ->where('city', $cityName)
        ->order('is_top desc, sort desc, id desc')
        ->limit(12)
        ->select();
    $hdCount = db('fl_huodong')->where('status', 1)->where('city', $cityName)->count();

    // 热门城市（用于底部互链，提升收录）
    $hotCityRows = db('fl_huodong')->where('status', 1)->field('city')->group('city')->limit(20)->select();
    $hotCities = array();
    foreach ($hotCityRows as $row) {
        if (!empty($row['city']) && $row['city'] !== $cityName) {
            $hotCities[] = $row['city'];
        }
    }
}

// 动态 TDK
if ($isIndex) {
    $seoTitle = '全国同城交友活动城市导航_' . $webname;
    $seoKeywords = '同城交友,全国交友,城市交友活动,' . $webname;
    $seoDesc = $webname . '覆盖全国各大城市的同城交友活动，选择你所在的城市，发现身边的单身派对、线下交友活动，遇见对的人。';
    $h1 = '全国同城交友 · 选择城市';
} else {
    $seoTitle = $cityName . '同城交友_' . $cityName . '单身交友活动_' . $webname;
    $seoKeywords = $cityName . '同城交友,' . $cityName . '交友,' . $cityName . '单身,' . $cityName . '交友活动,' . $webname;
    $seoDesc = $cityName . '同城交友平台，汇聚' . $cityName . '本地优质单身男女，提供' . $cityName . '单身派对、线下交友活动报名，认证真实，安全交友，在' . $cityName . '遇见对的人。';
    $h1 = $cityName . '同城交友';
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="keywords" content="<?php echo htmlspecialchars($seoKeywords); ?>">
<meta name="description" content="<?php echo htmlspecialchars($seoDesc); ?>">
<title><?php echo htmlspecialchars($seoTitle); ?></title>
<link rel="canonical" href="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/city.php' . ($isIndex ? '' : '?name=' . urlencode($cityName)); ?>">
<?php if (!$isIndex): ?>
<!-- 结构化数据，利于搜索引擎理解 -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "<?php echo htmlspecialchars($seoTitle); ?>",
  "description": "<?php echo htmlspecialchars($seoDesc); ?>",
  "breadcrumb": {
    "@type": "BreadcrumbList",
    "itemListElement": [
      {"@type": "ListItem", "position": 1, "name": "首页", "item": "<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']; ?>/"},
      {"@type": "ListItem", "position": 2, "name": "<?php echo htmlspecialchars($cityName); ?>同城交友"}
    ]
  }
}
</script>
<?php endif; ?>
<link rel="stylesheet" href="/css/comm.css">
<style>
    :root {
        --primary: #ff5e7b;
        --primary-dark: #e84968;
        --primary-light: #fff0f3;
        --text-main: #333;
        --text-sub: #888;
        --border: #f0f0f0;
    }
    * { box-sizing: border-box; }
    body { margin: 0; background: #f7f7f8; color: var(--text-main); font-family: -apple-system, BlinkMacSystemFont, "PingFang SC", "Helvetica Neue", Arial, sans-serif; }
    .city-container { max-width: 640px; margin: 0 auto; padding: 16px; }

    /* SEO 头部 */
    .city-hero {
        background: linear-gradient(135deg, #ff7a93 0%, #ff5e7b 100%);
        border-radius: 16px; padding: 26px 22px; color: #fff;
        box-shadow: 0 8px 24px rgba(255, 94, 123, 0.22);
    }
    .city-hero h1 { font-size: 22px; font-weight: 800; margin: 0 0 10px; }
    .city-hero p { font-size: 14px; line-height: 1.6; margin: 0; opacity: 0.95; }

    /* 通用卡片 */
    .card { background: #fff; border-radius: 14px; padding: 18px; margin-top: 16px; box-shadow: 0 4px 16px rgba(0,0,0,0.04); }
    .card-title { font-size: 16px; font-weight: 700; margin: 0 0 14px; display: flex; align-items: center; }
    .card-title::before { content: ''; width: 4px; height: 16px; background: var(--primary); border-radius: 2px; margin-right: 8px; }

    /* 快捷入口 */
    .quick-grid { display: -webkit-box; display: -webkit-flex; display: -ms-flexbox; display: flex; gap: 10px; }
    .quick-item { flex: 1; text-align: center; text-decoration: none; color: var(--text-main); background: var(--primary-light); border-radius: 12px; padding: 16px 8px; }
    .quick-item .t { font-size: 14px; font-weight: 700; color: var(--primary); }
    .quick-item .d { font-size: 11px; color: var(--text-sub); margin-top: 4px; }

    /* 活动列表 */
    .hd-item { display: -webkit-box; display: -webkit-flex; display: -ms-flexbox; display: flex; gap: 12px; padding: 12px 0; border-bottom: 1px solid var(--border); text-decoration: none; color: inherit; }
    .hd-item:last-child { border-bottom: none; }
    .hd-item .thumb { flex: 0 0 96px; width: 96px; height: 64px; border-radius: 8px; object-fit: cover; background: #ffeef2; }
    .hd-item .meta { flex: 1; min-width: 0; }
    .hd-item .meta .t { font-size: 14px; font-weight: 600; line-height: 1.4; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
    .hd-item .meta .s { font-size: 12px; color: var(--text-sub); margin-top: 6px; }

    /* 城市/省份链接云 */
    .link-cloud { display: -webkit-box; display: -webkit-flex; display: -ms-flexbox; display: flex; -webkit-flex-wrap: wrap; -ms-flex-wrap: wrap; flex-wrap: wrap; gap: 10px; }
    .link-cloud a {
        display: inline-block; padding: 7px 14px; background: #fff; border: 1px solid var(--border);
        border-radius: 20px; font-size: 13px; color: var(--text-main); text-decoration: none;
    }
    .link-cloud a:active { background: var(--primary-light); color: var(--primary); }

    /* SEO 文案区 */
    .seo-text { font-size: 13px; line-height: 1.9; color: #666; }
    .seo-text p { margin: 0 0 10px; }

    .empty-tip { text-align: center; color: var(--text-sub); font-size: 14px; padding: 20px 0; }
    .btn-block { display: block; text-align: center; background: var(--primary); color: #fff; text-decoration: none; border-radius: 24px; padding: 13px; font-size: 15px; font-weight: 600; margin-top: 12px; }
</style>
</head>
<body>
    <?php include 'comm/header.php'; ?>

    <div class="city-container">
        <div class="city-hero">
            <h1><?php echo htmlspecialchars($h1); ?></h1>
            <p><?php echo htmlspecialchars($seoDesc); ?></p>
        </div>

        <?php if ($isIndex): ?>
            <!-- 城市索引页：省份导航 -->
            <div class="card">
                <h2 class="card-title">选择省份 / 地区</h2>
                <div class="link-cloud">
                    <?php if (!empty($provinces)): ?>
                        <?php foreach ($provinces as $p): ?>
                            <?php $pName = isset($p['fullname']) && $p['fullname'] !== '' ? $p['fullname'] : $p['name']; ?>
                            <a href="/city.php?name=<?php echo urlencode($pName); ?>"><?php echo htmlspecialchars($pName); ?></a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-tip">暂无城市数据</div>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <!-- 城市页：快捷入口 -->
            <div class="card">
                <h2 class="card-title"><?php echo htmlspecialchars($cityName); ?>交友入口</h2>
                <div class="quick-grid">
                    <a class="quick-item" href="/fbby.php">
                        <div class="t">发布交友</div>
                        <div class="d">免费发布信息</div>
                    </a>
                    <a class="quick-item" href="/huodong.php?city=<?php echo urlencode($cityName); ?>">
                        <div class="t">同城活动</div>
                        <div class="d"><?php echo $hdCount; ?>个活动</div>
                    </a>
                    <a class="quick-item" href="/rz.php">
                        <div class="t">真人认证</div>
                        <div class="d">安全可信</div>
                    </a>
                </div>
            </div>

            <!-- 该城市的活动 -->
            <div class="card">
                <h2 class="card-title"><?php echo htmlspecialchars($cityName); ?>同城活动</h2>
                <?php if (!empty($hdList)): ?>
                    <?php foreach ($hdList as $hd): ?>
                        <a class="hd-item" href="/huodong_detail.php?id=<?php echo $hd['id']; ?>">
                            <img class="thumb" src="<?php echo $hd['cover'] ?: '/images/hd_default.jpg'; ?>" alt="<?php echo htmlspecialchars($hd['title']); ?>" onerror="this.src='/images/hd_default.jpg'">
                            <div class="meta">
                                <div class="t"><?php echo htmlspecialchars($hd['title']); ?></div>
                                <div class="s"><?php echo $hd['start_time'] > 0 ? date('m月d日 H:i', $hd['start_time']) : '时间待定'; ?> · <?php echo intval($hd['join_count']); ?>人报名</div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                    <a class="btn-block" href="/huodong.php?city=<?php echo urlencode($cityName); ?>">查看全部<?php echo htmlspecialchars($cityName); ?>活动</a>
                <?php else: ?>
                    <div class="empty-tip"><?php echo htmlspecialchars($cityName); ?>暂无活动，去发布你的交友信息吧～</div>
                    <a class="btn-block" href="/fbby.php">免费发布交友信息</a>
                <?php endif; ?>
            </div>

            <!-- SEO 文案 -->
            <div class="card">
                <h2 class="card-title">关于<?php echo htmlspecialchars($cityName); ?>同城交友</h2>
                <div class="seo-text">
                    <p><?php echo htmlspecialchars($cityName); ?>同城交友就上<?php echo htmlspecialchars($webname); ?>。我们汇聚了<?php echo htmlspecialchars($cityName); ?>本地大量真实单身男女会员，无论你是想找朋友、找对象，还是参加<?php echo htmlspecialchars($cityName); ?>本地的单身派对、线下交友活动，都能在这里找到合适的人。</p>
                    <p>平台支持真人视频认证，严格审核每一条信息，保障<?php echo htmlspecialchars($cityName); ?>交友的真实与安全。免费发布交友信息，快速被同城用户看到，在<?php echo htmlspecialchars($cityName); ?>遇见对的人。</p>
                </div>
            </div>

            <!-- 其它城市互链 -->
            <?php if (!empty($hotCities)): ?>
            <div class="card">
                <h2 class="card-title">其它热门城市交友</h2>
                <div class="link-cloud">
                    <?php foreach ($hotCities as $hc): ?>
                        <a href="/city.php?name=<?php echo urlencode($hc); ?>"><?php echo htmlspecialchars($hc); ?>交友</a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php include 'comm/footer.php'; ?>
</body>
</html>
