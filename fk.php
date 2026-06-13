<?php
require_once 'loaduser.php';
include_once 'config.php';

$pageTitle = "问题反馈";

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="author" content="<?php echo $webname; ?>" />
<meta name="keywords" content="问题反馈_<?php echo $webname; ?>">
<meta name="description" content="问题反馈_<?php echo $webname; ?>">
<title>问题反馈_<?php echo $webname; ?></title>
<link rel="stylesheet" href="/css/comm.css">
<!--<link rel="stylesheet" href="/css/feedback.css?t=123">-->

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  background: #f5f5f5;
  min-height: 100vh;
  padding-top: 50px;
  color: #333;
}

.container {
  max-width: 600px;
  margin: 0 auto;
  padding: 10px;
}

.welcome-card {
  background: linear-gradient(135deg, #00d9a3 0%, #00c090 100%);
  border-radius: 16px;
  padding: 24px;
  margin-bottom: 20px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
  color: white;
}

.welcome-title {
  font-size: 24px;
  font-weight: 700;
  margin-bottom: 8px;
  letter-spacing: -0.5px;
}

.welcome-desc {
  font-size: 14px;
  opacity: 0.95;
  line-height: 1.6;
}

.form-card {
  background: #ffffff;
  border-radius: 10px;
  padding: 10px;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
  border: 1px solid #e5e7eb;
}

.form-section {
  margin-bottom: 10px;
}

.form-section:last-child {
  margin-bottom: 0;
}

.section-label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 15px;
  font-weight: 600;
  color: #333;
  margin-bottom: 10px;
}

