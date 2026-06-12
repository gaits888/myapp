<?php
include_once 'loaduser.php';
include_once 'config.php';

// 城市筛选
$city = isset($_GET['city']) ? trim($_GET['city']) : '';

// 分页
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$pageSize = 10;

// 构建查询
$query = db('fl_huodong')->where('status', 1);
if ($city !== '') {
    $query = $query->where('city', $city);
}

// 总数
$total = $query->count();
$totalPages = max(1, ceil($total / $pageSize));
if ($page > $totalPages) {
    $page = $totalPages;
}

// 列表数据：置顶优先 + 排序权重 + 时间倒序
$list = db('fl_huodong')
    ->where('status', 1)
    ->where($city !== '' ? ['city' => $city] : [])
    ->order('is_top desc, sort desc, id desc')
    ->page($page, $pageSize)
    ->select();

// 城市标签（取所有上架活动的城市，去重）
$cityRows = db('fl_huodong')->where('status', 1)->field('city')->group('city')->select();
$cities = array();
foreach ($cityRows as $row) {
    if (!empty($row['city'])) {
        $cities[] = $row['city'];
    }
}

// 活动状态判断辅助
function hd_status_text($start, $end)
{
    $now = time();
    if ($end > 0 && $now > $end) {
        return array('已结束', 'ended');
    }
    if ($start > 0 && $now < $start) {
        return array('报名中', 'upcoming');
    }
    return array('进行中', 'ongoing');
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="keywords" content="同城活动_单身派对_交友活动_<?php echo $webname; ?>">
<meta name="description" content="<?php echo $webname; ?>同城交友活动，单身派对、剧本杀、城市漫步等线下活动，遇见对的人">
<title>同城活动<?php echo $city !== '' ? '·' . htmlspecialchars($city) : ''; ?>_<?php echo $webname; ?></title>
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
    .hd-container { max-width: 640px; margin: 0 auto; padding: 16px; }

    /* 城市筛选条 */
    .city-bar {
        display: -webkit-box; display: -webkit-flex; display: -ms-flexbox; display: flex;
        gap: 8px; overflow-x: auto; -webkit-overflow-scrolling: touch;
        padding-bottom: 4px; margin-bottom: 16px;
    }
    .city-bar::-webkit-scrollbar { display: none; }
    .city-chip {
        flex: 0 0 auto; padding: 7px 16px; border-radius: 20px; font-size: 13px;
        background: #fff; color: var(--text-sub); text-decoration: none; white-space: nowrap;
        border: 1px solid var(--border);
    }
    .city-chip.active { background: var(--primary); color: #fff; border-color: var(--primary); }

    /* 活动卡片 */
    .hd-card {
        display: block; background: #fff; border-radius: 14px; overflow: hidden;
        box-shadow: 0 4px 16px rgba(0,0,0,0.05); margin-bottom: 16px; text-decoration: none; color: inherit;
    }
    .hd-cover { position: relative; width: 100%; padding-top: 50%; overflow: hidden; background: linear-gradient(135deg, #ffd9e1, #ffeef2); }
    .hd-cover img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; }
    .hd-badge {
        position: absolute; top: 12px; left: 12px; padding: 4px 12px; border-radius: 20px;
        font-size: 12px; font-weight: 600; color: #fff;
    }
    .hd-badge.ongoing { background: #ff5e7b; }
    .hd-badge.upcoming { background: #ff9f43; }
    .hd-badge.ended { background: #aaa; }
    .hd-tag-top {
        position: absolute; top: 12px; right: 12px; padding: 4px 10px; border-radius: 20px;
        font-size: 11px; font-weight: 600; color: #fff; background: rgba(255,94,123,0.9);
    }
    .hd-body { padding: 14px 16px; }
    .hd-title { font-size: 17px; font-weight: 700; margin: 0 0 6px; line-height: 1.4; }
    .hd-sub { font-size: 13px; color: var(--text-sub); margin: 0 0 12px; line-height: 1.5;
        overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; }
    .hd-meta { display: -webkit-box; display: -webkit-flex; display: -ms-flexbox; display: flex; -webkit-box-align: center; -webkit-align-items: center; -ms-flex-align: center; align-items: center; -webkit-box-pack: justify; -webkit-justify-content: space-between; -ms-flex-pack: justify; justify-content: space-between; font-size: 12px; color: var(--text-sub); }
    .hd-meta .left { display: -webkit-box; display: -webkit-flex; display: -ms-flexbox; display: flex; -webkit-box-align: center; -webkit-align-items: center; -ms-flex-align: center; align-items: center; gap: 12px; }
    .hd-meta .city-label { color: var(--primary); font-weight: 600; }
    .hd-meta .join { color: var(--text-sub); }

    /* 分页 */
    .hd-pager { display: -webkit-box; display: -webkit-flex; display: -ms-flexbox; display: flex; -webkit-box-pack: center; -webkit-justify-content: center; -ms-flex-pack: center; justify-content: center; -webkit-box-align: center; -webkit-align-items: center; -ms-flex-align: center; align-items: center; gap: 10px; padding: 16px 0 30px; }
    .hd-pager a, .hd-pager span {
        min-width: 38px; height: 38px; line-height: 38px; text-align: center; border-radius: 8px;
        font-size: 14px; text-decoration: none; padding: 0 12px;
    }
    .hd-pager a { background: #fff; color: var(--text-main); border: 1px solid var(--border); }
    .hd-pager .cur { background: var(--primary); color: #fff; }
    .hd-pager .disabled { background: #f3f3f3; color: #ccc; }

    /* 空状态 */
    .hd-empty { text-align: center; color: var(--text-sub); padding: 60px 20px; }
    .hd-empty .icon { font-size: 40px; margin-bottom: 12px; }
</style>
</head>
<body>
    <?php include 'comm/header.php'; ?>

    <div class="hd-container">
        <!-- 城市筛选 -->
        <?php if (!empty($cities)): ?>
        <div class="city-bar">
            <a href="/huodong.php" class="city-chip <?php echo $city === '' ? 'active' : ''; ?>">全部</a>
            <?php foreach ($cities as $c): ?>
                <a href="/huodong.php?city=<?php echo urlencode($c); ?>" class="city-chip <?php echo $city === $c ? 'active' : ''; ?>"><?php echo htmlspecialchars($c); ?></a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- 活动列表 -->
        <?php if (!empty($list)): ?>
            <?php foreach ($list as $hd): ?>
                <?php list($stText, $stClass) = hd_status_text($hd['start_time'], $hd['end_time']); ?>
                <a class="hd-card" href="/huodong_detail.php?id=<?php echo $hd['id']; ?>">
                    <div class="hd-cover">
                        <img src="<?php echo $hd['cover'] ?: '/images/hd_default.jpg'; ?>" alt="<?php echo htmlspecialchars($hd['title']); ?>" onerror="this.src='/images/hd_default.jpg'">
                        <span class="hd-badge <?php echo $stClass; ?>"><?php echo $stText; ?></span>
                        <?php if ($hd['is_top'] == 1): ?><span class="hd-tag-top">置顶</span><?php endif; ?>
                    </div>
                    <div class="hd-body">
                        <h3 class="hd-title"><?php echo htmlspecialchars($hd['title']); ?></h3>
                        <p class="hd-sub"><?php echo htmlspecialchars($hd['subtitle']); ?></p>
                        <div class="hd-meta">
                            <div class="left">
                                <span class="city-label"><?php echo htmlspecialchars($hd['city']); ?></span>
                                <span><?php echo $hd['start_time'] > 0 ? date('m月d日 H:i', $hd['start_time']) : '时间待定'; ?></span>
                            </div>
                            <span class="join"><?php echo intval($hd['join_count']); ?>人已报名</span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>

            <!-- 分页 -->
            <?php if ($totalPages > 1): ?>
            <div class="hd-pager">
                <?php $cityParam = $city !== '' ? '&city=' . urlencode($city) : ''; ?>
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?><?php echo $cityParam; ?>">上一页</a>
                <?php else: ?>
                    <span class="disabled">上一页</span>
                <?php endif; ?>
                <span class="cur"><?php echo $page; ?> / <?php echo $totalPages; ?></span>
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?><?php echo $cityParam; ?>">下一页</a>
                <?php else: ?>
                    <span class="disabled">下一页</span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="hd-empty">
                <div class="icon">📅</div>
                <div>暂无活动，敬请期待～</div>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'comm/footer.php'; ?>
</body>
</html>
