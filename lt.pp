<?php
include_once 'loaduser.php';
include_once 'config.php';

// 初始化数据库连接
$db = new DbOperation();
$db3 = new DbOperation3();
$info_id = intval($_GET['id']);
// $infos = $db->table('infob')
//     ->where(['id' => $info_id])
//     ->find();
//查询信息是否存在。

//获得Cookie的值 
$py=Cookie::get('py'); 
$city=Cookie::get('selectedCityIds'); 
$currentAreas=Cookie::get('currentAreas'); 
$currentArea=$currentAreas;

$where = [];
$where['id'] = $info_id;
$infos = $db3->table('infob')->where($where)->field('id,uid,flag,sh')->find();
if (empty($infos)) {
    echo "<script > window.location.href = '404.html';</script>";
    die;
}

if ($infos['flage'] ==0 && $infos['uid'] == $userData['userId'] ) {
    echo "<script > window.location.href = 'sh.html';</script>";
    die;
}
if ($infos['sh'] ==2 && $infos['uid'] == $userData['userId'] ) {
    echo "<script > window.location.href = 'shno.html';</script>";
    die;
}

$where = [];
$where['infob.id'] = $info_id;
$where['infob.flag'] = 1;

$infos = $db3->table('infob')
    ->join('areab as city_area ON infob.city = city_area.id', 'LEFT')
    ->join('areab as district_area ON infob.cityid = district_area.id', 'LEFT')
    ->where($where)
    // 限制查询字段为：id、title、city、cityid、times、pics以及城市和区县名称
    ->field('infob.id, infob.title, infob.city, infob.cityid, infob.times, infob.fbtime,infob.price,infob.typeid,infob.content, infob.pics,infob.osspics,infob.oss,infob.sh,infob.age,infob.nums,infob.wmtj,infob.pj,infob.isopen, city_area.fullname as city_name, district_area.fullname as district_name')
    ->find();

    // var_dump($infos);

if (empty($infos)) {
    echo "<script > window.location.href = '404.html';</script>";
    die;
}
// 增加浏览量
$db3->querySql("UPDATE infob SET times = COALESCE(times, 0) + 1 WHERE id = $info_id", false);

//图片
$picsArrays =z_imgurl_arr($infos['pics'],$infos['osspics'],1,$infos['oss']);
$infos['pic']  = $picsArrays['img'];
$picsArray  =$picsArrays['arr'];

//视频
$videoArrays =z_imgurl_arr($infos['videos'],$infos['ossvideos'],1,$infos['oss']);
$videoArray  =$picsArrays['arr'];

$where = [];
$where['infob.flag'] = 1;
$where['infob.typeid'] = $infos['typeid'];
$where['infob.pics'] = ['!=',''];
$where[] = " infob.isrz < 2 ";
$where[] = " infob.ljxx = 0 ";

// var_dump($selectedCityIds);
if (!empty($city) && $city>0) {
    $where[] = "(infob.city = $city)";
}

$tj_list = $db3->table('infob')
    ->join('areab as city_area ON infob.city = city_area.id', 'LEFT')
    ->join('areab as district_area ON infob.cityid = district_area.id', 'LEFT')
    ->where($where)
    ->field('infob.id, infob.title, infob.city, infob.cityid, infob.times,infob.pics,infob.osspics,infob.oss,infob.sh,infob.price, city_area.fullname as city_name, district_area.fullname as district_name')
    ->order(' infob.id desc ')
    ->limit(0, 4)
    ->select();
    if ($userData['userId'] == 21500119) {
       // var_dump($db3->table('infob')->getLastSql());
    }


foreach ($tj_list as &$item) {
    // pics可能包含多个图片，用|分隔，提取第一张
    $item['pic'] = z_imgurl($item['pics'],$item['osspics'],1,$item['oss']);
}
unset($item);

// 是否收藏
$existingCollection= $db->table('usersc')->field('id,status')
    ->where(['user_id' => $userData['userId'], 'info_id' => $info_id,'infotype' => 1])
    ->find();
$is_sc = 0;
if (!empty($existingCollection)) {
    if ($existingCollection['status'] == 1) {
        $is_sc = 1;
    }
}
// var_dump($is_sc);
// 是否解锁
$is_js = 0;

// 首先判断用户是否已经解锁该信息
$isUnlocked = $db->table('fl_user_js')
    ->where(['user_id' => $userData['userId'], 'info_id' => $info_id])
    ->find();

if ($isUnlocked) {
    // 如果已经解锁
    $is_js = 1;
}

$is_money_sj = 0;
//如果没有解锁
if($is_js != 1){
    //用户是否是vip
    $vipclass_user = $userInfo['vipclass'] ?? 0;

    if ($vipclass_user > 0) {
        // 查询用户总查看次数
        $vipconfig = $db->table('fl_vip_config')->where(['id' => $vipclass_user])->find();
        $user_all_num = $vipconfig['see_nums'] ?? 0;
        //用户次数是否用尽。
        if($user_all_num - $userInfo['ckcs'] <=0){
            $is_money_sj = 1;
        }
    }
}
// var_dump($is_money_sj );

function typess($type){
switch($type){
    case '1':  $t="休闲会所";    break;
    case '2':  $t="外围模特";    break; 
    case '3':  $t="公寓楼凤";    break; 
    case '4':  $t="桑拿论坛";    break;
    case '5':  $t="兼职上门";    break; 
    case '6':  $t="夜场酒吧";    break; 
}
    return $t;      
}

