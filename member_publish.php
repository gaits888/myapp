<?php
/**
 * 用户个人发布信息列表页面
 */

// 引入load.php加载必要的文件
require_once '../../load_api.php';

// 获取POST数据中的分页参数
$page = isset($postData['page']) ? intval($postData['page']) : 1;
$pageSize = isset($postData['pageSize']) ? intval($postData['pageSize']) : 10;
$typeinfo = isset($postData['infotype']) ? intval($postData['infotype']) : 1;


//大分类
if (isset($postData['typeinfo']) && in_array($postData['typeinfo'],[1,2,3,4])) {
    $typeinfo = $postData['typeinfo'];
}
$user_vipclass =  db('userb')->where('id', $user_id)->value('vipclass');

if ($typeinfo == 1) {
    // 构建查询条件
    $where = [];
    $where['infob.uid'] = $user_id;
    if (isset($postData['status']) && intval($postData['status']) >= 0) {
        // 添加表前缀infob，使用键值对格式
        $where['infob.sh'] = intval($postData['status']);
    }
    // 查询总数 - 为了不影响计数逻辑，单独执行计数查询
    $total =  db3('infob')->where($where)->count();
    // 查询数据 - 联合查询areab表获取城市和区县名称
    $list = db3('infob')
        ->leftJoin('areab city_area', 'infob.city = city_area.id')
        ->where($where)
        // 限制查询字段为：id、title、city、cityid、times、pics以及城市和区县名称
        ->field('infob.id, infob.title, infob.city, infob.cityid, infob.times, infob.price,infob.pics,infob.osspics,infob.oss,infob.sh,infob.flag,infob.iszd, city_area.fullname as city_name')
        ->order('infob.iszd', 'DESC')
        ->order('infob.isrz', 'DESC')
        ->order('infob.fbtime', 'DESC')
        ->page($page, $pageSize)
        ->select();
    // echo $db->getLastSql();
    // 处理图片字段：从pics中提取第一张图片到pic字段
    foreach ($list as &$item) {
        // pics可能包含多个图片，用|分隔，提取第一张
        $item['pic'] = z_imgurl($item['pics'],$item['osspics'],1,$item['oss']);
        $item['vipclass'] = $user_vipclass;
        $item['infotype'] = 1;
    }
    unset($item);
    // 构建返回数据
    $responseData = [
        'total' => $total,
        'page' => $page,
        'pageSize' => $pageSize,
        'editurl' => 'publish_edit',
        'seeurl' => 'showinfo',
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
    $where['gdb.uid'] = $user_id;
    if (isset($postData['status']) && intval($postData['status']) >= 0) {
        // 添加表前缀gdb，使用键值对格式
        $where['gdb.flag'] = intval($postData['status']);
    }
    // 查询总数 - 为了不影响计数逻辑，单独执行计数查询
    $total = db3('gdb')->where($where)->count();
    // 查询数据 - 联合查询areab表获取城市和区县名称
    $list = db3('gdb')
        ->leftJoin('areab city_area', 'gdb.cityid = city_area.id')
        ->where($where)
        // 限制查询字段为：id、title、city、cityid、times、pics以及城市和区县名称
        ->field('gdb.id, gdb.uname as title, gdb.city, gdb.bdpics, gdb.cityid, gdb.times, gdb.price, gdb.pics,gdb.flag,gdb.iszd, city_area.fullname as city_name')
        ->order('gdb.iszd', 'DESC')
        ->order('gdb.isrz', 'DESC')
        ->order('gdb.fbtime', 'DESC')
        ->page($page, $pageSize)
        ->select();
    // 处理图片字段：从pics中提取第一张图片到pic字段
    foreach ($list as &$item) {
        // pics可能包含多个图片，用|分隔，提取第一张
        // $item['pic'] = z_imgurl($item['bdpics'],$item['pics'],2);

        //高端使用本地第一张
        if (isset($item['bdpics']) && !empty($item['bdpics'])) {
            $picsArray = explode('|', $item['bdpics']);
            $item['pic']  = !empty($picsArray[0]) ? $picsArray[0] : '';
        } else {
            $item['pic']  = '';
        }
        $item['vipclass'] = $user_vipclass;
        $item['infotype'] = 2;
    }
    unset($item);
    // 构建返回数据
    $responseData = [
        'total' => $total,
        'page' => $page,
        'pageSize' => $pageSize,
        'editurl' => 'highend_edit',
        'seeurl' => 'jiaoyou',
        'totalPages' => ceil($total / $pageSize),
        'list' => $list
    ];
    // 返回成功响应
    successResponse($responseData, 'success');
}


//包伴发布
if ($typeinfo == 3 || $typeinfo == 4) {
    // 构建查询条件
    $where = [];
    if ($typeinfo == 3 ) {
        $seeurl = 'banyou';
        $where['byb.typeid'] = 0;
    }else{
        $where['byb.typeid'] = 1;
        $seeurl = 'baoyang';
    }
    
    $where['byb.uid'] = $user_id;
    if (isset($postData['status']) && intval($postData['status']) >= 0) {
        // 添加表前缀byb，使用键值对格式
        $where['byb.flag'] = intval($postData['status']);
    }
    // 查询总数 - 为了不影响计数逻辑，单独执行计数查询
    $total = db3('byb')->where($where)->count();
    // 查询数据 - 联合查询areab表获取城市和区县名称
    $list = db3('byb')
        ->where($where)
        // 限制查询字段为：id、title、city、cityid、times、pics以及城市和区县名称
        ->field('byb.id, byb.uname as title, byb.times, byb.pics,byb.osspics,byb.oss,byb.flag, byb.jg as city_name, byb.city as district_name,byb.iszd,byb.price')
        ->order('byb.iszd', 'DESC')
        ->order('byb.isrz', 'DESC')
        ->order('byb.fbtime', 'DESC')
        ->page($page, $pageSize)
        ->select();
    // echo $db->getLastSql();
    // 处理图片字段：从pics中提取第一张图片到pic字段
    foreach ($list as &$item) {
        // pics可能包含多个图片，用|分隔，提取第一张
        $item['pic'] = z_imgurl($item['pics'],$item['osspics'],3,$item['oss']);
        $item['vipclass'] = $user_vipclass;
        $item['infotype'] = $typeinfo;
    }
    unset($item);
    // 构建返回数据
    $responseData = [
        'total' => $total,
        'page' => $page,
        'pageSize' => $pageSize,
        'editurl' => 'by_edit',
        'seeurl' => $seeurl,
        'totalPages' => ceil($total / $pageSize),
        'list' => $list
    ];


    // 返回成功响应
    successResponse($responseData, 'success');
}
errorResponse('error');