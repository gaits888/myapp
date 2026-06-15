<?php
include_once 'loaduser.php';
include_once 'config.php';

$js_jifen_config = $db->table('fl_config')
            ->where(['cname' => 'js_jifen'])
            ->find();
$js_jifen = $js_jifen_config['value'] ?? 0;
// 初始化数据库连接
$db = new DbOperation();
$db3 = new DbOperation3();
$by_id = intval($_GET['id'] ?? 0);

// 查询信息是否存在
$where = [];
$where['id'] = $by_id;
$infos = $db3->table('byb')->where($where)->field('id')->find();
if (empty($infos)) {
    echo "<script>window.location.href = '404.html';</script>";
    die;
}
// 增加浏览量
$db3->querySql("UPDATE byb SET times = COALESCE(times, 0) + 1 WHERE id = $by_id", false);
$where = [];
$where['byb.id'] = $by_id;
$where['byb.flag'] = 1;

$infos = $db3->table('byb')
    ->where($where)
    ->field('byb.id, byb.uid,byb.typeid, byb.uname, byb.city, byb.age, byb.times, byb.osspics,byb.pics, byb.videos,byb.ossvideos, byb.zy,byb.oss, byb.sg, byb.tz, byb.xl, byb.jg, byb.aihao, byb.price, byb.content, byb.mobile, byb.weixin, byb.qq, byb.isrz, byb.dz,byb.isopen')
    ->find();

if (empty($infos)) {
    echo "<script>window.location.href = '404.html';</script>";
    die;
}

// 处理图片
$picsArray = [];


$picsArrays =z_imgurl_arr($infos['pics'],$infos['osspics'],3,$infos['oss']);
$infos['pic']  = $picsArrays['img'];
$picsArray  =$picsArrays['arr'];

// 处理视频
$videosArray = [];

$videosArrays =z_imgurl_arr($infos['videos'],$infos['ossvideos'],3,$infos['oss']);
$videosArray  =$videosArrays['arr'];

// 是否收藏
$existingCollection = $db->table('usersc')->field('id,status')
    ->where(['user_id' => $userData['userId'], 'info_id' => $by_id])
    ->find();
$is_sc = 0;
if (!empty($existingCollection) && $existingCollection['status'] == 1) {
    $is_sc = 1;
}


if ($infos['typeid'] ==0) {
    $infotype =3;
    $title_str = '伴游';
}else{
    $infotype =4;
    $title_str = '包养';
}
// 是否解锁
$is_js = 0;
$isUnlocked = $db->table('fl_user_js')
    ->where(['user_id' => $userData['userId'], 'info_id' => $by_id, 'infotype' => $infotype])
    ->find();
if ($isUnlocked) {
    $is_js = 1;
}

// 判断所使用图片域名路径

if($infos['uid'] == 1000000){
    $img_url = $al_img_url;
}


$vipclass_user = $userInfo['vipclass'] ?? 0;
$vip_msgster  ='年度会员或以上可解锁她的联系方式';
if ($vipclass_user > 2) {
    $vip_msgster  ='因妹妹设有门槛要求,为防止无聊人土解锁口嗨,需线上支付50元诚意金才能解锁.';
}elseif($vipclass_user>0 && $vipclass_user <= 2){
    $vip_msgster ='包养和伴游需年度会员或以上才能解锁联系方式.';
}

if($is_js == 1){
    $vip_msgster ='该信息你已解锁，可直接查看联系方式：';
}

$is_money_sj = 0;
//如果没有解锁
if($is_js != 1){
    //用户是否是vip
    $vipclass_user = $userInfo['vipclass'] ?? 0;

    if ($vipclass_user > 1) {
        // 查询用户总查看次数
        $vipconfig = $db->table('fl_vip_config')->where(['id' => $vipclass_user])->find();
        $user_all_num = $vipconfig['see_nums'] ?? 0;
        //用户次数是否用尽。
        if($user_all_num - $userInfo['ckcs'] <=0){
            $is_money_sj = 1;
        }
    }
}

if ($currentArea=="全国"){
    $Area='';
}else{
    $Area=$currentArea;
}
$citytype=$Area.$title_str;
$cityweb=$webname;
$webtitle=$webname.$citytype.'_'.$infos['uname'];
$keywords=$webname.$title_str.'频道,'.$citytype.'网,'.$infos['zy'].$infos['uname'];
$description=$cityweb.$title_str.'频道,'.nl2br(htmlspecialchars($infos['content']));

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title><?php echo $webtitle; ?></title>
<meta name="author" content="<?php echo $cityname.$webname; ?>" />
<meta name="keywords" content="<?php echo $keywords; ?>">
<meta name="description" content="<?php echo $description; ?>">
<link href="/css/bb.css?t=123.128176" type="text/css" rel="stylesheet" media="all" />
<link href="/css/gr.css" type="text/css" rel="stylesheet" media="all" />
<script src="/js/jquery-3.6.0.min.js"></script>
<script src="/js/jquery.lazyload.min.js"></script>
<script src="/js/clipboard.min.js"></script>
<script>
$(document).ready(function() {
    $("img.lazy").lazyload({
        threshold: 200,
        effect: "fadeIn",
        event: "scroll",
        failure_limit: 10,
        skip_invisible: false,
        placeholder: "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
    });
});
</script>
 <style>