.section-label::before {
  content: '';
  width: 4px;
  height: 16px;
  background: linear-gradient(135deg, #2ded5c 0%, #00c090 100%);
  border-radius: 2px;
}

.section-hint {
  font-size: 13px;
  color: #666;
  margin-bottom: 10px;
  padding-left: 10px;
}

.textarea-wrapper {
  position: relative;
}

textarea {
  width: 100%;
  min-height: 160px;
  padding: 10px;
  padding-bottom: 10px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  color: #333;
  font-size: 16px;
  line-height: 1.6;
  transition: all 0.2s ease;
  outline: none;
  font-family: inherit;
  resize: vertical;
}

textarea::placeholder {
  color: #9ca3af;
}

textarea:focus {
  border-color: #00d9a3;
  background: #f0fdf9;
  box-shadow: 0 0 0 4px rgba(0, 217, 163, 0.08);
}

.char-count {
  position: absolute;
  bottom: 12px;
  right: 14px;
  font-size: 12px;
  color: #9ca3af;
  font-weight: 500;
}

.upload-area {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  column-gap: 10px;
  row-gap: 10px;
}

.upload-item {
  position: relative;
  aspect-ratio: 1;
  border: 2px dashed #e5e7eb;
  border-radius: 8px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
  background: #ffffff;
}

.upload-item:active {
  border-color: #00d9a3;
  background: #f0fdf9;
  transform: scale(0.98);
}

.upload-item.has-image {
  border-style: solid;
  border-color: #00d9a3;
  padding: 0;
  overflow: hidden;
  background: #ffffff;
}

.upload-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.upload-icon {
  font-size: 24px;
  color: #00d9a3;
  margin-bottom: 0px;
  font-weight: 600;
}

.upload-text {
  font-size: 12px;
  color: #666;
  text-align: center;
  font-weight: 500;
}

.remove-btn {
  position: absolute;
  top: 6px;
  right: 6px;
  width: 26px;
  height: 26px;
  background: rgba(0, 0, 0, 0.75);
  backdrop-filter: blur(4px);
  color: white;
  border: none;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  line-height: 1;
  transition: all 0.2s;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.remove-btn:active {
  background: rgba(0, 0, 0, 0.9);
  transform: scale(0.95);
}

.captcha-row {
  display: flex;
/*  gap: 10px;*/
  align-items: stretch;
}

.captcha-input {
  flex: 1;
  padding: 10px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  color: #333;
  font-size: 16px;
  transition: all 0.2s ease;
  outline: none;
  font-family: inherit;
  margin-right: 10px;
  width: 60%;
}

.captcha-input:focus {
  border-color: #00d9a3;
  background: #f0fdf9;
  box-shadow: 0 0 0 4px rgba(0, 217, 163, 0.08);
}

.captcha-image {
  width: 30%;
  height: auto;
  background: #f9fafb;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  cursor: pointer;
  user-select: none;
  transition: transform 0.2s;
  border: 1px solid #e5e7eb;
}

.captcha-image:active {
  transform: scale(0.98);
}

.captcha-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.submit-btn {
  width: 100%;
  padding: 12px;
  background:linear-gradient(135deg, #00d9a3 0%, #2ded5c 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
/*  margin-top: 10px;*/
  box-shadow: 0 4px 12px rgba(0, 217, 163, 0.35);
  letter-spacing: 0.5px;
}

.submit-btn:active {
  transform: translateY(1px);
  box-shadow: 0 2px 8px rgba(0, 217, 163, 0.3);
}

.submit-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.form-error {
  color: #dc2626;
  font-size: 13px;
  margin-top: 8px;
  display: none;
  padding-left: 12px;
  font-weight: 500;
}

.form-error.show {
  display: block;
}

input[type="file"] {
  display: none;
}


</style>

</head>
<body>
  <?php include 'comm/header.php'; ?>
  <div class="container">
    <div class="form-card">
      <form id="feedbackForm">
        <!-- 问题描述 -->
        <div class="form-section">
          <div class="section-label">问题描述</div>
          <div class="section-hint">请详细描述您遇到的问题或建议</div>
          <div class="textarea-wrapper">
            <textarea 
              id="content" 
              name="content" 
              maxlength="500"
              placeholder="请告诉我们您的想法..."
            ></textarea>
            <div class="char-count">
              <span id="charCount">0</span>/500
            </div>
          </div>
          <div class="form-error" id="contentError">请输入问题描述</div>
        </div>

        <!-- 图片上传 -->
        <div class="form-section">
          <div class="section-label">添加图片</div>
          <div class="section-hint">支持 JPG、PNG、GIF 格式，最多3张</div>
          <div class="upload-area" id="uploadArea">
            <div class="upload-item" id="uploadBtn">
              <div class="upload-icon">+</div>
              <div class="upload-text">上传图片</div>
            </div>
          </div>
          <input type="file" id="fileInput" accept="image/jpeg,image/png,image/gif" multiple>
          <div class="form-error" id="uploadError"></div>
        </div>

        <!-- 验证码 -->
        <div class="form-section">
          <div class="section-label">安全验证</div>
          <div class="captcha-row">
            <input 
              type="text" 
              id="captcha" 
              name="captcha" 
              class="captcha-input"
              placeholder="请输入验证码"
              maxlength="4"
            >
            <div class="captcha-image" id="captchaImage">
              <img id="captchaImg" src="/lib/yzmcode.html" alt="验证码" class="captcha-img" onclick="refreshCaptcha()">
            </div>
          </div>
          <div class="form-error" id="captchaError">请输入验证码</div>
        </div>

        <!-- 提交按钮 -->
        <button type="submit" class="submit-btn" id="submitBtn">提交反馈</button>
      </form>
    </div>
  </div>

  <?php include 'comm/alert_modal.php'; ?>

  <script>
    // 字符计数
    const contentTextarea = document.getElementById('content');
    const charCount = document.getElementById('charCount');
    
    contentTextarea.addEventListener('input', function() {
      charCount.textContent = this.value.length;
      hideFieldError('content');
    });

    // 图片上传
    const uploadBtn = document.getElementById('uploadBtn');
    const fileInput = document.getElementById('fileInput');
    const uploadArea = document.getElementById('uploadArea');
    let uploadedImages = [];

    uploadBtn.addEventListener('click', function() {
      if (uploadedImages.length >= 3) {
        showError('最多只能上传3张图片');
        return;
      }
      fileInput.click();
    });

    fileInput.addEventListener('change', function(e) {
      const files = Array.from(e.target.files);
      
      for (let file of files) {
        if (uploadedImages.length >= 3) {
          showError('最多只能上传3张图片');
          break;
        }

        // 验证文件类型
        if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
          showError('只支持 jpg、png、gif 格式');
          continue;
        }

        // 验证文件大小（5MB）
        if (file.size > 5 * 1024 * 1024) {
          showError('图片大小不能超过5MB');
          continue;
        }

        uploadedImages.push(file);
        addImagePreview(file);
      }

      fileInput.value = '';
      updateUploadBtn();
      hideFieldError('upload');
    });

    function addImagePreview(file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        const div = document.createElement('div');
        div.className = 'upload-item has-image';
        div.innerHTML = `
          <img src="${e.target.result}" alt="preview">
          <button type="button" class="remove-btn" onclick="removeImage(this)">×</button>
        `;
        uploadArea.insertBefore(div, uploadBtn);
      };
      reader.readAsDataURL(file);
    }

    window.removeImage = function(btn) {
      const item = btn.parentElement;
      const index = Array.from(uploadArea.children).indexOf(item) - 1;
      if (index >= 0 && index < uploadedImages.length) {
        uploadedImages.splice(index, 1);
      }
      item.remove();
      updateUploadBtn();
    };

    function updateUploadBtn() {
      uploadBtn.style.display = uploadedImages.length >= 3 ? 'none' : 'flex';
    }

    // 验证码刷新
    function refreshCaptcha() {
      const captchaImg = document.getElementById('captchaImg');
      captchaImg.src = '/lib/yzmcode.html?' + new Date().getTime();
    }

    // 输入验证码时隐藏错误
    document.getElementById('captcha').addEventListener('input', function() {
      hideFieldError('captcha');
    });

    // 显示/隐藏错误
    function showFieldError(field, message) {
      const errorEl = document.getElementById(field + 'Error');
      if (errorEl) {
        errorEl.textContent = message;
        errorEl.classList.add('show');
      }
    }

    function hideFieldError(field) {
      const errorEl = document.getElementById(field + 'Error');
      if (errorEl) {
        errorEl.classList.remove('show');
      }
    }

    // 表单提交
    document.getElementById('feedbackForm').addEventListener('submit', async function(e) {
      e.preventDefault();

      // 清除之前的错误
      document.querySelectorAll('.form-error').forEach(el => el.classList.remove('show'));

      // 验证
      const content = contentTextarea.value.trim();
      const captcha = document.getElementById('captcha').value.trim();

      let hasError = false;

      if (!content) {
        showFieldError('content', '请输入问题描述');
        hasError = true;
      }

      if (!captcha) {
        showFieldError('captcha', '请输入验证码');
        hasError = true;
      }

      if (hasError) return;

      // 准备上传图片
      const submitBtn = document.getElementById('submitBtn');
      submitBtn.disabled = true;
      submitBtn.textContent = '上传图片中...';

      try {
        // 上传所有图片
        const uploadedPaths = [];
        for (let i = 0; i < uploadedImages.length; i++) {
          const formData = new FormData();
          formData.append('file', uploadedImages[i]);
          formData.append('type', 'image');

          const response = await fetch('/uploads_api.html', {
            method: 'POST',
            body: formData
          });

          const result = await response.json();
          if (result.code === 200) {
            uploadedPaths.push(result.data.url);
          } else {
            throw new Error(result.msg || '图片上传失败');
          }
        }

        // 提交反馈
        submitBtn.textContent = '提交中...';

        const feedbackData = new FormData();
        feedbackData.append('content', content);
        feedbackData.append('pics', uploadedPaths.join(','));
        feedbackData.append('captcha', captcha);
        const feedbackResponse = await fetch('/opers/user/user_feedback.html', {
          method: 'POST',
          body: feedbackData
        });

        const feedbackResult = await feedbackResponse.json();
        if (feedbackResult.code === 200) {
            showSuccess(feedbackResult.msg || '反馈提交成功！', '反馈成功', 10000, '/user.html');
          } else {
            showInfo(feedbackResult.msg || '反馈提交失败');
            refreshCaptcha();
            submitBtn.disabled = false;
            submitBtn.textContent = '提交反馈';
          }
      } catch (error) {
        alert(error.message);
        submitBtn.disabled = false;
        submitBtn.textContent = '提交反馈';
      }
    });
  </script>
</body>
</html>
