<?php 
require_once 'loaduser.php';
require_once 'config.php';

$by_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
//查询信息是否存在。
$where = [];
$where['id'] = $by_id;
$infosnum = db3('byb')->where($where)->count();
if ($infosnum  <= 0) {
    echo "<script > window.location.href = '/404.html';</script>";
    die;
}

$where = [];
$where['id'] = $by_id;
$where['flag'] = 1;

$infos = db3('byb')
    ->where($where)
    ->field('id, uid,typeid, uname, city, age, times, osspics,pics, videos,ossvideos, zy,oss, sg, tz, xl, jg, aihao, price, content, mobile, weixin, qq, isrz, dz,fbtime,isopen')
    ->find();
// var_dump($infos);die;
if (empty($infos)) {
    echo "<script > window.location.href = '404.html';</script>";
    die;
}
// 增加浏览量
db3('byb')->where(['id' => $by_id])->inc('times', 1);

$picsArrays =z_imgurl_arr($infos['pics'],$infos['osspics'],3,$infos['oss']);

$infos['pic']  = $picsArrays['img'];
$picsArray  =$picsArrays['arr'];

$videosArrays =z_imgurl_arr($infos['videos'],$infos['ossvideos'],3,$infos['oss']);
$videosArray  =$videosArrays['arr'];

$mediaArray = [];

if (!empty($videosArray)) {
  foreach ($videosArray as $video) {
    $mediaArray[] = ['type' => 'video', 'url' => $video];
  }
}
foreach ($picsArray as $pic) {
    $mediaArray[] = ['type' => 'image', 'url' => $pic];
}
$mediaCount = count($mediaArray);


if ($infos['typeid'] ==0) {
    $infotype =3;
    $pageTitle = '伴游详情';

}else{
    $infotype =4;
    $pageTitle = '包养详情';
}

// 是否收藏
$existingCollection = db('usersc')->field('id,status')
    ->where(['user_id' => $userData['userId'], 'info_id' => $by_id,'infotype'=> $infotype])
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
        ->where(['user_id' => $userData['userId'], 'info_id' => $by_id, 'infotype' => $infotype])
        ->count();

    if ($isUnlocked > 0) {
        $is_js = 1;
    }
}

$vipclass_user = $userInfo['vipclass'] ?? 0;
$vip_msgster  ='需要年度会员或以上可解锁她的联系方式';
if ($vipclass_user > 2) {
    $vip_msgster  ='因妹妹设有门槛要求,防止无聊人土解锁口嗨,需线上支付50元诚意金才能解锁.';
}elseif($vipclass_user>0 && $vipclass_user <= 2){
    $vip_msgster ='包养和伴游需要年度会员或以上才能解锁联系方式.';
}

if($is_js == 1){
    $vip_msgster ='该信息你已解锁，可直接查看联系方式：';
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
    if ($vipclass_user > 2) {
        // 查询用户总查看次数
        $vipconfig = db('fl_vip_config')->field('id,see_nums')->where(['id' => $vipclass_user])->find();
        $user_all_num = $vipconfig['see_nums'] ?? 0;
        //用户次数是否用尽。
        if($user_all_num - $userInfo['ckcs'] <=0){
            $is_money_sj = 1;
        }
    }else{
        $is_money_sj =$user_id > 0 ? 3 : 4 ;
    }
}