</style>
</head>
<body>
    <!-- 头部导航 -->
    <header class="header">
        <div class="back-btn" onclick="window.history.back();">
            <svg viewBox="0 0 24 24"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
        </div>
        <h1 class="header-title"><?php echo $title_str;?>详情</h1>
        <div class="header-right"></div>
    </header>

    <!-- 主内容 -->
    <div class="main-content">
        <!-- 用户信息 -->
        <div class="user-info">
            <div class="user-avatar">
                <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-original="<?php echo $picsArray[0] ?? ''; ?>" class="lazy" onerror="this.src='upload/default.jpg';" alt="头像">
            </div>
            <div class="user-details">
                <div class="user-name">
                    <?php echo htmlspecialchars($infos['uname'] ?? ''); ?>
                    <?php if ($infos['isrz']==1): ?>
                    <span class="verified-badge">已认证</span>
                    <?php endif; ?>
                </div>
                <div class="user-views">阅读数：<?php echo $infos['times'] ?? 0; ?></div>
            </div>
            <a class="message-btn" href="message.php?to_uid=<?php echo $infos['uid']; ?>">私信</a>
        </div>


    <div class="profile-card">
        <div class="profile-header">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
            <h2>个人资料</h2>
        </div>

        <div class="profile-content">
            <!-- 年龄使用树苗图标 -->
            <div class="profile-item">
                <!-- 更换年龄图标为树苗样式 -->
              <svg t="1766300122360" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="8730" width="32" height="32"><path d="M909.341189 172.223026l-2.54677-19.543295-21.078581-3.449879a456.9374 456.9374 0 0 0-60.65285-3.829185c-41.868167 0-104.254987 5.418658-163.480923 31.337907-45.805726 20.030974-86.102482 66.793996-119.788475 138.952464a562.456741 562.456741 0 0 0-9.482652 21.457887 300.302048 300.302048 0 0 0-22.324873-48.099624 241.14836 241.14836 0 0 0-78.949852-82.598416 381.112307 381.112307 0 0 0-203.958302-59.894238 285.111742 285.111742 0 0 0-61.285027 5.888276l-18.730495 4.587797-1.770095 18.405376c0 0.487679-4.208491 41.832043 5.563156 95.061331 5.707654 30.669606 14.901311 59.117563 27.508722 84.603319a239.631136 239.631136 0 0 0 63.958231 81.06313c37.605489 30.326425 77.468753 52.759671 118.542183 66.541125 30.669606 10.259327 57.166846 19.073678 91.033461 19.073677 7.279064 0 14.034325 0.343182 20.319969 0.523804 9.392341 0.379306 17.827386 0.812799 25.449633 0.090311v341.664474h60.038735V449.152594l4.461362 0.722488c27.65322 4.316865 56.20955 8.669853 80.358703 8.669853 88.739562 0 157.430088-25.395446 204.1028-75.463849 78.570547-84.476884 63.488614-205.83677 62.766126-210.912247" p-id="8731" fill="#ff6b9d"></path></svg>
                <span class="profile-item-label">年龄</span>
                <span class="profile-item-separator">/</span>
                <span class="profile-item-value"><?php echo htmlspecialchars($infos['age'] ?? ''); ?>岁</span>
            </div>

            <!-- 身高使用简洁的上下箭头图标 -->
            <div class="profile-item">
               <svg t="1766300408899" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="11641" id="mx_n_1766300408901" width="32" height="32"><path d="M555.5901152 405.6984064c-26.48152747-29.5061472-64.33239573-46.44882347-103.8314688-46.44882347l-78.8169536 0c-39.49907307 0-77.3510336 16.94267627-103.84784213 46.44882347L142.13402453 547.2606752c-6.33958613 7.0796416-6.03941653 17.90758507 0.6199872 24.61501653l43.23209494 43.48860374c3.49070187 3.49616 8.11222613 5.5493216 13.143072 5.209856 4.91077867-0.16263787 9.53121067-2.35551467 12.75339733-6.08307734l91.10972587-104.88697493 0 462.33901867c0 9.88486507 7.93867307 17.87374827 17.7329408 17.87374826l61.12549013 0c9.7746208 0 17.72857493-7.9899744 17.72857493-17.87374826L399.57930773 702.6274816l25.63013654 0 0 269.31672747c0 9.88486507 7.9343072 17.87374827 17.72857493 17.87374826l61.07309653 0c9.7942688 0 17.7111104-7.9899744 17.67509014-17.87374826L521.68620693 509.62156373l91.13155734 104.87060267c3.25820693 3.72756373 7.90046933 5.92044053 12.80469866 6.08307733 4.7481408 0.37111893 9.65346133-1.73006933 13.10705174-5.209856l43.1840672-43.48860373c6.71288853-6.72380373 6.94429227-17.53537493 0.6003392-24.61501653L555.5901152 405.6984064zM875.5733248 35.2473728l-91.34549653 0c-10.97530027 0-19.88652373 8.9079488-19.88652374 19.92800107 0 10.97748267 8.89375893 19.90507947 19.88652374 19.90507946l71.44150826 0 0 188.86575787L807.110592 263.9462112c-11.0145952 0-19.88543147 8.9308704-19.88543147 19.92690987 0 10.97857493 8.87083627 19.90944533 19.88543147 19.90944533l48.55765333 0 0 188.84392747L807.110592 492.62649387c-11.0145952 0-19.88543147 8.91231467-19.88543147 19.90944533 0 10.99603947 8.87083627 19.92145173 19.88543147 19.92145173l48.55765333 0 0 188.8297376L807.110592 721.2871296c-11.0145952 0-19.88543147 8.8653792-19.88543147 19.90507947 0 11.0375168 8.87083627 19.92690987 19.88543147 19.92690986l48.55765333 0 0 188.86575787-71.44150826 0c-10.9927648 0-19.88652373 8.9068576-19.88652374 19.90398827 0 11.02005227 8.89375893 19.92690987 19.88652374 19.92690986l91.32693973 0c10.97420907 0 19.84504533-8.9079488 19.86360107-19.92690986L895.41727787 55.15245227C895.41727787 44.1553216 886.5475328 35.2473728 875.5733248 35.2473728zM412.36654293 333.4371616c54.69639893 0 99.22522667-44.84537067 99.22522667-99.93035307 0-55.0849824-44.52882773-99.90961387-99.22522667-99.90961386-54.7127712 0-99.221952 44.82463147-99.221952 99.90961386C313.14458987 288.6081632 357.65377067 333.4371616 412.36654293 333.4371616z" p-id="11642" fill="#ff6b9d"></path></svg>
                <span class="profile-item-label">身高</span>
                <span class="profile-item-separator">/</span>
                <span class="profile-item-value"><?php echo htmlspecialchars($infos['sg'] ?? ''); ?>cm</span>
            </div>

            <!-- 体重使用简洁的哑铃图标 -->
            <div class="profile-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6.5 6.5L17.5 17.5"></path>
                    <path d="M6.5 17.5L17.5 6.5"></path>
                    <circle cx="6.5" cy="6.5" r="2.5"></circle>
                    <circle cx="17.5" cy="6.5" r="2.5"></circle>
                    <circle cx="6.5" cy="17.5" r="2.5"></circle>
                    <circle cx="17.5" cy="17.5" r="2.5"></circle>
                </svg>
                <span class="profile-item-label">体重</span>
                <span class="profile-item-separator">/</span>
                <span class="profile-item-value"><?php echo htmlspecialchars($infos['tz'] ?? ''); ?>kg</span>
            </div>

            <!-- 学历使用简洁的毕业帽图标 -->
            <div class="profile-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                <span class="profile-item-label">学历</span>
                <span class="profile-item-separator">/</span>
                <span class="profile-item-value"><?php echo xl($infos['xl'] ?? ''); ?></span>
            </div>

            <!-- 爱好使用简洁的音乐符号图标 -->
            <div class="profile-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 18V5l12-2v13"></path>
                    <circle cx="6" cy="18" r="3"></circle>
                    <circle cx="18" cy="16" r="3"></circle>
                </svg>
                <span class="profile-item-label">爱好</span>
                <span class="profile-item-separator">/</span>
                <span class="profile-item-value"><?php echo htmlspecialchars($infos['aihao'] ?? ''); ?></span>
            </div>

            <!-- 职业使用简洁的书本图标 -->
            <div class="profile-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                <span class="profile-item-label">职业</span>
                <span class="profile-item-separator">/</span>
                <span class="profile-item-value"><?php echo htmlspecialchars($infos['zy'] ?? ''); ?></span>
            </div>

            <!-- 籍贯使用简洁的地球图标 -->
            <div class="profile-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="2" y1="12" x2="22" y2="12"></line>
                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                </svg>
                <span class="profile-item-label">籍贯</span>
                <span class="profile-item-separator">/</span>
                <span class="profile-item-value"><?php echo htmlspecialchars($infos['jg'] ?? ''); ?></span>
            </div>

            <!-- 位置使用简洁的地图定位图标 -->
            <div class="profile-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                </svg>
                <span class="profile-item-label">位置</span>
                <span class="profile-item-separator">/</span>
                <span class="profile-item-value"><?php echo htmlspecialchars($infos['city'] ?? ''); ?></span>
            </div>

            <!-- 价格使用简洁的对话气泡图标 -->
            <div class="profile-item highlight">
               <svg t="1766303082946" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="33077" width="32" height="32"><path d="M512.140096 815.25284a38.631024 38.631024 0 0 1-38.631024-38.631024v-97.322254h-136.372169a38.537937 38.537937 0 1 1 0-77.122418h136.372169V516.21148h-136.372169a38.584481 38.584481 0 1 1 0-77.122418h130.78696l-131.904002-131.717829a38.584481 38.584481 0 0 1 54.595411-54.455781l121.47828 121.33865 121.478281-121.385193a38.537937 38.537937 0 1 1 54.45578 54.548868l-131.671285 131.717828h130.786961a38.584481 38.584481 0 1 1 0 77.122418h-136.372169v86.012208h136.372169a38.537937 38.537937 0 0 1 0 77.122418h-136.372169v97.322254a38.631024 38.631024 0 0 1-38.631024 38.584481zM512.000465 1024C228.737318 1023.488023-0.488938 793.423984 0.023039 510.160837v-4.468166C0.814277 437.320413 15.382362 369.785936 42.842969 307.231603A508.486671 508.486671 0 0 1 325.221792 35.604307a508.998649 508.998649 0 0 1 391.848904 7.493487 38.537937 38.537937 0 1 1-30.904819 70.652885A435.041182 435.041182 0 0 0 113.588941 338.136422a431.17808 431.17808 0 0 0-6.143729 332.459524 431.922774 431.922774 0 0 0 230.669102 239.698522 434.948095 434.948095 0 0 0 572.576936-224.3392 435.83242 435.83242 0 0 0-73.398945-462.687963 38.537937 38.537937 0 0 1 3.258038-54.455781 38.677567 38.677567 0 0 1 54.455781 3.211495 513.141012 513.141012 0 0 1 86.384555 544.697438 508.393584 508.393584 0 0 1-282.332279 271.67384c-59.575555 23.504418-123.014213 35.559159-187.057935 35.605703z" fill="#ff6b9d" p-id="33078"></path></svg>
                <span class="profile-item-label">价格</span>
                <span class="profile-item-separator">/</span>
                <span class="profile-item-value"><?php echo htmlspecialchars($infos['price'] ?? '面议'); ?></span>
            </div>
        </div>
    </div>
    

