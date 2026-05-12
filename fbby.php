<?php 
include_once 'loaduser.php';
include_once 'config.php';


$typeid = $_GET['typeid'] ?? 0;
$pageTitle = $_GET['typeid']==1 ? '发布包养' :'发布伴游';

$provinceArr = db('areab')->where(['pid' => 0])->field('id, name')->select();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="author" content="<?php echo $webname; ?>" />
<meta name="keywords" content="发布<?php echo $pageTitle; ?>_<?php echo $webname; ?>">
<meta name="description" content="发布<?php echo $pageTitle; ?>_<?php echo $webname; ?>">
<title>发布<?php echo $pageTitle; ?>_<?php echo $webname; ?></title>
<link rel="stylesheet" href="/css/comm.css">
<link rel="stylesheet" href="/css/imgvideo.css">
<link rel="stylesheet" href="/css/fb.css">
<style>
    /* 全屏遮罩层样式 */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
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
        border: 4px solid rgba(255, 255, 255, 0.3);
        border-top-color: #ff5e7b;
        border-radius: 50%;
        -webkit-animation: spin 1s linear infinite;
        animation: spin 1s linear infinite;
        margin-bottom: 15px;
    }
    .loading-overlay .loading-text {
        color: #fff;
        font-size: 16px;
        text-align: center;
        padding: 0 20px;
    }
    @-webkit-keyframes spin {
        0% { -webkit-transform: rotate(0deg); transform: rotate(0deg); }
        100% { -webkit-transform: rotate(360deg); transform: rotate(360deg); }
    }
    @keyframes spin {
        0% { -webkit-transform: rotate(0deg); transform: rotate(0deg); }
        100% { -webkit-transform: rotate(360deg); transform: rotate(360deg); }
    }
    </style>
