<?php
/**
 * 信息查询接口(indexinfo.php)
 * 用于处理信息查询请求，支持关键词搜索、排序和分页
 * 说明：city代表市、cityid代表区域
 */

// 引入load.php加载必要的文件
require_once '../../load.php';

// 确保只接受POST请求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('只接受POST请求');
}


$userToken = Cookie::get('userToken');

// 判断token是否存在
if (empty($userToken)) {
    errorResponse('用户未登录，请先登录');
}

// 解密用户token
$userId = decryptUserToken($userToken);

// 判断token解密是否成功
if ($userId === false) {
    errorResponse('用户身份验证失败，请重新登录');
}
// 初始化数据库连接
$dbuser = new DbOperation();
$db3 = new DbOperation3();
// 根据用户ID查询用户信息
$userInfo = $dbuser->table('userb')
    ->where(['id' => $userId])
    ->find();

// 判断用户是否存在
if (empty($userInfo)) {
    errorResponse('用户不存在');
}


// 获取POST数据
$postData = $_POST;
// var_dump($postData);die;
// 初始化数据库连接
$db = new DbOperation();



$typeinfo = 1;
if (isset($postData['typeinfo']) && in_array($postData['typeinfo'],[1,2,3,4])) {
    $typeinfo = $postData['typeinfo'];
}

if ($typeinfo == 1) {

    // 构建查询条件
    $where = [];
    $where['infob.uid'] = $userId;

    $order = 'infob.iszd desc,infob.fbtime desc';

    if (isset($postData['status']) && intval($postData['status']) >= 0) {

        // 添加表前缀infob，使用键值对格式
        $where['infob.flag'] = intval($postData['status']);
    }
    // var_dump($where);die;

    // 分页参数
    $page = isset($postData['page']) && intval($postData['page']) > 0 ? intval($postData['page']) : 1;
    $pageSize = isset($postData['pageSize']) && intval($postData['pageSize']) > 0 ? intval($postData['pageSize']) : 10;
    $offset = ($page - 1) * $pageSize;

    // 查询总数 - 为了不影响计数逻辑，单独执行计数查询
    $total = $db3->table('infob')
        ->join('areab as city_area ON infob.city = city_area.id', 'LEFT')
        ->where($where)
        ->count();

    // 查询数据 - 联合查询areab表获取城市和区县名称
    $list = $db3->table('infob')
        ->join('areab as city_area ON infob.city = city_area.id', 'LEFT')
        ->where($where)
        // 限制查询字段为：id、title、city、cityid、times、pics以及城市和区县名称
        ->field('infob.id, infob.title, infob.city, infob.cityid, infob.times, infob.pics,infob.osspics,infob.oss,infob.sh,infob.flag,infob.iszd, city_area.fullname as city_name')
        ->order($order)
        ->limit($offset, $pageSize)
        ->select();
    // echo $db->getLastSql();
    // 处理图片字段：从pics中提取第一张图片到pic字段
    foreach ($list as &$item) {
        
        $item['pic'] = z_imgurl($item['pics'],$item['osspics'],1,$item['oss']);
        $item['vipclass'] = $userInfo['vipclass'];
        $item['infotype'] = 1;
        if ($postData['status'] != 1) {
            $item['times'] = 0;
        }
    }
    unset($item);
    // 构建返回数据
    $responseData = [
        'total' => $total,
        'page' => $page,
        'pageSize' => $pageSize,
        'editurl' => 'publish_edit',
        'seeurl' => 'publish_detail',
        'totalPages' => ceil($total / $pageSize),
        'list' => $list
    ];

    // 返回成功响应
    successResponse($responseData, 'success');
}