<div style="float:left;background:#f8f9fa;height:10px;width:100%;">&nbsp;</div>
        

        <!-- 详细介绍区块 -->
        <div class="detail-section">
            <div class="section-header">
                <h2>详细介绍</h2>
            </div>
            <div class="detail-content">
                <?php echo nl2br(htmlspecialchars($infos['content'] ?? '暂无详细介绍')); ?>
            </div>
            <?php if (!empty($picsArray) || !empty($videosArray)): ?>
            <div class="detail-images">
                <?php
                $mediaIndex = 0;
                // 先显示视频
                if (!empty($videosArray)):
                    foreach ($videosArray as $video):
                        if (empty($video)) continue;
                        $videoSrc = $video;
                ?>
                <div class="video-item" data-index="<?php echo $mediaIndex; ?>" onclick="openGallery(<?php echo $mediaIndex; ?>)">
                    <video src="<?php echo $videoSrc; ?>" muted></video>
                    <div class="play-icon">
                        <svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                </div>
                <?php
                        $mediaIndex++;
                    endforeach;
                endif;
                
                // 再显示图片
                if (!empty($picsArray)):
                    foreach ($picsArray as $pic):
                        if (empty($pic)) continue;
                        $picSrc = $pic;
                ?>
                <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-original="<?php echo $picSrc; ?>" alt="详情图片" onerror="this.src='upload/default_avatar.png';" data-index="<?php echo $mediaIndex; ?>" class="lazy" onclick="openGallery(<?php echo $mediaIndex; ?>)">
                <?php
                        $mediaIndex++;
                    endforeach;
                endif;
                ?>
            </div>
            <?php endif; ?>
        </div>
