<?php 
require_once 'loaduser.php';
include_once 'config.php';

$page_title = "高端详情";
$infotype = 2;

$gd_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
//查询信息是否存在。
$where = [];
$where['id'] = $gd_id;
$infosnum = db3('gdb')->where($where)->count();
if ($infosnum  <= 0) {
    echo "<script > window.location.href = '/404.html';</script>";
    die;
}

$where = [];
$where['id'] = $gd_id;
$where['flag'] = 1;

$infos = db3('gdb')
    ->where($where)
    ->field('id, uid, uname, city, age, times, pics, bdpics,videos,bdvideos, zy, age, sg, tz,xl, price, content,isrz,fbtime,isopen')
    ->find();
// var_dump($infos);die;
if (empty($infos)) {
    echo "<script > window.location.href = '/404.html';</script>";
    die;
}
// 增加浏览量
db3('gdb')->where(['id' => $gd_id])->inc('times', 1);

$picsArrays =z_imgurl_arr($infos['bdpics'],$infos['pics'],2);
$infos['pic']  = $picsArrays['img'];
$picsArray  =$picsArrays['arr'];

$videosArrays =z_imgurl_arr($infos['bdvideos'],$infos['videos'],2);
$videosArray  =$videosArrays['arr'];

$mediaArray = [];

foreach ($videosArray as $video) {
    $mediaArray[] = ['type' => 'video', 'url' => $video];
}
foreach ($picsArray as $pic) {
    $mediaArray[] = ['type' => 'image', 'url' => $pic];
}
$mediaCount = count($mediaArray);

// 是否收藏
$existingCollection = db('usersc')->field('id,status')
    ->where(['user_id' => $userData['userId'], 'info_id' => $gd_id,'infotype'=> $infotype])
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
      ->where(['user_id' => $userData['userId'], 'info_id' => $gd_id, 'infotype' => $infotype])
      ->count();

  if ($isUnlocked > 0) {
      $is_js = 1;
  }
}


$vipclass_user = $userInfo['vipclass'] ?? 0;
// var_dump($vipclass_user);
$vip_msgster  ='高端区需要季度会员或以上才能解锁联系方式';
if($is_js == 1){
    $vip_msgster ='该信息你已解锁，可直接查看联系方式：';
}else{
    if ($vipclass_user >=2) {
        $vip_msgster ='包养和伴游需要年度会员或以上才能解锁联系方式';
    }elseif($vipclass_user>0 && $vipclass_user < 2){
        $vip_msgster ='包养和伴游需要年度会员或以上才能解锁联系方式.';
    }
}
if($is_js == 3){
    $is_js = 1;
    $vip_msgster ='该信息免费解锁，可直接查看联系方式：';
}
$is_money_sj = 0;
//如果没有解锁
if($is_js != 1){
    //用户是否是vip
    $vipclass_user = $userInfo['vipclass'] ?? 0;

    if ($vipclass_user > 1) {
        // 查询用户总查看次数
        $vipconfig = db('fl_vip_config')->field('id,see_nums')->where(['id' => $vipclass_user])->find();
        $user_all_num = $vipconfig['see_nums'] ?? 0;
        //用户次数是否用尽。
        if($user_all_num - $userInfo['ckcs'] <=0){
            $is_money_sj = 1;
        }
    }
}

$pageTitle = "高端详情"; 

