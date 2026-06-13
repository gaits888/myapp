<?php 
include_once 'loaduser.php';
include_once 'config.php';

$pageTitle = "发布论坛";

$provinceArr = db('areab')->where(['pid' => 0])->field('id, fullname')->select();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="author" content="<?php echo $webname; ?>" />
<meta name="keywords" content="发布论坛_<?php echo $webname; ?>">
<meta name="description" content="发布论坛_<?php echo $webname; ?>">
<title>发布论坛_<?php echo $webname; ?></title>
<link rel="stylesheet" href="/css/comm.css">
<link rel="stylesheet" href="/css/imgvideo.css">
<!--<link rel="stylesheet" href="/css/fb.css">-->
<style>


* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
  background: #fff;
  min-height: 100vh;
  color: #333;
  padding: 50px 0 90px !important;
}

.publish-container {
  margin: 10px auto;
  padding: 0 10px;
}

/* 页面说明区域 */
.publish-intro {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px) saturate(180%);
  border-radius: 12px;
  padding: 10px;
  margin-bottom: 10px;
  border: 1px solid rgba(255, 105, 180, 0.2);
  box-shadow: 0 8px 32px rgba(255, 105, 180, 0.08);
  text-align: center;
}

.intro-title {
  font-size: 24px;
  font-weight: 700;
  background: linear-gradient(135deg, #ff69b4 0%, #ff1493 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: 8px;
}

.intro-desc {
  font-size: 14px;
  color: rgba(102, 102, 102, 0.8);
  line-height: 1.6;
}

/* 页面标题 */
/*.page-header {
  text-align: center;
  margin-bottom: 24px;
}

.page-header h1 {
  font-size: 28px;
  font-weight: 700;
  background: linear-gradient(135deg, #ff69b4 0%, #ff1493 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: 8px;
}

.page-header p {
  font-size: 14px;
  color: rgba(102, 102, 102, 0.8);
}*/

/* 表单区块 */
.form-section {
  /* 改为白色半透明背景 */
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px) saturate(180%);
  border-radius: 5px;
  padding: 10px;
  margin-bottom: 10px;
  /* 改为粉色边框 */
  border:1px solid rgba(0, 0, 0, 0.05);
  box-shadow: 0 8px 32px rgba(255, 105, 180, 0.08);
  transition: all 0.3s ease;
}

.form-section:active {
  /* 移动端点击效果,改为粉色边框 */
  border-color: rgba(255, 105, 180, 0.4);
  box-shadow: 0 12px 40px rgba(255, 105, 180, 0.15);
}

.section-title {
  font-size: 18px;
  font-weight: 700;
  /* 改为深灰色文字 */
  color: #333;
  margin-bottom: 10px;
  padding-bottom: 10px;
  /* 改为粉色边框 */
  border-bottom: 1px solid rgba(200 193 193 / 30%);
  display: flex;
  align-items: center;
  /* 移除gap，改为子元素margin-right */
}

.section-title::before {
  content: "";
  width: 3px;
  height: 20px;
  background: #ff7aa8;
  border-radius: 5px;
  margin-right: 5px;
}

.section-icon {
  width: 20px;
  height: 20px;
  /* 改为粉色 */
  color: #ff69b4;
  margin-left: auto;
}

/* 表单项 */
.form-item {
/*  margin-bottom: 20px;*/
}

.form-label {
  display: block;
  font-size: 14px;
  color: rgba(51, 51, 51, 0.75);
  margin-bottom: 5px;
  font-weight: 500;

  display: flex;
  align-items: center;
}


.label-icon {
  width: 16px;
  height: 16px;
  /* 改为粉色 */
  color: #ff69b4;
  /* 添加margin-right替代gap: 6px */
  margin-right: 6px;
}

