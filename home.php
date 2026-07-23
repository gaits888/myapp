<?php 
include_once 'loaduser.php';
include_once 'config.php';

if (!empty($userToken)) {
    $islogin_ok='1';
}else{
    $islogin_ok='0';
}

// 获取GET请求参数
$page = isset($_GET['page']) && intval($_GET['page']) > 0 ? intval($_GET['page']) : 1;
$types = isset($_GET['types']) && intval($_GET['types']) >= 0 ? intval($_GET['types']) : 0;

$filter = isset($_GET['filter']) ? trim($_GET['filter']) : 'all';
$keywords = isset($_GET['keywords']) ? trim($_GET['keywords']) : '';

$pageSize = 20;

if(!in_array($types,[0,1,2])){
    $types = 0;
}

// 导航排序：new=最新信息(默认) rz=认证信息(isrz=1) tj=推荐信息(fxy=1)
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'new';
if (!in_array($sort, ['new', 'rz', 'tj'])) {
    $sort = 'new';
}

$where = [];
$where[] = ['infob.flag','=',1];
$where[] = ['infob.pics','!=',''];
$where[] = ['infob.ljxx','=',0];
$where[] = ['infob.del','=',0];
$where[] = ['infob.ispt','=',0];
$where[] = ['infob.isrz','<',2];

// 导航筛选条件
if ($sort == 'rz') {
    $where[] = ['infob.isrz','=',1];
} elseif ($sort == 'tj') {
    $where[] = ['infob.fxy','=',1];
}

// 处理城市查询条件
if ($city_id > 1) {
    $where['infob.city'] = $city_id;
}

// 查询总数
$total = db3('infob')->where($where)->count();

// 如果没有指定城市，限制最多5000条
if ($city_id < 1) {
    $max_page = ceil(5000 / $pageSize); // 250页
    $page = max(1, min($page, $max_page)); // 确保页码在1到250之间
    $total = min($total, 5000);
} else {
    $page = max(1, $page); // 确保页码至少为1
}

// 计算总页数
$totalPages = ceil($total / $pageSize);

// 确保页码不超过总页数
if ($totalPages > 0 && $page > $totalPages) {
    $page = $totalPages;
}

// 查询数据
$listField = 'infob.id, infob.title, infob.city, infob.cityid, infob.fbtime, infob.age, infob.nums, infob.pj, infob.price, infob.iszd, infob.isrz, infob.times, infob.pics, infob.content, infob.osspics, infob.oss, city_area.fullname as city_name, district_area.fullname as district_name';

$infoList = db3('infob')
    ->leftJoin('areab city_area', 'infob.city = city_area.id')
    ->leftJoin('areab district_area', 'infob.cityid = district_area.id')
    ->where($where)
    ->field($listField)
    ->order('iszd', 'DESC')
    ->order('infob.id', 'DESC')
    ->limit($total)  // 限制在total范围内（全国时最多5000条）
    ->page($page, $pageSize)  // 分页
    ->select();

// ===== 将指定ID固定显示在第一条(仅第一页生效) =====
$pinnedId = 619448;

if ($page == 1) {
    // 1. 先从常规列表中剔除该ID, 避免出现两次
    foreach ($infoList as $pkey => $pval) {
        if ($pval['id'] == $pinnedId) {
            unset($infoList[$pkey]);
        }
    }
    $infoList = array_values($infoList); // 重建索引

    // 2. 单独查询该ID(使用与列表相同的字段和联表)
    $pinnedItem = db3('infob')
        ->leftJoin('areab city_area', 'infob.city = city_area.id')
        ->leftJoin('areab district_area', 'infob.cityid = district_area.id')
        ->where('infob.id', $pinnedId)
        ->field($listField)
        ->find();

    // 3. 存在则插入到第一条
    if (!empty($pinnedItem)) {
        array_unshift($infoList, $pinnedItem);
    }
}

foreach ($infoList as &$item) {
    $item['pic'] = z_imgurl($item['pics'], $item['osspics'], 1, $item['oss']);
}
unset($item);



$geturl=getCurrentDomain();