<?php if (!isset($infos['isopen']) ||  $infos['isopen'] ==0){ ?>
        <!-- 联系方式区域 -->
        <div style="width:100%;float:left;background:#fff;height:auto;margin-top:10px;">
        <div class="contact-section">
            <h2 class="contact-title">联系方式</h2>
            
            
             <!--如果已解锁的提示-->
             <!--如果是体验会员或高级会员的提示-->        
            <div class="vip-prompt-message" id="vip-see-message">
                    <?php echo $vip_msgster;?>
            </div>

            <input type="hidden" id="isUnlocked" value="<?php echo $is_js; ?>">
            <div id="contactButtons" class="contact-buttons">
                
                <!-- 已解锁状态 -->
                <div class="contact-btn contact-btn-secondary" id="viewContactBtn" onclick="loadContact()" <?php if ($is_js != 1){ echo 'style="display:none;"';} ?>>
                    <svg t="1765276236577" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="58756" width="16" height="16" style="margin-right:5px;"><path d="M512 1024a512 512 0 1 1 512-512 512 512 0 0 1-512 512z m174.933333-361.941333a166.826667 166.826667 0 0 0-59.733333-55.274667 20.458667 20.458667 0 0 0-20.053333-1.258667 365.461333 365.461333 0 0 1-34.986667 13.674667c-11.733333 3.712-24.106667 6.016-36.266667 8.533333a8.192 8.192 0 0 1-5.973333-1.728c-4.693333-4.074667-9.386667-8.405333-13.653333-12.8a331.370667 331.370667 0 0 1-70.186667-104.085333 203.818667 203.818667 0 0 1-16.426667-53.333333 16.192 16.192 0 0 1 4.266667-14.4 143.509333 143.509333 0 0 1 15.786667-16.021334c12.373333-9.984 25.386667-19.370667 38.4-28.757333a18.517333 18.517333 0 0 0 8.96-12.224 104.341333 104.341333 0 0 0-3.626667-51.2 159.424 159.424 0 0 0-52.693333-75.413333c-1.92-0.469333-3.626667-0.96-5.546667-1.429334-7.893333-0.170667-15.786667-0.597333-23.893333-0.448a118.485333 118.485333 0 0 0-68.053334 18.730667 173.013333 173.013333 0 0 0-21.12 20.181333l-1.92 7.104c0.426667 6.4 1.066667 12.970667 1.493334 19.477334a692.714667 692.714667 0 0 0 38.613333 188.202666 501.333333 501.333333 0 0 0 123.093333 192 571.434667 571.434667 0 0 0 119.466667 85.632 7.466667 7.466667 0 0 1 0.853333 0.938667c1.28 0.298667 2.346667 0.618667 3.626667 0.938667a95.082667 95.082667 0 0 0 54.4-25.834667 146.112 146.112 0 0 0 37.333333-49.066667 33.92 33.92 0 0 1 3.2-5.525333c0.426667-2.133333 1.066667-4.138667 1.493334-6.208a103.125333 103.125333 0 0 0-16.853334-40.405333z" p-id="58757" fill="#ffffff"></path></svg>
                    查看联系方式
                </div>
              
                <!-- 未解锁状态 -->
                <div class="contact-btn contact-btn-secondary" id="memberContactBtn" onclick="unlockByVip(<?php echo $vipclass_user;?>)" <?php if ($is_js == 1){ echo 'style="display:none;"';} ?>>

                    <svg t="1765276236577" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="58756" width="16" height="16" style="margin-right:5px;"><path d="M512 1024a512 512 0 1 1 512-512 512 512 0 0 1-512 512z m174.933333-361.941333a166.826667 166.826667 0 0 0-59.733333-55.274667 20.458667 20.458667 0 0 0-20.053333-1.258667 365.461333 365.461333 0 0 1-34.986667 13.674667c-11.733333 3.712-24.106667 6.016-36.266667 8.533333a8.192 8.192 0 0 1-5.973333-1.728c-4.693333-4.074667-9.386667-8.405333-13.653333-12.8a331.370667 331.370667 0 0 1-70.186667-104.085333 203.818667 203.818667 0 0 1-16.426667-53.333333 16.192 16.192 0 0 1 4.266667-14.4 143.509333 143.509333 0 0 1 15.786667-16.021334c12.373333-9.984 25.386667-19.370667 38.4-28.757333a18.517333 18.517333 0 0 0 8.96-12.224 104.341333 104.341333 0 0 0-3.626667-51.2 159.424 159.424 0 0 0-52.693333-75.413333c-1.92-0.469333-3.626667-0.96-5.546667-1.429334-7.893333-0.170667-15.786667-0.597333-23.893333-0.448a118.485333 118.485333 0 0 0-68.053334 18.730667 173.013333 173.013333 0 0 0-21.12 20.181333l-1.92 7.104c0.426667 6.4 1.066667 12.970667 1.493334 19.477334a692.714667 692.714667 0 0 0 38.613333 188.202666 501.333333 501.333333 0 0 0 123.093333 192 571.434667 571.434667 0 0 0 119.466667 85.632 7.466667 7.466667 0 0 1 0.853333 0.938667c1.28 0.298667 2.346667 0.618667 3.626667 0.938667a95.082667 95.082667 0 0 0 54.4-25.834667 146.112 146.112 0 0 0 37.333333-49.066667 33.92 33.92 0 0 1 3.2-5.525333c0.426667-2.133333 1.066667-4.138667 1.493334-6.208a103.125333 103.125333 0 0 0-16.853334-40.405333z" p-id="58757" fill="#ffffff"></path></svg>
                       查看联系方式
                   
                </div>
                
            </div>
        </div>
         </div>
<?php } ?>
<style type="text/css">
    .modal-body{
        margin-bottom: 0!important;
    }
    