$typename=typess($infos['typeid']);
$cityname=str_replace("市", "", $infos['city_name']);
$webtitle=$webname.'_'.$cityname.$typename;
$keywords=$infos['title'].','.$cityname.$typename.','.$cityname.$webname;
$description=$infos['content'].','.$cityname.$typename;

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $webtitle; ?></title>
<meta name="author" content="<?php echo $cityname.$webname; ?>" />
<meta name="keywords" content="<?php echo $keywords; ?>">
<meta name="description" content="<?php echo $description; ?>">
<link href="/favicon.ico" rel="shortcut icon"/>
<link href="/css/header.css" type="text/css" rel="stylesheet" media="all" />
<link href="/css/info.css?t=123.3678189" type="text/css" rel="stylesheet" media="all" />
<link href="/css/footer.css?t=123.676" type="text/css" rel="stylesheet" media="all" />
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
window.initialFavoriteState = <?php echo $is_sc; ?>;
window.currentInfoId = <?php echo $info_id; ?>;
</script>
<style>
body{ padding-bottom:0px; }
</style>
</head>
<body>
<header class="header">
    <div class="back-btn" onclick="window.history.back();">
        <svg fill="currentColor" viewBox="0 0 24 24">
            <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
        </svg>
    </div>
    <h1 class="header-title">信息详情</h1>
    <div class="header-spacer"></div>
</header>

    <main class="detail-container">
        <!-- Main image -->
        <!-- Added onclick to open album -->
        <div class="image-container" onclick="fl_openFullscreenAlbum(0)">
            
            <div style="position:absolute;z-index:5;right:20px;bottom:20px;">
            
            <svg t="1765470004028" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="72087" width="32" height="32"><path d="M983.172933 0.003258h-238.124922a40.214999 40.214999 0 0 0-40.726995 39.563367c0 21.876214 18.292239 39.656457 40.726995 39.656457h197.537562v192.091781c0 21.876214 18.245694 39.609912 40.68045 39.609912A40.214999 40.214999 0 0 0 1023.993018 271.361408V39.566625a40.308089 40.308089 0 0 0-40.820085-39.563367zM40.726995 310.924775a40.214999 40.214999 0 0 0 40.68045-39.563367V79.176537h197.537562A40.214999 40.214999 0 0 0 319.672002 39.566625H40.726995A40.214999 40.214999 0 0 0 0 39.566625v231.794783c0 21.829669 18.152604 39.563367 40.726995 39.563367zM278.805372 944.776453H81.45399v-192.091781c0-21.876214-18.292239-39.609912-40.726995-39.609912a40.214999 40.214999 0 0 0-40.726995 39.563367v231.794783c0 21.783124 18.245694 39.563367 40.726995 39.563366h238.218012a40.214999 40.214999 0 0 0 40.726995-39.563366c0-21.876214-18.292239-39.656457-40.86663-39.656457z m704.367561-231.701693a40.214999 40.214999 0 0 0-40.726995 39.563367v192.138326h-197.397927a40.214999 40.214999 0 0 0-40.726995 39.609912c0 21.876214 18.292239 39.609912 40.726995 39.609911h238.218012a40.214999 40.214999 0 0 0 40.726995-39.563366v-231.794783a40.308089 40.308089 0 0 0-40.820085-39.563367z m-172.170463 118.78319c10.705382 0 21.410763-4.049427 29.416527-12.194826a38.911735 38.911735 0 0 0-1.396354-56.040345l-102.585482-95.231351a283.832247 283.832247 0 0 0 53.526907-148.804803c10.472656-160.208362-114.826853-298.354329-279.550094-308.454625a357.233928 357.233928 0 0 0-18.94387-0.558541c-156.577842 0-288.114399 118.59701-298.121604 272.707959-10.379566 160.301452 114.919944 298.354329 279.643184 308.408079a302.729572 302.729572 0 0 0 208.103309-65.302828l101.747669 94.486629c7.912673 7.354132 18.059513 10.984652 28.159808 10.984652z m-102.259666-317.158565a208.56876 208.56876 0 0 1-68.421352 141.217946 219.693048 219.693048 0 0 1-162.349438 56.691978 218.343239 218.343239 0 0 1-149.596071-71.260605 207.125861 207.125861 0 0 1-53.992359-153.226592 208.56876 208.56876 0 0 1 68.421351-141.217946 219.693048 219.693048 0 0 1 162.256349-56.691977 218.343239 218.343239 0 0 1 149.596071 71.30715 205.496781 205.496781 0 0 1 54.085449 153.180046z" fill="#ffffff" p-id="72088"></path></svg>
            
            </div>
            <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-original="<?php echo $infos['pic']??''; ?>" class="main-image lazy">
        </div>
        <div class="title-detail">
            <h1 class="detail-main-title"><?php echo $infos['title']??''; ?></h1>
            
            <div class="detail-meta">
                <span class="detail-meta-item">
                  
                    <svg t="1764913725554" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="16769" width="16" height="16"><path d="M510.557138 204.354248c-302.839283 0-458.323497 306.150702-458.323497 306.150702s117.330242 306.189587 458.323497 306.189587c308.802088 0 458.300984-304.995389 458.300984-304.995389S818.167075 204.354248 510.557138 204.354248L510.557138 204.354248 510.557138 204.354248zM511.245823 701.866279c-110.729917 0-190.772928-83.72589-190.772928-191.364399 0-107.647719 80.049151-191.352119 190.772928-191.352119 110.723777 0 190.763718 83.697237 190.763718 191.352119C702.010565 618.140389 621.970624 701.866279 511.245823 701.866279L511.245823 701.866279 511.245823 701.866279zM511.245823 395.675668c-63.286372 0.145309-114.460892 53.321416-114.460892 114.827235 0 61.473073 51.175543 114.821095 114.460892 114.821095 63.282279 0 114.453728-53.352115 114.453728-114.821095C625.703645 448.975595 574.529125 395.556964 511.245823 395.675668L511.245823 395.675668 511.245823 395.675668z" fill="#8a8a8a" p-id="16770"></path></svg>

                    <span><?php echo $infos['times']??0; ?> 浏览</span>
                </span>
                <span class="detail-meta-item">
                   
                    <svg t="1764913611717" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="12522" width="16" height="16"><path d="M716 180H635.2v121.6h80.8V180z m-324 0H310.4v121.6H392V180z m404.8 80.8h-40.8v80.8H594.4V260.8H432v80.8H270.4V260.8h-40.8c-22.4 0-40.8 18.4-40.8 40.8v526.4c0 22.4 18.4 40.8 40.8 40.8h567.2c22.4 0 40.8-18.4 40.8-40.8V301.6c-0.8-22.4-18.4-40.8-40.8-40.8zM392 706.4H310.4V625.6H392v80.8zM392 544H310.4V463.2H392V544z m161.6 162.4H472.8V625.6h80.8v80.8z m0-162.4H472.8V463.2h80.8V544z m162.4 162.4H635.2V625.6h80.8v80.8z m0-162.4H635.2V463.2h80.8V544z" fill="#8a8a8a" p-id="12523"></path></svg>
                    <span><?php echo date('Y-m-d', $infos['fbtime']??time()); ?></span>
                </span>
            </div>
        </div>
        <!-- Detail info -->
        <div class="detail-info-section">
            <h2 class="detail-info-title">基本信息</h2>
            <div class="detail-info-table">

                <div class="detail-info-row">
                    <div class="detail-info-label">地区：</div>
                    <div class="detail-info-value">
                        <?php echo $infos['city_name']??''; ?>-<?php echo $infos['district_name']??''; ?>
                    </div>
                </div>

                <div class="detail-info-row">
                    <div class="detail-info-label">人数：</div>
                    <div class="detail-info-value">
                        <?php echo $infos['nums']??''; ?>
                    </div>
                </div>
                <div class="detail-info-row">
                    <div class="detail-info-label">年龄：</div>
                    <div class="detail-info-value">
                        <?php echo $infos['age']??''; ?>
                    </div>
                </div>
                <div class="detail-info-row">
                    <div class="detail-info-label">颜值：</div>
                    <div class="detail-info-value">
                        <?php echo $infos['wmtj']??''; ?>
                    </div>
                </div>
                <div class="detail-info-row">
                    <div class="detail-info-label">价格：</div>
                    <div class="detail-info-value">
                        <?php echo $infos['price']??''; ?>
                    </div>
                </div>

                 <div class="detail-info-row">
                    <div class="detail-info-label">评价：</div>
                    <div class="detail-info-value">
                        <?php echo $plArr[$infos['pj']]??''; ?>
                    </div>
                </div>



            </div>
        </div>

        <!-- Description -->
        <div class="detail-desc-section">
            <h2 class="detail-desc-title">详细介绍</h2>
            <div class="detail-desc-content">
                <?php echo $infos['content']??''; ?>
            </div>
        </div>
