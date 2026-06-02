<?php
include_once 'loaduser.php';
include_once 'config.php';

$pageTitle = "真人认证";

$rejectionReason = '视频中面部特征不够清晰，请重新录制';

// 设置默认审核状态值
// 0=未上传, 1=未上传, 2=待审核, 3=已认证, 4=审核未通过
$user_info = db('userb')->field('id,isrz,videos')->where('id', $user_id)->find();
$verifyCode = db('fl_config')->where('cname', 'rz_str')->value('value');

if ($user_info['isrz'] == 0) {
    if ($user_info['videos'] == '' || $user_info['videos'] == '0') {
        $sh = 0;
    }else{
        $sh = 2;
    }
}elseif($user_info['isrz'] == 1){
    $sh = 3;
}elseif($user_info['isrz'] == 2){
    $sh = 4;
}elseif($user_info['isrz'] == 3){
    $sh = 5;
}
// 定义状态文本
$statusText = [
    0 => '未上传',
    1 => '未上传',
    2 => '等待审核',
    3 => '已认证',
    4 => '审核未通过',
    5 => '无需认证'
];

// 定义状态描述
$statusDesc = [
    0 => '请上传视频完成认证',
    1 => '请上传视频完成认证',
    2 => '您的认证视频正在审核中，请耐心等待',
    3 => '恭喜您已通过认证！',
    4 => '很遗憾，您的认证未通过，请重新上传视频',
    5 => '您无需认证'
];

// 判断是否可以上传
$canUpload = ($sh == 0 || $sh == 1 || $sh == 4);

$Statusnum =  [
    0 => 'ings',
    1 => 'ings',
    2 => 'pending',
    3 => 'verified',
    4 => 'rejected',
    5 => 'none'
];
// pending(待认证), verified(已认证), rejected(认证失败), none(无需认证)
$verificationStatus = $Statusnum[$sh];

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="author" content="<?php echo $webname; ?>" />
<meta name="keywords" content="真人认证_<?php echo $webname; ?>">
<meta name="description" content="真人认证_<?php echo $webname; ?>">
<title>真人认证_<?php echo $webname; ?></title>
<link rel="stylesheet" href="/css/comm.css">
<link rel="stylesheet" href="/css/verification.css">
</head>
<body>
  <?php include 'comm/header.php'; ?>
  <div class="container">
      <?php if ($sh === 3): ?>
      <!-- 已认证状态 -->
      <div class="status-card">
        <div class="status-icon verified">
          <svg viewBox="0 0 24 24">
            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
          </svg>
        </div>
        <div class="status-title">认证已通过</div>
        <div class="status-desc">您的视频认证已通过审核，可以正常使用所有功能</div>
      </div>
      <?php endif; ?>
      <?php if ($sh === 5): ?>
      <!-- 无需认证状态 -->
      <div class="status-card">
        <div class="status-icon none">
          <svg viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/>
            <path d="M12 16v-4M12 8h.01"/>
          </svg>
        </div>
        <div class="status-title">无需认证</div>
        <div class="status-desc">您的账户当前无需进行视频认证</div>
      </div>
       <?php endif; ?>
      <!-- 待认证或认证失败状态 - 显示上传表单 -->
      
        <?php if ($sh === 2): ?>
        <div class="status-card" >
          <div class="status-icon pending">
            <svg viewBox="0 0 24 24">
              <circle cx="12" cy="12" r="10"/>
              <path d="M12 6v6l4 2"/>
            </svg>
          </div>
          <div class="status-title">等待审核</div>
          <div class="status-desc">您的视频认证正在审核中，请耐心等待</div>
        </div>
        <?php endif; ?>

        <?php if ($sh === 4): ?>
        <div class="status-card">
          <div class="status-icon rejected">
            <svg viewBox="0 0 24 24">
              <circle cx="12" cy="12" r="10"/>
              <path d="M15 9l-6 6M9 9l6 6"/>
            </svg>
          </div>
          <div class="status-title">认证失败</div>
          <div class="status-desc">很抱歉，您的视频认证未通过审核，请重新提交</div>
          <?php if (!empty($rejectionReason)): ?>
            <div class="rejection-reason">
              <div class="label">失败原因：</div>
              <div class="text"><?php echo htmlspecialchars($rejectionReason); ?></div>
            </div>
          <?php endif; ?>
        </div>
        <?php endif; ?>


      <?php if ($sh == 1 || $sh == 0 || $sh == 4 ): ?>
      <!-- 认证提示 -->
      <div class="verification-prompt">
        <div class="prompt-title">请朗读以下8个字并录制视频上传</div>
        <div class="prompt-text">大家好啊，我在这里</div>
      </div>

      <!-- 上传区域 -->
      <div class="upload-section">
        <div class="upload-area" id="uploadArea">
          <div class="upload-icon">
            <svg viewBox="0 0 24 24">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
              <polyline points="17 8 12 3 7 8"/>
              <line x1="12" y1="3" x2="12" y2="15"/>
            </svg>
          </div>
          <div class="upload-text">点击上传认证视频</div>
          <div class="upload-hint">支持mp4、mov格式，大小不超过50MB</div>
        </div>
        <input type="file" id="videoInput" accept="video/mp4,video/quicktime">
      </div>


      <!-- 提交按钮 -->
      <button class="submit-btn" id="submitBtn" disabled>提交认证</button>

      
      <!-- 认证须知 -->
      <div class="notice-section">
        <div class="notice-title">
