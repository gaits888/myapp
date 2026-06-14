<?php 
include_once 'loaduser.php';
include_once 'config.php';
$pageTitle = "发布中心";
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="author" content="<?php echo $webname; ?>" />
<meta name="keywords" content="发布中心_<?php echo $webname; ?>">
<meta name="description" content="发布中心_<?php echo $webname; ?>">
<title>发布中心_<?php echo $webname; ?></title>
<link rel="stylesheet" href="/css/comm.css">
<link rel="stylesheet" href="/css/fabu.css">
<style>

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
  color: #fff;
  overflow-x: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}

.container {
  width: 100%;
  max-width: 500px;
  padding: 10px 20px;
      margin-bottom: 70px;
}

.publish-desc {
  font-size: 11px;
  color: rgba(255, 255, 255, 0.55);
  line-height: 1.5;
  margin-top: 6px;
}

.button-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 10px;
}

.button-grid .function-btn {

}

.button-grid .function-btn:nth-child(2n) {
  margin-right: 0;
}

.function-btn {
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  aspect-ratio: 1;
  padding-top: 20px;
  padding-bottom: 20px;
  background: linear-gradient(135deg, rgba(0, 50, 40, 0.8) 0%, rgba(0, 40, 32, 0.85) 100%);
  backdrop-filter: blur(20px) saturate(180%);
  border: 1px solid rgba(0, 217, 163, 0.15);
  border-radius: 10px;
  color: #fff;
  font-size: 16px;
  font-weight: 400;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
}

.function-btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(0, 217, 163, 0.12) 0%, rgba(0, 217, 163, 0.04) 100%);
  opacity: 0;
  transition: opacity 0.4s ease;
}

.function-btn:hover::before {
  opacity: 1;
}

.function-btn:hover {
  border-color: rgba(0, 217, 163, 0.4);
  box-shadow: 0 12px 40px rgba(0, 217, 163, 0.15);
}

.btn-icon {
  width: 80px;
  height: 80px;
  margin-bottom: 16px;
  background-size: contain;
  background-position: center;
  background-repeat: no-repeat;
  filter: drop-shadow(0 4px 12px rgba(0, 217, 163, 0.3));
  transition: all 0.4s ease;
}

.function-btn:hover .btn-icon {
  transform: scale(1.1);
  filter: drop-shadow(0 6px 20px rgba(0, 217, 163, 0.5));
}

.function-btn:nth-child(1) .btn-icon {
  background-image: url('/images/dh/2.png');
}

.function-btn:nth-child(2) .btn-icon {
  background-image: url('/images/dh/3.png');
}

.function-btn:nth-child(3) .btn-icon {
  background-image: url('/images/dh/7.png');
}

.function-btn:nth-child(4) .btn-icon {
  background-image: url('/images/dh/8.png');
}

.btn-text {
      font-weight: 600;
    color: #00d9a3;
  position: relative;
  z-index: 1;
  letter-spacing: 2px;
  opacity: 0.9;
  transition: all 0.3s ease;
}

.function-btn:hover .btn-text {
  opacity: 1;
  letter-spacing: 3px;
}

.page-intro {
  text-align: center;
  margin-top: 10px;
  margin-bottom: 20px;
  color: #fff;
}

/* 移动端适配 */
@media (max-width: 480px) {
  .container {
    padding: 10px;
  }

  .function-btn {
    font-size: 15px;
  }

  .btn-icon {
    width: 64px;
    height: 64px;
    margin-bottom: 12px;
  }

  .btn-text {
    letter-spacing: 1.5px;
  }
}

@media (max-width: 375px) {
  .button-grid {
    margin-top: 0px;
  }

  .function-btn {
    font-size: 14px;
  }

  .btn-icon {
    width: 56px;
    height: 56px;
    margin-bottom: 10px;
  }
}

/* 表单样式 - 绿色主题 */
.publish-container {
  margin: 0 auto;
  padding: 0 10px;
}

.page-header {
  text-align: center;
  margin-bottom: 24px;
}

.page-header h1 {
  font-size: 28px;
  font-weight: 700;
  background: linear-gradient(135deg, #00d9a3 0%, #00c798 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: 8px;
}

.page-header p {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.6);
}

.form-section {
  background: rgba(0, 50, 40, 0.8);
  backdrop-filter: blur(20px) saturate(180%);
  border-radius: 5px;
  padding: 10px;
  margin-bottom: 10px;
  border: 1px solid rgba(0, 217, 163, 0.15);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
  transition: all 0.3s ease;
}

.form-section:hover {
  border-color: rgba(0, 217, 163, 0.25);
  box-shadow: 0 12px 40px rgba(0, 217, 163, 0.1);
}