//高端发布
if ($typeinfo == 2) {

    // 构建查询条件
    $where = [];
    $where['gdb.uid'] = $userId;

    $order = 'gdb.iszd desc,gdb.fbtime desc';

    if (isset($postData['status']) && intval($postData['status']) >= 0) {

        // 添加表前缀gdb，使用键值对格式
        $where['gdb.flag'] = intval($postData['status']);
    }
    // var_dump($where);die;

    // 分页参数
    $page = isset($postData['page']) && intval($postData['page']) > 0 ? intval($postData['page']) : 1;
    $pageSize = isset($postData['pageSize']) && intval($postData['pageSize']) > 0 ? intval($postData['pageSize']) : 10;
    $offset = ($page - 1) * $pageSize;

    // 查询总数 - 为了不影响计数逻辑，单独执行计数查询
    $total = $db3->table('gdb')
        ->where($where)
        ->count();

    // 查询数据 - 联合查询areab表获取城市和区县名称
    $list = $db3->table('gdb')
        ->join('areab as city_area ON gdb.cityid = city_area.id', 'LEFT')
        ->where($where)
        // 限制查询字段为：id、title、city、cityid、times、pics以及城市和区县名称
        ->field('gdb.id, gdb.uname as title, gdb.city, gdb.cityid, gdb.times, gdb.bdpics,gdb.pics,gdb.flag,gdb.iszd, city_area.fullname as city_name')
        ->order($order)
        ->limit($offset, $pageSize)
        ->select();
    // echo $db->getLastSql();
    // 处理图片字段：从pics中提取第一张图片到pic字段
    foreach ($list as &$item) {

        if (isset($item['bdpics']) && !empty($item['bdpics'])) {
            $picsArray = explode('|', $item['bdpics']);
            $img2 = !empty($picsArray[0]) ? $picsArray[0] : '';
        } else {
            $img2 = '';
        }

        if (substr($img2,0,1) === '/') {
            $re= !empty($img2) ? $img2 : '';
        }else{
            $re = !empty($img2) ? '/'.$img2 : '';
        }

        $item['pic'] =  $re;

        // $item['pic'] = z_imgurl($item['bdpics'],$item['pics'],2);
        $item['vipclass'] = $userInfo['vipclass'];
        $item['infotype'] = 2;
        if ($postData['status'] != 1) {
            $item['times'] = 0;
        }
    }
    unset($item);
    // 构建返回数据
    $responseData = [
        'total' => $total,
        'page' => $page,
        'pageSize' => $pageSize,
        'editurl' => 'highend_edit',
        'seeurl' => 'highend_detail',
        'totalPages' => ceil($total / $pageSize),
        'list' => $list
    ];

    // 返回成功响应
    successResponse($responseData, 'success');
}

//高端发布
if ($typeinfo == 3 || $typeinfo == 4) {

    // 构建查询条件
    $where = [];

    if ($typeinfo == 3 ) {
        $where['byb.typeid'] = 0;

        $editurl = 'by_edit';
        $seeurl = 'by_detail';
        $infotype =3;
    }else{
        $where['byb.typeid'] = 1;
        $editurl = 'by_edit';
        $seeurl = 'by_detail';
        $infotype =4;
    }

    $where['byb.uid'] = $userId;

    $order = 'byb.iszd desc,byb.fbtime desc';

    if (isset($postData['status']) && intval($postData['status']) >= 0) {

        // 添加表前缀byb，使用键值对格式
        $where['byb.flag'] = intval($postData['status']);
    }
    // var_dump($where);die;

    // 分页参数
    $page = isset($postData['page']) && intval($postData['page']) > 0 ? intval($postData['page']) : 1;
    $pageSize = isset($postData['pageSize']) && intval($postData['pageSize']) > 0 ? intval($postData['pageSize']) : 10;
    $offset = ($page - 1) * $pageSize;
// var_dump($where);
    // 查询总数 - 为了不影响计数逻辑，单独执行计数查询
    $total = $db3->table('byb')
        ->where($where)
        ->count();

    // 查询数据 - 联合查询areab表获取城市和区县名称
    $list = $db3->table('byb')
        ->where($where)
        // 限制查询字段为：id、title、city、cityid、times、pics以及城市和区县名称
        ->field('byb.id, byb.uname as title, byb.times, byb.pics,byb.osspics,byb.oss,byb.flag, byb.jg as city_name, byb.city as district_name,byb.iszd')
        ->order($order)
        ->limit($offset, $pageSize)
        ->select();
    // echo $db->getLastSql();
    // 处理图片字段：从pics中提取第一张图片到pic字段
    foreach ($list as &$item) {
        $item['pic'] = z_imgurl($item['pics'],$item['osspics'],3,$item['oss']);
        $item['vipclass'] = $userInfo['vipclass'];
        $item['infotype'] = $infotype;
        if ($postData['status'] != 1) {
            $item['times'] = 0;
        }
    }
    unset($item);
    // 构建返回数据
    $responseData = [
        'total' => $total,
        'page' => $page,
        'pageSize' => $pageSize,
        'editurl' => $editurl,
        'seeurl' => $seeurl,
        'totalPages' => ceil($total / $pageSize),
        'list' => $list
    ];

    // 返回成功响应
    successResponse($responseData, 'success');
}
errorResponse('error');