<?php 
include_once 'loaduser.php';
include_once 'comm/alert_modal.php';
$pageTitle = "修改资料";

$info_id = intval($_GET['id'] ?? 0);
if ($info_id <= 0) {
    echo '<script>alert("参数错误");history.back();</script>';
    exit;
}

$info = db3('infob')->where(['id' => $info_id, 'uid' => $user_id])->find();
if (!$info) {
    echo '<script>alert("信息不存在或无权编辑");history.back();</script>';
    exit;
}

$images = !empty($info['pics']) ? explode('|', $info['pics']) : [];
$videos = !empty($info['videos']) ? explode('|', $info['videos']) : [];


$citypid = db('areab')->where('id', $info['city'])->value('pid');


if ($citypid == 0) {
    $citypid = $info['city'];
}

$provinceArr = db('areab')->where(['pid' => 0])->field('id, fullname')->select();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title><?php echo $pageTitle; ?></title>
<link rel="stylesheet" href="/css/imgvideo.css?t=<?php echo time(); ?>">
<link rel="stylesheet" href="/css/fb.css?t=<?php echo time(); ?>">
<link rel="stylesheet" href="/css/comm.css?t=<?php echo time(); ?>">
</head>
<body>
    <?php include 'comm/header.php'; ?>

    <div class="publish-container">
       
  <input type="hidden" id="provincein" value="<?php echo $info['citypid']; ?>">
  <input type="hidden" id="cityin" value="<?php echo $info['city']; ?>">
  <input type="hidden" id="districtin" value="<?php echo $info['cityid']; ?>">
        <form id="publishForm">
            <!-- 基本信息部分 -->
            <div class="form-section">
                <h3 class="section-title">
                    基本信息
                    <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </h3>
                
                <div class="form-item">
                    <label class="form-label required">
                        信息标题
                    </label>
                    <input type="text" name="title" class="form-input" placeholder="请输入吸引人的标题" value="<?php echo htmlspecialchars($info['title']); ?>">
                    <div class="form-error" data-field="title">请输入信息标题</div>
                </div>
                
                <div class="form-item">
                    <label class="form-label required">
                        所属地区
                    </label>
                    <select name="province" id="province" class="form-select">
                        <option value="">请选择省份</option>
                        <?php foreach ($provinceArr as $k => $v) { ?>
                           <option value="<?php echo $v['id']; ?>" <?php echo $citypid == $v['id'] ? 'selected' : ''; ?>><?php echo $v['fullname']; ?></option>
                        <?php } ?>
                    </select>
                    <div class="form-error" data-field="province">请选择省份</div>
                </div>
                
                <div class="form-item">
                    <label class="form-label required">所在城市</label>
                    <select name="city" id="city" class="form-select">
                        <option value="">请先选择省份</option>
                    </select>
                    <div class="form-error" data-field="city">请选择城市</div>
                </div>
                
                <div class="form-item">
                    <label class="form-label required">所在区县</label>
                    <select name="district" id="district" class="form-select">
                        <option value="">请先选择城市</option>
                    </select>
                    <div class="form-error" data-field="district">请选择区县</div>
                </div>
                
                <div class="form-item">
                    <label class="form-label required">
                        发布类别
                    </label>
                    <select name="typeid" class="form-select">
                        <option value="">请选择分类</option>
                        <?php foreach ($typeArr as $k => $v) { ?>
                        <option value="<?php echo $k;?>" <?php echo $info['typeid'] == $k ? 'selected' : ''; ?>><?php echo $v;?></option>
                        <?php } ?>
                    </select>
                    <div class="form-error" data-field="typeid">请选择发布类别</div>
                </div>
                
                <div class="form-item">
                    <label class="form-label required">
                        信息来源
                    </label>
                    <select name="laiyuan" class="form-select">
                        <option value="">请选择来源</option>
                        <option value="自己开发" <?php echo $info['laiyuan'] == '自己开发' ? 'selected' : ''; ?>>自己开发</option>
                        <option value="网上看到" <?php echo $info['laiyuan'] == '网上看到' ? 'selected' : ''; ?>>网上看到</option>
                        <option value="朋友分享" <?php echo $info['laiyuan'] == '朋友分享' ? 'selected' : ''; ?>>朋友分享</option>
                        <option value="其它论坛" <?php echo $info['laiyuan'] == '其它论坛' ? 'selected' : ''; ?>>其它论坛</option>
                    </select>
                    <div class="form-error" data-field="laiyuan">请选择信息来源</div>
                </div>
                
                <div class="form-item">
                    <label class="form-label required">
                        综合评价
                    </label>
                    <select name="pj" class="form-select">
                        <option value="">请选择评价</option>
                        <option value="1" <?php echo $info['pj'] == '1' ? 'selected' : ''; ?>>★★★★★ 优秀</option>
                        <option value="2" <?php echo $info['pj'] == '2' ? 'selected' : ''; ?>>★★★★☆ 良好</option>
                        <option value="3" <?php echo $info['pj'] == '3' ? 'selected' : ''; ?>>★★★☆☆ 一般</option>
                        <option value="4" <?php echo $info['pj'] == '4' ? 'selected' : ''; ?>>★★☆☆☆ 较差</option>
                        <option value="5" <?php echo $info['pj'] == '5' ? 'selected' : ''; ?>>★☆☆☆☆ 很差</option>
                    </select>
                    <div class="form-error" data-field="pj">请选择综合评价</div>
                </div>
            </div>

            <!-- 详细信息部分 -->
            <div class="form-section">
                <h3 class="section-title">
                    详细信息
                    <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </h3>
                
                <div class="form-item">
                    <label class="form-label">
                        场所人数
                    </label>
                    <select name="nums" class="form-select">
                        <option value="">请选择场地人数</option>
                        <option value="个人兼职" <?php echo $info['nums'] == '个人兼职' ? 'selected' : ''; ?>>个人兼职</option>
                        <option value="2-5人" <?php echo $info['nums'] == '2-5人' ? 'selected' : ''; ?>>2-5人</option>
                        <option value="5-10人" <?php echo $info['nums'] == '5-10人' ? 'selected' : ''; ?>>5-10人</option>
                        <option value="10-20人" <?php echo $info['nums'] == '10-20人' ? 'selected' : ''; ?>>10-20人</option>
                        <option value="20-50人" <?php echo $info['nums'] == '20-50人' ? 'selected' : ''; ?>>20-50人</option>
                        <option value="50人以上" <?php echo $info['nums'] == '50人以上' ? 'selected' : ''; ?>>50人以上</option>
                        <option value="未知" <?php echo $info['nums'] == '未知' ? 'selected' : ''; ?>>未知</option>
                    </select>
                </div>
                
                <div class="form-item">
                    <label class="form-label">
                        年龄大小
                    </label>
                    <select name="age" class="form-select">
                        <option value="">请选择年龄大小</option>
                        <option value="18-20岁" <?php echo $info['age'] == '18-20岁' ? 'selected' : ''; ?>>18-20岁</option>
                        <option value="21-25岁" <?php echo $info['age'] == '21-25岁' ? 'selected' : ''; ?>>21-25岁</option>
                        <option value="26-30岁" <?php echo $info['age'] == '26-30岁' ? 'selected' : ''; ?>>26-30岁</option>
                        <option value="31-35岁" <?php echo $info['age'] == '31-35岁' ? 'selected' : ''; ?>>31-35岁</option>
                        <option value="36-40岁" <?php echo $info['age'] == '36-40岁' ? 'selected' : ''; ?>>36-40岁</option>
                        <option value="41-50岁" <?php echo $info['age'] == '41-50岁' ? 'selected' : ''; ?>>41-50岁</option>
                        <option value="50岁以上" <?php echo $info['age'] == '50岁以上' ? 'selected' : ''; ?>>50岁以上</option>
                        <option value="未知" <?php echo $info['age'] == '未知' ? 'selected' : ''; ?>>未知</option>
                    </select>
                </div>
                
                <div class="form-item">
                    <label class="form-label">
                        外貌形象
                    </label>
                    <input type="text" name="wmtj" class="form-input" placeholder="请描述外貌形象" value="<?php echo htmlspecialchars($info['wmtj']); ?>">
                </div>
                
                <div class="form-item">
                    <label class="form-label">
                        服务价格
                    </label>
                    <input type="text" name="price" class="form-input" placeholder="请输入服务价格" value="<?php echo htmlspecialchars($info['price']); ?>">
                </div>
                
                <div class="form-item">
                    <label class="form-label required">
                        详细内容
                    </label>
                    <textarea name="content" class="form-textarea" placeholder="详细内容有助于用户更全面了解信息，请尽可能详细描述"><?php echo htmlspecialchars($info['content']); ?></textarea>
                    <div class="form-error" data-field="content">请填写详细内容</div>
                    <div class="form-hint">建议填写200字以上，描述越详细越能吸引用户</div>
                </div>
            </div>

            

            <!-- 联系方式部分 -->
            <div class="form-section">
                <h3 class="section-title">
                    联系方式
                    <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                </h3>

                <div class="contact-hint">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    <span>手机号、微信、QQ、与你号 至少填写一项</span>
                </div>
                
                <div class="form-item">
                    <label class="form-label required">
                        联系人
                    </label>
                    <input type="text" name="uname" class="form-input" placeholder="请输入联系人姓名" value="<?php echo htmlspecialchars($info['uname']); ?>">
                    <div class="form-error" data-field="uname">请输入联系人姓名</div>
                </div>
                
                <div class="form-item">
                    <label class="form-label">
                        手机号码
                    </label>
                    <input type="tel" name="mobile" class="form-input" placeholder="请输入手机号码" value="<?php echo htmlspecialchars($info['mobile']); ?>">
                </div>
                
                <div class="form-item">
                    <label class="form-label">
                        微信号
                    </label>
                    <input type="text" name="weixin" class="form-input" placeholder="请输入微信号" value="<?php echo htmlspecialchars($info['weixin']); ?>">
                </div>
                
                <div class="form-item">
                    <label class="form-label">
                        QQ号码
                    </label>
                    <input type="text" name="qq" class="form-input" placeholder="请输入QQ号码" value="<?php echo htmlspecialchars($info['qq']); ?>">
                </div>
                
                <div class="form-item">
                    <label class="form-label">
                        与你号
                    </label>
                    <input type="text" name="yuni" class="form-input" placeholder="请输入与你号" value="<?php echo htmlspecialchars($info['yuli']); ?>">
                </div>
                
                <div class="form-error" data-field="contact">请至少填写一项联系方式</div>
                
                <div class="form-item">
                    <label class="form-label required">
                        详细地址
                    </label>
                    <input type="text" name="address" class="form-input" placeholder="请输入详细地址" value="<?php echo htmlspecialchars($info['address']); ?>">
                    <div class="form-error" data-field="address">请输入详细地址</div>
                    <div class="form-hint">详细地址有助于用户准确找到位置</div>
                </div>
            </div>



            <!-- 图片上传 -->
            <div class="form-section upload-section">
                <h3 class="section-title">
                    图片上传
                    <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                </h3>
                <div id="imageUploadContainer" class="upload-container">
                    <div class="upload-grid"></div>
                    <div class="upload-hint">支持JPG、PNG格式，单张图片不超过5MB，最多上传<span class="upload-limit">9张</span></div>
                </div>
            </div>

            <!-- 视频上传 -->
            <div class="form-section upload-section">
                <h3 class="section-title">
                    视频上传
                    <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                </h3>
                <div id="videoUploadContainer" class="upload-container">
                    <div class="upload-grid"></div>
                    <div class="upload-hint">支持MP4格式，单个视频不超过50MB，最多上传<span class="upload-limit">3个</span></div>
                </div>
            </div>
            <!-- 验证码 -->
            <div class="form-section">
                <div class="form-item">
                    <!-- 标签、输入框、验证码图片同一行布局 -->
                    <div class="captcha-row">
                        <label class="form-label required">验证码:</label>

                        <div class="captcha-group">
                          <input type="text" name="captcha" id="yzm" class="form-inputyz" placeholder="请输入验证码">
                            <img id="captchaImg" src="/lib/yzmcode.html" alt="验证码" class="captcha-img" onclick="refreshCaptcha()">
                        </div>
                        
                    </div>
                    <div class="form-error" data-field="yzm">请输入验证码</div>
                    <div class="form-hint">点击验证码图片可刷新</div>
                </div>
            </div>

            <!-- 提交按钮 -->
            <button type="submit" class="submit-button">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                确认修改
            </button>
        </form>

        
    </div>


    <script>
        window.infoData         = <?php echo json_encode($info); ?>;
        window.existingImages   = <?php echo json_encode($images); ?>;
        window.existingVideos   = <?php echo json_encode($videos); ?>;
        window.provinceSelect   = <?php echo json_encode($citypid); ?>;
        window.selectedCity     = <?php echo json_encode($info['city']); ?>;
        window.selectedDistrict = <?php echo json_encode($info['cityid'] ?? 0); ?>;
    </script>
    <script src="/js/imgvideo.js?t=<?php echo time();?>"></script>
    <script src="/js/publish_edit.js?t=<?php echo time();?>"></script>

    <!-- 提交加载层：半透明遮罩，不可手动关闭，后端返回后由 JS 移除 -->
    <div id="submitLoadingLayer" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.45);align-items:center;justify-content:center;flex-direction:column;gap:16px;">
        <div style="width:48px;height:48px;border:5px solid rgba(255,255,255,0.3);border-top-color:#fff;border-radius:50%;animation:spinLoader 0.8s linear infinite;"></div>
        <p style="color:#fff;font-size:15px;font-weight:500;letter-spacing:1px;margin:0;">数据正在飞速上传中，请稍等...</p>
    </div>
    <style>@keyframes spinLoader { to { transform: rotate(360deg); } }</style>
</body>
</html>