.section-title {
  font-size: 18px;
  font-weight: 600;
  color: #fff;
  margin-bottom: 10px;
  padding-bottom: 10px;
  border-bottom: 1px solid rgba(0, 217, 163, 0.2);
  display: flex;
  align-items: center;
}

.section-title::before {
  content: '';
  width: 3px;
  height: 20px;
  background: linear-gradient(180deg, #00d9a3 0%, #00c090 100%);
  border-radius: 2px;
  box-shadow: 0 0 8px rgba(0, 217, 163, 0.5);
  margin-right: 8px;
  margin-top: 2px;
}

.section-icon {
  width: 20px;
  height: 20px;
  color: #00d9a3;
  margin-left: auto;
}

.form-item {
  margin-bottom: 10px;
}

.form-label {
  display: block;
  font-size: 14px;
  color: rgba(255, 255, 255, 0.95);
  margin-bottom: 10px;
  display: flex;
  align-items: center;
}

.form-label.required::after {
  content: '*';
  color: #ff6b9d;
  font-size: 16px;
  font-weight: 700;
  display: none;
}

.label-icon {
  width: 16px;
  height: 16px;
  color: #00d9a3;
  margin-right: 5px;
}

.form-input,
.form-select,
.form-textarea {
  width: 100%;
  padding: 10px;
  background: rgba(15, 20, 25, 0.9);
  border: 1px solid rgba(0, 217, 163, 0.25);
  border-radius: 5px;
  color: rgba(208, 208, 208, 0.9);
  font-size: 16px;
  transition: all 0.3s ease;
  outline: none;
  font-family: inherit;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
  border-color: #00d9a3;
  background: rgba(15, 20, 25, 1);
  box-shadow: 0 0 0 4px rgba(0, 217, 163, 0.1), 0 4px 12px rgba(0, 217, 163, 0.2);
  transform: translateY(-1px);
}

.form-input::placeholder,
.form-textarea::placeholder {
  color: rgba(255, 255, 255, 0.35);
}

.form-select {
  cursor: pointer;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 16 16'%3E%3Cpath fill='%2300d9a3' d='M8 11L3 6h10z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 12px center;
  padding-right: 40px;
}

.form-select option {
  background: #1a1f2e;
  color: #fff;
  padding: 10px;
}

.form-textarea {
  min-height: 140px;
  resize: vertical;
  line-height: 1.6;
}

.captcha-row {
  display: flex;
  align-items: center;
  width: 100%;
}

.captcha-row .form-label {
  display: block;
  flex-shrink: 0;
  margin-bottom: 0;
  white-space: nowrap;
  min-width: auto;
  margin-bottom: 10px;
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
  align-items: center;
  width: 100%;
}

.form-inputyz {
  flex: 1;
  min-width: 0;
  padding: 10px;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(0, 217, 163, 0.2);
  border-radius: 5px;
  color: #fff;
  font-size: 16px;
  transition: all 0.3s ease;
}

.captcha-img {
  margin-left: 10px;
  width: 120px;
  height: 40px;
  flex-shrink: 0;
  background: rgba(0, 217, 163, 0.1);
  border: 1px solid rgba(0, 217, 163, 0.3);
  border-radius: 5px;
  cursor: pointer;
  transition: all 0.3s ease;
  object-fit: cover;
}

.form-error {
  display: none;
  color: #ff6b9d;
  font-size: 13px;
  margin-top: 8px;
  padding-left: 6px;
  align-items: center;
}

.form-error.show {
  display: flex;
}

.form-error::before {
  content: '⚠';
  font-size: 14px;
  margin-right: 4px;
}

.form-hint {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.5);
  margin-top: 8px;
  padding-left: 6px;
  line-height: 1.5;
}

.contact-hint {
  background: rgba(0, 217, 163, 0.1);
  border: 1px solid rgba(0, 217, 163, 0.2);
  border-radius: 5px;
  padding: 5px;
  margin-bottom: 10px;
  font-size: 13px;
  color: rgba(255, 255, 255, 0.8);
  display: flex;
  align-items: flex-start;
}

.contact-hint svg {
  width: 18px;
  height: 18px;
  color: #00d9a3;
  flex-shrink: 0;
  margin-right: 5px;
}