$Area=$city_name;
$Area = str_replace("市", "", $Area);
$citytype=$Area.$page_info;
$cityweb=$webname;
$webtitle=$webname.$citytype.'_'.$infos['uname'];
$keywords=$webname.$page_info.'频道,'.$citytype.'网,'.$infos['zy'].$infos['uname'];
$description=$cityweb.$page_info.'频道,'.nl2br(htmlspecialchars($infos['content']));

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="author" content="<?php echo $webname; ?>" />
<meta name="keywords" content="<?php echo $keywords; ?>">
<meta name="description" content="<?php echo $description; ?>">
<title><?php echo $webtitle; ?></title>
<link href="/favicon.ico" rel="shortcut icon"/>
<link rel="stylesheet" href="/css/comm.css">
<link rel="stylesheet" href="/css/gd_detail.css?t=<?php echo time(); ?>">
</head>
<body>
  <?php include 'comm/header.php'; ?>
  <main class="main-content">
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
          <h2 class="user-nickname">
            <?php echo $infos['uname']; ?> 
           </h2>
          <div class="user-badges">
         <?php if ($infos['isrz']  == 1) { echo "<span class='badge'>✓ 已认证</span>"; } else { echo "<span class='badge'>未认证</span>";} ?>
          </div>
        </div>
      </div>

    </div>

    <div class="detail-card">
      <h3 class="detail-title">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
          <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
        </svg>
        个人资料
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
            <div class="detail-item-value"><?php echo $infos['zy']; ?></div>
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
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
          <circle cx="8.5" cy="8.5" r="1.5"></circle>
          <polyline points="21 15 16 10 5 21"></polyline>
        </svg>
        她的相册
      </h3>
      <div class="album-grid">
  
        <?php if ($mediaCount > 0): ?>
        <?php foreach ($mediaArray as $index => $media): ?>
        <div class="album-item" onclick="openLightbox(<?php echo $index; ?>)">

        <?php if ($media['type'] === 'image'): ?>
        <img src="<?php echo $media['url']; ?>" alt="照片<?php echo $index + 1; ?>">
        <?php else: ?>
          <!-- 使用video标签直接显示视频第一帧作为封面，添加preload="metadata"加载视频元数据 -->
          <video src="<?php echo $media['url']; ?>" preload="metadata" muted playsinline></video>
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


    <!-- 详情介绍 -->
    <div class="description-section">
      <h3 class="description-title">详细介绍</h3>
      <div class="description-content">
        <?php echo $infos['content']; ?>
      </div>
    </div>

     <?php include_once 'comm/detail_collections.php'; ?>
       
       
    <!-- 联系方式按钮区域 -->
    <div class="contact-section">
        <h3 class="contact-title">联系方式</h3>
      <!-- <h3 class="contact-title">查看联系方式</h3>-->
      
      <!-- 联系方式说明 -->
      <div class="contact-notice">
        <div class="contact-notice-content">
          <div class="contact-notice-icon">
<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64">
  <circle cx="32" cy="32" r="28" fill="none" stroke="#f4b346" stroke-width="4"/>
  <line x1="32" y1="18" x2="32" y2="38" stroke="#f4b346" stroke-width="4" stroke-linecap="round"/>
  <circle cx="32" cy="47" r="3" fill="#f4b346"/>
</svg>
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
              <span id="detailContactspan">查看联系方式</span>
            <?php }else{ ?>
              <span id="detailContactspan">解锁联系方式</span>
            <?php } ?>
          
        </button>
        
      </div>
    </div>
  </main>
 <?php 
 include_once 'comm/album.php'; 
 include_once 'comm/alert_modal.php';
 ?>
 
<script>
    let galleryImages = <?php echo json_encode($mediaArray); ?>;
    let currentImageIndex = 0;
</script>

<script>
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
        const detailContactBtnsee = document.getElementById('detailContactBtnsee');
        let isJs = <?php echo $is_js; ?>;
        const isMoneySj = <?php echo $is_money_sj; ?>;
        if (detailContactBtn) {
            detailContactBtn.addEventListener('click', function() {

                if (isJs == 1 ) {
                        fetchContacts(infoId, 3,infotype);
                } else {
                  if (isMoneySj === 1) {
                    tconfirm('当前解锁次数已用完，是否支付70元解锁？', function() {
                      fetchContacts(infoId, 4,infotype);
                    },null,'提示');
                  } else if(isMoneySj === 3) {
                      showInfo('需年度会员或以上可解锁她的联系方式！','提示');
                     
                  } else if(isMoneySj === 4) {
                   
                     showInfo('您还未登录，请先登录!','提示','10000','/login.html');
                  }  else {
                    
                   
                    tconfirm('是否支付50元解锁？', function() {
                      fetchContacts(infoId, 1,infotype);
                    },null,'提示');
                  }
                }
            });
        }
    </script>
<script>
        function messages(vs){
            if (vs >1) {
                window.location.href = '/message.html?uid=<?php echo $infos['uid']; ?>';
            }else{
                showInfo('至少季度会员才可使用私信功能！','提示');
            }
        }
</script>
<script src="/js/info_collection.js"></script>
<script src="/js/contact.js?t=<?php echo time();?>"></script>
</body>
</html>