</head>
<body>
    <?php include 'comm/header.php'; ?>

    <div class="publish-container">
        <!-- 页面标题 -->
        <form id="publishForm">
            <!-- 基本信息部分 -->
            <div class="form-section">
                <h3 class="section-title">
                    基本信息
                </h3>
                <input type="hidden" name="typeid" value="<?php echo $typeid;?>">
                

                <div class="form-item">
                    <label class="form-label">
                        年龄
                    </label>
                    <select name="age" class="form-select">
                        <option value="0">请选择年龄</option>
                        <?php for($i = 18; $i <= 35; $i++): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?>岁</option>
                        <?php endfor; ?>
                    </select>
                    <div class="form-error" data-field="age">请选择年龄</div>
                </div>

                <div class="form-item">
                    <label class="form-label">
                        身高
                    </label>
                    <select name="sg" class="form-select">
                        <option value="0">请选择身高</option>
                        <?php for($i = 155; $i <= 180; $i++): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?>cm</option>
                        <?php endfor; ?>
                    </select>
                    <div class="form-error" data-field="sg">请选择身高</div>
                </div>

                <div class="form-item">
                    <label class="form-label">
                        体重
                    </label>
                    <select name="tz" class="form-select">
                        <option value="0">请选择体重</option>
                        <?php for($i = 35; $i <= 70; $i++): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?>Kg</option>
                        <?php endfor; ?>
                    </select>
                    <div class="form-error" data-field="tz">请选择体重</div>
                </div>
                <div class="form-item">
                    <label class="form-label">
                        学历
                    </label>
                    <select name="xl" class="form-select">
                        <option value="0">请选择学历</option>
                        <?php foreach ($xlArr as $k => $v) {?>
                               <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
                            <?php } ?>
                    </select>
                    <div class="form-error" data-field="xl">请选择学历</div>
                </div>


                <div class="form-item">
                    <label class="form-label">
                        职业
                    </label>
                    <select name="zy" class="form-select">
                        <option value="0">请选择职业</option>
                        <?php foreach ($zyArr as $key => $v): ?>
                        <option value="<?php echo $key;?>"><?php echo $v;?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-error" data-field="zy">请选择职业</div>
                </div>


                <div class="form-item">
                    <label class="form-label required">
                        籍贯
                    </label>
                    <select name="province" id="province" class="form-select">
                        <option value="">请选择省份</option>
                        <?php foreach ($provinceArr as $k => $v) { ?>
                           <option value="<?php echo $v['name']; ?>"><?php echo $v['name']; ?></option>
                        <?php } ?>
                    </select>
                    <div class="form-error" data-field="province">请选择省份</div>
                </div>
                
                <div class="form-item">
                    <label class="form-label required">
                        所在城市
                    </label>
                    <input type="text" name="city" class="form-input" placeholder="填写如：北京，上海，深圳，广州，杭州 等">
                    <div class="form-error" data-field="aihao">请输入所在城市</div>
                </div>
                

                <div class="form-item">
                    <label class="form-label required">
                        兴趣爱好
                    </label>
                    <input type="text" name="aihao" class="form-input" placeholder="填写如：唱歌，跳舞，看书，画画，健身 等">
                    <div class="form-error" data-field="aihao">请输入兴趣爱好</div>
                </div>
                <div class="form-item">
                    <label class="form-label required">
                        约会价格
                    </label>
                    <input type="text" name="price" class="form-input" placeholder="填写如：次2000 , 夜5000 , 一天/1万 , 私聊">
                    <div class="form-error" data-field="price">请输入约会价格</div>
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
                    <span>手机号、微信、QQ 至少填写一项</span>
                </div>
                
                <div class="form-item">
                    <label class="form-label required">
                        昵称
                    </label>
                    <input type="text" name="uname" class="form-input" placeholder="请输入昵称">
                    <div class="form-error" data-field="uname">请输入昵称</div>
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
              
                
                <div class="form-error" data-field="contact">请至少填写一项联系方式</div>
                
                
            </div>



            <!-- 图片上传 -->
            <div class="form-section upload-section">
                <h3 class="section-title">
                    图片上传
                </h3>
                <div id="imageUploadContainer" class="upload-container">
                    <div class="upload-grid"></div>
                    <div class="upload-hint">支持JPG、PNG格式，单张图片不超过5MB，最多上传<span class="upload-limit">9张</span></div>
                </div>
            </div>

            <!-- 视频上传 -->
            <div class="form-section upload-section">
                <h3 class="section-title">
                    视频上传
                </h3>
                <div id="videoUploadContainer" class="upload-container">
                    <div class="upload-grid"></div>
                    <div class="upload-hint">支持MP4格式，单个视频不超过50MB，最多上传<span class="upload-limit">3个</span></div>
                </div>
            </div>
            <!-- 验证码 -->
            <div class="form-section">
                <div class="form-item">
                    <!-- 标签、输入框、验证码图片同一行布局 -->
                    <div class="captcha-row">
                        <label class="form-label required">验证码:</label>

                        <div class="captcha-group">
                          <input type="text" name="captcha" id="yzm" class="form-inputyz" placeholder="请输入验证码">
                            <img id="captchaImg" src="/lib/yzmcode.php" alt="验证码" class="captcha-img" onclick="refreshCaptcha()">
                        </div>
                        
                    </div>
                    <div class="form-error" data-field="yzm">请输入验证码</div>
                    <div class="form-hint">点击验证码图片可刷新</div>
                </div>
            </div>

            <!-- 提交按钮 -->
            <button type="submit" id="submitBtn" class="submit-button">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                发布信息
            </button>
        </form>

    </div>
    
    <input type="hidden" name="typeid" id="typeid" value="<?php echo $typeid; ?>">

    <?php include 'comm/footer.php'; ?>

    <script src="/js/imgvideo.js?t=<?php time();?>"></script>


<script>
// 兼容老旧浏览器的事件绑定
function addEvent(el, type, fn) {
    if (!el) return;
    if (el.addEventListener) {
        el.addEventListener(type, fn, false);
    } else if (el.attachEvent) {
        el.attachEvent('on' + type, fn);
    } else {
        el['on' + type] = fn;
    }
}

