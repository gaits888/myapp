<?php
include_once 'loaduser.php';
include_once 'config.php';

$infotype = 1;
$info_id = intval($_GET['id']);

$where = [];
$where['id'] = $info_id;
$infos = db3('infob')->where($where)->field('id,uid,flag,sh')->find();
if (empty($infos) || $infos['flag'] != 1) {
    echo "<script > window.location.href = '/404.html';</script>";
    die;
}
if ($infos['flag'] == 0 && $infos['uid'] == $user_id) {
    echo "<script > window.location.href = '/sh.html';</script>";
    die;
}
if ($infos['flag'] == 2 && $infos['uid'] == $user_id) {
    echo "<script > window.location.href = '/shno.html';</script>";
    die;
}

$where = [];
$where['infob.id'] = $info_id;
$where['infob.flag'] = 1;

$infos = db3('infob')
    ->alias('infob')
    ->join('areab city_area', 'infob.city = city_area.id', 'LEFT')
    ->join('areab district_area', 'infob.cityid = district_area.id', 'LEFT')
    ->where($where)
    ->field('infob.id, infob.title, infob.city, infob.cityid, infob.times,infob.ossvideos,infob.videos, infob.fbtime,infob.price,infob.typeid,infob.content, infob.uname, infob.pics,infob.osspics,infob.oss,infob.sh,infob.age,infob.nums,infob.isrz,infob.wmtj,infob.pj, infob.fw,infob.isopen, city_area.fullname as city_name, district_area.fullname as district_name')
    ->find();

if (empty($infos)) {
    echo "<script > window.location.href = '404.html';</script>";
    die;
}

// 增加浏览量
db3('infob')->where(['id' => $info_id])->inc('times', 1);
$picsArrays = z_imgurl_arr($infos['pics'], $infos['osspics'], 1, $infos['oss']);
$infos['pic'] = $picsArrays['img'];
$picsArray = $picsArrays['arr'];

$videosArrays = z_imgurl_arr($infos['videos'], $infos['ossvideos'], 1, $infos['oss']);
$videosArray = $videosArrays['arr'];

$mediaArray = [];

foreach ($picsArray as $pic) {
    $mediaArray[] = ['type' => 'image', 'url' => $pic];
}
foreach ($videosArray as $video) {
    $mediaArray[] = ['type' => 'video', 'url' => $video];
}
$where = [];
$where['infob.flag'] = 1;
$where['infob.typeid'] = $infos['typeid'];

$tj_list = db3('infob')
    ->alias('infob')
    ->join('areab city_area', 'infob.city = city_area.id', 'LEFT')
    ->join('areab district_area', 'infob.cityid = district_area.id', 'LEFT')
    ->where($where)
    ->field('infob.id, infob.title, infob.city, infob.cityid, infob.times, infob.age, infob.pics,infob.osspics,infob.oss,infob.sh,infob.price, city_area.fullname as city_name, district_area.fullname as district_name')
    ->order('infob.times', 'desc')
    ->limit(0, 4)
    ->select();

foreach ($tj_list as &$item) {
    $item['pic'] = z_imgurl($item['pics'], $item['osspics'], 1, $item['oss']);
}
unset($item);

$existingCollection = db('usersc')->field('id,status')
    ->where(['user_id' => $user_id, 'info_id' => $info_id, 'infotype' => 1])
    ->find();
$is_sc = 0;
if (!empty($existingCollection)) {
    if ($existingCollection['status'] == 1) {
        $is_sc = 1;
    }
}
// 是否解锁
$is_js = 0;
if (isset($infos['isopen'])  && $infos['isopen'] == 1) {
  $is_js = 3;
}else{
    $isUnlocked = db('fl_user_js')
    ->where(['user_id' => $user_id, 'info_id' => $info_id])
    ->count();

    if ($isUnlocked > 0) {
        $is_js = 1;
    }
}
$vip_msgster = '本信息需要解锁后才能查看联系方式，你可以选择以下任意一种方式解锁：';
if ($is_js == 1) {
    $vip_msgster = '该信息你已经解锁，你可直接点击下方按钮查看联系方式：';
}
if($is_js == 3){
    $is_js = 1;
    $vip_msgster ='该信息免费解锁，可直接查看联系方式：';
}

