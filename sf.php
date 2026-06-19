<?php
error_reporting(0);
header('Content-Type:application/json;charset=utf-8');
date_default_timezone_set('PRC');
require ('../include/db2.php');
require ('../include/function.php');

// 统一的 JSON 返回：成功 code:1，失败 code:0
function api_return($code, $msg = '') {
    echo json_encode(array(
        'code' => $code,
        'msg'  => $msg,
    ), JSON_UNESCAPED_UNICODE);
    exit();
}

// 必须带 action
if (!isset($_POST['action'])) {
    api_return(0, '非法请求');
}

$uid   = isset($_POST['uid'])   ? trim($_POST['uid'])   : '';
$money = isset($_POST['money']) ? trim($_POST['money']) : '';
$token = isset($_POST['token']) ? trim($_POST['token']) : '';

$key = '!#$%7482djdj372';

sf($uid, $key, $token, $money);

function sf($uid, $key, $token, $money) {
    global $db;
    $ip = getip();

    // 1. 参数校验
    $uid = intval($uid);
    if ($uid <= 0) {
        api_return(0, '用户ID无效');
    }

    // 充值金额：必须为大于 0 的数字
    if ($money === '' || !is_numeric($money)) {
        api_return(0, '充值金额无效');
    }
    $price = round(floatval($money), 2);
    if ($price <= 0) {
        api_return(0, '充值金额必须大于0');
    }

    // 2. 签名校验
    $stoken = md5($uid . $key);
    if ($token !== $stoken) {
        api_return(0, '签名校验失败');
    }

    // 3. 查询用户是否存在
    $res = $db->query("select id,money from `userb` where id='" . $uid . "' limit 1");
    if (!$res || $db->num_row($res) == 0) {
        api_return(0, '用户不存在');
    }

    $row     = $db->fetch($res);
    $uid     = intval($row['id']);
    $oldmoney = floatval($row['money']);

    // 4. 累加余额（修复原来使用未定义 $price 且丢失充值金额的问题）
    $newmoney = round($oldmoney + $price, 2);
    $upRes = $db->query("update `userb` set money='" . $newmoney . "' where id='" . $uid . "' limit 1");
    if (!$upRes) {
        api_return(0, '余额更新失败');
    }

    // 5. 写入充值流水
    $ctime = time();
    $dno   = $ctime . mt_rand(100, 999);
    $sql = "insert into `payb` (`id`,`dno`,`uid`,`payid`,`money`,`ctime`,`paytime`,`flag`,`ip`) "
         . "VALUES (NULL,'" . $dno . "','" . $uid . "','9','" . $price . "','" . $ctime . "','" . $ctime . "','1','" . $ip . "')";
    $db->query($sql);

    // 6. 成功
    api_return(1, '充值成功');
}
?>
