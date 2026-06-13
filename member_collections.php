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
$where = [];
$where[]= ['user_id', '=' ,$user_id];
$where[]= ['status', '=' ,1];
$where[]= ['infotype', '=' ,$infotype];

$total = db('usersc')->where($where)->count();
$seeurl = 'by_detail';
// 查询数据
$list = db('usersc')
    ->where($where)
    ->field('id as collect_id,infotype, sc_time as collect_time,info_id')
    ->order('collect_id', 'DESC')
    ->page($page, $pageSize)
    ->select();
if ($infotype == 1) {
    // 论坛收藏
    $seeurl = 'showinfo';
    // 处理图片字段：从pics中提取第一张图片到pic字段
    foreach ($list as $k =>$item) {
        $infos = db3('infob')
        ->join('areab city_area', 'infob.city = city_area.id', 'LEFT')
        ->where('infob.id',$item['info_id'])
        ->field('infob.id, infob.title,infob.uid as info_user_id,infob.times, infob.pics,infob.osspics,infob.oss, infob.sh,city_area.fullname as city_name')
        ->find();
        if (!empty($infos)) {
            foreach ($infos as $key => $value) {
                $list[$k][$key] = $value;
            }
            $list[$k]['title'] = mb_substr($infos['title'],0,6);
            $list[$k]['pic'] = z_imgurl($infos['pics'],$infos['osspics'],1,$infos['oss']);

            

        }
    }

} elseif ($infotype == 2) {
    // 高端收藏
    $seeurl = 'jiaoyou';
    // 处理图片字段：从pics中提取第一张图片到pic字段
    foreach ($list as $k =>$item){
        $gdinfo =   db3('gdb')
        ->where('id',$item['info_id'])
        ->field(' id, uname as title,uid as info_user_id, times, pics, bdpics, flag as sh, city as city_name')
        ->find();
        if (!empty($gdinfo)) {
            foreach ($gdinfo as $key => $value) {
                $list[$k][$key] = $value;
            }
            $list[$k]['title'] = mb_substr($gdinfo['title'],0,6);
            $list[$k]['pic'] = z_imgurl($gdinfo['bdpics'],$gdinfo['pics'],2);
            
        }
    }
    
} elseif ($infotype == 3 || $infotype == 4) {
    // 伴游/伴友收藏
     if ($typeinfo == 3 ) {
        $seeurl = 'banyou';
    }else{
        $seeurl = 'baoyang';
    }
    // 处理图片字段：从pics中提取第一张图片到pic字段
    foreach($list as $k =>$item){

        $byinfo = db3('byb')
        ->where('id',$item['info_id'])
        ->field('id,uid as info_user_id, uname as title, times, pics, osspics,oss,flag as sh, jg as city_name')
        ->find();
        if (!empty($byinfo)) {
            foreach ($byinfo as $key => $value) {
                $list[$k][$key] = $value;
            }
            $list[$k]['title'] = mb_substr($byinfo['title'],0,6);
            $list[$k]['pic'] = z_imgurl($byinfo['pics'],$byinfo['osspics'],3,$byinfo['oss']);
        }
        
    }
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