$Area=$city_name;
$Area = str_replace("市", "", $Area);
$zy=zy($infos['zy']);
$cityweb=$Area.$webname;
$webtitle=$Area.$webname.'_'.$zy.'_'.$infos['uname'];
$keywords=$Area.$webname.','.$Area.'楼凤网,'.$Area.'楼凤信息,'.$zy.'_'.$infos['uname'];
$description=$Area.$webname.','.$Area.'外围模特,'.$Area.'外围小姐,'.$zy.'_'.$infos['uname'];

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title><?php echo $webtitle; ?></title>
<meta name="author" content="<?php echo $webname; ?>" />
<meta name="keywords" content="<?php echo $keywords; ?>">
<meta name="description" content="<?php echo $description; ?>">
<link href="/favicon.ico" rel="shortcut icon"/>
<link rel="stylesheet" href="/css/comm.css">
<link rel="stylesheet" href="/css/gd_detail.css?t=123.5678">
</head>
<body>
  <?php include 'comm/header.php'; ?>
  <!-- 主内容 -->
  <main class="main-content">
    <!-- 用户信息卡片 -->
    <div class="user-card">
      <button class="message-btn"  onclick="messages(<?php echo $userInfo['vipclass'];?>)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
        发私信
      </button>

      <div class="user-info">
        <img src="<?php echo $infos['pic']; ?>" alt="用户头像" class="user-avatar">
        <div class="user-details">
          <h2 class="user-nickname"><?php echo $infos['uname']; ?></h2>
          <div class="user-badges">
            <?php if ($infos['isrz']  == 1) { ?>
             <span class="badge">✓ 已认证</span>
            <?php } ?>
             <?php if ($infos['isrz']  == 0) { ?>
             <span class="badge">✓ 未认证</span>
            <?php } ?>
            <!-- <span class="badge">VIP会员</span> -->
          </div>
        </div>
      </div>

    </div>

    <!-- 详细信息 -->
    <div class="detail-card">
      <h3 class="detail-title">
        详细信息
      </h3>
      <div class="detail-list">

        <div class="detail-item">
          <div class="detail-item-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="4" y1="21" x2="4" y2="14"></line>
              <line x1="4" y1="10" x2="4" y2="3"></line>
              <line x1="12" y1="21" x2="12" y2="12"></line>
              <line x1="12" y1="8" x2="12" y2="3"></line>
              <line x1="20" y1="21" x2="20" y2="16"></line>
              <line x1="20" y1="12" x2="20" y2="3"></line>
              <line x1="2" y1="14" x2="6" y2="14"></line>
              <line x1="10" y1="8" x2="14" y2="8"></line>
              <line x1="18" y1="16" x2="22" y2="16"></line>
            </svg>
          </div>
          <div class="detail-item-content">
            <div class="detail-item-label">身高</div>
            <div class="detail-item-value"><?php echo $infos['sg']; ?>Cm</div>
          </div>
        </div>

        <div class="detail-item">
          <div class="detail-item-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"></path>
              <path d="M12 6v6l4 2"></path>
            </svg>
          </div>
          <div class="detail-item-content">
            <div class="detail-item-label">体重</div>
            <div class="detail-item-value"><?php echo $infos['tz']; ?>Kg</div>
          </div>
        </div>

        <div class="detail-item">
          <div class="detail-item-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
              <line x1="16" y1="2" x2="16" y2="6"></line>
              <line x1="8" y1="2" x2="8" y2="6"></line>
              <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
          </div>
          <div class="detail-item-content">
            <div class="detail-item-label">年龄</div>
            <div class="detail-item-value"><?php echo $infos['age']; ?>岁</div>
          </div>
        </div>

        <div class="detail-item">
          <div class="detail-item-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="10" r="3"></circle>
              <path d="M12 21.7C17.3 17 20 13 20 10a8 8 0 1 0-16 0c0 3 2.7 6.9 8 11.7z"></path>
            </svg>
          </div>
          <div class="detail-item-content">
            <div class="detail-item-label">所在地区</div>
            <div class="detail-item-value"><?php echo $infos['city']; ?></div>
          </div>
        </div>

        <div class="detail-item">
          <div class="detail-item-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
              <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
            </svg>
          </div>
          <div class="detail-item-content">
            <div class="detail-item-label">职业</div>
            <div class="detail-item-value"><?php echo $zyArr[$infos['zy']]; ?></div>
          </div>
        </div>

        <div class="detail-item">
          <div class="detail-item-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
              <path d="M6 12v5c0 2 2 3 6 3s6-1 6-3v-5"></path>
            </svg>
          </div>
          <div class="detail-item-content">
            <div class="detail-item-label">学历</div>
            <div class="detail-item-value"><?php echo $xlArr[$infos['xl']]; ?></div>
          </div>
        </div>

        <div class="detail-item">
          <div class="detail-item-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="12" y1="1" x2="12" y2="23"></line>
              <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
            </svg>
          </div>
          <div class="detail-item-content">
            <div class="detail-item-label">服务价格</div>
            <div class="price-value"><?php echo $infos['price']; ?></div>
          </div>
        </div>
      </div>
    </div>

    <!-- 个人相册 -->
    <div class="album-section">
      <h3 class="detail-title">
        个人相册
      </h3>
      