</style>
<?php if (isset($infos['isopen']) &&  $infos['isopen'] ==1){ 
            $contact_infos = $db3->table('byb')
            ->where($where)
            ->field('mobile, weixin, qq')
            ->find();


                include_once 'lib/opens.php';
              }
      ?>
        <!-- 底部操作栏 -->
        <div style="width:100%;float:left;background:#fff;height:auto;margin-top:10px;">
        <div class="action-buttons-fixed">
            <input type="hidden" id="isFavorite" value="<?php echo $is_sc; ?>">
            <div class="action-item <?php echo $is_sc == 1 ? 'active' : ''; ?>" id="favoriteBtn" onclick="toggleCollect()">
                <svg id="favoriteIcon" viewBox="0 0 24 24" fill="<?php echo $is_sc == 1 ? '#ff6b9d' : 'none'; ?>" stroke="<?php echo $is_sc == 1 ? '#ff6b9d' : '#999'; ?>" stroke-width="1.5">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
                <span id="favoriteText"><?php echo $is_sc == 1 ? '已收藏' : '收藏'; ?></span>
            </div>
            <div class="action-item" id="reportBtn" onclick="showReport()" style="margin-left:10px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="1.5">
                <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/>
                <line x1="4" y1="22" x2="4" y2="15"/>
            </svg>
                <span>举报</span>
            </div>
        </div>
         </div>
    
    </div>