<?php if (!isset($infos['isopen']) ||  $infos['isopen'] ==0){ ?>
        <!-- Contact section -->
        <div class="contact-section">
            <h2 class="contact-title">联系方式</h2>

               <div class="vip-prompt-message" id="vip-see-messageno" <?php echo $is_js ==1 ? 'style="display:none;"' : ''; ?>>
                    本信息需要解锁后才能查看联系方式，你可以选择以下任意一种方式解锁：
                </div>

                <div class="vip-prompt-message" id="vip-see-message" <?php echo $is_js ==0 ? 'style="display:none;"' : ''; ?>>
                    该信息你已解锁，可直接查看联系方式：
                </div>

            
            <div id="contactButtons" class="contact-buttons" align="center"  style="text-align:center;">
               
                    <div class="contact-btn-primary" <?php if($is_js==0){ echo 'style="display: none;"'; } ?> id="viewedContactBtn" onclick="showContactModal()">
                    <span style="display: inline-flex; align-items: center; gap: 8px;">
                    <svg t="1765276236577" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="58756" width="16" height="16" style="margin-right:5px;"><path d="M512 1024a512 512 0 1 1 512-512 512 512 0 0 1-512 512z m174.933333-361.941333a166.826667 166.826667 0 0 0-59.733333-55.274667 20.458667 20.458667 0 0 0-20.053333-1.258667 365.461333 365.461333 0 0 1-34.986667 13.674667c-11.733333 3.712-24.106667 6.016-36.266667 8.533333a8.192 8.192 0 0 1-5.973333-1.728c-4.693333-4.074667-9.386667-8.405333-13.653333-12.8a331.370667 331.370667 0 0 1-70.186667-104.085333 203.818667 203.818667 0 0 1-16.426667-53.333333 16.192 16.192 0 0 1 4.266667-14.4 143.509333 143.509333 0 0 1 15.786667-16.021334c12.373333-9.984 25.386667-19.370667 38.4-28.757333a18.517333 18.517333 0 0 0 8.96-12.224 104.341333 104.341333 0 0 0-3.626667-51.2 159.424 159.424 0 0 0-52.693333-75.413333c-1.92-0.469333-3.626667-0.96-5.546667-1.429334-7.893333-0.170667-15.786667-0.597333-23.893333-0.448a118.485333 118.485333 0 0 0-68.053334 18.730667 173.013333 173.013333 0 0 0-21.12 20.181333l-1.92 7.104c0.426667 6.4 1.066667 12.970667 1.493334 19.477334a692.714667 692.714667 0 0 0 38.613333 188.202666 501.333333 501.333333 0 0 0 123.093333 192 571.434667 571.434667 0 0 0 119.466667 85.632 7.466667 7.466667 0 0 1 0.853333 0.938667c1.28 0.298667 2.346667 0.618667 3.626667 0.938667a95.082667 95.082667 0 0 0 54.4-25.834667 146.112 146.112 0 0 0 37.333333-49.066667 33.92 33.92 0 0 1 3.2-5.525333c0.426667-2.133333 1.066667-4.138667 1.493334-6.208a103.125333 103.125333 0 0 0-16.853334-40.405333z" p-id="58757" fill="#ffffff"></path></svg>
                    
                    查看联系方式
                     </span>
                </div>

                <div style="margin-top:15px;<?php if($is_js==1){ echo 'display: none;'; } ?>" class="contact-btn-secondary" id="memberContactBtn" align="center"  style="text-align:center;">
                    
                    <svg t="1765275619882" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="50873" width="16" height="16" style="margin-right:5px;"><path d="M512 1024C229.233778 1024 0 794.766222 0 512S229.233778 0 512 0s512 229.233778 512 512-229.233778 512-512 512z m284.444444-595.228444a72.490667 72.490667 0 0 0-20.807111-50.432 71.253333 71.253333 0 0 0-100.892444 0 66.816 66.816 0 0 0-20.807111 50.432 68.707556 68.707556 0 0 0 20.807111 50.432c2.474667 3.015111 5.461333 5.575111 8.817778 7.566222a78.833778 78.833778 0 0 1-93.312 13.866666 104.661333 104.661333 0 0 1-49.180445-58.638222 189.084444 189.084444 0 0 1 5.034667-51.057778 71.864889 71.864889 0 0 0 37.205333-63.047111 70.599111 70.599111 0 0 0-20.807111-50.432 70.001778 70.001778 0 0 0-100.892444 0 71.864889 71.864889 0 0 0-20.807111 50.432 72.490667 72.490667 0 0 0 20.807111 50.446223c5.632 5.304889 12.003556 9.770667 18.915555 13.226666a131.100444 131.100444 0 0 1 3.797334 48.554667 105.287111 105.287111 0 0 1-49.820445 59.875555 81.351111 81.351111 0 0 1-95.217778-14.492444 71.864889 71.864889 0 0 0 30.264889-58.624 71.253333 71.253333 0 0 0-70.613333-69.347556 72.533333 72.533333 0 0 0-50.446222 20.807112 71.224889 71.224889 0 0 0 0 100.864 70.627556 70.627556 0 0 0 30.890666 18.275555l59.278223 247.125333a28.373333 28.373333 0 0 0 27.107555 21.432889H658.346667a27.733333 27.733333 0 0 0 27.121777-21.432889l59.264-247.125333A71.864889 71.864889 0 0 0 796.444444 428.771556z" fill="#ffffff" p-id="50874"></path></svg>
                    
                    会员解锁
                </div>

                    <div class="contact-btn-primary" <?php if($is_js==1){ echo 'style="display: none;"'; } ?> id="pointsContactBtn"  onclick="viewContactWithPoints()" align="center" style="text-align:center;">
                        
                 <svg t="1765275985800" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="57499" width="16" height="16" style="margin-right:5px;"><path d="M513.024 2.4576C232.2432 2.4576 4.5056 230.1952 4.5056 510.976s227.7376 508.5184 508.5184 508.5184 508.5184-227.7376 508.5184-508.5184S794.0096 2.4576 513.024 2.4576z m0 765.1328c-130.8672 0-236.7488-54.4768-236.7488-121.6512 0-16.7936 6.7584-32.9728 18.6368-47.5136 36.0448 43.6224 120.0128 74.1376 218.112 74.1376 97.8944 0 182.0672-30.5152 218.112-74.1376 12.0832 14.5408 18.6368 30.72 18.6368 47.5136 0.2048 67.1744-105.8816 121.6512-236.7488 121.6512z m0-134.9632c-130.8672 0-236.7488-54.4768-236.7488-121.6512 0-17.408 7.168-34.2016 20.0704-49.152 36.864 42.8032 120.0128 72.4992 216.6784 72.4992 96.6656 0 179.8144-29.9008 216.6784-72.4992 12.9024 14.9504 20.0704 31.744 20.0704 49.152 0.2048 67.1744-105.8816 121.6512-236.7488 121.6512z m0-134.9632c-130.8672 0-236.7488-54.4768-236.7488-121.6512s106.0864-121.6512 236.7488-121.6512 236.7488 54.4768 236.7488 121.6512c0.2048 67.1744-105.8816 121.6512-236.7488 121.6512z" fill="#ffffff" p-id="57500"></path></svg>
                    
                    积分解锁
                </div>

            </div>

            <!-- Already viewed - show contact details -->
            <div id="contactDetails" class="contact-details hidden">
                <div class="contact-item">
                    <div class="contact-item-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                        </svg>
                    </div>
                    <div class="contact-item-content">
                        <div class="contact-item-label">微信号</div>
                        <div class="contact-item-value">wxid_abc123456</div>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-item-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56-.35-.12-.74-.03-1.01.24l-1.57 1.97c-2.83-1.35-5.48-3.9-6.89-6.83l1.95-1.66c.27-.28.35-.67.24-1.02-.37-1.11-.56-2.3-.56-3.53 0-.54-.45-.99-.99-.99H4.19C3.65 3 3 3.24 3 3.99 3 13.28 10.73 21 20.01 21c.71 0 .99-.63.99-1.18v-3.45c0-.54-.45-.99-.99-.99z"/>
                        </svg>
                    </div>
                    <div class="contact-item-content">
                        <div class="contact-item-label">联系电话</div>
                        <div class="contact-item-value">138-0000-0000</div>
                    </div>
                </div>
            </div>

            <!-- Already viewed - show contact details -->
            <div id="contactViewed" class="contact-viewed hidden">
                <svg viewBox="0 0 24 24">
                    <path d="M9 16.17L4.83 12 3.41 13.41 9 19 21 7l-1.41-1.41z"/>
                </svg>
                已查看联系方式，信息已展开
            </div>
        </div>
<?php } ?>
<?php if (isset($infos['isopen']) &&  $infos['isopen'] ==1){ 
            $contact_infos = $db3->table('infob')
            ->where($where)
            ->field('mobile, weixin, qq,yuli')
            ->find();


                include_once 'lib/opens.php';
              }
      ?>
        <div class="detail-actions">
            
             <input type="hidden" id="isFavorite" value="0">
             
            <div class="action-btn <?php if($is_sc == 1) {echo 'favorited';} ?>" id="favoriteBtn" >
                
                <svg id="favoriteIcon" viewBox="0 0 24 24" fill="<?php echo $is_sc == 1 ? '#ff6b9d' : 'none'; ?>" stroke="<?php echo $is_sc == 1 ? '#ff6b9d' : '#999'; ?>" stroke-width="1.5">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
            </svg>

                <span id="favoriteText">
                    <?php if($is_sc == 1) {
                        echo '已收藏';
                    }else{
                        echo '收藏';
                    } ?>
               </span>
            </div>
            <input type="hidden" id="iscollect" value="<?php echo $is_sc; ?>">

            <div class="action-btn" id="reportBtn" style="margin-left:10px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="1.5">
                <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/>
                <line x1="4" y1="22" x2="4" y2="15"/>
            </svg>
                举报
            </div>
        </div>

        <!-- 举报模态框 -->
        <div id="reportModal" class="report-modal">
            <div class="modal-overlay" onclick="closeReportModal()"></div>
            <div class="modal-content">
                <div class="modal-header">
                    <h3>举报信息</h3>
                    <button class="modal-close" id="closeReportModal" aria-label="关闭">
                        ×
                    </button>
                </div>
                <div class="modal-body" style="max-width: 100%;">
                    <form class="report-form">
                        <div class="report-description">
                            <label for="reportDescription">举报内容：</label>
                            <textarea id="reportDescription" placeholder="请详细描述举报内容"></textarea>
                        </div>
                        <div class="report-captcha" style="transform: translateZ(0); 
            -webkit-transform: translateZ(0); ">
                            <label for="captchaCode">验证码：</label>
                            <div class="captcha-input-group">
                                <input style="width: 50%;" type="text" id="captchaCode" placeholder="输入验证码">
                                <img id="captchaImg" src='/lib/yzmcode.php' alt="验证码" onclick="refreshCaptcha()">
                            </div>
                        </div>
                        <div class="report-submit">
                            <button type="button" style="background: #f5f5f5;color: #666;float: left;" class="submit-btn" id="closeReportModalsss">取 消</button>

                            <button style="float: right;" type="button" id="submitReportBtn" class="submit-btn">提 交</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="recommend-section">
            <h2 class="recommend-title">猜你喜欢</h2>
            <div class="recommend-grid">
                <!-- Wrapped each card with <a> tag instead of onclick -->
               <?php foreach ($tj_list as $key => $value) {?>

                <a href="<?php echo $ym.'/'.$py; ?>/loufeng/<?php echo $value['id'];?>.html" class="recommend-link">
                    <div class="recommend-item">
                        <!-- 将 recommend-info 移入 recommend-image-wrapper 内部 -->
                        <div class="recommend-image-wrapper">
                            <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-original="<?php echo $value['pic'];?>" alt="<?php echo $value['title'];?>" class="recommend-image lazy">
                            <div class="recommend-info">
                                <div class="recommend-item-title">
                                    <?php echo $value['title'];?>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
                <?php } ?>

            </div>
        </div>
    </main>

    <?php include_once '/comm/footer.php'; ?>