$Area=$city_name;
$Area = str_replace("市", "", $Area);
$webtitle=$Area.$webname."_全国领先的楼凤小姐信息平台";
$keywords=$Area.$webname.",".$Area."楼凤网,".$Area."楼凤信息,".$Area."凤楼资源,".$Area."楼凤论坛";
$description=$webname."是全国领先的楼凤信息平台，千万真实用户注册，真实用户验证，智能匹配系统，帮你快速找到心仪的小姐，立即开启你的同城约炮。";
?>
<!DOCTYPE html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<meta name="apple-mobile-web-app-status-bar-style" content="black" /> 
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<meta name="renderer" content="webkit" />
<meta name="applicable-device" content="mobile" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-touch-fullscreen" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no" />
<meta name="format-detection" content="email=no" />
<meta name="full-screen" content="yes">
<meta name="browsermode" content="application">
<meta name="x5-fullscreen" content="true">
<meta name="x5-page-mode" content="app">
<meta name="author" content="<?php echo $webname; ?>" />
<meta name="keywords" content="<?php echo $keywords; ?>">
<meta name="description" content="<?php echo $description; ?>">
<title><?php echo $webtitle; ?></title>
<link href="/favicon.ico" rel="shortcut icon" type="image/x-icon" />
<link rel="stylesheet" href="/css/comm.css?t=123.15">
<link rel="stylesheet" href="/css/index.css?t=123.678">
<link rel="stylesheet" href="/css/swiper45.min.css">
<link rel="stylesheet" href="/css/baner.css">
<script src="/js/jquery-3.6.0.min.js"></script>
<script src="/js/jquery.lazyload.min.js"></script>
<script src="/js/swiper45.min.js"></script>
<script src="/js/clipboard2.min.js"></script>
<script src="/js/jsbridge-mini.js"></script>
<script src="/layer/layer.js"></script>
<style>
.sort-nav{
  display:flex;
  align-items:center;
  justify-content:space-between;
  background:linear-gradient(135deg,#fff5f8 0%,#ffffff 100%);
  border-radius:10px;
  padding:12px 10px 12px 10px;
  margin:10px 12px;
  box-shadow:0 2px 8px rgba(255,105,150,0.08);
}
.sort-nav-brand{
  display:flex;
  align-items:center;
  gap:6px;
  flex-shrink:0;
}
.sort-nav-flame{
  width:20px;
  height:20px;
  color:#ff6b9d;
}
.sort-nav-title{
  font-size:15px;
  font-weight:700;
  color:#333;
}
.sort-nav-links{
  display:flex;
  align-items:center;
  gap:16px;
}
.sort-nav-item{
  position:relative;
  font-size:15px;
  color:#666;
  text-decoration:none;
  padding:4px 0;
  transition:color .2s;
}
.sort-nav-item.active{
  color:#ff4d8d;
  font-weight:600;
}
.sort-nav-item.active::after{
  content:"";
  position:absolute;
  left:0;
  right:0;
  bottom:-2px;
  height:1px;
  border-radius:2px;
  background:#ff4d8d;
}
.sort-nav-brand svg {
    width: 20px;
    height: 20px;
    fill: url(#fireGradient);
    filter: drop-shadow(0 2px 4px rgba(255, 107, 157, 0.3));
    animation: pulse 2s ease-in-out infinite;
}

.hot-title {
    padding: 0;
    background: transparent;
    font-size: 17px;
    font-weight: 700;
    color: #2c3e50;
    border-bottom: none;
    white-space: nowrap;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 6px;
    background: linear-gradient(135deg, #ff6b9d 0%, #ff8fb3 50%, #ffb347 100%);
    -webkit-background-clip: text;
</style>
</head>
<body>
  <div class="container">
<!-- 头部 -->
<?php include_once 'comm/header_index.php'; ?>

<!-- 公告 -->
<div class="notice-container">
        <div class="notice-inner">
            <span class="notice-tag">公告</span>
            <div class="notice-content">
                <div class="notice-list" id="noticeList">
                    <!-- 公告项会通过JS动态生成 -->
                </div>
            </div>
        </div>
</div>

    <!-- 广告轮播 -->
<div class="banner-container">
    <div class="swiper-container" id="bannerSwiper">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <a href="index.html">
                <img src="/images/baner_1.png" alt="论坛信息">
                </a>
            </div>
            <div class="swiper-slide">
                <a href="gdlist.html">
                <img src="/images/baner_2.png" alt="高端约会">
                </a>
            </div>
            <div class="swiper-slide">
                <a href="bylist.html">
                <img src="/images/baner_3.jpg" alt="商务伴游">
                </a>
            </div>
        </div>
        <div class="swiper-pagination"></div>
    </div>
</div>

    
    <!-- 功能菜单网格 -->
    <div class="card">
      <div class="menu-grid">
        <!-- 第一行 -->
        <a href="/" class="menu-item">
          <div class="menu-icon">
            <img src="/images/dh/h3.png" alt="论坛版块">
          </div>
          <div class="menu-label">论坛</div>
        </a>

        <a href="/gdlist.html" class="menu-item">
          <div class="menu-icon">
            <img src="/images/dh/2.png" alt="高端版块">
          </div>
          <div class="menu-label">高端</div>
        </a>

        <a href="/bylist.html?typeid=2" class="menu-item">
          <div class="menu-icon">
            <img src="/images/dh/h6.png" alt="伴游版块">
          </div>
          <div class="menu-label">伴游</div>
        </a>

        <a href="/bylist.html?typeid=1" class="menu-item">
          <div class="menu-icon">
            <img src="/images/dh/h8.png" alt="包养版块">
          </div>
          <div class="menu-label">包养</div>
        </a>
      </div>
    </div>

    <!-- 排序导航条 -->
    <div class="sort-nav">
      <div class="sort-nav-brand">
<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="fireGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" style="stop-color:#ff6b9d;stop-opacity:1"></stop>
                    <stop offset="50%" style="stop-color:#ff8fb3;stop-opacity:1"></stop>
                    <stop offset="100%" style="stop-color:#ffb347;stop-opacity:1"></stop>
                </linearGradient>
            </defs>
            <path d="M13.5.67s.74 2.65.74 4.8c0 2.06-1.35 3.73-3.41 3.73-2.07 0-3.63-1.67-3.63-3.73l.03-.36C5.21 7.51 4 10.62 4 14c0 4.42 3.58 8 8 8s8-3.58 8-8C20 8.61 17.41 3.8 13.5.67zM11.71 19c-1.78 0-3.22-1.4-3.22-3.14 0-1.62 1.05-2.76 2.81-3.12 1.77-.36 3.6-1.21 4.62-2.58.39 1.29.59 2.65.59 4.04 0 2.65-2.15 4.8-4.8 4.8z"></path>
        </svg>
        <span class="sort-nav-title hot-title">论坛中心</span>
      </div>
      <div class="sort-nav-links">
        <a href="/" class="sort-nav-item<?php echo $sort=='new'?' active':''; ?>">最新信息</a>
        <a href="/?sort=rz" class="sort-nav-item<?php echo $sort=='rz'?' active':''; ?>">认证信息</a>
        <a href="/?sort=tj" class="sort-nav-item<?php echo $sort=='tj'?' active':''; ?>">推荐信息</a>
      </div>
    </div>

    <!-- 内容列表 -->
    <div class="content-section">
      <?php foreach ($infoList as $k => $v): ?>
      <a href="<?php echo $geturl; ?>/showinfo/<?php echo $v['id']; ?>.html" title="<?php echo $v['title']; ?>">
        <!-- 调整卡片结构，让图片从顶部延伸到底部 -->
        <div class="post-card">
          <!-- 左侧内容区 -->
          <div class="post-card-left" >
            <div class="post-meta">
               <?php if ($v['iszd']==1): ?>
              <!-- <span class="tag tag-pinned clefts">置顶</span> -->
              <?php endif; ?>
                
                 <?php if ($v['isrz']==1): ?>
              <span class="tag tag-pinnedrz clefts">认证</span>
              <?php endif; ?>

              <div class="post-meta-item clefts">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                  <line x1="16" y1="2" x2="16" y2="6"></line>
                  <line x1="8" y1="2" x2="8" y2="6"></line>
                  <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <?php echo date('Y/m/d',$v['fbtime']); ?>
              </div>
            </div>
            <div class="post-title" ><?php echo $v['title']; ?></div>
            <div class="post-content">
              <?php echo $v['content']; ?>
            </div>
            <div class="post-footer">
              <div class="post-location">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                  <circle cx="12" cy="10" r="3"></circle>
                </svg>
                <?php echo $v['city_name']; ?>
              </div>
              <span class="tag tag-merchant">
                <div class="post-meta-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
                <?php echo $v['times']; ?>
              </div>
              </span>
            </div>
          </div>
          <!-- 右侧图片 -->
          <div class="post-thumbnail">
            <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-original="<?php echo $v['pic']; ?>" onerror="this.src='upload/default_avatar.png';" class="lazy" alt="<?php echo $v['title']; ?>">
          </div>
        </div>
      </a>
      <?php endforeach ?>
    </div>








<?php
// 确保当前页不超过总页数
$page = min($page, $totalPages);
?>
<link rel="stylesheet" href="/css/pagination.css?t=1.123982739">
<!-- 分页 -->
<div class="pagination">
  <div class="pagination-btn" onclick="prevPage()" id="prevBtn" <?php echo $page <= 1 ? 'disabled' : ''; ?>>
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <polyline points="15 18 9 12 15 6"></polyline>
    </svg>
    <span>上一页</span>
  </div>
  
  <select id="pageSelector" onchange="goToPage(this.value)" class="pagination-select">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <option value="<?php echo $i; ?>" <?php echo $i == $page ? 'selected' : ''; ?>>
        <?php echo $i; ?> / <?php echo $totalPages; ?>
      </option>
    <?php endfor; ?>
  </select>

  <div class="pagination-btn" onclick="nextPage()" id="nextBtn" <?php echo $page >= $totalPages ? 'disabled' : ''; ?>>
    <span>下一页</span>
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <polyline points="9 18 15 12 9 6"></polyline>
    </svg>
  </div>
</div>


<script>
<?php
echo "var currentPageStrs = '$currentPageStrs';var doamin_url = '$doamin_url';";
?>
  var currentPages = <?php echo $page; ?>;
  var totalPages = <?php echo $totalPages; ?>;

  function prevPage() {
    if (currentPages > 1) {
      goToPage(currentPages - 1);
    }
  }

  function nextPage() {
    if (currentPages < totalPages) {
      goToPage(currentPages + 1);
    }
  }

  function goToPage(page) {
    page = parseInt(page);
    if (currentPageStrs === 'home') {
      window.location.href = '/' + page + '.html';
    } else {
      window.location.href = doamin_url + page + '.html';
    }
  }
</script>

    
    
    
    
    
    
    
    
</div>


<div class="app" id="app" style="display:none;">
温馨提示：下载APP，操作体验更佳. &nbsp; [<a href="/app.php" style="color:#598b16;">立即下载</a>]
<span class="closeapp" id="closeapp">×</span>
</div>  
  
<input type="hidden" name="islogin" id="islogin" value="<?php echo $islogin_ok; ?>">
  
  
<?php include_once 'comm/footer.php'; ?>


<script>
function copyUrl(obj){
	var url = obj;
	var input = document.createElement('input');
	document.body.appendChild(input);
	input.setAttribute('value', url);
	input.select();
	document.execCommand("copy"); 
	if (document.execCommand('copy')) {
		document.execCommand('copy');
		layer.open({content: '复制成功！',btn: '好的'});
	}
	document.body.removeChild(input);
}

function isweixin(){
  var ua = navigator.userAgent.toLowerCase();
  if (ua.match(/MicroMessenger/i) == "micromessenger") {
	return true;
  } else {
	return false;
  }
}

function getDeviceType() {
  const ua = navigator.userAgent.toLowerCase();
  if (/android/.test(ua)) {
    return 'Android';
  } else if (/iphone|ipad|ipod/.test(ua)) {
    return 'iOS';
  } else {
    return 'Other';
  }
}

function downapp(){
  layer.open({
    content: '当前为【<?php echo $webname; ?>】网页版，是否下载APP？'
    ,btn: ['下载APP', '不要']
    ,yes: function(index,layero){
      window.location.href='/app.php';
      layer.close(index);
    }
	,no: function(index,layero){
	  layer.close(index);
	}
  });	
}

var dev=getDeviceType();
var res=$('#islogin').val();
if (!jsBridge.inApp && dev=='Android') {   
	$('#app').show();
}
if (!jsBridge.inApp && dev=='Android' && res=='1') {   
	$('#app').show();
	downapp();
}

//定时关闭顶部网址条
setTimeout(() =>{ $('#app').hide(); }, 30*1000);
$('#app').click(function(){
	$('#app').hide();
});

</script>


<script src="/js/baner.js"></script>
<script src="/js/notice.js?t=1"></script>
<script src="/js/list_lazy.js"></script>

</body>
</html>