<div class="album-grid">

    <?php if ($mediaCount > 0): ?>
        <?php
        // 将图片排在视频前面，同类型保持原顺序
        usort($mediaArray, function($a, $b) {
            if ($a['type'] === 'image' && $b['type'] === 'video') {
                return -1; // a 在前
            }
            if ($a['type'] === 'video' && $b['type'] === 'image') {
                return 1;  // b 在前
            }
            return 0; // 同类型保持原有顺序
        });
        ?>
        <?php foreach ($mediaArray as $index => $media): ?>
            <div class="album-item" onclick="openLightbox(<?php echo $index; ?>)">

                <?php if ($media['type'] === 'image'): ?>
                    <img src="<?php echo $media['url']; ?>" alt="照片<?php echo $index + 1; ?>">
                <?php else: ?>
                    <video src="<?php echo $media['url']; ?>" preload="metadata"></video>
                    <div class="video-indicator">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <polygon points="5 3 19 12 5 21 5 3"></polygon>
                        </svg>
                    </div>
                <?php endif; ?>

            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

        <?php include_once 'comm/jb.php'; ?>
    </div>
<?php if (!isset($infos['isopen']) ||  $infos['isopen'] ==0){ ?>
     <!-- 操作按钮 -->
       <?php include_once 'comm/detail_collections.php'; ?>
<?php } ?> 
    <!-- 详情介绍 -->
    <!--
    <div class="description-section">
      <h3 class="description-title">详情介绍</h3>
      <div class="description-content">
        <p>这里是详细的介绍内容，可以包含服务介绍、个人特点、注意事项等详细信息。内容可以很长，支持多段落展示。</p>
        <p>这是第二段介绍内容，可以根据实际需求添加更多信息。文字内容支持换行和多段落排版，方便用户阅读。</p>
        <p>可以添加更多段落来描述服务项目、个人特色、服务范围等重要信息，让用户更全面地了解服务内容。</p>
      </div>
    </div>
    -->

<?php if (isset($infos['isopen']) &&  $infos['isopen'] ==1){ 
      $contact_infos= db3('gdb')
        ->where($where)
        ->field('mobile, weixin, qq')
        ->find();
      include_once 'lib/opens.php'; 
    }
?>
    