/* 输入框样式 */
.form-input,
.form-select,
.form-textarea {
  width: 100%;
  padding: 10px;
  /* 改为白色背景 */
  background: rgba(255, 255, 255, 0.95);
  /* 改为粉色边框 */
  border: 1px solid rgba(168 168 168 / 30%);
  border-radius: 5px;
  /* 改为深色文字 */
  color: #aaa;
  font-size: 16px;
  transition: all 0.3s ease;
  outline: none;
  font-family: inherit;
  margin-bottom: 10px;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
  /* 改为粉色边框 */
  border-color: #ff7373;
  background: rgba(255, 255, 255, 1);
  box-shadow: 0 0 0 1px rgba(255, 105, 180, 0.1), 0 4px 12px rgba(255, 105, 180, 0.2);
  transform: translateY(-1px);
}

.form-input::placeholder,
.form-textarea::placeholder {
  /* 改为浅灰色 */
  color: rgba(153, 153, 153, 0.6);
}

.form-select {
  cursor: pointer;
  /* 加前缀隐藏低版本安卓/旧 WebKit 的原生下拉箭头，避免出现两个三角 */
  -webkit-appearance: none;
  -moz-appearance: none;
  -ms-appearance: none;
  appearance: none;
  /* 改为粉色箭头 */
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 16 16'%3E%3Cpath fill='%23999999' d='M8 11L3 6h10z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 12px center;
  padding-right: 40px;
}

/* 隐藏 IE/旧浏览器 select 的原生下拉箭头 */
.form-select::-ms-expand {
  display: none;
}

.form-select option {
  /* 改为白色背景 */
  background: #fff;
  color: #333;
  padding: 10px;
}

.form-textarea {
  min-height: 140px;
  resize: vertical;
  line-height: 1.6;
}

/* 验证码区域 */
.captcha-row {
  display: flex;
  align-items: center;
  /* 移除gap: 10px */
  width: 100%;
}

.captcha-row .form-label {
  display: block;
  flex-shrink: 0;
  margin-bottom: 0;
  white-space: nowrap;
  min-width: auto;
  margin-right: 12px;
  margin-bottom: 5px;
}

.captcha-row .form-input {
  flex: 1;
  min-width: 0;
  width: auto;
  margin: 0;
}

.captcha-group {
  display: flex;
  flex-direction: row;
  /* 移除gap: 10px */
  align-items: center;
  width: 100%;
}

.form-inputyz {
  flex: 1;
  min-width: 0;
  padding: 10px;
  background: rgba(255, 255, 255, 0.95);
  border: 1px solid rgb(168 168 168 / 30%);
  border-radius: 5px;
  color: #333;
  font-size: 16px;
  transition: all 0.3s ease;
  /* 添加margin-right替代gap: 10px */
  margin-right: 12px;
}

.form-inputyz:focus {
  border-color: #ff7373;
  background: rgba(255, 255, 255, 1);
  box-shadow: 0 0 0 1px rgba(255, 105, 180, 0.1), 0 4px 12px rgba(255, 105, 180, 0.2);
  transform: translateY(-1px);
}

.captcha-img {
  width: 120px;
  height: 40px;
  flex-shrink: 0;
  /* 改为粉色半透明背景 */
  background: rgba(255, 105, 180, 0.1);
  /* 改为粉色边框 */
  border: 1px solid rgba(255, 105, 180, 0.3);
  border-radius: 5px;
  cursor: pointer;
  transition: all 0.3s ease;
  object-fit: cover;
}

/* 错误提示 */
.form-error {
  display: none;
  /* 保持粉色 */
  color: #ff1493;
  font-size: 13px;
  margin-top: 8px;
  padding-left: 6px;
  align-items: center;
  /* 移除gap: 4px */
}

.form-error.show {
  display: flex;
}

.form-error::before {
  content: "⚠";
  font-size: 14px;
  /* 添加margin-right替代gap: 4px */
  margin-right: 4px;
}

/* 提示文本 */
.form-hint {
  font-size: 13px;
  /* 改为灰色 */
  color: rgba(102, 102, 102, 0.8);
  margin-top: 8px;
  padding-left: 6px;
  line-height: 1.5;
}

