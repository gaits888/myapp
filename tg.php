<?php 
include_once 'loaduser.php';
$userId = $userData['userId'];
if ($userId<=0) {
    
    echo "<script >alert('请先登录！');</script>";
        echo "<script > window.location.href = 'login.html';</script>";
        die;
}

$rk_url_arr = $db->table('fl_rk_url')
            ->where(['type' => 1])
            ->find();
$rk_url_config = $rk_url_arr['url'] ?? '';

$user_infos =$userInfo = $db->table('userb')
    ->where(['id' => $userId])
    ->field('id, user_code')
    ->find();
$user_inviteCode = $user_infos['user_code'];

//跳转域名
//$rk_url_config='';
$rk_url = $rk_url_config.'?inviteCode='.$user_inviteCode;

// var_dump($rk_url);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>推广素材</title>
    <meta name="keywords" content="<?php echo $currentPageArr['keywords'];?>">
    <meta name="description" content="<?php echo $currentPageArr['description'];?>">
    <link href="css/header.css" type="text/css" rel="stylesheet" media="all" />
    <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "SF Pro Text", "Segoe UI", Roboto, "Helvetica Neue", Arial, "PingFang SC", "Hiragino Sans GB", "Microsoft YaHei UI", "Microsoft YaHei", sans-serif;
            background: linear-gradient(135deg, #fafafa 0%, #fff 100%);
            min-height: 100vh;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .header {
            background: linear-gradient(135deg, #ff6b9d 0%, #ffa8c5 100%);
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: white;
            box-shadow: 0 2px 12px rgba(255, 107, 157, 0.2);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .back-btn {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border-radius: 50%;
            transition: all 0.25s;
            background: rgba(255, 255, 255, 0.2);
            margin-left: 10px;
        }

        .back-btn:active {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(0.96);
        }

        .back-btn svg {
            width: 24px;
            height: 24px;
        }

        .header-title {
            font-size: 17px;
            font-weight: 600;
            flex: 1;
            text-align: center;
            letter-spacing: 1px;
        }

        .header-spacer {
            width: 32px;
        }

        .promo-container {
            padding: 10px;
            max-width: 640px;
            margin: 0 auto;
        }

        .promo-header {
            text-align: center;
            margin-top: 15px;
            margin-bottom: 15px;
        }

        .promo-title {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, #ff6b9d 0%, #ff8fb3 50%, #ffb347 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }

        .promo-subtitle {
            font-size: 14px;
            color: #7f8c8d;
            line-height: 1.6;
        }

        .promo-info {
            background: linear-gradient(135deg, #fff0f6 0%, #ffe7f3 100%);
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 10px;
            box-shadow: 0 4px 12px rgba(255, 107, 157, 0.1);
        }

        .promo-info-title {
            font-size: 16px;
            font-weight: 600;
            color: #ff6b9d;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .promo-info-title svg {
            width: 20px;
            height: 20px;
            fill: #ff6b9d;
        }

        .promo-info-list {
            list-style: none;
        }

        .promo-info-list li {
            font-size: 14px;
            color: #2c3e50;
            line-height: 1.8;
            padding-left: 20px;
            position: relative;
        }

        .promo-info-list li::before {
            content: '●';
            position: absolute;
            left: 0;
            color: #ff8fb3;
            font-size: 12px;
        }

        .poster-carousel {
            position: relative;
            margin-bottom: 10px;
        }

        .poster-wrapper {
            position: relative;
            width: 100%;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            background: white;
            touch-action: pan-y;
        }

        .poster-container {
            display: flex;
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            background: linear-gradient(135deg, #fafafa 0%, #fff 100%);
        }

        .poster-slide {
            min-width: 100%;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
        }

        .poster-image {
            width: 100%;
            height: auto; 
            object-fit: contain;
            display: block;
            user-select: none;
            -webkit-user-drag: none;
            pointer-events: none;
        }

        .qr-overlay {
            position: absolute;
            bottom: 10px;
            right: 10px;
            width: 100px;
            height: 100px;
            background: white;
            border-radius: 10px;
            padding: 8px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #ff6b9d;
        }

        .qr-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #ff6b9d20 0%, #ffa8c520 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: #ff6b9d;
            font-weight: 600;
            text-align: center;
        }

        .carousel-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 10;
        }

        .carousel-nav:active {
            transform: translateY(-50%) scale(0.9);
            background: white;
        }

        .carousel-nav.prev {
            left: 10px;
        }

        .carousel-nav.next {
            right: 10px;
        }

        .carousel-nav svg {
            width: 24px;
            height: 24px;
            fill: #ff6b9d;
        }

        .carousel-dots {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #e0e0e0;
            transition: all 0.3s;
            cursor: pointer;
            margin-right: 10px;
        }

        .dot.active {
            background: linear-gradient(135deg, #ff6b9d 0%, #ff8fb3 100%);
            width: 24px;
            border-radius: 4px;
        }

        .action-tips {
            text-align: center;
            padding: 10px;
            background: white;
            border-radius: 12px;
            margin-bottom: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .action-tips-text {
            font-size: 13px;
            color: #7f8c8d;
            line-height: 1.6;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .action-tips-text svg {
            width: 18px;
            height: 18px;
            fill: #ff6b9d;
            margin-right: 5px;
        }

        .promo-steps {
            background: white;
            border-radius: 10px;
            padding: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            margin-bottom: 20px;
        }

        .steps-title {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
            text-align: left;
        }

        .step-item {
            display: flex;
            margin-bottom: 10px;
            align-items: flex-start;
        }

        .step-item:last-child {
            margin-bottom: 0;
        }

        .step-number {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff6b9d 0%, #ff8fb3 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            flex-shrink: 0;
            box-shadow: 0 4px 8px rgba(255, 107, 157, 0.3);
            margin-top:10px;
            margin-right: 10px;
        }

        .step-content {
            flex: 1;
            padding-top: 4px;
        }

        .step-content h4 {
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .step-content p {
            font-size: 13px;
            color: #7f8c8d;
            line-height: 1.6;
        }

        .save-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .save-modal.show {
            display: flex;
            opacity: 1;
        }

        .save-modal-content {
            background: white;
            border-radius: 20px;
            padding: 32px 24px 24px;
            width: 90%;
            max-width: 360px;
            text-align: center;
            transform: scale(0.9);
            transition: transform 0.3s;
        }

        .save-modal.show .save-modal-content {
            transform: scale(1);
        }

        .save-modal-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 16px;
            background: linear-gradient(135deg, #ff6b9d20 0%, #ffa8c520 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .save-modal-icon svg {
            width: 36px;
            height: 36px;
            fill: #ff6b9d;
        }

        .save-modal-title {
            font-size: 20px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 12px;
        }

        .save-modal-text {
            font-size: 14px;
            color: #7f8c8d;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .save-modal-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #ff6b9d 0%, #ff8fb3 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(255, 107, 157, 0.3);
        }

        .save-modal-btn:active {
            transform: scale(0.96);
            box-shadow: 0 2px 8px rgba(255, 107, 157, 0.2);
        }

        @media (min-width: 769px) {
            .promo-container {
                padding: 32px 24px;
            }
        }
    </style>
</head>
<body>
    <!-- 头部 -->
    <style type="text/css">
        .back-btn {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border-radius: 50%;
            transition: all 0.25s;
            background: rgba(255, 255, 255, 0.2);
        }

        .back-btn:active {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(0.96);
        }

        .back-btn svg {
            width: 24px;
            height: 24px;
        }
    </style>
    <header class="header">
        <div class="back-btn" onclick="window.history.back();">
            <svg fill="currentColor" viewBox="0 0 24 24">
                <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
            </svg>
        </div>
        <h1 class="header-title">推广素材</h1>
        <div class="header-spacer"></div>
    </header>

    <!-- 主要内容 -->
    <div class="promo-container">
        <!-- 推广说明 -->
        <div class="promo-header">
            <h2 class="promo-title">邀请好友 躺着赚钱</h2>
            <p class="promo-subtitle">分享海报给好友，好友注册即可获得丰厚奖励</p>
        </div>

        <!-- 推广信息 -->
        <div class="promo-info">
            <div class="promo-info-title">
                推广奖励
            </div>
            <ul class="promo-info-list">
                <li>好友注册成功，您可获得 <strong style="color: #ff6b9d;">5积分</strong> 奖励</li>
                <li>好友发布信息，您可获得 <strong style="color: #ff6b9d;">10积分</strong> 奖励</li>
                <li>好友购买VIP会员，您可获得 <strong style="color: #ff6b9d;">40%</strong> 分润</li>
              <!--  <li>累计推广满10人，升级为 <strong style="color: #ff6b9d;">推广大使</strong></li>  -->
            </ul>
        </div>

        <!-- 海报轮播 -->
        <div class="poster-carousel">
            <div class="poster-wrapper" id="posterWrapper">
                <div class="poster-container" id="posterContainer">
                    <div class="poster-slide">
                        <img src="/images/tg/1.jpg" alt="推广海报1" class="poster-image">
                        <div class="qr-overlay">
                            <div class="qr-placeholder">扫码<br>注册</div>
                        </div>
                    </div>
                    <div class="poster-slide">
                        <img src="/images/tg/2.jpg" alt="推广海报2" class="poster-image">
                        <div class="qr-overlay">
                            <div class="qr-placeholder">扫码<br>注册</div>
                        </div>
                    </div>
                    <div class="poster-slide">
                        <img src="/images/tg/3.jpg" alt="推广海报3" class="poster-image">
                        <div class="qr-overlay">
                            <div class="qr-placeholder">扫码<br>注册</div>
                        </div>
                    </div>
                    <div class="poster-slide">
                        <img src="/images/tg/4.jpg" alt="推广海报4" class="poster-image">
                        <div class="qr-overlay">
                            <div class="qr-placeholder">扫码<br>注册</div>
                        </div>
                    </div>
                    <div class="poster-slide">
                        <img src="/images/tg/5.jpg" alt="推广海报5" class="poster-image">
                        <div class="qr-overlay">
                            <div class="qr-placeholder">扫码<br>注册</div>
                        </div>
                    </div>
                </div>
                <div class="carousel-nav prev" onclick="prevSlide()">
                    <svg viewBox="0 0 24 24">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                    </svg>
                </div>
                <div class="carousel-nav next" onclick="nextSlide()">
                    <svg viewBox="0 0 24 24">
                        <path d="M8.59 16.59L10 18l6-6-6-6-1.41 1.41L13.17 12z"/>
                    </svg>
                </div>
            </div>
            <div class="carousel-dots" id="carouselDots"></div>
        </div>

        <!-- 操作提示 -->
        <div class="action-tips">
            <div class="action-tips-text">
                <svg viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
                长按上面的海报，可保存图片分享给好友.
            </div>
        </div>

        <!-- 推广步骤 -->
        <div class="promo-steps">
            <h3 class="steps-title">推广步骤：</h3>
            <div class="step-item">
                <div class="step-number">1</div>
                <div class="step-content">
                    <h4>选择海报</h4>
                    <p>左右滑动选择您喜欢的推广海报样式.</p>
                </div>
            </div>
            <div class="step-item">
                <div class="step-number">2</div>
                <div class="step-content">
                    <h4>保存分享</h4>
                    <p>长按海报保存到相册，分享给微信好友或朋友圈.</p>
                </div>
            </div>
            <div class="step-item">
                <div class="step-number">3</div>
                <div class="step-content">
                    <h4>获得奖励</h4>
                    <p>好友扫码注册成功后，您即可获得推广奖励.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 保存提示弹窗 -->
    <div class="save-modal" id="saveModal">
        <div class="save-modal-content">
            <div class="save-modal-icon">
                <svg viewBox="0 0 24 24">
                    <path d="M19 12v7H5v-7H3v7c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-7h-2zm-6 .67l2.59-2.58L17 11.5l-5 5-5-5 1.41-1.41L11 12.67V3h2v9.67z"/>
                </svg>
            </div>
            <h3 class="save-modal-title">长按保存海报</h3>
            <p class="save-modal-text">请长按海报图片，在弹出菜单中选择"保存图片"，即可将推广海报保存到相册</p>
            <button class="save-modal-btn" onclick="closeSaveModal()">我知道了</button>
        </div>
    </div>

    <script>
        let currentSlide = 0;
        const totalSlides = 5;
        let touchStartX = 0;
        let touchEndX = 0;
        let longPressTimer = null;

        // 初始化轮播点
        function initDots() {
            const dotsContainer = document.getElementById('carouselDots');
            for (let i = 0; i < totalSlides; i++) {
                const dot = document.createElement('div');
                dot.className = 'dot' + (i === 0 ? ' active' : '');
                dot.onclick = () => goToSlide(i);
                dotsContainer.appendChild(dot);
            }
        }

        // 更新轮播显示
        function updateCarousel() {
            const container = document.getElementById('posterContainer');
            container.style.transform = `translateX(-${currentSlide * 100}%)`;
            
            // 更新点指示器
            document.querySelectorAll('.dot').forEach((dot, index) => {
                dot.classList.toggle('active', index === currentSlide);
            });
        }

        // 上一张
        function prevSlide() {
            if (window.slideChanging) return;
            window.slideChanging = true;
            
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            updateCarousel();
            
            setTimeout(() => {
                window.slideChanging = false;
            }, 300);
        }

        // 下一张
        function nextSlide() {
            if (window.slideChanging) return;
            window.slideChanging = true;
            
            currentSlide = (currentSlide + 1) % totalSlides;
            updateCarousel();
            
            setTimeout(() => {
                window.slideChanging = false;
            }, 300);
        }

        // 跳转到指定slide
        function goToSlide(index) {
            currentSlide = index;
            updateCarousel();
        }

        // 触摸事件处理
        const posterWrapper = document.getElementById('posterWrapper');

        posterWrapper.addEventListener('touchstart', (e) => {
            touchStartX = e.touches[0].clientX;
            
            // 长按检测
            longPressTimer = setTimeout(() => {
                saveMergedImage(currentSlide);
            }, 800);
        });

        posterWrapper.addEventListener('touchmove', (e) => {
            // 移动时取消长按
            if (longPressTimer) {
                clearTimeout(longPressTimer);
                longPressTimer = null;
            }
        });

        posterWrapper.addEventListener('touchend', (e) => {
            // 清除长按计时器
            if (longPressTimer) {
                clearTimeout(longPressTimer);
                longPressTimer = null;
            }

            touchEndX = e.changedTouches[0].clientX;
            handleSwipe();
        });

        // 处理滑动
        function handleSwipe() {
            const swipeThreshold = 50;
            const diff = touchStartX - touchEndX;

            if (Math.abs(diff) > swipeThreshold) {
                if (diff > 0) {
                    nextSlide();
                } else {
                    prevSlide();
                }
            }
        }

        // 点击图片左右区域切换
        posterWrapper.addEventListener('click', (e) => {
            const rect = posterWrapper.getBoundingClientRect();
            const clickX = e.clientX - rect.left;
            const width = rect.width;

            if (clickX < width / 3) {
                prevSlide();
            } else if (clickX > width * 2 / 3) {
                nextSlide();
            }
        });

        // 显示保存提示弹窗
        function showSaveModal() {
            document.getElementById('saveModal').classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        // 关闭保存提示弹窗
        function closeSaveModal() {
            document.getElementById('saveModal').classList.remove('show');
            document.body.style.overflow = '';
        }

        // 点击弹窗外部关闭
        document.getElementById('saveModal').addEventListener('click', (e) => {
            if (e.target.id === 'saveModal') {
                closeSaveModal();
            }
        });

        // 返回
        function goBack() {
            window.history.back();
        }

        // 生成二维码函数
        function generateQRCode(element, url) {
            // 创建二维码
            const typeNumber = 0;
            const errorCorrectionLevel = 'H';
            const qr = qrcode(typeNumber, errorCorrectionLevel);
            qr.addData(url);
            qr.make();
            
            // 创建二维码图像元素
            const qrImg = qr.createImgTag(4, 0);
            element.innerHTML = qrImg;
            
            // 设置图片样式使其填满容器
            const img = element.querySelector('img');
            if (img) {
                img.style.width = '100%';
                img.style.height = '100%';
                img.style.borderRadius = '8px';
            }
        }
        
        // 合并图片和二维码为一张图
        function mergeImageWithQR(posterImage, qrElement, callback) {
            // 创建canvas元素
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            
            // 加载海报图片
            const img = new Image();
            img.crossOrigin = 'anonymous';
            img.src = posterImage.src;
            
            img.onload = function() {
                // 设置canvas大小与图片相同
                canvas.width = img.width;
                canvas.height = img.height;
                
                // 绘制海报图片
                ctx.drawImage(img, 0, 0);
                
                // 获取二维码图像
                const qrImg = qrElement.querySelector('img');
                if (qrImg) {
                    // 创建临时图像对象加载二维码
                    const tempQrImg = new Image();
                    tempQrImg.crossOrigin = 'anonymous';
                    tempQrImg.src = qrImg.src;
                    
                    tempQrImg.onload = function() {
                        const qrWidth = 180;
                        const qrHeight = 180;
                        const qrX = img.width - qrWidth - 10; // 右边距10px
                        const qrY = img.height - qrHeight - 10; // 底边距10px
                        
                        // 绘制二维码背景（白色）
                        ctx.fillStyle = 'white';
                        ctx.fillRect(qrX, qrY, qrWidth, qrHeight);
                        
                        // 绘制二维码图像
                        ctx.drawImage(tempQrImg, qrX + 8, qrY + 8, qrWidth - 16, qrHeight - 16);
                        
                        // 绘制边框
                        ctx.strokeStyle = '#ff6b9d';
                        ctx.lineWidth = 2;
                        ctx.strokeRect(qrX, qrY, qrWidth, qrHeight);
                        
                        // 生成合并后的图片URL
                        const mergedImageUrl = canvas.toDataURL('image/png');
                        callback(mergedImageUrl);
                    };
                } else {
                    callback(canvas.toDataURL('image/png'));
                }
            };
        }
        
        // 保存合并后的图片
        function saveMergedImage(slideIndex) {
            const slides = document.querySelectorAll('.poster-slide');
            if (slideIndex >= 0 && slideIndex < slides.length) {
                const slide = slides[slideIndex];
                const posterImage = slide.querySelector('.poster-image');
                const qrElement = slide.querySelector('.qr-placeholder');
                
                mergeImageWithQR(posterImage, qrElement, function(mergedImageUrl) {
                    // 创建临时链接用于下载
                    const link = document.createElement('a');
                    link.href = mergedImageUrl;
                    link.download = 'promotion-poster-' + (slideIndex + 1) + '.png';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    
                    // 显示保存成功提示
                    showSaveModal();
                });
            }
        }
        
        // 页面加载时初始化
        window.addEventListener('DOMContentLoaded', () => {
            initDots();
            
            // 获取所有的qr-placeholder元素并生成二维码
            const qrPlaceholders = document.querySelectorAll('.qr-placeholder');
            const url = '<?php echo $rk_url; ?>';
            
            qrPlaceholders.forEach(placeholder => {
                generateQRCode(placeholder, url);
            });
        });
    </script>
</body>
</html>
