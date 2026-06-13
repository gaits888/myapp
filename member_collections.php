<?php
/**
 * 用户个人收藏信息列表页面
 */
// 引入load.php加载必要的文件
require_once '../../load_api.php';

// 获取POST数据中的分页参数
$page = isset($postData['page']) && intval($postData['page']) > 0 ? intval($postData['page']) : 1;
$pageSize = isset($postData['pageSize']) && intval($postData['pageSize']) > 0 ? intval($postData['pageSize']) : 4;
$infotype = isset($postData['infotype']) ? intval($postData['infotype']) : 0;

if (!in_array($postData['infotype'],[1,2,3,4])) {
    errorResponse('缺少必要参数');
}
$baseWhere = [];
$baseWhere[] = ['user_id', '=', $user_id];
$baseWhere[] = ['status', '=', 1];
$seeurl = 'by_detail';

if ($infotype == 1 || $infotype == 2) {
    // 论坛(1)/高端(2)：usersc.infotype 已能正确区分，直接按 infotype 过滤并由数据库分页
    $where = $baseWhere;
    $where[] = ['infotype', '=', $infotype];

    $total = db('usersc')->where($where)->count();
    $list = db('usersc')
        ->where($where)
        ->field('id as collect_id,infotype, sc_time as collect_time,info_id')
        ->order('collect_id', 'DESC')
        ->page($page, $pageSize)
        ->select();

    if ($infotype == 1) {
        // 论坛收藏
        $seeurl = 'showinfo';
        foreach ($list as $k => $item) {
            $infos = db3('infob')
            ->join('areab city_area', 'infob.city = city_area.id', 'LEFT')
            ->where('infob.id', $item['info_id'])
            ->field('infob.id, infob.title,infob.uid as info_user_id,infob.times, infob.pics,infob.osspics,infob.oss, infob.sh,city_area.fullname as city_name')
            ->find();
            if (!empty($infos)) {
                foreach ($infos as $key => $value) {
                    $list[$k][$key] = $value;
                }
                $list[$k]['title'] = mb_substr($infos['title'], 0, 6);
                $list[$k]['pic'] = z_imgurl($infos['pics'], $infos['osspics'], 1, $infos['oss']);
            }
        }
    } else {
        // 高端收藏
        $seeurl = 'jiaoyou';
        foreach ($list as $k => $item) {
            $gdinfo = db3('gdb')
            ->where('id', $item['info_id'])
            ->field(' id, uname as title,uid as info_user_id, times, pics, bdpics, flag as sh, city as city_name')
            ->find();
            if (!empty($gdinfo)) {
                foreach ($gdinfo as $key => $value) {
                    $list[$k][$key] = $value;
                }
                $list[$k]['title'] = mb_substr($gdinfo['title'], 0, 6);
                $list[$k]['pic'] = z_imgurl($gdinfo['bdpics'], $gdinfo['pics'], 2);
            }
        }
    }

} else {
    // 伴游(3)/包养(4)：两者都存在于 byb 表，usersc.infotype 无法区分，
    // 必须根据 byb.typeid 区分（typeid==1 为包养，其余为伴游）。
    // 由于 usersc(db) 与 byb(db3) 不在同一数据库连接，无法 join，
    // 因此取出该用户全部 byb 收藏后在 PHP 端按 typeid 过滤并分页。
    $seeurl = ($infotype == 4) ? 'baoyang' : 'banyou';

    $where = $baseWhere;
    $where[] = ['infotype', 'in', [3, 4]];

    $allCollections = db('usersc')
        ->where($where)
        ->field('id as collect_id,infotype, sc_time as collect_time,info_id')
        ->order('collect_id', 'DESC')
        ->select();

    $filtered = [];
    foreach ($allCollections as $item) {
        $byinfo = db3('byb')
        ->where('id', $item['info_id'])
        ->field('id,uid as info_user_id, uname as title, times, pics, osspics,oss,flag as sh, jg as city_name, typeid')
        ->find();
        if (empty($byinfo)) {
            continue;
        }
        // typeid==1 为包养，其余为伴游；按请求的分类过滤
        $isBaoyang = (intval($byinfo['typeid']) == 1);
        if (($infotype == 4 && !$isBaoyang) || ($infotype == 3 && $isBaoyang)) {
            continue;
        }
        $row = $item;
        foreach ($byinfo as $key => $value) {
            $row[$key] = $value;
        }
        $row['title'] = mb_substr($byinfo['title'], 0, 6);
        $row['pic'] = z_imgurl($byinfo['pics'], $byinfo['osspics'], 3, $byinfo['oss']);
        $filtered[] = $row;
    }

    // PHP 端分页
    $total = count($filtered);
    $offset = ($page - 1) * $pageSize;
    $list = array_slice($filtered, $offset, $pageSize);
}

// 构建返回数据
$responseData = [
    'total' => $total,
    'page' => $page,
    'pageSize' => $pageSize,
    'seeurl' => $seeurl,
    'totalPages' => ceil($total / $pageSize),
    'list' => $list
];

// 返回成功响应
successResponse($responseData, 'success');
