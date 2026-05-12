<?php 
include_once 'loaduser.php';

$pageTitle = "发布论坛";

$provinceArr = db('areab')->where(['pid' => 0])->field('id, fullname')->select();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title><?php echo $pageTitle; ?></title>
<link rel="stylesheet" href="/css/comm.css">
<link rel="stylesheet" href="/css/imgvideo.css?t=<?php echo time(); ?>">
<link rel="stylesheet" href="/css/fb.css?t=<?php echo time(); ?>">
</head>
<body>
    <?php 
    include 'comm/header.php'; ?>

    <div class="container" style="margin:10px;">
        
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
                    <input type="text" name="title" class="form-input" placeholder="请输入吸引人的标题">
                    <div class="form-error" data-field="title">请输入信息标题</div>
                </div>
                
                <div class="form-item">
                    <label class="form-label required">
                        所属地区
                    </label>
                    <select name="province" id="province" class="form-select">
                        <option value="">请选择省份</option>
                        <?php foreach ($provinceArr as $k => $v) { ?>
                           <option value="<?php echo $v['id']; ?>"><?php echo $v['fullname']; ?></option>
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
                        <option value="<?php echo $k;?>"><?php echo $v;?></option>
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
                        <option value="自己开发">自己开发</option>
                        <option value="网上看到">网上看到</option>
                        <option value="朋友分享">朋友分享</option>
                        <option value="其它论坛">其它论坛</option>
                    </select>
                    <div class="form-error" data-field="laiyuan">请选择信息来源</div>
                </div>
                
                <div class="form-item">
                    <label class="form-label required">
                        综合评价
                    </label>
                    <select name="pj" class="form-select">
                        <option value="">请选择评价</option>
                        <option value="1">★★★★★ 优秀</option>
                        <option value="2">★★★★☆ 良好</option>
                        <option value="3">★★★☆☆ 一般</option>
                        <option value="4">★★☆☆☆ 较差</option>
                        <option value="5">★☆☆☆☆ 很差</option>
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
                        <option value="个人兼职">个人兼职</option>
                        <option value="2-5人">2-5人</option>
                        <option value="5-10人">5-10人</option>
                        <option value="10-20人">10-20人</option>
                        <option value="20-50人">20-50人</option>
                        <option value="50人以上">50人以上</option>
                        <option value="未知">未知</option>
                    </select>
                </div>
                
                <div class="form-item">
                    <label class="form-label">
                        年龄大小
                    </label>
                    <select name="age" class="form-select">
                        <option value="">请选择年龄大小</option>
                        <option value="18-20岁">18-20岁</option>
                        <option value="21-25岁">21-25岁</option>
                        <option value="26-30岁">26-30岁</option>
                        <option value="31-35岁">31-35岁</option>
                        <option value="36-40岁">36-40岁</option>
                        <option value="41-50岁">41-50岁</option>
                        <option value="50岁以上">50岁以上</option>
                        <option value="未知">未知</option>
                    </select>
                </div>
                
                <div class="form-item">
                    <label class="form-label">
                        外貌形象
                    </label>
                    <input type="text" name="wmtj" class="form-input" placeholder="填写如：漂亮大方 高贵优雅 身材好 性感 等">
                </div>
                
                <div class="form-item">
                    <label class="form-label">
                        服务价格
                    </label>
                    <input type="text" name="price" class="form-input" placeholder="填写如：一次800, 一夜3000，私聊 等">
                </div>
                
                <div class="form-item">
                    <label class="form-label required">
                        详细内容
                    </label>
                    <textarea name="content" class="form-textarea" placeholder="详细内容有助于用户更全面了解信息，请尽可能详细描述"></textarea>
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
                    <input type="text" name="uname" class="form-input" placeholder="请输入联系人姓名">
                    <div class="form-error" data-field="uname">请输入联系人姓名</div>
                </div>
                
                <div class="form-item">
                    <label class="form-label">
                        手机号码
                    </label>
                    <input type="tel" name="mobile" class="form-input" placeholder="请输入手机号码">
                </div>
                
                <div class="form-item">
                    <label class="form-label">
                        微信号
                    </label>
                    <input type="text" name="weixin" class="form-input" placeholder="请输入微信号">
                </div>
                
                <div class="form-item">
                    <label class="form-label">
                        QQ号码
                    </label>
                    <input type="text" name="qq" class="form-input" placeholder="请输入QQ号码">
                </div>
                
                <div class="form-item">
                    <label class="form-label">
                        与你号
                    </label>
                    <input type="text" name="yuni" class="form-input" placeholder="请输入与你号">
                </div>
                
                <div class="form-error" data-field="contact">请至少填写一项联系方式</div>
                
                <div class="form-item">
                    <label class="form-label required">
                        详细地址
                    </label>
                    <input type="text" name="address" class="form-input" placeholder="请输入详细地址">
                    <div class="form-error" data-field="address">请输入详细地址</div>
                    <div class="form-hint">详细地址有助于用户准确找到位置</div>
                </div>
            </div>

            <!-- 图片上传 -->
            <div class="form-section upload-section">
                <h3 class="section-title">
                    上传图片
                    <svg class="section-icon" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 2L7.17 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2h-3.17L15 2H9zm3 15c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"/>
                        <circle cx="12" cy="12" r="3"/>
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
                    上传视频
                    <svg class="section-icon" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/>
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
                立即发布
            </button>
        </form>
    </div>



    <script src="/js/imgvideo.js"></script>
    <script src="/js/publish.js"></script>

    <!-- 提交加载层：半透明遮罩，不可手动关闭，后端返回后由 JS 移除 -->
    <div id="submitLoadingLayer" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.45);align-items:center;justify-content:center;flex-direction:column;gap:16px;">
        <div style="width:48px;height:48px;border:5px solid rgba(255,255,255,0.3);border-top-color:#fff;border-radius:50%;animation:spinLoader 0.8s linear infinite;"></div>
        <p style="color:#fff;font-size:15px;font-weight:500;letter-spacing:1px;margin:0;">数据正在飞速上传中，请稍等...</p>
    </div>
    <style>@keyframes spinLoader { to { transform: rotate(360deg); } }</style>

    <?php include_once 'comm/alert_modal.php'; ?>
</body>
</html>