<?php if (!isset($infos['isopen']) ||  $infos['isopen'] ==0){ ?>
       <div id="contactModal" class="contact-modal hidden" onclick="closeContactModal(event)">
        <div class="contact-modal-content" onclick="event.stopPropagation()">
            <div class="contact-modal-header">
                <h3 class="contact-modal-title">联系方式</h3>
                <div class="contact-modal-close" onclick="closeContactModal()">
                    <svg viewBox="0 0 24 24">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                    </svg>
                </div>
            </div>
            
            <div class="contact-modal-body">
                <div class="modal-contact-item">
                    <div class="modal-contact-icon phone">
                        <svg viewBox="0 0 24 24">
                            <path d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56-.35-.12-.74-.03-1.01.24l-1.57 1.97c-2.83-1.35-5.48-3.9-6.89-6.83l1.95-1.66c.27-.28.35-.67.24-1.02-.37-1.11-.56-2.3-.56-3.53 0-.54-.45-.99-.99-.99H4.19C3.65 3 3 3.24 3 3.99 3 13.28 10.73 21 20.01 21c.71 0 .99-.63.99-1.18v-3.45c0-.54-.45-.99-.99-.99z"/>
                        </svg>
                    </div>
                    <div class="modal-contact-text" ><p style="font-size:14px;font-weight:500;">电话</p><span id="modal-mobile"></span></div>
                    <div class="modal-contact-copy" id="copy-mobile" onclick="copyContactText('modal-mobile')">
                         <svg viewBox="0 0 24 24"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/></svg>复制
                    </div>
                </div>
                <div class="modal-contact-item">
                    <div class="modal-contact-icon wechat">
                        
                        <svg t="1765473969257" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="81652" width="48" height="48"><path d="M699.6 357.2c11.9 0 23.6 0.9 35.2 2.1-31.6-145.8-189.2-254.2-369-254.2C164.7 105.1 0 240.8 0 413c0 99.4 54.8 181 146.4 244.3l-36.6 108.9 127.9-63.5c45.8 8.9 82.5 18.2 128.1 18.2 11.5 0 22.8-0.6 34.1-1.5-7.2-24.2-11.3-49.5-11.3-75.8 0-158.1 137.2-286.4 311-286.4zM503 259c27.5 0 45.8 18 45.8 45.2 0 27.1-18.2 45.3-45.8 45.3-27.4 0-54.9-18.2-54.9-45.3C448 277 475.5 259 503 259z m-256 90.5c-27.4 0-55.1-18.2-55.1-45.3 0-27.2 27.7-45.2 55.1-45.2 27.4 0 45.7 17.9 45.7 45.2-0.1 27.1-18.3 45.3-45.7 45.3z" p-id="81653" fill="#ffffff"></path><path d="M1024 639.3c0 70-44.2 141.3-124.3 200.8l-5.4 4 22.8 74.7-83.3-45.1-3.6 0.9c-35.3 8.8-71.7 17.8-107.4 17.8-166.3 0-301.6-113.6-301.6-253.2S556.5 386 722.8 386c163.3 0.1 301.2 116 301.2 253.3zM621.9 594c27.7 0 45.8-17.9 45.8-36.2 0-18-18.1-36.2-45.8-36.2-18.2 0-36.6 18.1-36.6 36.2 0 18.3 18.4 36.2 36.6 36.2z m201.2 0c27.4 0 45.8-17.9 45.8-36.2 0-18-18.3-36.2-45.8-36.2-18.1 0-36.3 18.1-36.3 36.2 0 18.3 18.2 36.2 36.3 36.2z m0 0" p-id="81654" fill="#ffffff"></path></svg>

                    </div>
                    <div class="modal-contact-text"><p style="font-size:14px;font-weight:500;">微信</p><span id="modal-weixin"></span></div>
                    <div class="modal-contact-copy" id="copy-weixin" onclick="copyContactText('modal-weixin')">
                        <svg viewBox="0 0 24 24"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/></svg>复制
                    </div>
                </div>
                <div class="modal-contact-item">
                    <div class="modal-contact-icon qq">
                        
                  <svg t="1765474296361" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="89956" width="48" height="48"><path d="M931.507451 840.8889c-23.05197 2.785996-89.719883-105.481862-89.719883-105.481862 0 62.689918-32.271958 144.493811-102.101866 203.571733 33.683956 10.383986 109.685856 38.33395 91.60588 68.84191-14.631981 24.685968-251.019672 15.761979-319.263582 8.07399-68.243911 7.68799-304.631601 16.611978-319.263582-8.07399-18.089976-30.49996 57.835924-58.427924 91.56588-68.84191-69.839909-59.077923-102.117866-140.889816-102.117866-203.583733 0 0-66.667913 108.267858-89.717883 105.481862-10.739986-1.299998-24.847967-59.287922 18.693975-199.407739 20.521973-66.047914 43.989942-120.955842 80.287895-211.557724C185.366427 196.125743 281.964301 0.012 512 0c227.473702 0.012 326.311573 192.265748 320.527581 429.925437 36.235953 90.445882 59.823922 145.699809 80.287894 211.555724 43.535943 140.119817 29.431961 198.105741 18.691976 199.407739z" fill="#ffffff" p-id="89957"></path></svg>
                        
                    </div>
                    <div class="modal-contact-text" ><p style="font-size:14px;font-weight:500;">QQ</p><span id="modal-qq"></span></div>
                    <div class="modal-contact-copy" id="copy-qq" onclick="copyContactText('modal-qq')">
                         <svg viewBox="0 0 24 24"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/></svg>复制
                    </div>
                </div>
                <div class="modal-contact-item">
                    <div class="modal-contact-icon cloud">
                        
                        <svg t="1765474433417" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="92013" width="48" height="48"><path d="M981.25962 291.954992a476.085215 476.085215 0 0 0-110.062711-152.296077 511.919586 511.919586 0 0 0-160.828069-102.383917 546.474158 546.474158 0 0 0-396.73768 0 511.919586 511.919586 0 0 0-162.534468 102.383917A478.218213 478.218213 0 0 0 42.74038 291.954992a451.769035 451.769035 0 0 0-42.659966 187.277249 461.154227 461.154227 0 0 0 127.979897 316.110344l23.036381 196.662441a36.260971 36.260971 0 0 0 35.834371 31.994974 36.260971 36.260971 0 0 0 17.917185-4.692596l148.45668-85.319931a547.327357 547.327357 0 0 0 358.343711-13.651189 511.919586 511.919586 0 0 0 162.534468-102.383917A478.218213 478.218213 0 0 0 981.25962 666.936089a453.902033 453.902033 0 0 0 0-374.981097z m-691.091441 243.161803a55.031355 55.031355 0 1 1 55.031356-55.031355 55.031355 55.031355 0 0 1-53.324957 55.031355z m220.125422 0a55.031355 55.031355 0 1 1 55.031356-55.031355A55.031355 55.031355 0 0 1 512 535.116795z m220.125422 0a55.031355 55.031355 0 1 1 55.031356-55.031355 55.031355 55.031355 0 0 1-53.751557 55.031355z" fill="#ffffff" p-id="92014"></path></svg>
                    </div>
                    <div class="modal-contact-text" ><p style="font-size:14px;font-weight:500;">与你</p><span id="modal-yuli"></span></div>
                    <div class="modal-contact-copy" id="copy-yuli" onclick="copyContactText('modal-yuli')">
                        <svg viewBox="0 0 24 24"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/></svg>复制
                    </div>
                </div>

                <div class="modal-contact-item" style="display: none;">

                    <div class="modal-contact-icon telegram">
                        <svg viewBox="0 0 24 24">
                            <path d="M9.78 18.65l.28-4.23 7.68-6.92c.34-.31-.07-.46-.52-.19L7.74 13.3 3.64 12c-.88-.25-.89-.86.2-1.3l15.97-6.16c.73-.33 1.43.18 1.15 1.3l-2.72 12.81c-.19.91-.74 1.13-1.5.71L12.6 16.3l-1.99 1.93c-.23.23-.42.42-.83.42z"/>
                        </svg>
                    </div>
                    <div class="modal-contact-text" id="modal-telegram"></div>
                    <div class="modal-contact-copy" id="copy-telegram" onclick="copyContactText('modal-telegram')">
                        <svg viewBox="0 0 24 24"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/></svg>复制
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
    <!-- Added full-screen album HTML structure from comm/photo_album.html -->
    <div class="fl_fullscreen_album" id="fl_fullscreen_album">
        <!-- 顶部控制栏 -->
        <div class="fl_album_header">
            <div class="fl_album_title">精选相册</div>
            <div class="fl_close_btn" id="fl_close_btn" aria-label="关闭相册">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
        </div>

        <!-- 相册主容器 -->
        <div class="fl_album_container">
            <!-- 图片显示区域 -->
            <div class="fl_image_wrapper">
                <img class="fl_album_image" id="fl_album_image" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" alt="相册图片">
            </div>

            <!-- 左侧导航按钮 -->
            <div class="fl_nav_btn fl_prev_btn" id="fl_prev_btn" aria-label="上一张">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/>
                </svg>
            </div>

            <!-- 右侧导航按钮 -->
            <div class="fl_nav_btn fl_next_btn" id="fl_next_btn" aria-label="下一张">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </div>

        <!-- 底部信息栏 -->
        <div class="fl_album_footer">
            <!-- 图片计数器 -->
            <div class="fl_counter">
                <span id="fl_current_num">1</span> / <span id="fl_total_num">4</span>
            </div>

            <!-- 缩略图导航 -->
            <div class="fl_thumbnails" id="fl_thumbnails">
                <!-- 缩略图将通过JavaScript动态生成 -->
            </div>
        </div>
    </div>

<script>
const hasViewedContact = false; 
const fl_albumPhotos = [
<?php
  foreach ($picsArray as $key => $value) {
  ?>
  { src: '<?php echo $value;?>', alt: '相册图片 <?php echo $key+1;?>' },

<?php }?>
];
</script>


<script>
document.addEventListener('DOMContentLoaded', function() {
const favoriteBtn = document.getElementById('favoriteBtn');
const favoriteText = document.getElementById('favoriteText');
const iscollectInput = document.getElementById('iscollect');

if (!favoriteBtn || !favoriteText || !iscollectInput) {
    return;
}

let isFavorited = parseInt(iscollectInput.value) === 1;

const uids=<?php echo $userData['userId']??0;?>;
favoriteBtn.addEventListener('click', function() {
    if (uids<=0) {
        showInfo('未登录，请先登录！');
        return;
    }
    // updateFavoriteDisplay();
    sendFavoriteRequest();
});

function updateFavoriteDisplay(idsss) {
    if (idsss == 1) {
        favoriteBtn.classList.add('favorited');
        favoriteIcon.setAttribute('fill', '#ff6b9d');
            favoriteIcon.setAttribute('stroke', '#ff6b9d');
        favoriteText.textContent = '已收藏';
        
    } else {
        favoriteBtn.classList.remove('favorited');
        favoriteIcon.setAttribute('fill', 'none');
            favoriteIcon.setAttribute('stroke', '#999');
        favoriteText.textContent = '收藏';
        
    }
}

function sendFavoriteRequest() {
    const infoId = window.currentInfoId;
    if (!infoId) {
        console.error('Info ID is not available');
        return;
    }

    fetch('/oper/info/user_collections.html', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
        },
        body: `info_id=${encodeURIComponent(infoId)}`,
    })
    .then(response => response.json())
    .then(result => {
        if (result.code ===200 ) {
            showInfo(result.msg);

            if (result.msg === '取消收藏成功') {
                updateFavoriteDisplay(0);
            }else{
                updateFavoriteDisplay(1);
            }
            
        }
    })
    .catch(error => {
        console.error('收藏请求失败:', error);
        // 如果请求失败，恢复之前的状态
     
    });
}

// 查看联系方式功能
const viewedContactBtn = document.getElementById('viewedContactBtn');
const pointsContactBtn = document.getElementById('pointsContactBtn');
const memberContactBtn = document.getElementById('memberContactBtn');
const isMoneySj = <?php echo $is_money_sj; ?>;
if (viewedContactBtn) {
    viewedContactBtn.addEventListener('click', () => sendContactRequest(3));
}

if (pointsContactBtn) {
    pointsContactBtn.addEventListener('click', () => sendContactRequest(2));
}

if (memberContactBtn) {

    memberContactBtn.addEventListener('click', () => {

        // console.log(isMoneySj)
        // Check if payment is required due to exhausted free attempts
        if (isMoneySj === 1) {
            // Prompt for payment
            if (confirm('今日免费解锁次数已用完，是否支付10元继续解锁？')) {
                // User confirms, send request for paid unlock (type 4)
                sendContactRequest(4);
            }
            // If user cancels, do nothing
        } else {
            // Normal member unlock (type 1)
            sendContactRequest(1);
        }
    });

    // memberContactBtn.addEventListener('click', () => sendContactRequest(1));
}



function sendContactRequest(type) {
    const infoId = window.currentInfoId;
    if (!infoId) {
        console.error('Info ID is not available');
        return;
    }

    fetch('/oper/info/seeinfos.html', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
        },
        body: `info_id=${encodeURIComponent(infoId)}&type=${type}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.code === 200) {
            updateContactModal(data.data);
            openContactModal();
            if(type !=3){
                    showSuccess(data.msg || '操作成功！', '操作成功');
                    
                    document.getElementById('vip-see-message').style.display = 'flex';
                    document.getElementById('vip-see-messageno').style.display = 'none';
                }
            // 隐藏积分和会员查看按钮，显示已查看按钮
            if (pointsContactBtn) pointsContactBtn.style.display = 'none';
            if (memberContactBtn) memberContactBtn.style.display = 'none';
            if (viewedContactBtn) viewedContactBtn.style.display = 'block';
        } else {
            showInfo(data.msg);
        }
    })
    .catch(error => {
        console.error('查看联系方式请求失败:', error);
        alert('网络请求失败，请稍后重试');
    });
}

function updateContactModal(contact) {
    // 更新手机
    const mobileElement = document.getElementById('modal-mobile');
    const copyMobileBtn = document.getElementById('copy-mobile');
    if (mobileElement) {
        mobileElement.textContent = contact.mobile || '未提供';
    }

    // 更新微信
    const weixinElement = document.getElementById('modal-weixin');
    const copyWeixinBtn = document.getElementById('copy-weixin');
    if (weixinElement) {
        weixinElement.textContent = contact.weixin || '未提供';
    }

    // 更新QQ
    const qqElement = document.getElementById('modal-qq');
    const copyQqBtn = document.getElementById('copy-qq');
    if (qqElement) {
        qqElement.textContent = contact.qq || '未提供';
    }

    // 更新邮箱
    const yuliElement = document.getElementById('modal-yuli');
    const copyYuliBtn = document.getElementById('copy-yuli');
    if (yuliElement) {
        yuliElement.textContent = contact.yuli || '未提供';
    }

    // 更新飞信
    const feijiElement = document.getElementById('modal-telegram');
    const copyFeijiBtn = document.getElementById('copy-telegram');
    if (feijiElement) {
        feijiElement.textContent = contact.feiji || '未提供';
    }
}

function openContactModal() {
    const contactModal = document.getElementById('contactModal');
    if (contactModal) {
        contactModal.classList.remove('hidden');
        document.body.classList.add('modal-open');
    }
}
});

function refreshCaptcha() {
    document.getElementById('captchaImg').src = '/lib/yzmcode.php?r=' + Math.random();
}

document.addEventListener('DOMContentLoaded', function() {
    refreshCaptcha();

    // Use the new modal ID and class names
    const reportModal = document.getElementById('reportModal');
    const reportBtn = document.getElementById('reportBtn');
    const closeBtn = document.getElementById('closeReportModal');
    const reportContent = document.getElementById('reportDescription');
    const submitBtn = document.getElementById('submitReportBtn');
    const captchaInput = document.getElementById('captchaCode');

    const closeReportModalsss = document.getElementById('closeReportModalsss');

    if (!reportModal || !reportBtn) {
        return;
    }

    function openReportModal() {
        reportModal.classList.add('show');
        document.body.style.overflow = 'hidden';
        refreshCaptcha();
    }

    function closeReportModal() {
        reportModal.classList.remove('show');
        document.body.style.overflow = '';
        if (reportContent) reportContent.value = '';
        if (captchaInput) captchaInput.value = '';
    }

    reportBtn.addEventListener('click', openReportModal);

    if (closeBtn) {
        closeBtn.addEventListener('click', closeReportModal);
    }
    if (closeReportModalsss) {
        closeReportModalsss.addEventListener('click', closeReportModal);
    }

    // Close modal by clicking overlay
    reportModal.querySelector('.modal-overlay').addEventListener('click', closeReportModal);

    if (submitBtn) {
        submitBtn.addEventListener('click', function() {
            const content = reportContent.value.trim();
            const captcha = captchaInput.value.trim();
            const infoId = window.currentInfoId;

            if (!content) {
                showInfo('请填写举报内容');
                return;
            }

            if (!captcha) {
                showInfo('请输入验证码');
                return;
            }

            if (!infoId) {
                showInfo('无法获取信息');
                return;
            }

            fetch('/oper/info/inforeport.html', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                },
                body: `info_id=${encodeURIComponent(infoId)}&jb_msg=${encodeURIComponent(content)}&captcha=${encodeURIComponent(captcha)}`,
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                try {
                    const result = JSON.parse(data);
                    
                    if (result.code === 200 || result.success) {
                        showSuccess(result.msg || '举报成功');
                        closeReportModal();
                    } else {
                        showError(result.msg || '举报失败，请稍后重试');
                        refreshCaptcha(); // Refresh captcha if submission fails but not network error
                    }
                } catch (e) {
                    // If response is not JSON, treat it as a success message
                    showSuccess(data || '举报成功');
                    closeReportModal();
                }
            })
            .catch(error => {
                console.error('举报请求失败:', error);
                showInfo('举报失败，请稍后重试');
                refreshCaptcha(); // Refresh captcha on network error
            });
        });
    }
});

</script>

<script src="/js/copy.js"></script>
<script src="/js/xc.js"></script>

<?php  include_once 'comm/alert_modal.php'; ?>
    
</body>
</html>