<?php if (!isset($infos['isopen']) ||  $infos['isopen'] ==0){ ?>
    <!-- 联系方式按钮区域 -->
    <div class="contact-section">
      <h3 class="contact-title">联系方式</h3>
      
      <!-- 联系方式说明 -->
      <div class="contact-notice">
        <div class="contact-notice-content">
          <div class="contact-notice-icon">
<svg t="1773988673635" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="83370" width="32" height="32"><path d="M193.472 161.216c-14.112-12.096-34.272-12.096-48.384 0-14.112 14.112-14.112 34.272 0 48.384l48.384 48.384c14.112 14.112 34.272 14.112 48.384 0s14.112-34.272 0-48.384L193.472 161.216z m637.056 0L784.16 209.6c-14.112 14.112-14.112 34.272 0 48.384s34.272 14.112 48.384 0l48.384-48.384c14.112-14.112 14.112-34.272 0-48.384-16.128-12.096-36.288-12.096-50.4 0zM108.8 427.328H42.272c-18.144 0-34.272 14.112-34.272 34.272 0 18.144 14.112 34.272 34.272 34.272H108.8c18.144 0 34.272-14.112 34.272-34.272 0-18.144-16.128-34.272-34.272-34.272z m872.928 0H915.2c-18.144 0-34.272 14.112-34.272 34.272 0 18.144 14.112 34.272 34.272 34.272h66.528c18.144 0 34.272-14.112 34.272-34.272 0-18.144-14.112-34.272-34.272-34.272z m-504-385.056V108.8c0 18.144 14.112 34.272 34.272 34.272 18.144 0 34.272-14.112 34.272-34.272V42.272c0-20.16-16.128-34.272-34.272-34.272-18.144 0-34.272 14.112-34.272 34.272zM276.128 512c0-129.024 104.832-235.872 235.872-235.872S747.872 382.976 747.872 512 641.024 747.872 512 747.872 276.128 641.024 276.128 512zM209.6 512c0 167.328 135.072 302.4 302.4 302.4s302.4-135.072 302.4-302.4-135.072-302.4-302.4-302.4-302.4 135.072-302.4 302.4z m235.872 469.728c0 18.144 14.112 34.272 34.272 34.272h66.528c18.144 0 34.272-14.112 34.272-34.272s-14.112-34.272-34.272-34.272h-66.528c-20.16 2.016-34.272 16.128-34.272 34.272z m-68.544-100.8c0 18.144 14.112 34.272 34.272 34.272h201.6c18.144 0 34.272-14.112 34.272-34.272 0-18.144-14.112-34.272-34.272-34.272H411.2c-18.144 2.016-34.272 16.128-34.272 34.272z" p-id="83371" fill="#ff5e7b"></path></svg>
          </div>
          <div class="contact-notice-text">
            <p><?php echo $vip_msgster;?></p>

          </div>
        </div>
      </div>
      
      <!-- 联系方式按钮 -->
      <div class="contact-buttons">
        <button class="contact-btn" id="detailContactBtn">
      <svg t="1773995252848" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="93446" width="32" height="32"><path d="M512 1024C229.233778 1024 0 794.766222 0 512S229.233778 0 512 0s512 229.233778 512 512-229.233778 512-512 512z m284.444444-595.228444a72.490667 72.490667 0 0 0-20.807111-50.432 71.253333 71.253333 0 0 0-100.892444 0 66.816 66.816 0 0 0-20.807111 50.432 68.707556 68.707556 0 0 0 20.807111 50.432c2.474667 3.015111 5.461333 5.575111 8.817778 7.566222a78.833778 78.833778 0 0 1-93.312 13.866666 104.661333 104.661333 0 0 1-49.180445-58.638222 189.084444 189.084444 0 0 1 5.034667-51.057778 71.864889 71.864889 0 0 0 37.205333-63.047111 70.599111 70.599111 0 0 0-20.807111-50.432 70.001778 70.001778 0 0 0-100.892444 0 71.864889 71.864889 0 0 0-20.807111 50.432 72.490667 72.490667 0 0 0 20.807111 50.446223c5.632 5.304889 12.003556 9.770667 18.915555 13.226666a131.100444 131.100444 0 0 1 3.797334 48.554667 105.287111 105.287111 0 0 1-49.820445 59.875555 81.351111 81.351111 0 0 1-95.217778-14.492444 71.864889 71.864889 0 0 0 30.264889-58.624 71.253333 71.253333 0 0 0-70.613333-69.347556 72.533333 72.533333 0 0 0-50.446222 20.807112 71.224889 71.224889 0 0 0 0 100.864 70.627556 70.627556 0 0 0 30.890666 18.275555l59.278223 247.125333a28.373333 28.373333 0 0 0 27.107555 21.432889H658.346667a27.733333 27.733333 0 0 0 27.121777-21.432889l59.264-247.125333A71.864889 71.864889 0 0 0 796.444444 428.771556z" fill="#ffffff" p-id="93447"></path></svg>
          
            <?php if ($is_js == 1) { ?>
              <span id="detailContactspan">查看她的联系方式</span>
            <?php }else{ ?>
              <span id="detailContactspan">解锁她的联系方式</span>
            <?php } ?>
          
        </button>
        
      </div>
    </div>
<?php } ?> 
  </main>
  
  
 <?php // include_once 'comm/album.php'; ?>
 
<style>
  /* Lightbox Modal */
.lightbox-modal {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.95);
  z-index: 10000;
  align-items: center;
  justify-content: center;
}

.lightbox-modal.active {
  display: flex;
}

.lightbox-content {
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}

.lightbox-image {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
  cursor: default;
}

.lightbox-video {
  max-width: 90%;
  max-height: 90%;
  object-fit: contain;
  cursor: default;
}

.lightbox-close{
    position: fixed;
    top: 20px;
    right: 20px;
    background: rgba(0, 0, 0, 0.7);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    color: rgba(255, 255, 255, 0.7);
    font-size: 24px;
    cursor: pointer;
    padding: 0;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    z-index: 10001;
}


.lightbox-close:active {
  color: #fff;
  transform: rotate(90deg);
}

.lightbox-nav {
  position: fixed;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: #fff;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  z-index: 10001;
}

.lightbox-nav:active {
  background: rgba(255, 255, 255, 0.2);
  transform: translateY(-50%) scale(0.95);
}

.lightbox-prev {
  left: 10px;
}

.lightbox-next {
  right: 10px;
}

