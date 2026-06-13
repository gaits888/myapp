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

$rnd=mt_rand(10,19).'.'.mt_rand(100000,999999);
$share="https://ndd.worthcloud.tv/qq/?t=".$rnd;
$promotionLink = $share . '&fid='.$user_code;

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
<!--<link rel="stylesheet" href="/css/promotion.css?t=123">-->
<style>
 body {
            padding-top: 50px;
            padding-bottom: 80px;
        }

        .page-container {
            max-width: 600px;
            margin: 0 auto;
            padding: var(--spacing-md);
        }

        /* 素材展示区 */
        .material-card {
            background: var(--bg-white);
            border-radius: var(--radius-lg);
            padding: var(--spacing-md);
            box-shadow: var(--shadow-md);
            margin-bottom: var(--spacing-md);
        }

        .material-title {
            font-size: var(--font-lg);
            font-weight: var(--font-semibold);
            color: var(--text-primary);
            margin-bottom: var(--spacing-md);
        }

        /* 图片容器 */
        .image-container {
            position: relative;
            width: 100%;
            height: auto; /* 改为自动高度以适应竖图 */
            background: var(--bg-light);
            border-radius: var(--radius-lg);
            overflow: hidden;
            margin-bottom: var(--spacing-md);
        }

        #materialCanvas {
            width: 100%;
            height: auto; /* 改为自动高度 */
            display: block; /* 确保canvas正确显示 */
        }

        /* 导航按钮 */
        .nav-buttons {
            display: flex;
            margin-bottom: var(--spacing-md);
        }

        .nav-btn {
            flex: 1;
            padding: var(--spacing-md);
            background: var(--bg-light);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            color: var(--text-primary);
            font-size: var(--font-base);
            font-weight: var(--font-medium);
            cursor: pointer;
            transition: all var(--transition-base);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--spacing-sm);
        }

        .nav-btn:active {
            transform: scale(0.98);
            background: var(--bg-pink-light);
        }

        .nav-btn svg {
            width: 18px;
            height: 18px;
        }

        /* 保存按钮 */
        .save-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(to right, rgb(248 164 10 / 68%), #ff8fb3);
            border: none;
            border-radius: var(--radius-lg);
            color: var(--white);
            font-size: var(--font-lg);
            font-weight: var(--font-semibold);
            cursor: pointer;
            transition: all var(--transition-base);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--spacing-sm);
        }

        .save-btn:active {
            transform: scale(0.98);
            opacity: 0.9;
        }

        .save-btn svg {
            width: 20px;
            height: 20px;
        }

        /* 提示信息 */
        .tips-card {
            background: var(--bg-gradient-light);
            border-radius: var(--radius-lg);
            padding: var(--spacing-md);
            margin-bottom: var(--spacing-md);
        }

        .tips-title {
            font-size: var(--font-base);
            font-weight: var(--font-semibold);
            color: var(--primary-dark);
            margin-bottom: var(--spacing-md);
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
        }

        .tips-title svg {
            width: 20px;
            height: 20px;
        }

        .tips-text {
            font-size: var(--font-sm);
            color: var(--text-secondary);
            line-height: var(--line-height-relaxed);
        }

        /* 页码指示器 */
        .page-indicator {
            display: none;
            text-align: center;
            color: var(--text-secondary);
            font-size: var(--font-sm);
            margin-bottom: var(--spacing-md);
        }

        /* 长按提示 */
        .long-press-hint {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: var(--spacing-md) var(--spacing-md);
            border-radius: var(--radius-lg);
            font-size: var(--font-lg);
            pointer-events: none;
            opacity: 0;
            transition: opacity var(--transition-base);
            z-index: 9999;
        }

        .long-press-hint.show {
            opacity: 1;
        }

        /* 长按进度指示器 */
        .long-press-progress {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: var(--radius-lg);
            border: 4px solid var(--primary-color);
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .long-press-progress.active {
            opacity: 1;
            animation: pulse 2s ease-in-out;
        }

        @keyframes pulse {
            0%, 100% { 
                border-color: var(--primary-color);
                box-shadow: 0 0 0 0 rgba(236, 72, 153, 0.4);
            }
            50% { 
                border-color: var(--primary-dark);
                box-shadow: 0 0 0 10px rgba(236, 72, 153, 0);
            }
        }

</style>
</head>
<body>
    <?php 
    $pageTitle = "推广素材";
    include 'comm/header.php'; 
    ?>

    <div class="page-container">
        <div class="material-card">
            <!-- <h2 class="material-title">推广海报</h2> -->
            
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
                <button class="nav-btn" onclick="previousImage();">
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
            <button class="save-btn" onclick="saveImage()" style="margin-left:10px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                保存推广图片
            </button>
        </div>

        <!-- 提示信息 -->
        <!--
        <div class="tips-card">
            <h4 class="tips-title">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M12 16v-4" stroke="white" stroke-width="2"></path>
                    <path d="M12 8h.01" stroke="white" stroke-width="2"></path>
                </svg>
                使用说明
            </h4>
            <p class="tips-text">
                点击保存按钮将带有您专属推广二维码的海报保存到相册，分享给好友扫码注册即可获得推广收益。每张海报右下角都自动生成了您的专属推广二维码。
            </p>
        </div>
        -->
    </div>

    <?php include 'comm/footer.php'; ?>

    <script src="/js/qrcode.min.js"></script>
    <script>
        const promotionUrl = '<?php echo $promotionLink;?>';
        
        // 素材图片列表
        const images = [
            '/images/tu/1.jpg',
            '/images/tu/2.jpg',
            '/images/tu/3.jpg',
            '/images/tu/4.jpg',
            '/images/tu/5.jpg',
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
            img.crossOrigin = 'anonymous';
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
            // 创建临时div用于生成二维码
            const tempDiv = document.createElement('div');
            tempDiv.style.display = 'none';
            document.body.appendChild(tempDiv);
            
            try {
                // 使用QRCode库生成二维码
                const qr = new QRCode(tempDiv, {
                    text: promotionUrl,
                    width: 150,
                    height: 150,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
                
                // 等待二维码生成完成
                setTimeout(() => {
                    const qrImg = tempDiv.querySelector('img');
                    if (qrImg && qrImg.complete) {
                        drawQRCodeOnCanvas(qrImg);
                    } else if (qrImg) {
                        qrImg.onload = function() {
                            drawQRCodeOnCanvas(qrImg);
                        };
                    } else {
                        console.error('[v0] 二维码图片生成失败');
                    }
                    
                    // 清理临时元素
                    document.body.removeChild(tempDiv);
                }, 300);
                
            } catch (error) {
                console.error('[v0] 二维码生成异常:', error);
                document.body.removeChild(tempDiv);
            }
        }
        
        function drawQRCodeOnCanvas(qrImg) {
            const qrSize = 150;
            const padding = 30;
            const bgPadding = 15;
            const x = canvas.width - qrSize - padding;
            const y = canvas.height - qrSize - padding - 40; // 为文字留出空间
            
            // 绘制白色圆角背景
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
            ctx.fillText('扫码注册', x + qrSize / 2, y + qrSize + 30);
            
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
        
        // 保存图片
        function saveImage() {
            try {
                // 将canvas转换为图片并下载
                const link = document.createElement('a');
                link.download = `推广海报_${currentIndex + 1}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();
                
                // 使用alert_modal提供的showAlert函数
                showAlert({
                    title: '保存成功',
                    message: '图片已保存到相册',
                    type: 'success'
                });
            } catch (err) {
                console.error('保存失败:', err);
                showAlert({
                    title: '保存失败',
                    message: '保存失败，请重试',
                    type: 'error'
                });
            }
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
    
<?php include 'comm/alert_modal.php'; ?>
    
</body>
</html>