<?php if (!isset($infos['isopen']) ||  $infos['isopen'] ==0){ ?>
    <!-- 联系方式弹窗 -->
    <div class="contact-modal" id="contactModal">
        <div class="modal-overlay" onclick="closeContactModal()"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3>联系方式</h3>
                <button class="modal-close" onclick="closeContactModal()">×</button>
            </div>
            <div class="modal-body">
                <div class="contact-info-list" id="contactList"></div>
            </div>
        </div>
    </div>
<?php } ?>
    <!-- 举报弹窗 -->
    <!-- 更新举报弹窗HTML结构，与highend_detail.php一致 -->
    <div class="report-modal" id="reportModal">
        <div class="modal-overlay" onclick="closeReport()"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3>举报信息</h3>
                <button class="modal-close" onclick="closeReport()">×</button>
            </div>
            <div class="modal-body">
                <div class="report-form">
                    <div class="report-description">
                        <label>举报内容：</label>
                        <textarea id="reportContent" placeholder="请填写举报内容" rows="4"></textarea>
                    </div>
                    <div class="captcha-row" style="transform: translateZ(0); 
            -webkit-transform: translateZ(0); ">
                        <input type="text" id="reportCaptcha" style="width:50%;" placeholder="请输入验证码">
                        <img id="captchaImg" src="/lib/yzmcode.php" alt="验证码" onclick="refreshCaptcha()">
                    </div>
                    <div class="report-submit">
                        <button class="cancel-btn" style="float: left;width: 45%;" onclick="closeReport()">取 消</button>
                        <button class="submit-btn" style="float: right;width: 45%;" onclick="submitReport()">提 交</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 相册弹窗 -->
    <div class="gallery-modal" id="galleryModal">
        <div class="gallery-header">
            <div class="gallery-counter" id="galleryCounter">1/1</div>
            <div class="gallery-close" onclick="closeGallery()">×</div>
        </div>
        <div class="gallery-content" id="galleryContent">
            <div class="gallery-nav gallery-prev" onclick="prevMedia()">
                
                   <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:25px;height:25px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                   
            </div>
            <div class="gallery-nav gallery-next" onclick="nextMedia()">
                
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:25px;height:25px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                
            </div>
        </div>
        <div class="gallery-thumbnails" id="galleryThumbnails"></div>
    </div>
    

