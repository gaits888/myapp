<?php
// 前端用户界面 - 扫码付款上传凭证
$groups = array(
    1 => array(
        'alipay' => 'qrcode/group1_alipay.jpg',
        'wechat' => 'qrcode/group1_wechat.jpg',
    ),
    2 => array(
        'alipay' => 'qrcode/group2_alipay.jpg',
        'wechat' => 'qrcode/group2_wechat.jpg',
    ),
);

// 获取UID参数
$uid = isset($_GET['uid']) ? trim($_GET['uid']) : '';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>线下扫码</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background:#f5f5f5; color:#333; min-height:100vh; }
.container { max-width:480px; margin:0 auto; padding:20px 15px; }
h1 { text-align:center; font-size:22px; margin-bottom:20px; color:red; }

/* 收款码组切换 */
.group-tabs { display:flex; justify-content:center; margin-bottom:20px; gap:10px; }
.group-tab { padding:10px 25px; border:1px solid #4a90d9; border-radius:20px; background:#fff; color:#4a90d9; cursor:pointer; font-size:14px; transition:all .2s; width:48%; text-align:center; }
.group-tab.active { background:#4a90d9; color:#fff; }

/* 支付方式切换 */
.pay-tabs { display:flex; justify-content:center; margin-bottom:15px; gap:0; }
.pay-tab { flex:1; text-align:center; padding:10px; cursor:pointer; font-size:15px; font-weight:600; border-bottom:3px solid #ddd; color:#999; transition:all .2s; }
.pay-tab.active-alipay { border-color:#1677ff; color:#1677ff; }
.pay-tab.active-wechat { border-color:#07c160; color:#07c160; }

/* 二维码展示 */
.qrcode-box { text-align:center; background:#fff; border-radius:12px; padding:20px; margin-bottom:20px; box-shadow:0 2px 8px rgba(0,0,0,.08); }
.qrcode-box img { max-width:260px; width:100%; border-radius:8px; }
.qrcode-tip { margin-top:12px; font-size:13px; color:#888; }

/* 表单 */
.form-box { background:#fff; border-radius:12px; padding:20px; box-shadow:0 2px 8px rgba(0,0,0,.08); }
.form-group { margin-bottom:15px; }
.form-group label { display:block; font-size:14px; color:#666; margin-bottom:6px; }
.form-group .hint { font-size:12px; color:#999; margin-top:4px; }

/* 金额选项 */
.money-options { display:flex; flex-wrap:wrap; gap:8px; }
.money-opt { flex:1; min-width:calc(33.33% - 8px); padding:10px 5px; text-align:center; border:2px solid #ddd; border-radius:8px; font-size:15px; font-weight:600; cursor:pointer; transition:all .2s; background:#fff; color:#666; }
.money-opt:hover { border-color:#4a90d9; color:#4a90d9; }
.money-opt.selected { border-color:#4a90d9; background:#4a90d9; color:#fff; }

/* 上传 */
.upload-area { border:2px dashed #ccc; border-radius:8px; padding:30px 15px; text-align:center; cursor:pointer; transition:all .2s; background:#fafafa; position:relative; }
.upload-area:hover { border-color:#4a90d9; background:#f0f7ff; }
.upload-area.has-file { border-color:#52c41a; background:#f6ffed; }
.upload-area p { color:#999; font-size:14px; margin-top:8px; }
.upload-area img { max-width:200px; max-height:200px; border-radius:6px; margin-top:8px; }
.compress-info { font-size:12px; color:#52c41a; margin-top:6px; display:none; }

/* 删除按钮 */
.delete-btn { position:absolute; top:10px; right:10px; width:32px; height:32px; background:#ff4d4f; color:#fff; border:none; border-radius:50%; font-size:18px; line-height:32px; text-align:center; cursor:pointer; display:none; box-shadow:0 2px 8px rgba(0,0,0,.2); transition:all .2s; z-index:10; }
.delete-btn:hover { background:#ff7875; transform:scale(1.1); }
.upload-area.has-file .delete-btn { display:block; }

/* 提交按钮 */
.submit-btn { width:100%; padding:14px; background:linear-gradient(135deg,#4a90d9,#357abd); color:#fff; border:none; border-radius:8px; font-size:16px; font-weight:600; cursor:pointer; margin-top:10px; transition:opacity .2s; }
.submit-btn:disabled { opacity:.5; cursor:not-allowed; }
.submit-btn:active:not(:disabled) { opacity:.8; }

/* 结果弹窗 */
.msg-overlay { display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,.4); z-index:100; justify-content:center; align-items:center; }
.msg-overlay.show { display:flex; }
.msg-box { background:#fff; border-radius:12px; padding:30px; text-align:center; max-width:320px; width:90%; }
.msg-box .icon { font-size:48px; margin-bottom:12px; }
.msg-box .text { font-size:16px; margin-bottom:20px; }
.msg-box button { padding:10px 30px; background:#4a90d9; color:#fff; border:none; border-radius:6px; font-size:14px; cursor:pointer; }

/* 温馨提示 */
.tips-box {
    margin-top: 10px;
    background: linear-gradient(135deg, #fff9e6 0%, #fff3cc 100%);
    border-left: 2px solid #ffc107;
    border-radius: 8px;
    padding: 16px;
    box-shadow: 0 2px 8px rgba(255, 193, 7, 0.15);
}
.tips-title {
    font-size: 15px;
    font-weight: 600;
    color: #d39e00;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
}
.tips-content {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.tips-item {
    display: flex;
    align-items: flex-start;
    font-size: 13px;
    color: #666;
    line-height: 1.6;
}
.tips-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 18px;
    height: 18px;
    background: #52c41a;
    color: #fff;
    border-radius: 50%;
    font-size: 11px;
    font-weight: bold;
    margin-right: 8px;
    flex-shrink: 0;
    margin-top: 1px;
}

/* loading */
.loading { display:inline-block; width:20px; height:20px; border:2px solid #fff; border-radius:50%; border-top-color:transparent; animation:spin .6s linear infinite; vertical-align:middle; margin-right:6px; }
@keyframes spin { to { transform:rotate(360deg); } }

@media(max-width:480px) {
    .container { padding:15px 10px; }
    h1 { font-size:20px; }
    .qrcode-box img { max-width:220px; }
    .money-opt { min-width:calc(33.33% - 8px); font-size:14px; padding:8px 5px; }
}

/* 返回按钮 */
.back-bar { display:flex; align-items:center; margin-bottom:15px; }
.back-btn { display:inline-flex; align-items:center; color:#4a90d9; text-decoration:none; font-size:14px; padding:6px 12px; border-radius:6px; transition:background .2s; cursor:pointer; }
.back-btn:hover { background:rgba(74,144,217,.1); }
.back-btn svg { width:18px; height:18px; margin-right:4px; }

/* 相册弹窗 */
.gallery-overlay { display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,.9); z-index:200; }
.gallery-overlay.show { display:block; }
.gallery-header { position:absolute; top:0; left:0; right:0; padding:15px; display:flex; justify-content:space-between; align-items:center; z-index:10; }
.gallery-title { color:#fff; font-size:16px; font-weight:600; }
.gallery-close { width:36px; height:36px; background:rgba(255,255,255,.2); border:none; border-radius:50%; color:#fff; font-size:24px; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background .2s; }
.gallery-close:hover { background:rgba(255,255,255,.3); }
.gallery-body { position:absolute; top:60px; bottom:60px; left:0; right:0; display:flex; align-items:center; justify-content:center; }
.gallery-img-wrap { max-width:90%; max-height:100%; position:relative; }
.gallery-img-wrap img { max-width:100%; max-height:100%; object-fit:contain; border-radius:8px; }
.gallery-nav { position:absolute; top:50%; width:100%; display:flex; justify-content:space-between; padding:0 10px; transform:translateY(-50%); }
.gallery-nav-btn { width:44px; height:44px; background:rgba(255,255,255,.2); border:none; border-radius:50%; color:#fff; font-size:20px; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background .2s; }
.gallery-nav-btn:hover { background:rgba(255,255,255,.35); }
.gallery-nav-btn:disabled { opacity:.3; cursor:not-allowed; }
.gallery-footer { position:absolute; bottom:0; left:0; right:0; padding:15px; text-align:center; }
.gallery-counter { color:rgba(255,255,255,.7); font-size:14px; }

/* loading */
.loading { display:inline-block; width:20px; height:20px; border:2px solid #fff; border-radius:50%; border-top-color:transparent; animation:spin .6s linear infinite; vertical-align:middle; margin-right:6px; }
@keyframes spin { to { transform:rotate(360deg); } }
</style>
</head>
<body>
<div class="container">
    <!-- 返回按钮 -->
    <div class="back-bar">
        <a class="back-btn" onclick="goBack();">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
            返回
        </a>
    </div>

    <h1>欢迎使用平台线下扫码支付(实时到账)</h1>

    <!-- 收款码组切换 -->
    <div class="group-tabs">
        <div class="group-tab active" data-group="1">可用收款码</div>
        <div class="group-tab" data-group="2">备用收款码</div>
    </div>

    <!-- 支付方式切换 -->
    <div class="pay-tabs">
        <div class="pay-tab active-alipay" data-pay="alipay">支付宝</div>
        <div class="pay-tab" data-pay="wechat">微信支付</div>
    </div>

    <!-- 二维码 -->
    <div class="qrcode-box">
        <img id="qrcodeImg" src="qrcode/group1_alipay.jpg" alt="收款码">
        <p class="qrcode-tip" style="color:red;">请扫上方二维码付款，付款后上传凭证</p>
    </div>

    <!-- 表单 -->
    <div class="form-box">
        <input type="hidden" id="uid" value="<?php echo htmlspecialchars($uid); ?>">

        <div class="form-group">
            <label>选择充值金额（元）</label>
            <div class="money-options">
                <div class="money-opt" data-money="100">100</div>
                <div class="money-opt" data-money="200">200</div>
                <div class="money-opt" data-money="500">500</div>
                <div class="money-opt" data-money="1000">1000</div>
                <div class="money-opt" data-money="2000">2000</div>
                <div class="money-opt" data-money="5000">5000</div>
            </div>
            <p class="hint">请选择整数金额，仅支持以上面额</p>
        </div>

        <div class="form-group">
            <label>上传付款凭证 (<a onclick="opens();" style="color:red;">示例</a>)</label>
            <div class="upload-area" id="uploadArea">
                <input type="file" id="fileInput" accept="image/jpeg,image/png,image/gif,image/webp" style="display:none">
                <button type="button" class="delete-btn" id="deleteBtn" title="删除图片">&times;</button>
                <div id="uploadPlaceholder">
                    <p style="font-size:28px;">&#128247;</p>
                    <p>点击上传付款截图</p>
                    <p style="font-size:12px;color:#bbb;">支持 JPG/PNG/GIF/WEBP 格式</p>
                </div>
                <img id="previewImg" style="display:none;">
                <p class="compress-info" id="compressInfo"></p>
            </div>
        </div>
        <button class="submit-btn" id="submitBtn" onclick="submitOrder()">提交凭证</button>
    </div>
    


    <div class="tips-box">
        <div class="tips-title">💡 温馨提示</div>
        <div class="tips-content">
            <div class="tips-item">
                <span class="tips-icon">✓</span>
                <span>请扫上方二维码付款，付款后上传凭证</span>
            </div>
            <div class="tips-item">
                <span class="tips-icon">✓</span>
                <span>如当前二维码异常，可切换备用码付款</span>
            </div>
            <div class="tips-item">
                <span class="tips-icon">✓</span>
                <span>上传付款图后，大约1分钟自动到账余额</span>
            </div>
        </div>
    </div>
    
</div>

<!-- 结果弹窗 -->
<div class="msg-overlay" id="msgOverlay">
    <div class="msg-box">
        <div class="icon" id="msgIcon"></div>
        <div class="text" id="msgText"></div>
        <button onclick="closeMsg()">确定</button>
    </div>
</div>

<!-- 相册弹窗 -->
<div class="gallery-overlay" id="galleryOverlay">
    <div class="gallery-header">
        <span class="gallery-title">凭证示例</span>
        <button class="gallery-close" onclick="closeGallery();">&times;</button>
    </div>
    <div class="gallery-body">
        <div class="gallery-img-wrap">
            <img id="galleryImg" src="" alt="示例图片">
        </div>
    </div>
    <div class="gallery-nav">
        <button class="gallery-nav-btn" id="galleryPrev" onclick="prevImage();">&#10094;</button>
        <button class="gallery-nav-btn" id="galleryNext" onclick="nextImage();">&#10095;</button>
    </div>
    <div class="gallery-footer">
        <span class="gallery-counter" id="galleryCounter">1 / 2</span>
    </div>
</div>

<script>
var currentGroup = 1;
var currentPay = 'alipay';
var selectedFile = null;
var compressedBlob = null;
var selectedMoney = '';

var qrcodeData = <?php echo json_encode($groups); ?>;

// 页面加载时检查UID
(function() {
    var uid = document.getElementById('uid').value;
    if (!uid) {
        alert('缺少必要参数：uid');
    }
})();

// 组切换
document.querySelectorAll('.group-tab').forEach(function(tab) {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.group-tab').forEach(function(t) { t.classList.remove('active'); });
        this.classList.add('active');
        currentGroup = parseInt(this.dataset.group);
        updateQrcode();
    });
});

// 支付方式切换
document.querySelectorAll('.pay-tab').forEach(function(tab) {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.pay-tab').forEach(function(t) {
            t.classList.remove('active-alipay', 'active-wechat');
        });
        currentPay = this.dataset.pay;
        document.querySelectorAll('.pay-tab').forEach(function(t) {
            t.classList.remove('active-alipay', 'active-wechat');
            if (t.dataset.pay === currentPay) {
                t.classList.add(currentPay === 'alipay' ? 'active-alipay' : 'active-wechat');
            }
        });
        updateQrcode();
    });
});

function updateQrcode() {
    var src = qrcodeData[currentGroup][currentPay];
    document.getElementById('qrcodeImg').src = src;
}

// 金额选择
document.querySelectorAll('.money-opt').forEach(function(opt) {
    opt.addEventListener('click', function() {
        document.querySelectorAll('.money-opt').forEach(function(o) { o.classList.remove('selected'); });
        this.classList.add('selected');
        selectedMoney = this.dataset.money;
    });
});

// 上传区域
var uploadArea = document.getElementById('uploadArea');
var fileInput = document.getElementById('fileInput');
var previewImg = document.getElementById('previewImg');
var placeholder = document.getElementById('uploadPlaceholder');
var deleteBtn = document.getElementById('deleteBtn');
var compressInfo = document.getElementById('compressInfo');

uploadArea.addEventListener('click', function(e) {
    if (e.target === deleteBtn) return;
    fileInput.click();
});

fileInput.addEventListener('change', function() {
    if (this.files && this.files[0]) {
        var file = this.files[0];

        // 格式验证
        var allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (allowedTypes.indexOf(file.type) === -1) {
            showMsg('error', '仅支持 JPG/PNG/GIF/WEBP 格式的图片');
            this.value = '';
            return;
        }

        // 大小验证（最大10MB原始文件）
        if (file.size > 10 * 1024 * 1024) {
            showMsg('error', '图片不能超过10MB');
            this.value = '';
            return;
        }

        selectedFile = file;
        // JS压缩处理
        compressImage(file, function(blob, dataUrl) {
            compressedBlob = blob;
            previewImg.src = dataUrl;
            previewImg.style.display = 'block';
            placeholder.style.display = 'none';
            uploadArea.classList.add('has-file');

            // 显示压缩信息
            var originalSize = (file.size / 1024).toFixed(1);
            var compressedSize = (blob.size / 1024).toFixed(1);
            compressInfo.textContent = '已压缩: ' + originalSize + 'KB → ' + compressedSize + 'KB';
            compressInfo.style.display = 'block';
        });
    }
});

// 图片压缩函数（使用Canvas）
function compressImage(file, callback) {
    var reader = new FileReader();
    reader.onload = function(e) {
        var img = new Image();
        img.onload = function() {
            // GIF不压缩，保持原图
            if (file.type === 'image/gif') {
                callback(file, e.target.result);
                return;
            }

            var canvas = document.createElement('canvas');
            var ctx = canvas.getContext('2d');

            // 最大尺寸限制（宽/高不超过1280px）
            var maxWidth = 1280;
            var maxHeight = 1280;
            var width = img.width;
            var height = img.height;

            if (width > maxWidth || height > maxHeight) {
                var ratio = Math.min(maxWidth / width, maxHeight / height);
                width = Math.round(width * ratio);
                height = Math.round(height * ratio);
            }

            canvas.width = width;
            canvas.height = height;
            ctx.drawImage(img, 0, 0, width, height);

            // 压缩为JPEG，质量0.7
            canvas.toBlob(function(blob) {
                // 生成预览用的dataURL
                var previewUrl = canvas.toDataURL('image/jpeg', 0.7);
                callback(blob, previewUrl);
            }, 'image/jpeg', 0.7);
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

// 删除图片
deleteBtn.addEventListener('click', function(e) {
    e.stopPropagation();
    fileInput.value = '';
    selectedFile = null;
    compressedBlob = null;
    previewImg.src = '';
    previewImg.style.display = 'none';
    placeholder.style.display = '';
    uploadArea.classList.remove('has-file');
    compressInfo.style.display = 'none';
});

// 提交订单
function submitOrder() {
    var uid = document.getElementById('uid').value.trim();
    if (!uid) { showMsg('error', '缺少用户UID参数'); return; }
    if (!selectedMoney) { showMsg('error', '请选择充值金额'); return; }
    if (!compressedBlob) { showMsg('error', '请上传付款凭证'); return; }

    var btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="loading"></span>提交中...';

    var formData = new FormData();
    formData.append('uid', uid);
    formData.append('money', selectedMoney);
    formData.append('pay_type', currentPay === 'alipay' ? 1 : 2);
    formData.append('group_id', currentGroup);
    formData.append('voucher', compressedBlob, 'voucher.jpg');

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'api.php?action=submit', true);
    xhr.onload = function() {
        btn.disabled = false;
        btn.innerHTML = '提交凭证';
        try {
            var res = JSON.parse(xhr.responseText);
            if (res.status) {
                showMsg('success', '凭证提交成功，请等待审核');
                // 重置表单
                selectedMoney = '';
                document.querySelectorAll('.money-opt').forEach(function(o) { o.classList.remove('selected'); });
                compressedBlob = null;
                selectedFile = null;
                previewImg.style.display = 'none';
                placeholder.style.display = '';
                uploadArea.classList.remove('has-file');
                fileInput.value = '';
                compressInfo.style.display = 'none';
            } else {
                showMsg('error', res.msg || '提交失败');
            }
        } catch(e) {
            showMsg('error', '服务器响应异常');
        }
    };
    xhr.onerror = function() {
        btn.disabled = false;
        btn.innerHTML = '提交凭证';
        showMsg('error', '网络错误，请重试');
    };
    xhr.send(formData);
}

function showMsg(type, text) {
    document.getElementById('msgIcon').textContent = type === 'success' ? '\u2705' : '\u274C';
    document.getElementById('msgText').textContent = text;
    document.getElementById('msgOverlay').classList.add('show');
}

function closeMsg() {
    document.getElementById('msgOverlay').classList.remove('show');
}

// 返回上一页
function goBack() {
    if (document.referrer) {
        window.history.back();
    } else {
        window.location.href = '/';
    }
}

// 相册功能
var galleryImages = ['alishow.jpg', 'wxshow.jpg'];
var galleryIndex = 0;

function opens() {
    galleryIndex = 0;
    updateGalleryImage();
    document.getElementById('galleryOverlay').classList.add('show');
}

function closeGallery() {
    document.getElementById('galleryOverlay').classList.remove('show');
}

function prevImage() {
    if (galleryIndex > 0) {
        galleryIndex--;
        updateGalleryImage();
    }
}

function nextImage() {
    if (galleryIndex < galleryImages.length - 1) {
        galleryIndex++;
        updateGalleryImage();
    }
}

function updateGalleryImage() {
    document.getElementById('galleryImg').src = galleryImages[galleryIndex];
    document.getElementById('galleryCounter').textContent = (galleryIndex + 1) + ' / ' + galleryImages.length;
    document.getElementById('galleryPrev').disabled = (galleryIndex === 0);
    document.getElementById('galleryNext').disabled = (galleryIndex === galleryImages.length - 1);
}

// 点击遮罩关闭相册
document.getElementById('galleryOverlay').addEventListener('click', function(e) {
    if (e.target === this) {
        closeGallery();
    }
});
</script>
</body>
</html>
