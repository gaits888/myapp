<?php 
require_once 'loaduser.php';
include_once 'config.php';

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
$vip_msgster  ='需年度会员或以上可解锁她的联系方式';
if ($vipclass_user > 2) {
    $vip_msgster  ='因妹妹设有门槛要求,防止无聊人土解锁口嗨,需线上支付50元诚意金才能解锁.';
}elseif($vipclass_user>0 && $vipclass_user <= 2){
    $vip_msgster ='包养和伴游需年度会员或以上才能解锁联系方式.';
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
<link rel="stylesheet" href="/css/by_detail.css?t=123">
</head>
<body>
<?php 
include 'comm/header.php'; 
?>
  <!-- 主内容 -->
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
          <h2 class="user-nickname"><?php echo $infos['uname']; ?></h2>
          <div class="user-badges">
            <?php if ($infos['isrz']  == 1) { ?>
             <span class="badge">✓ 已认证</span>
            <?php } ?>
            <!-- <span class="badge">VIP会员</span> -->
          </div>
        </div>
      </div>

    </div>
    </div>

    <!-- 详细信息 -->
    <div class="detail-card">
      <h3 class="detail-title">
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
        她的相册
      </h3>
      <div class="album-grid">
        <?php 
        if ($mediaCount > 0): 
          // 重新排序：图片在前，视频在后
          $sortedMedia = [];
          $imageIndex = 0;
          $videoIndex = 0;
          
          // 先添加所有图片
          foreach ($mediaArray as $media) {
            if ($media['type'] === 'image') {
              $sortedMedia[] = $media;
            }
          }
          // 再添加所有视频
          foreach ($mediaArray as $media) {
            if ($media['type'] !== 'image') {
              $sortedMedia[] = $media;
            }
          }
        ?>
        <?php foreach ($sortedMedia as $index => $media): ?>
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
      
    </div>


    <!-- 详情介绍 -->
    <div class="description-section">
      <h3 class="description-title">个人介绍</h3>
      <div class="description-content">
        <?php echo $infos['content']; ?>
      </div>
    </div>
    
    
<?php if (!isset($infos['isopen']) ||  $infos['isopen'] ==0){ ?>
    <!-- 操作按钮 -->
       <?php include_once 'comm/detail_collections.php'; ?>
   <?php } ?>    

<?php if (isset($infos['isopen']) &&  $infos['isopen'] ==1){ 
      $contact_infos= db3('byb') ->where($where) ->field('mobile, weixin, qq') ->find();
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
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
          </svg>
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
  <?php include_once 'comm/album.php'; ?>
  <?php include 'comm/jb.php'; ?>
  <script src="/js/info_collection.js"></script>
  <script src="/js/contact.js?t=<?php echo time(); ?>"></script>
  <script>
    let galleryImages = <?php echo json_encode($sortedMedia ?? $mediaArray); ?>;
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
                  } else {
                   
                    tconfirm('是否支付50元诚意金解锁？', function() {
                      fetchContacts(infoId, 1,infotype);
                    },null,'提示');
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
     
<?php include_once 'comm/alert_modal.php'; ?>

</body>
</html>
