<?php
/**
 * 用户反馈接口
 */
// load_api.php 自动验证用户是否登录
require_once '../../load_api.php';
//验证码验证
require_once '../yzm/captcha.php';

$content = $postData['content'] ?? '';
$pics = $postData['pics'] ?? '';

// 验证必填字段
if (empty(trim($content))) {
    errorResponse('问题描述不能为空');
}
// 构建插入数据
$insertData = [
    'content' => trim($content),
    'user_id' => $user_id,
    'pics' => $pics,
    'addtime' => time()
];
// 插入数据到数据库
$result = db('fl_user_feeedback')->insert($insertData);

if ($result) {
    successResponse([],'反馈提交成功');
} else {
    errorResponse('反馈提交失败，请稍后重试');
}