<script>
        var by_id = <?php echo $by_id; ?>;
        var is_sc = <?php echo $is_sc; ?>;
        var is_js = <?php echo $is_js; ?>;
        var infotype = <?php echo $infotype; ?>;
        var mediaList = [
            <?php if (!empty($videosArray)) { ?>
            <?php  foreach ($videosArray as $video): if (!empty($video)): ?>
            {type: 'video', src: '<?php echo $video; ?>'},
            <?php endif; endforeach; ?>
            <?php } ?>
            <?php if (!empty($picsArray)) { ?>
            <?php foreach ($picsArray as $pic): if (!empty($pic)): ?>
            {type: 'image', src: '<?php echo $pic; ?>'},
            <?php endif; endforeach; ?>
            <?php } ?>
        ];
        var currentIndex = 0;


        // 加载联系方式
        function loadContact() {
            fetch('/oper/info/seeinfoby.html', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'info_id=' + by_id + '&type=3'
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.code == 1 || data.code == 0 || data.code == 200 || data.status == 'success') {
                    var info = data.data || data;
                    renderContactList(info);
                    showContactModal();
                } else {
                    showInfo(data.msg || '网络错误', '获取失败');
                }
            })
            .catch(function() {
                showInfo('网络错误');
            });
        }

        // 渲染联系方式列表
        function renderContactList(info) {
            var html = '';
            // 手机号
            html += '<div class="contact-info-item">';
            html += '<div class="contact-info-icon phone-icon"><svg viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg></div>';
            html += '<div class="contact-info-content">';
            if (info.mobile) {
                html += '<div class="contact-info-value">' + info.mobile + '</div>';
                html += '</div>';
                html += '<div class="contact-copy-btn copy-btn" data-clipboard-text="' + info.mobile + '"><svg viewBox="0 0 24 24"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/></svg>复制</div>';
            } else {
                html += '<div class="contact-info-value empty">暂无</div>';
                html += '</div>';
            }
            html += '</div>';
            
            // 微信
            html += '<div class="contact-info-item">';
            html += '<div class="contact-info-icon wechat-icon"><svg t="1765473969257" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="81652" width="48" height="48"><path d="M699.6 357.2c11.9 0 23.6 0.9 35.2 2.1-31.6-145.8-189.2-254.2-369-254.2C164.7 105.1 0 240.8 0 413c0 99.4 54.8 181 146.4 244.3l-36.6 108.9 127.9-63.5c45.8 8.9 82.5 18.2 128.1 18.2 11.5 0 22.8-0.6 34.1-1.5-7.2-24.2-11.3-49.5-11.3-75.8 0-158.1 137.2-286.4 311-286.4zM503 259c27.5 0 45.8 18 45.8 45.2 0 27.1-18.2 45.3-45.8 45.3-27.4 0-54.9-18.2-54.9-45.3C448 277 475.5 259 503 259z m-256 90.5c-27.4 0-55.1-18.2-55.1-45.3 0-27.2 27.7-45.2 55.1-45.2 27.4 0 45.7 17.9 45.7 45.2-0.1 27.1-18.3 45.3-45.7 45.3z" p-id="81653" fill="#ffffff"></path><path d="M1024 639.3c0 70-44.2 141.3-124.3 200.8l-5.4 4 22.8 74.7-83.3-45.1-3.6 0.9c-35.3 8.8-71.7 17.8-107.4 17.8-166.3 0-301.6-113.6-301.6-253.2S556.5 386 722.8 386c163.3 0.1 301.2 116 301.2 253.3zM621.9 594c27.7 0 45.8-17.9 45.8-36.2 0-18-18.1-36.2-45.8-36.2-18.2 0-36.6 18.1-36.6 36.2 0 18.3 18.4 36.2 36.6 36.2z m201.2 0c27.4 0 45.8-17.9 45.8-36.2 0-18-18.3-36.2-45.8-36.2-18.1 0-36.3 18.1-36.3 36.2 0 18.3 18.2 36.2 36.3 36.2z m0 0" p-id="81654" fill="#ffffff"></path></svg></div>';
            html += '<div class="contact-info-content">';
            if (info.weixin) {
                html += '<div class="contact-info-value">' + info.weixin + '</div>';
                html += '</div>';
                html += '<div class="contact-copy-btn copy-btn" data-clipboard-text="' + info.weixin + '"><svg viewBox="0 0 24 24"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/></svg>复制</div>';
            } else {
                html += '<div class="contact-info-value empty">暂无</div>';
                html += '</div>';
            }
            html += '</div>';
            
            // QQ
            html += '<div class="contact-info-item">';
            html += '<div class="contact-info-icon qq-icon"><svg viewBox="0 0 1024 1024"><path d="M824.8 613.2c-16-51.4-34.4-94.6-62.7-165.3C766.5 262.2 689.3 112 511.5 112 331.7 112 256.2 265.2 261 447.9c-28.4 70.8-46.7 113.7-62.7 165.3-34 109.5-23 154.8-14.6 155.8 18 2.2 70.1-82.4 70.1-82.4 0 49 25.2 112.9 79.8 159-26.4 8.1-85.7 29.9-71.6 53.8 11.4 19.3 196.2 12.3 249.5 6.3 53.3 6 238.1 13 249.5-6.3 14.1-23.8-45.3-45.7-71.6-53.8 54.6-46.2 79.8-110.1 79.8-159 0 0 52.1 84.6 70.1 82.4 8.5-1.1 19.5-46.4-14.5-155.8z"/></svg></div>';
            html += '<div class="contact-info-content">';
            if (info.qq) {
                html += '<div class="contact-info-value">' + info.qq + '</div>';
                html += '</div>';
                html += '<div class="contact-copy-btn copy-btn" data-clipboard-text="' + info.qq + '"><svg viewBox="0 0 24 24"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/></svg>复制</div>';
            } else {
                html += '<div class="contact-info-value empty">暂无</div>';
                html += '</div>';
            }
            html += '</div>';
            
            document.getElementById('contactList').innerHTML = html;
        }

        // 显示联系方式弹窗
        function showContactModal() {
            document.getElementById('contactModal').classList.add('show');
        }

        // 关闭联系方式弹窗
        function closeContactModal() {
            document.getElementById('contactModal').classList.remove('show');
        }

        
        // 使用 Clipboard.js 插件，兼容所有浏览器
        var clipboard = new ClipboardJS('.copy-btn');
        
        clipboard.on('success', function(e) {
            alert('复制成功！');
            e.clearSelection();
        });
        
        clipboard.on('error', function(e) {
            alert('复制失败，请手动复制');
        });

        

        // VIP解锁
        function unlockByVip(user_vip_class) {
            if (user_vip_class <3) {
                showInfo('解锁失败，需年度或永久Vip才可解锁！');
                return;
            }
            const isMoneySj = <?php echo $is_money_sj; ?>;
            viptype = 1;
            if (isMoneySj === 1) {
                if (confirm('当前解锁次数已用完，是否支付70元解锁？')) {
                    // User confirms, send request for paid unlock (type 4)
                     viptype = 4;
                }else{
                    return;
                }
            } else {
                if (confirm('是否支付50元解锁？')) {
                    // User confirms, send request for paid unlock (type 4)
                     viptype = 1;
                }else{
                    return;
                }
            }
            fetch('/oper/info/seeinfoby.html', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'info_id=' + by_id + '&type=' + viptype
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.code == 1 || data.code == 0 || data.code == 200 || data.status == 'success') {
                    is_js = 1;
                    var info = data.data || data;
                    showSuccess(data.msg || '操作成功！', '操作成功', 10000);
                    document.getElementById('viewContactBtn').style.display = 'flex';
                    document.getElementById('memberContactBtn').style.display = 'none';

                    renderContactList(info);
                    showContactModal();
                } else {
                    // alert(data.msg || '解锁失败，请确认您是VIP会员');
                    showInfo(data.msg || '解锁失败，请解锁会员');
                }
            })
            .catch(function() {   showInfo('网络错误'); });
        }

        

        // 收藏/取消收藏
        function toggleCollect() {
            fetch('/oper/info/user_collections.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'info_id=' + by_id + '&is_by=1&infotype='+infotype
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.code == 200 || data.status == 'success') {
                    is_sc = !is_sc;
                    var btn = document.getElementById('favoriteBtn');
                    var icon = document.getElementById('favoriteIcon');
                    var text = document.getElementById('favoriteText');
                    
                    if (is_sc) {
                        btn.classList.add('active');
                        icon.setAttribute('fill', '#ff6b9d');
                        icon.setAttribute('stroke', '#ff6b9d');
                        text.innerText = '已收藏';
                        showSuccess('收藏成功');
                    } else {
                        btn.classList.remove('active');
                        icon.setAttribute('fill', 'none');
                        icon.setAttribute('stroke', '#999');
                        text.innerText = '收藏';
                        showSuccess('取消收藏成功');
                    }
                }else{
                    showInfo(data.msg || '操作失败');
                }
            });
        }

        // 举报弹窗
        function showReport() {
            document.getElementById('reportModal').classList.add('show');
        }

        function closeReport() {
            document.getElementById('reportModal').classList.remove('show');
        }

        function submitReport() {
            var content = document.getElementById('reportContent').value.trim();
            var captcha = document.getElementById('reportCaptcha').value.trim();
            if (!content) {
                showInfo('请输入举报原因！');
                return;
            }
            if (!captcha) {
                showInfo('请输入验证码！');
                return;
            }
            fetch('/oper/info/inforeport.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'info_id=' + by_id + '&infotype=3&jb_msg=' + encodeURIComponent(content) + '&captcha=' + encodeURIComponent(captcha)
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.code == 1 || data.code == 0 || data.code == 200 || data.status == 'success') {
                    showSuccess('举报成功');
                    closeReport();
                } else {
                    // alert(data.msg || '举报失败');
                    showInfo(data.msg ||'举报失败');
                    refreshCaptcha();
                }
            });
        }

        function refreshCaptcha() {
            document.getElementById('captchaImg').src = '/lib/yzmcode.php?r=' + Math.random();
        }

        // 页面加载时刷新验证码
        document.addEventListener('DOMContentLoaded', function() {
            refreshCaptcha();
        });

        // 相册功能
        function openGallery(index) {
            currentIndex = index;
            renderGallery();
            document.getElementById('galleryModal').classList.add('show');
        }

        function closeGallery() {
            document.getElementById('galleryModal').classList.remove('show');
            var video = document.querySelector('#galleryContent video');
            if (video) video.pause();
        }

        function renderGallery() {
            var content = document.getElementById('galleryContent');
            var thumbs = document.getElementById('galleryThumbnails');
            var counter = document.getElementById('galleryCounter');
            var item = mediaList[currentIndex];
            
            // 保留导航按钮
            var navButtons = '<button class="gallery-nav gallery-prev" onclick="prevMedia()">‹</button><button class="gallery-nav gallery-next" onclick="nextMedia()">›</button>';
            
            if (item.type === 'video') {
                content.innerHTML = '<video src="' + item.src + '" controls autoplay style="max-width:100%;max-height:100%;"></video>' + navButtons;
            } else {
                content.innerHTML = '<img src="' + item.src + '" style="max-width:100%;max-height:100%;" onerror="this.src='+ "'upload/default_avatar.png';"+'" >' + navButtons;
            }
            
            counter.innerText = (currentIndex + 1) + '/' + mediaList.length;
            
            var thumbHtml = '';
            for (var i = 0; i < mediaList.length; i++) {
                var m = mediaList[i];
                var activeClass = i === currentIndex ? 'active' : '';
                if (m.type === 'video') {
                    thumbHtml += '<div class="gallery-thumb ' + activeClass + '" onclick="switchGallery(' + i + ')"><video src="' + m.src + '" muted></video></div>';
                } else {
                    thumbHtml += '<div class="gallery-thumb ' + activeClass + '" onclick="switchGallery(' + i + ')"><img src="' + m.src + '" onerror="this.onerror=null;this.src=\'/upload/default.png\';"></div>';
                }
            }
            thumbs.innerHTML = thumbHtml;
        }

        function switchGallery(index) {
            var video = document.querySelector('#galleryContent video');
            if (video) video.pause();
            currentIndex = index;
            renderGallery();
        }

        function prevMedia() {
            if (currentIndex > 0) {
                switchGallery(currentIndex - 1);
            }
        }

        function nextMedia() {
            if (currentIndex < mediaList.length - 1) {
                switchGallery(currentIndex + 1);
            }
        }

        document.addEventListener('keydown', function(e) {
            if (!document.getElementById('galleryModal').classList.contains('show')) return;
            if (e.key === 'ArrowLeft') {
                prevMedia();
            } else if (e.key === 'ArrowRight') {
                nextMedia();
            } else if (e.key === 'Escape') {
                closeGallery();
            }
        });
</script>
<?php include_once 'comm/alert_modal.php'; ?>
</body>
</html>