.lightbox-counter {
  position: fixed;
  bottom: 30px;
  left: 50%;
  transform: translateX(-50%);
  background: rgba(0, 0, 0, 0.7);
  backdrop-filter: blur(10px);
  color: #fff;
  padding: 8px 20px;
  border-radius: 20px;
  font-size: 14px;
  z-index: 10001;
}

</style>
 

<div class="lightbox-modal" id="lightbox" onclick="if(event.target === this) closeLightbox()">
  <!-- 灯箱内容区域，支持图片和视频 -->
  <div id="lightboxContent" class="lightbox-content">
    <img src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" alt="照片" class="lightbox-image" id="lightboxImage" style="display: none;">
    <video src="" class="lightbox-video" id="lightboxVideo" controls style="display: none;"></video>
  </div>
  
  <div class="lightbox-close" onclick="closeLightbox()">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <line x1="18" y1="6" x2="6" y2="18"></line>
      <line x1="6" y1="6" x2="18" y2="18"></line>
    </svg>
  </div>

  <div class="lightbox-nav lightbox-prev" onclick="navigateLightbox(-1)">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M15 18l-6-6 6-6"/>
    </svg>
  </div>

  <div class="lightbox-nav lightbox-next" onclick="navigateLightbox(1)">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M9 18l6-6-6-6"/>
    </svg>
  </div>

  <div class="lightbox-counter" id="lightboxCounter">1 / 4</div>
</div>

<script>
// Open lightbox
function openLightbox(index) {
  currentImageIndex = index;
  updateLightboxMedia();
  document.getElementById('lightbox').classList.add('active');
}

// Close lightbox
function closeLightbox() {
  document.getElementById('lightbox').classList.remove('active');
  // 暂停视频播放
  const video = document.getElementById('lightboxVideo');
  if (video.style.display !== 'none') {
    video.pause();
  }
}

// Navigate lightbox
function navigateLightbox(direction) {
  currentImageIndex = (currentImageIndex + direction + galleryImages.length) % galleryImages.length;
  updateLightboxMedia();
}

// Update lightbox media
function updateLightboxMedia() {
  const media = galleryImages[currentImageIndex];
  const image = document.getElementById('lightboxImage');
  const video = document.getElementById('lightboxVideo');
  
  if (media.type === 'image') {
    image.src = media.url;
    image.style.display = 'block';
    video.style.display = 'none';
    video.pause();
  } else {
    video.src = media.url;
    video.style.display = 'block';
    image.style.display = 'none';
  }
  
  document.getElementById('lightboxCounter').textContent = `${currentImageIndex + 1} / ${galleryImages.length}`;
}

</script>

 
 
 
 
  <script src="/js/info_collection.js"></script>
  <script src="/js/contact.js"></script>
  <script>
    let galleryImages = <?php echo json_encode($mediaArray); ?>;
    let currentImageIndex = 0;

  </script>

  <script type="text/javascript">
        const media = <?php 
            if (!empty($picsArray) && is_array($picsArray)) {
                $mediaItems = [];
                foreach ($picsArray as $url) {
                    $mediaItems[] = '{"type": "image", "url": "' . addslashes($url) . '"}';
                }
                echo '[' . implode(',', $mediaItems) . ']';
            } else {
                echo '[]';
            }
        ?>;
        const infoId = <?php echo $infos['id']; ?>;
        const infotype = <?php echo $infotype; ?>;

        const detailContactBtn = document.getElementById('detailContactBtn');
        const isMoneySj = <?php echo $is_money_sj; ?>;
        let isJs = <?php echo $is_js; ?>;
        if (detailContactBtn) {
            detailContactBtn.addEventListener('click', function() {
               if (isJs == 1 ) {
                        fetchContacts(infoId, 3,2);
                } else {
                  if (isMoneySj === 1) {
                    tconfirm('当前解锁次数已用完，是否支付20元解锁？', function() {
                      fetchContacts(infoId, 4,2);
                    },null,'提示');
                    
                  } else {
                      fetchContacts(infoId, 1,2);
                  }
                }
                
            });
        }
    </script>
     <script type="text/javascript">
                
                function messages(vs){
                    if (vs >1) {
                        window.location.href = '/message.html?uid=<?php echo $infos['uid']; ?>';
                    }else{
                        showInfo('至少季度会员才可使用私信功能！','提示');
                    }
                }
            </script>
            
<?php 
include_once 'comm/alert_modal.php';
?>
</body>
</html>
