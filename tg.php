<?php
include_once 'loaduser.php';
include_once 'config.php';

$base_url_config = db('fl_rk_url')
            ->where(['type' => 1])
            ->find();
$base_url = $base_url_config['url'] ?? '';

$user_code_arr = db('userb')
            ->where(['id' => $user_id])
            ->field('user_code')
            ->find();
$user_code = $user_code_arr['user_code'] ?? '';

$promotionLink = $base_url . '?inviteCode='.$user_code;

$pageTitle = "推广素材";
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="author" content="<?php echo $webname; ?>" />
<meta name="keywords" content="推广素材_<?php echo $webname; ?>">
<meta name="description" content="推广素材_<?php echo $webname; ?>">
<title>推广素材_<?php echo $webname; ?></title>
<link rel="stylesheet" href="/css/comm.css">
<link rel="stylesheet" href="/css/promotion.css?t=<?php echo time(); ?>">
</head>
<body>
<?php  include 'comm/header.php';  ?>

<div class="page-container">
        <div class="material-card">
            
            <!-- 页码指示器 -->
            <div class="page-indicator">
                <span id="currentPage">1</span> / <span id="totalPages">4</span>
            </div>
            
            <!-- 图片容器 -->
            <div class="image-container">
                <canvas id="materialCanvas"></canvas>
                <!-- 长按进度指示器 -->
                <div class="long-press-progress" id="longPressProgress"></div>
            </div>

            <!-- 导航按钮 -->
            <div class="nav-buttons">
                <button class="nav-btn" onclick="previousImage()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                    上一张
                </button>
                <button class="nav-btn" onclick="nextImage()">
                    下一张
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
            </div>

            <!-- 保存按钮 -->
            <button class="save-btn" onclick="saveImage()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                保存图片
            </button>
        </div>

        <!-- 提示信息 -->
        <div class="tips-card">
            <h4 class="tips-title">
                使用说明
            </h4>
            <p class="tips-text">
                点击保存按钮将带有您专属推广二维码的海报保存到相册，分享给好友扫码注册即可获得推广收益。每张海报右下角都自动生成了您的专属推广二维码。
            </p>
        </div>
    </div>


