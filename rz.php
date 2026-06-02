<?php
include_once 'loaduser.php';
$pageTitle = "视频认证";


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
<title><?php echo $pageTitle; ?></title>
<link rel="stylesheet" href="/css/comm.css">
<link rel="stylesheet" href="/css/verification.css?t=<?php echo time(); ?>">
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
<svg t="1780394656019" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="9834" width="32" height="32" style="margin-right:5px;"><path d="M512 102.4c-227.84 0-409.6 181.76-409.6 409.6S284.16 921.6 512 921.6 921.6 739.84 921.6 512 739.84 102.4 512 102.4z m-39.424 160.256c10.752-10.752 19.968-17.92 37.376-17.92 19.456-1.536 37.376 7.68 44.544 21.504 8.192 9.728 12.8 31.232 10.752 44.032 0 3.584-2.56 27.648-3.584 34.304l-14.336 181.76c0 17.92-3.584 35.84-10.752 49.664-3.584 10.752-14.336 17.92-28.672 17.92-10.752 0-21.504-7.168-25.088-17.92-7.168-17.92-10.752-32.256-10.752-49.664l-9.728-178.176c-3.584-32.256-3.584-21.504-3.584-39.424 0-17.408 3.584-31.744 13.824-46.08z m78.848 502.272c-10.752 10.752-25.088 14.336-35.84 14.336-14.336 0-28.672-3.584-39.424-14.336s-17.92-25.088-17.92-42.496c0-14.336 3.584-28.672 14.336-39.424 10.752-10.752 25.088-17.92 39.424-17.92s28.672 7.168 39.424 17.92 14.336 25.088 14.336 39.424c-0.512 17.408-4.096 31.744-14.336 42.496z" fill="#FCBD45" p-id="9835"></path></svg>
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

          const uploadResponse = await fetch('/uploads_api.php', {
            method: 'POST',
            body: formData
          });

          // 检查响应状态
          if (!uploadResponse.ok) {
            showAlert('视频上传失败：服务器错误 ' + uploadResponse.status);
            submitBtn.disabled = false;
            submitBtn.textContent = '提交认证';
            return;
          }

          // 先获取响应文本，再尝试解析 JSON
          const uploadText = await uploadResponse.text();
          let uploadResult;
          try {
            uploadResult = JSON.parse(uploadText);
          } catch (parseError) {
            // JSON 解析失败，说明服务器返回了错误页面
            showAlert('服务器返回了无效的响应，请稍后重试');
            submitBtn.disabled = false;
            submitBtn.textContent = '提交认证';
            return;
          }
          
          // 根据上传接口返回结果处理
          if (uploadResult.code !== 200) {
            showAlert('视频上传失败：' + (uploadResult.msg || '未知错误'));
            submitBtn.disabled = false;
            submitBtn.textContent = '提交认证';
            return;
          }

          // 上传成功，继续提交认证
          submitBtn.textContent = '提交认证中...';
          
          const submitResponse = await fetch('/opers/member/videos.html', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `filename=${encodeURIComponent(uploadResult.data.url)}`
          });

          if (!submitResponse.ok) {
            showAlert('提交认证失败：服务器错误 ' + submitResponse.status);
            submitBtn.disabled = false;
            submitBtn.textContent = '提交认证';
            return;
          }

          // 同样先获取文本再解析
          const submitText = await submitResponse.text();
          let submitResult;
          try {
            submitResult = JSON.parse(submitText);
          } catch (parseError) {
            showAlert('认证服务返回了无效的响应，请稍后重试');
            submitBtn.disabled = false;
            submitBtn.textContent = '提交认证';
            return;
          }
          
          if (submitResult.code === 200) {
            showSuccess(submitResult.msg || '认证视频上传成功！请等待审核', '上传成功', 10000, 'user.html');
          } else {
            showAlert('提交认证失败：' + (submitResult.msg || '未知错误'));
            submitBtn.disabled = false;
            submitBtn.textContent = '提交认证';
          }
        } catch (error) {
          showAlert('网络错误：' + (error.message || '请检查网络连接后重试'));
          submitBtn.disabled = false;
          submitBtn.textContent = '提交认证';
        }
      });
    }
  </script>
</body>
</html>