$is_money_sj = 0;
if ($is_js != 1) {
    $vipclass_user = $userInfo['vipclass'] ?? 0;
    if ($vipclass_user > 0) {
        $vipconfig = db('fl_vip_config')->where(['id' => $vipclass_user])->find();
        $user_all_num = $vipconfig['see_nums'] ?? 0;
        if ($user_all_num - $userInfo['ckcs'] <= 0) {
            $is_money_sj = 1;
        }
    }
}

function typess($type){
switch($type){
    case '1':  $t="楼凤性息";    break;
    case '2':  $t="外围模特";    break; 
    case '3':  $t="公寓楼凤";    break; 
    case '4':  $t="楼凤论坛";    break;
    case '5':  $t="兼职上门";    break; 
    case '6':  $t="小姐信息";    break; 
}
    return $t;      
}

$typename=typess($infos['typeid']);
$cityname=str_replace("市", "", $city_name);
$webtitle=$cityname.$webname.'_'.$infos['title'];
$keywords=$infos['title'].','.$cityname.$typename.','.$cityname.$webname;
$description=$infos['content'].','.$cityname.$typename;

$pageTitle = "信息详情";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Cache-control" content="no-cache" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
<meta name="format-detection" content="telephone=no" />
<meta name="keywords" content="<?php echo $keywords; ?>" />
<meta name="description" content="<?php echo $description; ?>" />
<meta name="author" content="<?php echo $webname; ?>" />
<title><?php echo $webtitle; ?></title>
<link href="/favicon.ico" rel="shortcut icon"/>
<link rel="stylesheet" href="/css/comm.css">
<!--<link rel="stylesheet" href="/css/publish_detail.css">-->
<style>
body {
            background: var(--bg-gray);
            /* 添加padding-top为50px以适配固定header */
            padding-top: 50px;
        }

        /* 公告横幅 */
        .banner {
            max-width: 600px;
            margin: var(--spacing-md) auto;
            padding: 0 var(--spacing-md);
        }

        .banner-content {
            background: linear-gradient(90deg, var(--bg-pink-light), var(--bg-pink-lighter));
            border-radius: var(--radius-lg);
            padding: var(--spacing-md) var(--spacing-md);
            display: flex;
            align-items: center;
            margin: var(--spacing-md);
        }

        .banner-tag {
            background: var(--bg-activity);
            color: var(--color-activity);
            padding: var(--spacing-xs) var(--spacing-sm);
            border-radius: var(--radius-sm);
            font-size: var(--font-xs);
            font-weight: var(--font-medium);
            white-space: nowrap;
            margin-right: var(--spacing-md);
        }

        .banner-text {
            flex: 1;
            font-size: var(--font-sm);
            color: var(--text-secondary);
        }

        /* 主内容区域 */
        .main-content {
            max-width: 600px;
            margin: 10px auto;
            margin-bottom: 0px;
            padding: 0 var(--spacing-md) var(--spacing-md);
        }

        /* 详情卡片 */
        .detail-card {
            background: var(--bg-white);
            border-radius: 10px;
            box-shadow: var(--shadow-md);
            overflow: hidden;
        }

        /* 图片区域 */
        .detail-image {
            width: 100%;
            position: relative;
            height: auto;
            overflow: hidden;
            border-radius: 0;
        }

        .carousel-container {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .carousel-slide {
            display: none;
            width: 100%;
            height: 100%;
        }

        .carousel-slide.active {
            display: block;
        }

        .carousel-slide img,
        .carousel-slide video {
            width: 100%;
            height: auto;
            object-fit: cover;
        }

        .carousel-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            box-shadow: var(--shadow-md);
            transition: all var(--transition-base);
            z-index: 10;
        }

        .carousel-btn:active {
            transform: translateY(-50%) scale(0.95);
            background: rgba(255, 255, 255, 1);
        }

        .carousel-btn.prev {
            left: 10px;
        }

        .carousel-btn.next {
            right: 10px;
        }

        .carousel-btn svg {
            width: 20px;
            height: 20px;
        }

        .carousel-indicator {
            position: absolute;
            bottom: 15px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 4px 12px;
            border-radius: 25px;
            font-size: var(--font-xs);
            z-index: 10;
        }

        .detail-image img {
            border-radius: 5px;
        }
        .verified-badge{
            background: linear-gradient(to right, rgb(248 164 10 / 50%), #ff8fb3);
            color: #fff;
            font-size: 10px;
            border-radius: var(--radius-lg);
            padding: 3px 8px;
        }

        /* 内容区域 */
        .detail-content {
            padding: 0 var(--spacing-md) var(--spacing-md);
        }

        .detail-title {
            font-size: 16px;
            font-weight: var(--font-bold);
            color: var(--text-primary);
            margin: var(--spacing-md) 0 0 0;
        }

        /* 统计信息 */
        .detail-stats {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            padding: var(--spacing-md) 0;
            border-bottom:1px solid #f0efef;
            margin-bottom: var(--spacing-md);
            color: var(--text-secondary);
            font-size: var(--font-sm);
        }

        .stat-item {
            display: flex;
            align-items: center;
            margin-right: var(--spacing-md);
        }

        .stat-item svg {
            width: 16px;
            height: 16px;
            margin-right: var(--spacing-xs);
        }

        .badge {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: var(--white);
            padding: 2px var(--spacing-sm);
            border-radius: var(--radius-sm);
            font-size: var(--font-xs);
            display: inline-flex;
            align-items: center;
            margin-right: var(--spacing-xs);
        }

        .badge svg {
            margin-right: var(--spacing-xs);
        }

        /* 详细信息 */
        .detail-info {
            display: flex;
            flex-direction: column;
            margin-bottom: var(--spacing-md);
        }

        .info-row {
            display: flex;
            font-size: var(--font-base);
            margin-bottom: var(--spacing-md);
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-label {
            color: var(--text-secondary);
            width: 50px;
            flex-shrink: 0;
        }

        .info-value {
            color: var(--text-primary);
            flex: 1;
        }

        /* 详情介绍 */
        .detail-description {
            margin-top: var(--spacing-md);
            padding-top: var(--spacing-md);
            border-top: 1px solid #f0efef;
        }

        .description-title {
            font-size: var(--font-lg);
            font-weight: var(--font-semibold);
            color: var(--text-primary);
            margin-bottom: var(--spacing-md);
            text-align: left;
        }

        .description-content {
            color: var(--text-secondary);
            font-size: var(--font-base);
            line-height: 1.6;
        }

        .description-content p {
            margin-bottom: var(--spacing-md);
        }

        .description-content p:last-child {
            margin-bottom: 0;
        }

        /* 操作按钮 */
        /* 查看方式按钮样式 */
        .view-method-section {
            max-width: 600px;
            margin: 0 auto;
            padding: 0 var(--spacing-md);
            margin-bottom: 10px;
        }

        .view-method-card {
            background: var(--bg-white);
            border-radius: 10px;
            box-shadow: var(--shadow-md);
            padding: var(--spacing-md);
            margin-bottom: 10px;
        }

        .view-method-title {
            font-size: var(--font-lg);
            font-weight: var(--font-semibold);
            color: var(--text-primary);
            margin-bottom: var(--spacing-md);
            text-align: left;
        }

        /* 添加联系方式说明样式 */
        .contact-notice {
            background: linear-gradient(90deg, var(--bg-pink-light), #ffefe6);
            border-left: 0px solid var(--primary-color);
            border-radius: var(--radius-md);
            padding: var(--spacing-md);
            margin-bottom: 0px;
        }

        .contact-notice-content {
            display: flex;
            margin: var(--spacing-md);
        }

        .contact-notice-icon {
            flex-shrink: 0;
            color: var(--primary-color);
            margin-right: var(--spacing-md);
        }

        .contact-notice-icon svg {
            width: 20px;
            height: 20px;
            margin-top: 10px;
        }

        .contact-notice-text {
            flex: 1;
            color: var(--text-secondary);
            font-size: var(--font-sm);
            line-height: 1.6;
        }

        .contact-notice-text p {
            margin-bottom: var(--spacing-sm);
        }

        .contact-notice-text p:last-child {
            margin-bottom: 0;
        }

        .contact-notice-list {
            margin-top: var(--spacing-sm);
            padding-left: var(--spacing-md);
        }

        .contact-notice-list li {
            margin-bottom: var(--spacing-xs);
            color: var(--text-primary);
        }

        .view-method-buttons {
            display: flex;
            flex-direction: column;
            margin-bottom: var(--spacing-md);
        }

        .view-method-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 15px;
            padding: 12px;
            background: linear-gradient(to right, rgb(248 164 10 / 57%), #ff7da7);
            background: linear-gradient(to right, rgb(10 149 248 / 57%), #9cff7de0);
            color: var(--white);
            border: none;
            border-radius: var(--radius-md);
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            transition: all var(--transition-base);
        }

        .view-method-btn:last-child {
            margin-bottom: 0;
        }

        .view-method-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .view-method-btn svg {
            width: 20px;
            height: 20px;
            margin-right: var(--spacing-sm);
        }

        .view-method-btn.secondary {
            background:linear-gradient(to right, rgb(248 98 10 / 53%), #c35cf1);
            background:linear-gradient(to right, rgba(248 164 10 / 57%), #ff7da7);
            color: #fff;
        }

        /* 响应式 */
        @media (max-width: 480px) {
            .detail-stats {
                font-size: var(--font-xs);
            }

            .stat-item {
                margin-right: 30px;
            }

            .detail-title {
               font-size: 14px;
               display: flex;
               align-items: center;
               justify-content: center;
               justify-content: flex-start;
               width: 98%;           /* 必须有固定宽度或最大宽度 */
               white-space: nowrap;    /* 强制文本在一行内显示 */
               overflow: hidden;       /* 超出部分隐藏 */
               text-overflow: ellipsis; /* 超出部分显示省略号 */
            }
        }
</style>
</head>
<body>
<?php include 'comm/header.php'; ?>
    <!-- 主内容 -->
    <main class="main-content">
        <div class="detail-card">


            <!-- 内容区域 -->
            <div class="detail-content">
                <h1 class="detail-title">
                     <?php if ($infos['isrz'] == 1): ?>
                        <span class="verified-badge" style="margin-right:5px;">已认证</span>
                    <?php endif; ?>
                    <?php echo !empty($infos['title']) ? htmlspecialchars($infos['title']) : '梦女郎'; ?>
                </h1>

                <!-- 统计信息 -->
                <div class="detail-stats">
                    <!--
                    <div class="stat-item">
                        <span class="badge">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <?php echo $infos['uname']; ?>
                        </span>
                    </div>
                    -->
                    <div class="stat-item">
                       <svg t="1773982937173" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="57254" width="20" height="20"><path d="M512 888c207.66 0 376-168.34 376-376S719.66 136 512 136 136 304.34 136 512s168.34 376 376 376z m0 72C264.576 960 64 759.424 64 512S264.576 64 512 64s448 200.576 448 448-200.576 448-448 448z m32-480.167l114.19 114.191a8 8 0 0 1 0 11.314l-36.769 36.77a8 8 0 0 1-11.313 0L480 512V264a8 8 0 0 1 8-8h48a8 8 0 0 1 8 8v215.833z" fill="#bfbfbf" p-id="57255"></path></svg>
                        <span><?php echo date('Y/m/d', $infos['fbtime']); ?></span>
                    </div>
                    <div class="stat-item">
                        <svg t="1773988075720" class="icon" viewBox="0 0 1194 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="68818" width="32" height="32"><path d="M426.666667 512a170.666667 170.666667 0 1 0 341.333333 0 170.666667 170.666667 0 0 0-341.333333 0z m264.874666 0a94.208 94.208 0 1 1-188.416 0 94.208 94.208 0 0 1 188.416 0z m392.618667 0H1194.666667c-95.744 250.026667-326.826667 426.666667-597.333334 426.666667C326.826667 938.666667 95.573333 762.026667 0 512h110.08C200.704 713.728 372.48 843.861333 597.333333 843.861333S993.621333 713.728 1084.16 512z m0 0H1194.666667c-95.744-250.026667-326.826667-426.666667-597.333334-426.666667C326.826667 85.333333 95.573333 261.973333 0 512h110.08C200.704 310.272 372.48 180.138667 597.333333 180.138667S993.621333 310.272 1084.16 512z" p-id="68819" fill="#cdcdcd"></path></svg><?php echo $infos['times']; ?>次</span>
                    </div>
                </div>

                <!-- 详细信息 -->
                <div class="detail-info">
                    <div class="info-row">
                        <span class="info-label">地区:</span>
                        <span class="info-value"><?php echo !empty($infos['district_name']) ? $infos['district_name'] : '未知城市'; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">年龄:</span>
                        <span class="info-value"><?php echo $infos['age']; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">颜值:</span>
                        <span class="info-value"><?php echo !empty($infos['wmtj']) ? $infos['wmtj'] : '暂无外貌形象描述'; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">人数:</span>
                        <span class="info-value"><?php echo !empty($infos['nums']) ? $infos['nums'] : '未知'; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">价格:</span>
                        <span class="info-value"><?php echo !empty($infos['price']) ? $infos['price'] : '价格面议'; ?></span>
                    </div>
                     <div class="info-row">
                        <span class="info-label">评价:</span>
                        <span class="info-value"><?php $pj=selectpj($infos['pj']); echo $pj;  ?></span>
                    </div>
                </div>

                <!-- 详情介绍 -->
                <div class="detail-description">
                    <h3 class="description-title">详情介绍</h3>
                    <div class="description-content">
                        <?php echo !empty($infos['content']) ? nl2br(htmlspecialchars($infos['content'])) : '暂无详情介绍'; ?>
                    </div>
                    
                    
                    
                    
                    
                                <!-- 图片区域 -->
            <div class="detail-image" style="margin-top:10px;">
                <div class="carousel-container">

                    <?php if (!empty($mediaArray)): ?>
                        <?php foreach ($mediaArray as $k => $value): ?>
                            <?php $firstMedia = $mediaArray[$k]; ?>
                            <?php if ($firstMedia['type'] === 'video'): ?>
                                <div class="carousel-slide <?php if ($k == 0) { echo 'active'; } ?>" onclick="openLightbox(<?php echo $k; ?>)">      
                                    <video src="<?php echo htmlspecialchars($firstMedia['url']); ?>" controls></video>
                                </div>
                            <?php else: ?>
                                <div class="carousel-slide <?php if ($k == 0) { echo 'active'; } ?>" onclick="openLightbox(<?php echo $k; ?>)">
                                    <img src="<?php echo htmlspecialchars($firstMedia['url']); ?>" alt="用户照片1">
                                </div>
                            <?php endif; ?>
                        <?php endforeach ?>
                    <?php endif; ?>

                    <!-- 上一张按钮 -->
                    <div class="carousel-btn prev" onclick="changeSlide(-1)">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                    </div>

                    <!-- 下一张按钮 -->
                    <div class="carousel-btn next" onclick="changeSlide(1)">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M9 18l6-6-6-6"></path>
                        </svg>
                    </div>

                    <!-- 指示器 -->
                    <div class="carousel-indicator">
                        <span id="currentSlide">1</span> / <span id="totalSlides">6</span>
                    </div>
                </div>
            </div>
                    
                    
                    
                    
                </div>

            </div>
        </div>
    </main>
    <style type="text/css">
        .contact-modal-content{
            margin: 0 10px 10px;
        }
        
    </style>
<?php if (isset($infos['isopen']) &&  $infos['isopen'] ==1){ 
      $contact_infos= db3('infob') ->where($where) ->field('mobile, weixin, qq,yuli') ->find();
      include_once 'lib/opens.php'; 
  }
?>
<?php if (!isset($infos['isopen']) ||  $infos['isopen'] ==0){ ?>
    <!-- 查看方式按钮区域 -->
    <section class="view-method-section">
        <div class="view-method-card">
            <h2 class="view-method-title">联系方式</h2>
            <!-- 添加联系方式说明 -->
            <div class="contact-notice">
                <div class="contact-notice-content">
                    <div class="contact-notice-icon">
                        <svg t="1773988673635" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="83370" width="32" height="32"><path d="M193.472 161.216c-14.112-12.096-34.272-12.096-48.384 0-14.112 14.112-14.112 34.272 0 48.384l48.384 48.384c14.112 14.112 34.272 14.112 48.384 0s14.112-34.272 0-48.384L193.472 161.216z m637.056 0L784.16 209.6c-14.112 14.112-14.112 34.272 0 48.384s34.272 14.112 48.384 0l48.384-48.384c14.112-14.112 14.112-34.272 0-48.384-16.128-12.096-36.288-12.096-50.4 0zM108.8 427.328H42.272c-18.144 0-34.272 14.112-34.272 34.272 0 18.144 14.112 34.272 34.272 34.272H108.8c18.144 0 34.272-14.112 34.272-34.272 0-18.144-16.128-34.272-34.272-34.272z m872.928 0H915.2c-18.144 0-34.272 14.112-34.272 34.272 0 18.144 14.112 34.272 34.272 34.272h66.528c18.144 0 34.272-14.112 34.272-34.272 0-18.144-14.112-34.272-34.272-34.272z m-504-385.056V108.8c0 18.144 14.112 34.272 34.272 34.272 18.144 0 34.272-14.112 34.272-34.272V42.272c0-20.16-16.128-34.272-34.272-34.272-18.144 0-34.272 14.112-34.272 34.272zM276.128 512c0-129.024 104.832-235.872 235.872-235.872S747.872 382.976 747.872 512 641.024 747.872 512 747.872 276.128 641.024 276.128 512zM209.6 512c0 167.328 135.072 302.4 302.4 302.4s302.4-135.072 302.4-302.4-135.072-302.4-302.4-302.4-302.4 135.072-302.4 302.4z m235.872 469.728c0 18.144 14.112 34.272 34.272 34.272h66.528c18.144 0 34.272-14.112 34.272-34.272s-14.112-34.272-34.272-34.272h-66.528c-20.16 2.016-34.272 16.128-34.272 34.272z m-68.544-100.8c0 18.144 14.112 34.272 34.272 34.272h201.6c18.144 0 34.272-14.112 34.272-34.272 0-18.144-14.112-34.272-34.272-34.272H411.2c-18.144 2.016-34.272 16.128-34.272 34.272z" p-id="83371" fill="#ff5e7b"></path></svg>
                    </div>

                    <div class="contact-notice-text">
                        <p><?php echo $vip_msgster;?></p>
                       <!--  <ul class="contact-notice-list">
                            <li>使用积分解锁（消耗一定积分）</li>
                            <li>使用会员权限免费查看（需开通会员）</li>
                            <li>直接付费查看联系方式</li>
                        </ul> -->
                    </div>
                </div>
            </div>
            
            <div class="view-method-buttons">
                <button class="view-method-btn" id="pointsContactBtn" onclick="fetchContacts(<?php echo $info_id; ?>, 2, 1);" <?php if ($is_js == 1) { echo 'style="display: none;"'; } ?>>
                    <svg t="1773995198035" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="91340" width="32" height="32"><path d="M55.003429 251.538286c0 83.894857 87.113143 161.426286 228.498285 203.373714 141.385143 41.947429 315.611429 41.947429 456.996572 0 141.385143-41.947429 228.498286-119.478857 228.498285-203.373714C968.996571 121.782857 764.379429 16.676571 512 16.676571 259.620571 16.676571 55.003429 121.819429 55.003429 251.538286zM512 557.056c-186.660571 0-347.172571-57.526857-418.121143-139.958857C68.900571 446.098286 55.003429 478.208 55.003429 512c0 129.718857 204.617143 234.861714 456.996571 234.861714 252.379429 0 456.996571-105.142857 456.996571-234.861714 0-33.792-13.897143-65.901714-38.875428-94.902857-70.948571 82.432-231.460571 139.958857-418.121143 139.958857z m0 266.934857c-189.074286 0-351.378286-59.026286-420.864-143.177143-23.259429 28.16-36.132571 59.136-36.132571 91.648 0 129.718857 204.617143 234.861714 456.996571 234.861715 252.379429 0 456.996571-105.142857 456.996571-234.861715 0-32.548571-12.873143-63.488-36.132571-91.648-69.485714 84.114286-231.753143 143.177143-420.864 143.177143z" fill="#ffffff" p-id="91341"></path></svg>
                    积分解锁联系方式
                </button>
                <button class="view-method-btn secondary" id="memberContactBtn" <?php if ($is_js == 1) { echo 'style="display: none;"'; } ?>>
                    <svg t="1773995252848" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="93446" width="32" height="32"><path d="M512 1024C229.233778 1024 0 794.766222 0 512S229.233778 0 512 0s512 229.233778 512 512-229.233778 512-512 512z m284.444444-595.228444a72.490667 72.490667 0 0 0-20.807111-50.432 71.253333 71.253333 0 0 0-100.892444 0 66.816 66.816 0 0 0-20.807111 50.432 68.707556 68.707556 0 0 0 20.807111 50.432c2.474667 3.015111 5.461333 5.575111 8.817778 7.566222a78.833778 78.833778 0 0 1-93.312 13.866666 104.661333 104.661333 0 0 1-49.180445-58.638222 189.084444 189.084444 0 0 1 5.034667-51.057778 71.864889 71.864889 0 0 0 37.205333-63.047111 70.599111 70.599111 0 0 0-20.807111-50.432 70.001778 70.001778 0 0 0-100.892444 0 71.864889 71.864889 0 0 0-20.807111 50.432 72.490667 72.490667 0 0 0 20.807111 50.446223c5.632 5.304889 12.003556 9.770667 18.915555 13.226666a131.100444 131.100444 0 0 1 3.797334 48.554667 105.287111 105.287111 0 0 1-49.820445 59.875555 81.351111 81.351111 0 0 1-95.217778-14.492444 71.864889 71.864889 0 0 0 30.264889-58.624 71.253333 71.253333 0 0 0-70.613333-69.347556 72.533333 72.533333 0 0 0-50.446222 20.807112 71.224889 71.224889 0 0 0 0 100.864 70.627556 70.627556 0 0 0 30.890666 18.275555l59.278223 247.125333a28.373333 28.373333 0 0 0 27.107555 21.432889H658.346667a27.733333 27.733333 0 0 0 27.121777-21.432889l59.264-247.125333A71.864889 71.864889 0 0 0 796.444444 428.771556z" fill="#ffffff" p-id="93447"></path></svg>
                    会员解锁联系方式
                </button>
                <button class="view-method-btn secondary" id="viewedContactBtn" onclick="fetchContacts(<?php echo $info_id; ?>, 3, 1);" <?php if ($is_js == 0) { echo 'style="display: none;"'; } ?>>
                   <!--  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg> -->
                    查看联系方式
                </button>
            </div>
        </div>
        
                 <?php if (!isset($infos['isopen']) ||  $infos['isopen'] ==0){ ?>
                <?php include_once 'comm/detail_collections.php'; ?>
                <?php } ?>
                
                
    </section>
<?php } ?>
    <?php include_once 'comm/alert_modal.php'; ?>
    <!-- 添加举报弹窗HTML -->
    <?php include 'comm/jb.php'; ?>
    <script src="/js/contact.js?t=<?php echo time(); ?>"></script>
    <!-- Adding JavaScript to toggle favorite state -->
    <script>
        let currentIndex = 0;
        const slides = document.querySelectorAll('.carousel-slide');
        const totalSlides = slides.length;
        
        document.getElementById('totalSlides').textContent = totalSlides;

        function changeSlide(direction) {
            // 暂停当前视频
            const currentSlide = slides[currentIndex];
            const currentVideo = currentSlide.querySelector('video');
            if (currentVideo) {
                currentVideo.pause();
            }

            // 移除当前active类
            slides[currentIndex].classList.remove('active');
            
            // 计算新索引（支持循环）
            currentIndex = currentIndex + direction;
            
            if (currentIndex >= totalSlides) {
                currentIndex = 0;  // 从最后一张到第一张
            } else if (currentIndex < 0) {
                currentIndex = totalSlides - 1;  // 从第一张到最后一张
            }
            // 添加新的active类
            slides[currentIndex].classList.add('active');
            // 更新指示器
            document.getElementById('currentSlide').textContent = currentIndex + 1;
        }

        function toggleFavorite(btn) {
            const isAlreadyFavorited = btn.classList.contains('favorited');
            const text = btn.querySelector('.favorite-text');
            
            if (isAlreadyFavorited) {
                btn.classList.remove('favorited');
                text.textContent = '收藏';
            } else {
                btn.classList.add('favorited');
                text.textContent = '已收藏';
            }
        }
    </script>
    <script type="text/javascript">
        let galleryImages = <?php echo json_encode($mediaArray); ?>;
        let currentImageIndex = 0;
        let isJs = <?php echo $is_js; ?>;
    </script>

    <script type="text/javascript">
        const medias = <?php 
            if (!empty($mediaArray) && is_array($mediaArray)) {
                $mediaItems = [];
                foreach ($mediaArray as $media) {
                    $mediaItems[] = '{"type": "' . addslashes($media['type']) . '", "url": "' . addslashes($media['url']) . '"}';
                }
                echo '[' . implode(',', $mediaItems) . ']';
            } else {
                echo '[]';
            }
        ?>;
        const infoId = <?php echo $infos['id']; ?>;
        const infotype = <?php echo $infotype; ?>;

        const memberContactBtn = document.getElementById('memberContactBtn');
        const isMoneySj = <?php echo $is_money_sj; ?>;
        
        if (memberContactBtn) {
            memberContactBtn.addEventListener('click', function() {
                if (isJs === 1) {
                    fetchContacts(infoId, 3, 1);
                } else {
                    if (isMoneySj === 1) {
                        tconfirm('当前解锁次数已用完，是否支付10元解锁？', function() {
                            fetchContacts(infoId, 4, 1);
                        }, null, '提示');
                    } else {
                        fetchContacts(infoId, 1, 1);
                    }
                }
            });
        }
    </script>
    <?php 
          include_once 'comm/album.php';       
          include_once 'comm/alert_modal.php';  
    ?>
    <script src="/js/contact.js?t=123.6789"></script>
    <script src="/js/info_collection.js"></script>
</body>
</html>