// 刷新验证码
function refreshCaptcha() {
    var captchaImg = document.getElementById('captchaImg');
    if (captchaImg) {
        captchaImg.src = '/lib/yzmcode.php?t=' + new Date().getTime();
    }
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
        var textEl = overlay.getElementsByClassName('loading-text')[0];
        if (textEl) {
            textEl.innerHTML = text || '数据正在上传中，请稍等片刻...';
        }
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

// 验证表单 - 从上到下依次验证，遇到第一个未填/未选立即 alert 并聚焦
function validateForm() {
    // 按页面顺序定义必填字段
    var fields = [
        { name: 'age',     isSelect: true,  msg: '请选择年龄' },
        { name: 'sg',      isSelect: true,  msg: '请选择身高' },
        { name: 'tz',      isSelect: true,  msg: '请选择体重' },
        { name: 'xl',      isSelect: true,  msg: '请选择学历' },
        { name: 'zy',      isSelect: true,  msg: '请选择职业' },
        { name: 'province',isSelect: true,  msg: '请选择籍贯' },
        { name: 'city',    isSelect: false, msg: '请输入所在城市' },
        { name: 'aihao',   isSelect: false, msg: '请输入兴趣爱好' },
        { name: 'price',   isSelect: false, msg: '请输入约会价格' },
        { name: 'content', isSelect: false, msg: '请填写详细内容' },
        { name: 'uname',   isSelect: false, msg: '请输入昵称' }
    ];

    var i, field, el, val;

    // 逐一校验普通字段
    for (i = 0; i < fields.length; i++) {
        field = fields[i];
        el = document.querySelector('[name="' + field.name + '"]');
        if (!el) continue;

        val = el.value ? el.value.replace(/\s/g, '') : '';
        var empty = field.isSelect ? (!val || val === '0') : !val;

        if (empty) {
            alert(field.msg);
            scrollToElement(el);
            el.focus();
            return false;
        }
    }

    // 联系方式：手机、微信、QQ 至少填写一项
    var mobileEl = document.querySelector('input[name="mobile"]');
    var weixinEl = document.querySelector('input[name="weixin"]');
    var qqEl     = document.querySelector('input[name="qq"]');
    var hasContact = (mobileEl && mobileEl.value.replace(/\s/g, '')) ||
                     (weixinEl && weixinEl.value.replace(/\s/g, '')) ||
                     (qqEl     && qqEl.value.replace(/\s/g, ''));
    if (!hasContact) {
        alert('手机号、微信、QQ 至少填写一项');
        scrollToElement(mobileEl);
        mobileEl && mobileEl.focus();
        return false;
    }

    // 图片：至少上传一张
    if (window.imageUploader) {
        var imgs = window.imageUploader.files;
        if (!imgs || imgs.length === 0) {
            alert('请至少上传一张图片');
            var imgContainer = document.getElementById('imageUploadContainer');
            if (imgContainer) scrollToElement(imgContainer);
            return false;
        }
    }

    // 验证码
    var yzmEl = document.getElementById('yzm');
    if (!yzmEl || !yzmEl.value.replace(/\s/g, '')) {
        alert('请输入验证码');
        scrollToElement(yzmEl);
        yzmEl && yzmEl.focus();
        return false;
    }

    return true;
}

// 兼容老旧浏览器的滚动到元素
function scrollToElement(el) {
    if (!el) return;
    
    if (el.scrollIntoView) {
        try {
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } catch (e) {
            // 老旧浏览器不支持 options 参数
            el.scrollIntoView(true);
        }
    } else {
        // 更老的浏览器
        var rect = el.getBoundingClientRect();
        var scrollTop = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop;
        var targetTop = rect.top + scrollTop - (window.innerHeight / 2);
        window.scrollTo(0, targetTop);
    }
}

// 上传所有文件（图片和视频）
function uploadAllFiles() {
    return new Promise(function(resolve, reject) {
        var imagePromise, videoPromise;
        
        // 上传图片
        if (window.imageUploader && window.imageUploader.files && window.imageUploader.files.length > 0) {
            imagePromise = window.imageUploader.uploadAll();
        } else {
            imagePromise = Promise.resolve(true);
        }
        
        // 上传视频
        if (window.videoUploader && window.videoUploader.files && window.videoUploader.files.length > 0) {
            videoPromise = window.videoUploader.uploadAll();
        } else {
            videoPromise = Promise.resolve(true);
        }
        
        // 等待全部上传完成
        imagePromise.then(function(imgResult) {
            if (imgResult === false) {
                throw new Error('图片上传失败');
            }
            return videoPromise;
        }).then(function(vidResult) {
            if (vidResult === false) {
                throw new Error('视频上传失败');
            }
            resolve(true);
        }).catch(function(err) {
            reject(err);
        });
    });
}

// 提交表单
function submitForm(e) {
    // 阻止默认提交
    if (e && e.preventDefault) {
        e.preventDefault();
    } else if (window.event) {
        window.event.returnValue = false;
    }
    
    // 验证表单
    if (!validateForm()) {
        return false;
    }
    
    // 获取提交按钮
    var submitBtn = document.getElementById('submitBtn');
    if (!submitBtn) return false;
    
    var originalHTML = submitBtn.innerHTML;
    
    // 禁用提交按钮
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>提交中...';
    
    // 显示全屏遮罩层
    showLoadingOverlay('数据正在上传中，请稍等片刻...');
    
    // 上传图片和视频
    uploadAllFiles().then(function() {
        // 收集表单数据
        var formData = new FormData();
        
        // 遍历表单所有字段，自动添加到 formData
        var formFields = ['typeid', 'province', 'city', 'age', 'sg', 'tz', 'xl', 'zy', 'aihao', 'price', 'content', 'uname', 'mobile', 'weixin', 'qq'];
        for (var i = 0; i < formFields.length; i++) {
            var fieldName = formFields[i];
            var fieldEl = document.querySelector('[name="' + fieldName + '"]');
            if (fieldEl) {
                formData.append(fieldName, fieldEl.value);
            }
        }
        
        // 图片和视频（从上传组件获取已上传的文件路径）
        if (window.imageUploader && window.imageUploader.getFiles) {
            var images = window.imageUploader.getFiles();
            formData.append('images', JSON.stringify(images));
        }
        if (window.videoUploader && window.videoUploader.getFiles) {
            var videos = window.videoUploader.getFiles();
            formData.append('videos', JSON.stringify(videos));
        }
        
        // 验证码
        var yzm = document.getElementById('yzm');
        if (yzm) formData.append('captcha', yzm.value);
        
        // 提交到后台
        var xhr;
        if (window.XMLHttpRequest) {
            xhr = new XMLHttpRequest();
        } else {
            xhr = new ActiveXObject('Microsoft.XMLHTTP');
        }
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                hideLoadingOverlay();
                
                if (xhr.status >= 200 && xhr.status < 300) {
                    var result;
                    try {
                        result = JSON.parse(xhr.responseText);
                    } catch (e) {
                        result = { code: 500, msg: '服务器响应格式错误' };
                    }
                    
                    if (result.code === 200) {
                        alert('发布成功！');
                        window.location.href = 'member_publish.html';
                    } else {
                        alert(result.msg || '发布失败，请重试');
                        refreshCaptcha();
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalHTML;
                    }
                } else {
                    alert('网络错误，请稍后重试');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHTML;
                }
            }
        };
        
        xhr.open('POST', '/opers/forum/fbby.html', true);
        xhr.send(formData);
        
    }).catch(function(err) {
        hideLoadingOverlay();
        alert(err.message || '文件上传失败，请重试');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalHTML;
    });
    
    return false;
}

// 页面加载完成后初始化
(function() {
    // 兼�� DOMContentLoaded
    function init() {
        // 绑定验证码图片点击刷新
        var captchaImg = document.getElementById('captchaImg');
        if (captchaImg) {
            addEvent(captchaImg, 'click', refreshCaptcha);
            // 添加手指样式
            captchaImg.style.cursor = 'pointer';
        }
        
        // 绑定表单提交
        var form = document.getElementById('publishForm');
        if (form) {
            addEvent(form, 'submit', submitForm);
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
    }
    
    // 兼容各种浏览器的 DOM ready
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        setTimeout(init, 1);
    } else if (document.addEventListener) {
        document.addEventListener('DOMContentLoaded', init);
    } else if (document.attachEvent) {
        document.attachEvent('onreadystatechange', function() {
            if (document.readyState === 'complete') {
                init();
            }
        });
    } else {
        window.onload = init;
    }
})();
</script>

<?php 
include_once 'comm/alert_modal.php';
?>
</body>
</html>