<script src="/js/qrcode.min.js"></script>
<script>
        const promotionUrl = '<?php echo $promotionLink;?>';
        
        // 素材图片列表
        const images = [
            '/images/tg/1.png',
            '/images/tg/2.png',
            '/images/tg/3.png',
            '/images/tg/4.png',
            '/images/tg/5.png',
            '/images/tg/6.png',
        ];
        
        let currentIndex = 0;
        const canvas = document.getElementById('materialCanvas');
        const ctx = canvas.getContext('2d');
        
        // 长按相关变量
        let touchStartTime = 0;
        let longPressTimer = null;
        let isLongPress = false;

        // 初始化
        function init() {
            updatePageIndicator();
            loadImage();
            initLongPressEvents();
        }
        
        function loadImage() {
            const img = new Image();
            // 图片为同源资源，不设置 crossOrigin，避免低版本安卓画布污染/加载失败
            img.onload = function() {
                // 使用9:16的竖图比例（手机拍摄图片比例）
                const targetWidth = 800;
                const targetHeight = Math.floor(targetWidth * 16 / 9); // 1422px
                
                canvas.width = targetWidth;
                canvas.height = targetHeight;
                
                // 绘制背景图片（拉伸填充整个canvas）
                ctx.drawImage(img, 0, 0, targetWidth, targetHeight);
                
                // 生成并绘制二维码
                generateAndDrawQRCode();
            };
            img.onerror = function() {
                console.error('[v0] 图片加载失败:', images[currentIndex]);
                canvas.width = 800;
                canvas.height = 1422; // 9:16比例
                ctx.fillStyle = '#f5f5f5';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.fillStyle = '#999';
                ctx.font = '24px Arial';
                ctx.textAlign = 'center';
                ctx.fillText('图片加载失败', canvas.width/2, canvas.height/2);
                
                // 即使图片加载失败也显示二维码
                generateAndDrawQRCode();
            };
            img.src = images[currentIndex];
        }
        
        function generateAndDrawQRCode() {
            // 创建临时div用于生成二维码（用屏幕外定位代替 display:none，避免部分浏览器不渲染）
            const tempDiv = document.createElement('div');
            tempDiv.style.position = 'absolute';
            tempDiv.style.left = '-9999px';
            tempDiv.style.top = '0';
            document.body.appendChild(tempDiv);

            function cleanup() {
                if (tempDiv.parentNode) {
                    document.body.removeChild(tempDiv);
                }
            }

            try {
                // 使用QRCode库生成二维码
                new QRCode(tempDiv, {
                    text: promotionUrl,
                    width: 150,
                    height: 150,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });

                // 低版本安卓上 QRCode 可能输出 <canvas> 而非 <img>，需轮询兼容两种情况
                var tries = 0;
                var timer = setInterval(function() {
                    tries++;
                    var qrCanvas = tempDiv.querySelector('canvas');
                    var qrImg = tempDiv.querySelector('img');

                    if (qrCanvas) {
                        // canvas 同步绘制，可直接使用
                        clearInterval(timer);
                        drawQRCodeOnCanvas(qrCanvas);
                        cleanup();
                    } else if (qrImg && qrImg.complete && qrImg.naturalWidth > 0) {
                        clearInterval(timer);
                        drawQRCodeOnCanvas(qrImg);
                        cleanup();
                    } else if (tries >= 30) {
                        // 超过约3秒仍未生成，放弃
                        clearInterval(timer);
                        console.error('[v0] 二维码生成超时');
                        cleanup();
                    }
                }, 100);

            } catch (error) {
                console.error('[v0] 二维码生成异常:', error);
                cleanup();
            }
        }
        
        function drawQRCodeOnCanvas(qrImg) {
            const qrSize = 150;
            const padding = 30;
            const bgPadding = 15;
            const x = canvas.width - qrSize - padding;
            const y = canvas.height - qrSize - padding - 40; // 为文字留出空间
            
            // 绘制白色��角背景
            ctx.fillStyle = 'white';
            ctx.shadowColor = 'rgba(0, 0, 0, 0.2)';
            ctx.shadowBlur = 15;
            ctx.shadowOffsetX = 0;
            ctx.shadowOffsetY = 3;
            
            const bgX = x - bgPadding;
            const bgY = y - bgPadding;
            const bgWidth = qrSize + bgPadding * 2;
            const bgHeight = qrSize + bgPadding * 2 + 40;
            const radius = 15;
            
            ctx.beginPath();
            ctx.moveTo(bgX + radius, bgY);
            ctx.lineTo(bgX + bgWidth - radius, bgY);
            ctx.quadraticCurveTo(bgX + bgWidth, bgY, bgX + bgWidth, bgY + radius);
            ctx.lineTo(bgX + bgWidth, bgY + bgHeight - radius);
            ctx.quadraticCurveTo(bgX + bgWidth, bgY + bgHeight, bgX + bgWidth - radius, bgY + bgHeight);
            ctx.lineTo(bgX + radius, bgY + bgHeight);
            ctx.quadraticCurveTo(bgX, bgY + bgHeight, bgX, bgY + bgHeight - radius);
            ctx.lineTo(bgX, bgY + radius);
            ctx.quadraticCurveTo(bgX, bgY, bgX + radius, bgY);
            ctx.closePath();
            ctx.fill();
            
            // 重置阴影
            ctx.shadowColor = 'transparent';
            ctx.shadowBlur = 0;
            ctx.shadowOffsetX = 0;
            ctx.shadowOffsetY = 0;
            
            // 绘制二维码图片
            ctx.drawImage(qrImg, x, y, qrSize, qrSize);
            
            // 在二维码下方添加提示文字
            ctx.fillStyle = '#333';
            ctx.font = 'bold 16px Arial';
            ctx.textAlign = 'center';
            ctx.fillText('扫一扫 立即约', x + qrSize / 2, y + qrSize + 30);
            
            console.log('[v0] 二维码绘制完成');
        }
        
        // 上一张
        function previousImage() {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            updatePageIndicator();
            loadImage();
        }
        
        // 下一张
        function nextImage() {
            currentIndex = (currentIndex + 1) % images.length;
            updatePageIndicator();
            loadImage();
        }
        
        // 更新页码指示器
        function updatePageIndicator() {
            document.getElementById('currentPage').textContent = currentIndex + 1;
            document.getElementById('totalPages').textContent = images.length;
        }
        
        // 保存图片：在 WebView/低版本安卓里，JS 无法直接写入相册，
        // 因此将海报以 <img> 形式展示在全屏遮罩层，提示用户长按保存。
        // （<img> 在 WebView 中支持长按"保存图片"，而 <canvas> 不支持）
        function saveImage() {
            try {
                var dataUrl = canvas.toDataURL('image/png');
                showSaveOverlay(dataUrl);
            } catch (err) {
                console.error('[v0] 生成图片失败:', err);
                showAlert({
                    title: '操作失败',
                    message: '图片生成失败，请重试',
                    type: 'error'
                });
            }
        }

        // 展示全屏遮罩层，让用户长按图片保存
        function showSaveOverlay(dataUrl) {
            // 已存在则先移除，避免重复叠加
            var old = document.getElementById('saveImageOverlay');
            if (old && old.parentNode) {
                old.parentNode.removeChild(old);
            }

            var overlay = document.createElement('div');
            overlay.id = 'saveImageOverlay';
            overlay.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.92);z-index:99999;overflow:auto;-webkit-overflow-scrolling:touch;text-align:center;';

            // 关闭按钮（右上角圆形 X，方便用户操作）
            var closeBtn = document.createElement('div');
            closeBtn.innerHTML = '&times;';
            closeBtn.style.cssText = 'position:fixed;top:16px;right:16px;width:40px;height:40px;line-height:38px;text-align:center;font-size:28px;color:#fff;background:rgba(0,0,0,0.5);border:1px solid rgba(255,255,255,0.6);border-radius:50%;z-index:100000;cursor:pointer;';
            closeBtn.addEventListener('click', function() {
                if (overlay.parentNode) {
                    overlay.parentNode.removeChild(overlay);
                }
            });

            var tip = document.createElement('div');
            tip.style.cssText = 'color:#fff;font-size:15px;line-height:1.6;padding:16px 16px 8px 16px;';
            tip.innerHTML = '长按下方图片，选择"<b>保存图片</b>"到相册';

            var posterImg = document.createElement('img');
            posterImg.src = dataUrl;
            posterImg.style.cssText = 'display:block;width:90%;max-width:360px;margin:8px auto 24px auto;border-radius:8px;';

            overlay.appendChild(closeBtn);
            overlay.appendChild(tip);
            overlay.appendChild(posterImg);

            // 点击遮罩空白处关闭（点图片或关闭按钮不触发）
            overlay.addEventListener('click', function(e) {
                if (e.target !== posterImg && e.target !== closeBtn) {
                    overlay.parentNode.removeChild(overlay);
                }
            });

            document.body.appendChild(overlay);
        }
        
        // 初始化长按事件
        function initLongPressEvents() {
            const imageContainer = document.querySelector('.image-container');
            const progressIndicator = document.getElementById('longPressProgress');
            
            // 触摸开始
            imageContainer.addEventListener('touchstart', (e) => {
                touchStartTime = Date.now();
                isLongPress = false;
                
                // 显示进度指示器
                progressIndicator.classList.add('active');
                
                // 设置2秒定时器
                longPressTimer = setTimeout(() => {
                    isLongPress = true;
                    showConfirmDialog();
                    try {
                        if (navigator.vibrate) {
                            navigator.vibrate(50);
                        }
                    } catch (error) {
                        // 忽略震动功能的错误
                        console.log('[v0] 震动功能不可用');
                    }
                }, 2000);
            });
            
            // 触摸结束
            imageContainer.addEventListener('touchend', (e) => {
                clearTimeout(longPressTimer);
                progressIndicator.classList.remove('active');
                
                const touchDuration = Date.now() - touchStartTime;
                
                // 如果不是长按且触摸时间很短，可能是点击
                if (!isLongPress && touchDuration < 200) {
                    // 短按可以用于其他功能，这里留空
                }
            });
            
            // 触摸取消（比如滑动离开）
            imageContainer.addEventListener('touchcancel', (e) => {
                clearTimeout(longPressTimer);
                progressIndicator.classList.remove('active');
            });
            
            // 触摸移动（如果移动太多则取消长按）
            let startX = 0, startY = 0;
            imageContainer.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
                startY = e.touches[0].clientY;
            });
            
            imageContainer.addEventListener('touchmove', (e) => {
                const moveX = Math.abs(e.touches[0].clientX - startX);
                const moveY = Math.abs(e.touches[0].clientY - startY);
                
                // 如果移动超过20px，取消长按
                if (moveX > 20 || moveY > 20) {
                    clearTimeout(longPressTimer);
                    progressIndicator.classList.remove('active');
                }
            });
        }
        
        // 显示确认对话框
        function showConfirmDialog() {
            showConfirm({
                title: '保存图片',
                message: '确定保存推广图片吗？',
                type: 'info',
                confirmText: '确定',
                cancelText: '取消',
                onConfirm: function() {
                    // 用户点击确定后保存图片
                    saveImage();
                }
            });
        }
        
        // 页面加载完成后初始化
        window.onload = init;
    </script>
    

<?php include 'comm/footer.php'; ?>
<?php include 'comm/alert_modal.php'; ?>
    
</body>
</html>