.submit-button {
  width: 100%;
  padding: 10px;
  background: linear-gradient(135deg, #00d9a3 0%, rgba(0, 192, 144, 0.5) 100%);
  border: none;
  border-radius: 5px;
  color: #fff;
  font-size: 17px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 8px 24px rgba(0, 217, 163, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
}

.submit-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 32px rgba(0, 217, 163, 0.5);
}

.submit-button:active {
  transform: translateY(0);
}

.submit-button svg {
  width: 20px;
  height: 20px;
  margin-right: 5px;
}

.notice-card {
  background: linear-gradient(135deg, #0a1f1a 0%, #0f2820 50%, #0a1f1a 100%);
  border-radius: 12px;
  padding: 20px;
  max-width: 500px;
  margin: 0 auto;
  border: 1px solid rgba(0, 217, 163, 0.3);
  box-shadow: 0 4px 20px rgba(0, 217, 163, 0.15);
}

.notice-header {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
  padding-bottom: 10px;
  border-bottom: 1px solid rgba(0, 217, 163, 0.3);
}

.notice-icon {
  width: 24px;
  height: 24px;
  margin-right: 5px;
  flex-shrink: 0;
}

.notice-title {
  font-size: 18px;
  font-weight: 600;
  color: #ffffff;
}

.notice-list {
  list-style: none;
}

.notice-item {
  display: flex;
  align-items: flex-start;
  margin-bottom: 16px;
  line-height: 1.6;
}

.notice-item:last-child {
  margin-bottom: 0;
}

.notice-dot {
  width: 8px;
  height: 8px;
  min-width: 8px;
  background: linear-gradient(135deg, #00d9a3 0%, #00c090 100%);
  border-radius: 50%;
  margin-right: 10px;
  margin-top: 6px;
  flex-shrink: 0;
  box-shadow: 0 0 6px rgba(0, 217, 163, 0.5);
}

.notice-text {
  font-size: 14px;
  color: #a0a0b8;
}

.notice-item.warning .notice-text {
  color: #b3f0dd;
}

.submit-overlay {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.6);
  z-index: 9999;
  justify-content: center;
  align-items: center;
}

.submit-overlay.show {
  display: flex;
}

.submit-overlay-content {
  background: white;
  padding: 30px 40px;
  border-radius: 12px;
  text-align: center;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.submit-spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #00d9a3;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 15px;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

.submit-overlay-text {
  font-size: 16px;
  color: #333;
  font-weight: 500;
}

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

@media (max-width: 768px) {
  .page-header h1 {
    font-size: 20px;
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

@media screen and (max-width: 480px) {
  .notice-card {
    margin-top: 15px;
    padding: 10px;
  }

  .notice-title {
    font-size: 14px;
  }

  .notice-text {
    font-size: 12px;
  }

  .notice-item {
    margin-bottom: 10px;
  }
}
.footer-nav::before {
    border-top: 1px solid var(--color-border-medium);
  }
  
  
</style>
</head>
<body>
<div class="container">
<?php include 'comm/header.php'; ?>

    <div class="page-intro">
            <h2 style="font-size:22px;color:#00cf9b;margin-top:80px;">选择发布类型</h2>
            <p style="font-size:13px;margin-top:5px;color:#00cf9b;">请选择您要发布的内容类型</p>
        </div>
    
    <div class="button-grid">
      <!-- Updated button structure with separate icon element -->
      <a href="javascript:void(0);" onclick="rzUrlpublish(<?php echo $user_id ?? 0;?>,'publish.html')"   class="function-btn">
        <div class="btn-icon"></div>
        <span class="btn-text">论坛</span>
        <div class="publish-desc">楼凤 · 上门 · 会所</div>
      </a>

      <a href="javascript:void(0);" onclick="rzUrlpublish(<?php echo $user_id ?? 0;?>,'fbgd.html')"  class="function-btn">
        <div class="btn-icon"></div>
        <span class="btn-text">高端</span>
        <div class="publish-desc">模特 · 网红 · 空姐</div>
      </a>

      <a href="javascript:void(0);" onclick="rzUrlpublish(<?php echo $user_id ?? 0;?>,'fbby.html?typeid=0')"   class="function-btn">
        <div class="btn-icon"></div>
        <span class="btn-text">伴游</span>
        <div class="publish-desc">商务伴游 · 真人认证</div>
      </a>

      <a href="javascript:void(0);" onclick="rzUrlpublish(<?php echo $user_id ?? 0;?>,'fbby.html?typeid=1')"  class="function-btn">
        <div class="btn-icon"></div>
        <span class="btn-text">包养</span>
        <div class="publish-desc">富豪包养 · 真人认证</div>
      </a>
    </div>
  </div>

  
<?php include 'comm/footer.php'; ?>
<script type="text/javascript">
                    function rzUrlpublish(user_id,url){
                        if(user_id == 0){
                            showInfo('您还未登陆，请登录后再发布','提示','100000');
                        } else {
                            window.location.href = url;
                        }
                        return false;
                    }
</script>
<?php  include_once 'comm/alert_modal.php'; ?>
</body>
</html>