<svg t="1774155627998" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="236941" width="32" height="32"><path d="M180.821333 150.784a32.853333 32.853333 0 1 0-46.506666 46.506667l46.506666 46.506666a32.853333 32.853333 0 0 0 46.506667-46.506666l-46.506667-46.506667z m624.768 0l-46.506666 46.506667a32.853333 32.853333 0 1 0 46.506666 46.506666l46.506667-46.506666a32.853333 32.853333 0 1 0-46.506667-46.506667z m-706.986666 260.266667H32.938667a32.853333 32.853333 0 0 0 0 65.706666h65.706666a32.853333 32.853333 0 1 0 0-65.706666z m854.954666 0h-65.749333a32.853333 32.853333 0 1 0 0 65.706666h65.706667a32.853333 32.853333 0 1 0 0-65.706666zM460.330667 32.853333v65.749334a32.853333 32.853333 0 1 0 65.749333 0V32.896a32.853333 32.853333 0 0 0-65.706667 0zM263.04 493.226667a230.144 230.144 0 1 1 460.330667 0 230.144 230.144 0 0 1-460.330667 0z m-65.749333 0a295.936 295.936 0 1 0 591.872 0 295.936 295.936 0 0 0-591.872 0z m230.144 460.330666c0 18.133333 14.762667 32.853333 32.896 32.853334h65.749333a32.853333 32.853333 0 0 0 0-65.706667h-65.706667a32.853333 32.853333 0 0 0-32.938666 32.853333z m-65.706667-98.645333c0 18.133333 14.677333 32.853333 32.853333 32.853333h197.290667a32.853333 32.853333 0 0 0 0-65.706666H394.581333a32.853333 32.853333 0 0 0-32.853333 32.853333z" fill="#FB5555" p-id="236942"></path></svg>
          认证须知
        </div>
        <ul class="notice-list">
          <li>1.请上传本人真实认证视频</li>
          <li>2.视频中需清晰展示本人面部特征</li>
          <li>3.视频时长建议3-5秒</li>
          <li>4.文件大小不超过50MB</li>
          <li>5.真人认证视频，只用于真人Ai认证识别，认证完成后，视频将自动销毁，此视频不会在平台展示和保留</li>
        </ul>
      </div>
      <?php endif; ?>
      
  </div>

  <?php include 'comm/alert_modal.php'; ?>

  <script>
    const uploadArea = document.getElementById('uploadArea');
    const videoInput = document.getElementById('videoInput');
    const submitBtn = document.getElementById('submitBtn');
    let uploadedVideo = null;

    // 点击上传区域触发文件选择
    if (uploadArea) {
      uploadArea.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-video-btn') || 
            e.target.closest('.remove-video-btn')) {
          return;
        }
        if (!uploadedVideo) {
          videoInput.click();
        }
      });
    }

    // 文件选择变化
    if (videoInput) {
      videoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        // 验证文件类型
        if (!['video/mp4', 'video/quicktime'].includes(file.type)) {
          showAlert('请上传mp4或mov格式的视频文件');
          return;
        }

        // 验证文件大小（50MB）
        if (file.size > 50 * 1024 * 1024) {
          showAlert('视频文件大小不能超过50MB');
          return;
        }

        uploadedVideo = file;
        showVideoPreview(file);
        submitBtn.disabled = false;
      });
    }

    // 显示视频预览
    function showVideoPreview(file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        uploadArea.classList.add('has-video');
        uploadArea.innerHTML = `
          <video class="video-preview" controls>
            <source src="${e.target.result}" type="${file.type}">
          </video>
          <button type="button" class="remove-video-btn" onclick="removeVideo()">×</button>
        `;
      };
      reader.readAsDataURL(file);
    }

    // 移除视频
    window.removeVideo = function() {
      uploadedVideo = null;
      uploadArea.classList.remove('has-video');
      uploadArea.innerHTML = `
        <div class="upload-icon">
          <svg viewBox="0 0 24 24">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="17 8 12 3 7 8"/>
            <line x1="12" y1="3" x2="12" y2="15"/>
          </svg>
        </div>
        <div class="upload-text">点击上传认证视频</div>
        <div class="upload-hint">支持mp4、mov格式，大小不超过50MB</div>
      `;
      videoInput.value = '';
      submitBtn.disabled = true;
      submitBtn.textContent = '提交认证';
    };

    // 提交认证
    if (submitBtn) {
      submitBtn.addEventListener('click', async function() {
        if (!uploadedVideo) {
          showAlert('请先上传认证视频');
          return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = '上传中...';

        try {
          // 第一步：上传视频文件
          const formData = new FormData();
          formData.append('file', uploadedVideo);
          formData.append('type', 'video');

          const uploadResponse = await fetch('/uploads_api.html', {
            method: 'POST',
            body: formData
          });

          const uploadResult = await uploadResponse.json();
          if (uploadResult.code === 200) {
            // 第二步：提交认证
            submitBtn.textContent = '提交认证中...';
            
            const submitResponse = await fetch('/opers/member/videos.html', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
              },
              body: `filename=${encodeURIComponent(uploadResult.data.url)}`
            });

            const submitResult = await submitResponse.json();
            if (submitResult.code === 200) {
              showSuccess(submitResult.msg || '认证视频提交成功！请等待审核', '提交成功', 10000, 'user.html');
            } else {
              showInfo(submitResult.msg || '提交失败');
              submitBtn.disabled = false;
              submitBtn.textContent = '提交认证';
            }
          } else {
            showInfo(uploadResult.msg || '视频上传失败');
          }
        } catch (error) {
          showAlert(error.message);
          submitBtn.disabled = false;
          submitBtn.textContent = '提交认证';
        }
      });
    }
  </script>
</body>
</html>