.contact-hint {
  background: rgba(236 201 218 / 9%);
  border:1px solid rgb(248 164 10 / 26%);
  border-radius: 5px;
  padding: 10px;
  margin-bottom: 10px;
  font-size: 14px;
  color: rgba(51, 51, 51, 0.9);
  display: flex;
  align-items: flex-start;
  /* 移除gap: 10px */
}

.contact-hint svg {
  width: 18px;
  height: 18px;
  /* 改为粉色 */
  color: #ff69b4;
  flex-shrink: 0;
  margin-top: 1px;
  /* 添加margin-right替代gap: 10px */
  margin-right: 10px;
}

/* 提交按钮 */
.submit-button {
  width: 100%;
  padding: 12px;
  background: linear-gradient(to right, rgb(248 164 10 / 68%), #ff8fb3);
  border: none;
  border-radius: 25px;
  color: #fff;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  /* 改为粉色阴影 */
  box-shadow: 0 8px 24px rgba(255, 105, 180, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  /* 移除gap: 8px */
}

.submit-button:active {
  transform: translateY(-2px);
  /* 改为粉色阴影 */
  box-shadow: 0 12px 32px rgba(255, 105, 180, 0.5);
}

.submit-button svg {
  width: 20px;
  height: 20px;
  /* 添加margin-left替代gap: 8px */
  margin-left: 8px;
}

/* 发布须知 */
.notice-section {
  /* 改为白色半透明背景 */
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px) saturate(180%);
  border-radius: 5px;
  padding: 24px;
  /* 改为粉色边框 */
  border: 1px solid rgba(255, 105, 180, 0.2);
  box-shadow: 0 8px 32px rgba(255, 105, 180, 0.08);
}

.notice-title {
  font-size: 17px;
  font-weight: 700;
  /* 改为深灰色 */
  color: #333;
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  /* 移除gap: 10px */
}

.notice-title svg {
  width: 22px;
  height: 22px;
  /* 改为粉色 */
  color: #ff69b4;
  /* 添加margin-right替代gap: 10px */
  margin-right: 10px;
}

.notice-section ul {
  list-style: none;
  padding: 0;
}

.notice-section li {
  font-size: 14px;
  /* 改为深灰色 */
  color: rgba(51, 51, 51, 0.85);
  line-height: 1.8;
  padding-left: 24px;
  position: relative;
  margin-bottom: 10px;
}

.notice-section li::before {
  content: "";
  position: absolute;
  left: 0;
  top: 9px;
  width: 8px;
  height: 8px;
  /* 改为粉色渐变 */
  background: linear-gradient(135deg, #ff69b4 0%, #ff1493 100%);
  border-radius: 50%;
  box-shadow: 0 0 8px rgba(255, 105, 180, 0.5);
}

/* 响应式 */
@media (max-width: 768px) {
  .page-header h1 {
    font-size: 16px;
  }

  .form-section {
    padding: 10px;
  }

  .section-title {
    font-size: 16px;
  }

  .captcha-row {
    flex-direction: column;
    align-items: stretch;
  }
}

/* 加载动画 */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.form-section {
  animation: fadeInUp 0.6s ease-out;
}

.form-section:nth-child(1) {
  animation-delay: 0.1s;
}
.form-section:nth-child(2) {
  animation-delay: 0.2s;
}
.form-section:nth-child(3) {
  animation-delay: 0.3s;
}
.form-section:nth-child(4) {
  animation-delay: 0.4s;
}

.notice-section {
  animation: fadeInUp 0.6s ease-out 0.5s both;
}




    /* 全屏遮罩层样式 */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 99999;
    }
    .loading-overlay .loading-spinner {
        width: 50px;
        height: 50px;
        border: 4px solid #fff;
        border-top-color: #ff5e7b;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-bottom: 15px;
    }
    .loading-overlay .loading-text {
        color: #fff;
        font-size: 16px;
        text-align: center;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    </style>
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
                    <!--
                    <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    -->
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
                        所属省份
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
                        所属类别
                    </label>
                    <select name="typeid" class="form-select">
                        <option value="">请选择类别</option>
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
                    <input type="text" name="wmtj" class="form-input" placeholder="请描述外貌形象">
                </div>
                
                <div class="form-item">
                    <label class="form-label">
                        服务价格
                    </label>
                    <input type="text" name="price" class="form-input" placeholder="请输入服务价格">
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
                </h3>

                <div class="contact-hint">
                    <span>手机号、微信、QQ、与你号 至少填写一项</span>
                </div>
                
                <div class="form-item">
                    <label class="form-label required">
                        联系人
                    </label>
                    <input type="text" name="uname" class="form-input" placeholder="请输入联系人">
                    <div class="form-error" data-field="uname">请输入联系人</div>
                </div>
                
                <div class="form-item">
                    <label class="form-label">
                        手机号码
                    </label>
                    <input type="tel" name="mobile" class="form-input" placeholder="请输入手机号">
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
                    <input type="text" name="qq" class="form-input" placeholder="请输入QQ号">
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
                    <!--<div class="form-hint">详细地址有助于用户准确找到位置</div> -->
                </div>
            </div>

            <!-- 图片上传 -->
            <div class="form-section upload-section">
                <h3 class="section-title">
                    上传图片
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

    <?php include 'comm/footer.php'; ?>

    <script src="/js/imgvideo.js?t=<?php echo time(); ?>"></script>
    <script>
        

/**
 * 发布页面表单验证和提交
 */

function showFieldError(fieldName, errorMessage) {
  let field = document.getElementById(fieldName)

  // 如果通过 id 找不到，尝试通过 name 属性查找
  if (!field) {
    field = document.querySelector(`[name="${fieldName}"]`)
  }

  if (!field) return

  const formItem = field.closest(".form-item")
  if (!formItem) return

  const errorElement = formItem.querySelector(`.form-error[data-field="${fieldName}"]`)
  if (errorElement) {
    errorElement.textContent = errorMessage
    errorElement.classList.add("show")
  }

  // 跳转到错误字段
  field.scrollIntoView({ behavior: "smooth", block: "center" })
  field.focus()
}

function hideFieldError(fieldId) {
  let field = document.getElementById(fieldId)

  // 如果通过 id 找不到，尝试通过 name 属性查找
  if (!field) {
    field = document.querySelector(`[name="${fieldId}"]`)
  }

  if (!field) return

  const formItem = field.closest(".form-item")
  if (!formItem) return

  const errorElement = formItem.querySelector(".form-error")
  if (errorElement) {
    errorElement.classList.remove("show")
  }
}

function showAlert(message, title = "提示", duration = 2000, redirectUrl = null) {
  // 简单的 alert 实现
  alert(message)
  if (redirectUrl) {
    setTimeout(() => {
      window.location.href = redirectUrl
    }, duration)
  }
}

function validateForm() {
  // 定义验证字段顺序（从上到下）
  var fields = [
    { name: 'title', type: 'input', msg: '请输入信息标题' },
    { name: 'province', type: 'select', msg: '请选择省份' },
    { name: 'city', type: 'select', msg: '请选择城市' },
    { name: 'district', type: 'select', msg: '请选择区县' },
    { name: 'typeid', type: 'select', msg: '请选择发布类别' },
    { name: 'laiyuan', type: 'select', msg: '请选择信息来源' },
    { name: 'pj', type: 'select', msg: '请选择综合评价' },
    { name: 'nums', type: 'select', msg: '请选择场所人数' },
    { name: 'age', type: 'select', msg: '请选择年龄大小' },
    { name: 'wmtj', type: 'input', msg: '请输入外貌形象' },
    { name: 'price', type: 'input', msg: '请输入服务价格' },
    { name: 'content', type: 'textarea', msg: '请填写详细内容', minLen: 10 },
    { name: 'uname', type: 'input', msg: '请输入联系人' },
    { name: 'contact', type: 'contact', msg: '请至少填写一项联系方式（手机号、微信、QQ或与你号）' },
    { name: 'address', type: 'input', msg: '请输入详细地址' },
    { name: 'images', type: 'images', msg: '请至少上传一张图片' },
    { name: 'yzm', type: 'input', msg: '请输入验证码', id: 'yzm' }
  ];

  for (var i = 0; i < fields.length; i++) {
    var field = fields[i];
    var el = null;
    var isEmpty = false;

    if (field.type === 'contact') {
      // 联系方式至少填一项
      var mobile = document.querySelector('input[name="mobile"]');
      var weixin = document.querySelector('input[name="weixin"]');
      var qq = document.querySelector('input[name="qq"]');
      var yuni = document.querySelector('input[name="yuni"]');
      var hasContact = (mobile && mobile.value.trim()) ||
                       (weixin && weixin.value.trim()) ||
                       (qq && qq.value.trim()) ||
                       (yuni && yuni.value.trim());
      if (!hasContact) {
        alert(field.msg);
        if (mobile) {
          mobile.scrollIntoView({ behavior: 'smooth', block: 'center' });
          mobile.focus();
        }
        return false;
      }
    } else if (field.type === 'images') {
      // 图片上传验证
      if (window.imageUploader) {
        var images = window.imageUploader.files;
        if (!images || images.length === 0) {
          alert(field.msg);
          var imageSection = document.getElementById('imageUploadContainer');
          if (imageSection) {
            imageSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
          return false;
        }
      } else {
        alert('图片上传组件未初始化');
        return false;
      }
    } else {
      // 普通字段验证
      if (field.id) {
        el = document.getElementById(field.id);
      } else {
        el = document.querySelector('[name="' + field.name + '"]');
      }

      if (!el) continue;

      var val = el.value ? el.value.trim() : '';
      
      if (field.type === 'select') {
        isEmpty = !val;
      } else {
        isEmpty = !val;
        // 最小长度验证
        if (!isEmpty && field.minLen && val.length < field.minLen) {
          alert(field.msg + '，至少需要' + field.minLen + '个字符');
          el.scrollIntoView({ behavior: 'smooth', block: 'center' });
          el.focus();
          return false;
        }
      }

      if (isEmpty) {
        alert(field.msg);
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        el.focus();
        return false;
      }
    }
  }

  return true;
}

// 显示遮罩层
function showLoadingOverlay(text) {
  var overlay = document.getElementById('loadingOverlay');
  if (!overlay) {
    overlay = document.createElement('div');
    overlay.id = 'loadingOverlay';
    overlay.className = 'loading-overlay';
    overlay.innerHTML = '<div class="loading-spinner"></div><div class="loading-text">' + (text || '数据正在上传中，请稍等片刻...') + '</div>';
    document.body.appendChild(overlay);
  } else {
    overlay.querySelector('.loading-text').innerText = text || '数据正在上传中，请稍等片刻...';
    overlay.style.display = 'flex';
  }
}

// 隐藏遮罩层
function hideLoadingOverlay() {
  var overlay = document.getElementById('loadingOverlay');
  if (overlay) {
    overlay.style.display = 'none';
  }
}

// 提交表单
async function submitForm(event) {
  event.preventDefault()

  if (!validateForm()) {
    return
  }

  // 获取提交按钮
  const submitBtn = document.querySelector(".submit-button")
  if (!submitBtn) return

  const originalHTML = submitBtn.innerHTML
  
  // 禁用提交按钮
  submitBtn.disabled = true
  submitBtn.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>提交中...'
  
  // 显示全屏遮罩层
  showLoadingOverlay('数据正在上传中，请稍等片刻...')

  try {
    // 先上传所有图片和视频

    // 上传图片
    if (window.imageUploader) {
      const imageResult = await window.imageUploader.uploadAllFiles()
      if (!imageResult) {
        throw new Error("图片上传失败")
      }
    }

    // 上传视频
    if (window.videoUploader) {
      const videoResult = await window.videoUploader.uploadAllFiles()
      if (!videoResult) {
        throw new Error("视频上传失败")
      }
    }

    submitBtn.innerHTML =
      '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>提交数据中...'

    // 收集表单数据
    const formData = new FormData()

    // 基本信息
    formData.append("title", document.querySelector('input[name="title"]').value.trim())
    formData.append("province", document.getElementById("province").value)
    formData.append("city", document.getElementById("city").value)
    formData.append("district", document.getElementById("district").value)
    formData.append("typeid", document.querySelector('select[name="typeid"]').value)
    formData.append("laiyuan", document.querySelector('select[name="laiyuan"]').value)
    formData.append("pj", document.querySelector('select[name="pj"]').value)

    // 详细信息（可选字段）
    const nums = document.querySelector('select[name="nums"]')
    if (nums && nums.value) formData.append("nums", nums.value)

    const age = document.querySelector('select[name="age"]')
    if (age && age.value) formData.append("age", age.value)

    const wmtj = document.querySelector('input[name="wmtj"]')
    if (wmtj && wmtj.value.trim()) formData.append("wmtj", wmtj.value.trim())

    const price = document.querySelector('input[name="price"]')
    if (price && price.value.trim()) formData.append("price", price.value.trim())

    formData.append("content", document.querySelector('textarea[name="content"]').value.trim())

    // 联系方式
    formData.append("uname", document.querySelector('input[name="uname"]').value.trim())

    const mobile = document.querySelector('input[name="mobile"]')
    if (mobile && mobile.value.trim()) formData.append("mobile", mobile.value.trim())

    const weixin = document.querySelector('input[name="weixin"]')
    if (weixin && weixin.value.trim()) formData.append("weixin", weixin.value.trim())

    const qq = document.querySelector('input[name="qq"]')
    if (qq && qq.value.trim()) formData.append("qq", qq.value.trim())

    const yuni = document.querySelector('input[name="yuni"]')
    if (yuni && yuni.value.trim()) formData.append("yuni", yuni.value.trim())

    formData.append("address", document.querySelector('input[name="address"]').value.trim())

    // 验证码
    formData.append("captcha", document.getElementById("yzm").value.trim())

    // 图片和视频（从上传组件获取已上传的文件路径）
    if (window.imageUploader) {
      const images = window.imageUploader.getFiles()
      formData.append("images", JSON.stringify(images))
    }

    if (window.videoUploader) {
      const videos = window.videoUploader.getFiles()
      formData.append("videos", JSON.stringify(videos))
    }


    const response = await fetch("/opers/forum/publish.html", {
      method: "POST",
      body: formData,
      credentials: "include",
    })

    const result = await response.json()

    if (result.code === 200) {
      hideLoadingOverlay()
      alert("发布成功！");
      window.location.href='member_publish.html';
    } else {
      hideLoadingOverlay()
      showAlert(result.msg || "发布失败，请重试")
      // 刷新验证码
      refreshCaptcha()
      submitBtn.disabled = false
      submitBtn.innerHTML = originalHTML
    }
  } catch (error) {
    console.error("提交错误:", error)
    hideLoadingOverlay()
    showAlert(error.message || "网络错误，请稍后重试")
    submitBtn.disabled = false
    submitBtn.innerHTML = originalHTML
  }
}

// 刷新验证码
function refreshCaptcha() {
  const captchaImg = document.getElementById("captchaImg")
  if (captchaImg) {
    captchaImg.src = "/lib/yzmcode.html?r=" + Math.random()
  }
}

/**
 * 获取城市或区县列表
 * @param {number} pid 上级id
 * @param {number} type 2:获取城市 3:获取区县
 * @returns {Promise<Array>} 城市或区县列表
 */
async function getRegionList(pid, type) {
  try {
    const formData = new FormData()
    formData.append("pid", pid)
    formData.append("type", type)

    const response = await fetch("/opers/city/getcity.html", {
      method: "POST",
      body: formData,
      credentials: "include",
    })

    const result = await response.json()

    if (result.code === 200) {
      return result.data || []
    } else {
      console.error("获取地区列表失败:", result.msg)
      return []
    }
  } catch (error) {
    console.error("获取地区列表错误:", error)
    return []
  }
}

/**
 * 更新城市下拉框
 * @param {number} provinceId 省份id
 */
async function updateCityOptions(provinceId) {
  const citySelect = document.getElementById("city")
  const districtSelect = document.getElementById("district")

  if (!provinceId) {
    citySelect.innerHTML = '<option value="">请先选择省份</option>'
    districtSelect.innerHTML = '<option value="">请先选择城市</option>'
    return
  }

  // 显示加载状态
  citySelect.innerHTML = '<option value="">加载中...</option>'
  districtSelect.innerHTML = '<option value="">请先选择城市</option>'

  // 获取城市列表
  const cities = await getRegionList(provinceId, 2)

  // 更新城市下拉框
  citySelect.innerHTML = '<option value="">请选择城市</option>'
  cities.forEach((city) => {
    const option = document.createElement("option")
    option.value = city.id
    option.textContent = city.fullname || city.name
    citySelect.appendChild(option)
  })
}

/**
 * 更新区县下拉框
 * @param {number} cityId 城市id
 */
async function updateDistrictOptions(cityId) {
  const districtSelect = document.getElementById("district")

  if (!cityId) {
    districtSelect.innerHTML = '<option value="">请先选择城市</option>'
    return
  }

  // 显示加载状态
  districtSelect.innerHTML = '<option value="">加载中...</option>'

  // 获取区县列表
  const districts = await getRegionList(cityId, 3)

  // 更新区县下拉框
  districtSelect.innerHTML = '<option value="">请选择区县</option>'
  districts.forEach((district) => {
    const option = document.createElement("option")
    option.value = district.id
    option.textContent = district.fullname || district.name
    districtSelect.appendChild(option)
  })
}

// 页面加载完成后初始化
document.addEventListener("DOMContentLoaded", () => {
  // 绑定表单提交事件
  const publishForm = document.getElementById("publishForm")
  if (publishForm) {
    publishForm.addEventListener("submit", submitForm)
  }

  // 验证码图片点击刷新
  const captchaImg = document.getElementById("captchaImg")
  if (captchaImg) {
    captchaImg.addEventListener("click", refreshCaptcha)
    captchaImg.style.cursor = "pointer"
  }

  // 为所有表单元素添加输入监听
  const formInputs = document.querySelectorAll(".form-input, .form-select, .form-textarea, .form-inputyz")
  formInputs.forEach((input) => {
    const fieldName = input.id || input.name

    input.addEventListener("input", () => {
      hideFieldError(fieldName)
    })

    input.addEventListener("change", () => {
      hideFieldError(fieldName)
    })
  })

  // 省份选择变化
  const provinceSelect = document.getElementById("province")
  if (provinceSelect) {
    provinceSelect.addEventListener("change", async (e) => {
      const provinceId = e.target.value
      await updateCityOptions(provinceId)
      hideFieldError("province")
    })
  }

  // 城市选择变化
  const citySelect = document.getElementById("city")
  if (citySelect) {
    citySelect.addEventListener("change", async (e) => {
      const cityId = e.target.value
      await updateDistrictOptions(cityId)
      hideFieldError("city")
    })
  }
  
  // 初始化图片上传组件
  if (typeof MediaUploader !== 'undefined') {
    window.imageUploader = new MediaUploader({
      container: '#imageUploadContainer',
      type: 'image',
      maxFiles: 9,
      maxSize: 10
    });
    
    window.videoUploader = new MediaUploader({
      container: '#videoUploadContainer',
      type: 'video',
      maxFiles: 3,
      maxSize: 50
    });
  }
})


        
    </script>
    <script>
        // 刷新验证码
        function refreshCaptcha() {
            document.getElementById('captchaImg').src = '/lib/yzmcode.html?' + Math.random();
        }
    </script>
    
<?php 
include_once 'comm/alert_modal.php';
?>
</body>
</html>
