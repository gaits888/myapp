<?php 
include_once 'loaduser.php';
include_once 'config.php';

$page_title = "绑定收款方式";

$bankList = db('fl_bank_card')->where('status',1)->order('sort', 'ASC')->select();

$boundData = db('bankb')->where('uid', $userData['userId'])->find();

$boundBank = ($boundData && $boundData['bankcard']) ? $boundData : [];
$boundAlipay = ($boundData && $boundData['zfb']) ? $boundData : [];
$boundUsdt = ($boundData && $boundData['usdt']) ? $boundData : [];
$history_url ='tx.html';
$pageTitle = "绑定收款方式";

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="author" content="<?php echo $webname; ?>" />
<meta name="keywords" content="绑定收款方式_<?php echo $webname; ?>">
<meta name="description" content="绑定收款方式_<?php echo $webname; ?>">
<title>绑定收款方式_<?php echo $webname; ?></title>
<link rel="stylesheet" href="/css/comm.css">
<!--<link rel="stylesheet" href="/css/bind_pay.css">-->
<style>
 body {
            background: var(--bg-gray);
            padding-top: 50px;
        }

        .container {
            min-height: 100vh;
            padding-bottom: var(--spacing-3xl);
        }

        .main-content {
            max-width: 600px;
            margin: 0 auto;
            padding: var(--spacing-md) var(--spacing-md);
        }

        .payment-tabs {
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
            margin-bottom: var(--spacing-md);
            background: var(--bg-white);
            padding: var(--spacing-md);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
        }

        .payment-tab {
            -webkit-box-flex: 1;
            -webkit-flex: 1;
            -ms-flex: 1;
            flex: 1;
            padding: var(--spacing-md);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            background: var(--bg-white);
            cursor: pointer;
            transition: all var(--transition-base);
            text-align: center;
        }

        /* 用 margin 代替 gap，兼容低版本浏览器 */
        .payment-tab + .payment-tab {
            margin-left: var(--spacing-md);
        }

        .payment-tab:hover {
            border-color: var(--primary-light);
        }

        .payment-tab.active {
            border-color: rgba(248 144 10 / 78%);
            background: #fdfdfd;
        }

        .payment-tab-icon {
            width: 32px;
            height: 32px;
            margin: 0 auto var(--spacing-sm);
        }

        .payment-tab-name {
            font-size: var(--font-sm);
            font-weight: var(--font-medium);
            color: var(--text-primary);
        }

        .payment-tab.active .payment-tab-name {
            color: rgba(248 144 10 / 78%);
        }

        .form-card {
            background: var(--bg-white);
            border-radius: var(--radius-lg);
            padding: var(--spacing-md);
            box-shadow: var(--shadow-md);
        }

        .form-title {
            font-size: 18px;
            font-weight: var(--font-semibold);
            color: var(--text-primary);
            margin-bottom: var(--spacing-md);
        }

        .form-group {
            margin-bottom: var(--spacing-md);
        }

        .form-label {
            display: block;
            font-size: var(--font-base);
            font-weight: var(--font-medium);
            color: var(--text-primary);
            margin-bottom: var(--spacing-sm);
        }

        .form-label .required {
            color: var(--primary-color);
            margin-left: var(--spacing-xs);
        }

        .form-hint {
            font-size: var(--font-xs);
            color: var(--text-tertiary);
            margin-top: 10px;
        }

        .upload-area {
            border: 2px dashed var(--border-light);
            border-radius: var(--radius-md);
            padding: var(--spacing-3xl) var(--spacing-md);
            text-align: center;
            cursor: pointer;
            transition: all var(--transition-base);
        }

        .upload-area:hover {
            border-color: var(--primary-color);
            background: var(--bg-pink-light);
        }

        .upload-area.has-image {
            padding: 0;
            border: none;
        }

        .upload-icon {
            width: 48px;
            height: 48px;
            stroke: rgba(248 164 10 / 53%);
            margin: 0 auto var(--spacing-md);
        }

        .upload-text {
            font-size: var(--font-base);
            color: var(--text-secondary);
            margin-bottom: var(--spacing-xs);
        }

        .upload-hint {
            font-size: var(--font-xs);
            color: var(--text-tertiary);
        }

        .upload-preview {
            width: 100%;
            border-radius: var(--radius-md);
            overflow: hidden;
        }

        .upload-preview img {
            width: 100%;
            height: auto;
            display: block;
        }

        .upload-preview-actions {
            padding: var(--spacing-md);
            background: var(--bg-light);
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-pack: center;
            -webkit-justify-content: center;
            -ms-flex-pack: center;
            justify-content: center;
        }

        /* 用 margin 代替 gap */
        .upload-preview-actions > * + * {
            margin-left: var(--spacing-md);
        }

        .submit-btn {
/*            margin-top: var(--spacing-md);*/
        }

        .form-content {
            display: none;
        }

        .form-content.active {
            display: block;
        }

        .bound-list {
            background: var(--bg-white);
            border-radius: var(--radius-lg);
            padding: var(--spacing-md);
            box-shadow: var(--shadow-md);
            margin: var(--spacing-md) 0 0 0;
        }

        .bound-list-title {
            font-size: var(--font-lg);
            font-weight: var(--font-semibold);
            color: var(--text-primary);
            margin-bottom: var(--spacing-md);
        }

        .bound-item {
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -webkit-align-items: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: justify;
            -webkit-justify-content: space-between;
            -ms-flex-pack: justify;
            justify-content: space-between;
            padding: var(--spacing-md);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            margin-bottom: var(--spacing-md);
        }

        .bound-item:last-child {
            margin-bottom: 0;
        }

        .bound-item-left {
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -webkit-align-items: center;
            -ms-flex-align: center;
            align-items: center;
        }

        .bound-item-icon {
            width: 32px;
            height: 32px;
        }

        /* 用 margin 代替 gap：图标与文字间距 */
        .bound-item-left > .bound-item-icon + .bound-item-info {
            margin-left: var(--spacing-md);
        }

        .bound-item-info {
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-orient: vertical;
            -webkit-box-direction: normal;
            -webkit-flex-direction: column;
            -ms-flex-direction: column;
            flex-direction: column;
        }

        /* 用 margin 代替 gap：上下两行文字间距 */
        .bound-item-info > * + * {
            margin-top: var(--spacing-xs);
        }

        .bound-item-name {
            font-size: var(--font-base);
            font-weight: var(--font-medium);
            color: var(--text-primary);
        }

        .bound-item-detail {
            font-size: var(--font-sm);
            color: var(--text-secondary);
        }

        .unbind-btn {
            padding: var(--spacing-sm) var(--spacing-md);
            background: transparent;
            border: 1px solid var(--border-medium);
            border-radius: var(--radius-sm);
            color: var(--text-secondary);
            font-size: var(--font-sm);
            cursor: pointer;
            transition: all var(--transition-base);
        }

        .unbind-btn:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
</style>
</head>
<body>
    <div class="container">
        <?php 
       
        include 'comm/header.php'; 
        ?>

        <!-- 主内容区 -->
        <main class="main-content">
            

            <!-- 支付方式选择 -->
            <div class="payment-tabs">
                <div class="payment-tab active" data-type="bank" onclick="switchPaymentType('bank')">
                    <svg class="payment-tab-icon" viewBox="0 0 24 24" fill="none">
                        <rect x="2" y="5" width="20" height="14" rx="2" fill="#4A90E2" stroke="#4A90E2"/>
                        <rect x="2" y="9" width="20" height="3" fill="#357ABD"/>
                        <rect x="4" y="14" width="6" height="2" rx="1" fill="white"/>
                        <circle cx="18" cy="15" r="1.5" fill="#FFD700"/>
                    </svg>
                    <div class="payment-tab-name">银行卡</div>
                </div>
                <div class="payment-tab" data-type="alipay" onclick="switchPaymentType('alipay')">
                    <svg t="1774086998137" class="payment-tab-icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="149109" width="32" height="32"><path d="M265.467 566.651c-10.535 8.53-21.878 20.874-25.089 36.629-4.415 21.478-0.902 48.474 19.87 69.549 25.089 25.589 63.324 32.616 79.884 33.816 44.857 3.215 92.628-18.965 128.65-44.356 14.154-9.933 38.239-29.905 61.419-60.817-51.778-26.69-116.414-56.298-185.457-53.388-35.523 1.505-60.813 8.83-79.277 18.567z m656.527 118.218c22.885-53.588 35.526-112.599 35.526-174.517 0-246.174-200.312-446.484-446.481-446.484-246.272 0.098-446.484 200.413-446.484 446.583 0 246.171 200.31 446.484 446.484 446.484 148.622 0 280.394-72.958 361.578-184.956-76.769-38.236-203.22-100.959-281.196-138.993-37.135 42.352-92.125 84.803-154.246 103.363-39.035 11.546-74.16 16.059-110.892 8.531-36.428-7.424-63.229-24.485-78.877-41.544-7.931-8.731-17.063-19.771-23.688-33.018 0.602 1.705 1.005 2.71 1.005 2.71s-3.813-6.527-6.725-16.964a73.978 73.978 0 0 1-2.91-15.854 85.332 85.332 0 0 1-0.201-11.344c-0.298-6.725-0.097-13.746 1.504-20.975 3.616-17.66 11.144-38.235 30.71-57.303 42.754-41.848 100.055-44.156 129.761-43.955 43.955 0.201 120.427 19.47 184.75 42.249 17.866-37.938 29.208-78.579 36.634-105.575h-267.45v-28.9H468.19V356.6H301.797v-28.901h166.289v-58.106c0-7.928 1.605-14.451 14.449-14.451h65.029v72.254h180.744v28.906h-180.64v57.802h144.608s-14.546 80.991-59.907 160.771c100.759 36.029 242.461 91.53 289.625 109.994z" fill="#00AAEE" p-id="149110"></path></svg>
                    <div class="payment-tab-name">支付宝</div>
                </div>
                <div class="payment-tab" data-type="usdt" onclick="switchPaymentType('usdt')">
                   <svg t="1774087148905" class="payment-tab-icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="152472" width="32" height="32"><path d="M566.272 457.728v74.24c-3.072 0.512-19.968 1.536-54.272 1.536-28.16 0-48.64-1.024-55.808-1.536V457.728c-109.568 5.12-190.976 24.064-190.976 46.592 0 22.528 81.408 41.984 190.976 46.592 7.168 0.512 27.136 1.024 55.296 1.024 35.328 0 51.712-1.024 54.784-1.024 109.056-4.608 190.464-24.064 190.464-46.592 0-22.528-81.408-41.984-190.464-46.592z" fill="#26A17B" p-id="152473"></path><path d="M512 61.952c-248.32 0-450.048 201.216-450.048 450.048s201.216 450.048 450.048 450.048 450.048-201.216 450.048-450.048-201.728-450.048-450.048-450.048z m54.272 506.88v213.504H456.192v-212.992c-123.904-5.632-216.576-30.208-216.576-59.392S332.288 455.68 456.192 450.048v-66.56H303.616V282.112h414.72v101.376h-152.064v66.56c123.392 5.632 216.576 30.208 216.576 59.392s-93.184 53.76-216.576 59.392z" fill="#26A17B" p-id="152474"></path></svg>
                    <div class="payment-tab-name">USDT</div>
                </div>
            </div>

            <!-- 银行卡表单 -->
            <div class="form-card form-content active" id="bankForm">
                <h2 class="form-title">绑定银行卡</h2>
                <form onsubmit="submitBankCard(event)">
                    <div class="form-group">
                        <label class="form-label">
                            选择银行
                          
                        </label>
                        <select class="select" id="bankForm-select">
                            <option value="">请选择银行</option>

                             <?php foreach($bankList as $bank): ?>
                            <option value="<?php echo $bank['id']; ?>" <?php echo ($boundBank && $boundBank['bankname'] == $bank['id']) ? 'selected' : ''; ?>><?php echo $bank['name']; ?></option>
                            <?php endforeach; ?>

                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            持卡人姓名
                            
                        </label>
                        <input type="text" class="input" placeholder="请输入持卡人姓名" value="<?php echo $boundBank ? $boundBank['uname'] : ''; ?>" id="realName">
                        <div class="form-hint">请确保姓名与银行卡信息一致</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            银行卡号
                           
                        </label>
                        <input type="text" class="input" placeholder="请输入银行卡号" value="<?php echo $boundBank ? $boundBank['bankcard'] : ''; ?>" maxlength="30" id="bankids">
                        <div class="form-hint">请输入16-19位银行卡号</div>
                    </div>
                  
                    <button type="submit" class="btn btn-primary btn-full submit-btn">提交绑定</button>
                </form>
            </div>

            <!-- 支付宝表单 -->
            <div class="form-card form-content" id="alipayForm">
                <h2 class="form-title">绑定支付宝</h2>
                <form onsubmit="submitAlipay(event)">
                    <div class="form-group">
                        <label class="form-label">
                            上传收款码
                          
                        </label>
                        <div class="upload-area" id="alipayUploadArea" onclick="document.getElementById('alipayFileInput').click()">
                            
                            <?php if ($boundAlipay && $boundAlipay['zfb']) { ?>
                                <img src="<?php echo ($boundAlipay && $boundAlipay['zfb']) ? $boundAlipay['zfb'] : ''; ?>" style="max-width: 80%; border-radius: 5px;" onerror="this.style.display='none'">
                                <div class="upload-text" style="margin-top: 10px;">点击重新上传</div>
                            <?php }else{ ?>
                                <svg class="upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                                <div class="upload-text">点击上传支付宝收款码</div>
                            <div class="upload-hint">支持JPG、PNG格式，不超过5MB</div>
                            <?php } ?>
                            

                            
                        </div>
                        <input type="file" id="alipayFileInput" accept="image/*" style="display: none;" onchange="handleFileUpload(event, 'alipay')" required>
                        <div id="alipayPreview" class="upload-preview" style="display: none;">
                            <img id="alipayPreviewImg" src="/placeholder.svg" alt="预览">
                            <div class="upload-preview-actions">
                                <button type="button" class="btn btn-sm btn-outline" onclick="document.getElementById('alipayFileInput').click()">重新上传</button>
                                <button type="button" class="btn btn-sm btn-secondary" onclick="removeUpload('alipay')">删除</button>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-full submit-btn"><?php echo $boundAlipay ? '更新绑定' : '确认绑定'; ?></button>
                </form>
            </div>

            <!-- USDT表单 -->
            <div class="form-card form-content" id="usdtForm">
                <h2 class="form-title">绑定USDT</h2>
                <form onsubmit="submitUsdt(event)">
                    <div class="form-group">
                        <label class="form-label">
                            USDT钱包地址 (TRC20)
                            <span class="required">*</span>
                        </label>
                        <input type="text" class="input" placeholder="请输入USDT钱包地址" value="<?php echo $boundUsdt ? $boundUsdt['usdt'] : ''; ?>"  id="usdtinp">
                        <div class="form-hint">注：本平台仅支持TRC20网络，请仔细核对地址避免转账错误</div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-full submit-btn"><?php echo $boundUsdt ? '更新绑定' : '确认绑定'; ?></button>
                </form>
            </div>



            <!-- 已绑定收款方式列表 -->
            <div class="bound-list" id="boundList">
                <h2 class="bound-list-title">已绑定收款方式</h2>
                <div id="boundListContainer">
                     <?php if($boundBank): ?>
                    <div class="bound-item" data-id="1">
                        <div class="bound-item-left">
                            <svg class="bound-item-icon" viewBox="0 0 24 24" fill="none">
                                <rect x="2" y="5" width="20" height="14" rx="2" fill="#4A90E2" stroke="#4A90E2"/>
                                <rect x="2" y="9" width="20" height="3" fill="#357ABD"/>
                                <rect x="4" y="14" width="6" height="2" rx="1" fill="white"/>
                                <circle cx="18" cy="15" r="1.5" fill="#FFD700"/>
                            </svg>
                            <div class="bound-item-info">
                                <span class="bound-item-name"><?php echo $bankList[$boundBank['bankname']-1]['name'];?></span>
                                <span class="bound-item-detail"><?php echo substr($boundBank['bankcard'], 0, 4) . ' **** **** ' . substr($boundBank['bankcard'], -4); ?></span>
                            </div>
                        </div>
                        <button class="unbind-btn">已绑定</button>
                    </div>
                    <?php endif; ?>

                    <?php if($boundAlipay): ?>
                    <div class="bound-item" data-id="2">
                        <div class="bound-item-left">
                            <svg t="1774086998137" class="payment-tab-icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="149109" width="32" height="32"><path d="M265.467 566.651c-10.535 8.53-21.878 20.874-25.089 36.629-4.415 21.478-0.902 48.474 19.87 69.549 25.089 25.589 63.324 32.616 79.884 33.816 44.857 3.215 92.628-18.965 128.65-44.356 14.154-9.933 38.239-29.905 61.419-60.817-51.778-26.69-116.414-56.298-185.457-53.388-35.523 1.505-60.813 8.83-79.277 18.567z m656.527 118.218c22.885-53.588 35.526-112.599 35.526-174.517 0-246.174-200.312-446.484-446.481-446.484-246.272 0.098-446.484 200.413-446.484 446.583 0 246.171 200.31 446.484 446.484 446.484 148.622 0 280.394-72.958 361.578-184.956-76.769-38.236-203.22-100.959-281.196-138.993-37.135 42.352-92.125 84.803-154.246 103.363-39.035 11.546-74.16 16.059-110.892 8.531-36.428-7.424-63.229-24.485-78.877-41.544-7.931-8.731-17.063-19.771-23.688-33.018 0.602 1.705 1.005 2.71 1.005 2.71s-3.813-6.527-6.725-16.964a73.978 73.978 0 0 1-2.91-15.854 85.332 85.332 0 0 1-0.201-11.344c-0.298-6.725-0.097-13.746 1.504-20.975 3.616-17.66 11.144-38.235 30.71-57.303 42.754-41.848 100.055-44.156 129.761-43.955 43.955 0.201 120.427 19.47 184.75 42.249 17.866-37.938 29.208-78.579 36.634-105.575h-267.45v-28.9H468.19V356.6H301.797v-28.901h166.289v-58.106c0-7.928 1.605-14.451 14.449-14.451h65.029v72.254h180.744v28.906h-180.64v57.802h144.608s-14.546 80.991-59.907 160.771c100.759 36.029 242.461 91.53 289.625 109.994z" fill="#00AAEE" p-id="149110"></path></svg>
                            <div class="bound-item-info">
                                <span class="bound-item-name">支付宝</span>
                                <span class="bound-item-detail">收款码已上传</span>
                            </div>
                        </div>
                        <button class="unbind-btn">已绑定</button>
                    </div>
                     <?php endif; ?>


                    <?php if($boundUsdt): ?>
                    <div class="bound-item" data-id="3">
                        <div class="bound-item-left">
                            <svg t="1774087148905" class="payment-tab-icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="152472" width="32" height="32"><path d="M566.272 457.728v74.24c-3.072 0.512-19.968 1.536-54.272 1.536-28.16 0-48.64-1.024-55.808-1.536V457.728c-109.568 5.12-190.976 24.064-190.976 46.592 0 22.528 81.408 41.984 190.976 46.592 7.168 0.512 27.136 1.024 55.296 1.024 35.328 0 51.712-1.024 54.784-1.024 109.056-4.608 190.464-24.064 190.464-46.592 0-22.528-81.408-41.984-190.464-46.592z" fill="#26A17B" p-id="152473"></path><path d="M512 61.952c-248.32 0-450.048 201.216-450.048 450.048s201.216 450.048 450.048 450.048 450.048-201.216 450.048-450.048-201.728-450.048-450.048-450.048z m54.272 506.88v213.504H456.192v-212.992c-123.904-5.632-216.576-30.208-216.576-59.392S332.288 455.68 456.192 450.048v-66.56H303.616V282.112h414.72v101.376h-152.064v66.56c123.392 5.632 216.576 30.208 216.576 59.392s-93.184 53.76-216.576 59.392z" fill="#26A17B" p-id="152474"></path></svg>
                            <div class="bound-item-info">
                                <span class="bound-item-name">USDT (TRC20)</span>
                                <span class="bound-item-detail"><?php echo substr($boundUsdt['usdt'], 0, 2) . '...' . substr($boundUsdt['usdt'], -1); ?></span>
                            </div>
                        </div>
                        <button class="unbind-btn">已绑定</button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        // 切换支付方式
        function switchPaymentType(type) {
            // 更新选项卡状态
            const tabs = document.querySelectorAll('.payment-tab');
            tabs.forEach(tab => {
                if (tab.dataset.type === type) {
                    tab.classList.add('active');
                } else {
                    tab.classList.remove('active');
                }
            });

            // 显示对应表单
            const forms = document.querySelectorAll('.form-content');
            forms.forEach(form => {
                form.classList.remove('active');
            });

            if (type === 'bank') {
                document.getElementById('bankForm').classList.add('active');
            } else if (type === 'alipay') {
                document.getElementById('alipayForm').classList.add('active');
            } else if (type === 'usdt') {
                document.getElementById('usdtForm').classList.add('active');
            }
        }

        // 处理文件上传
        function handleFileUpload(event, type) {
            const file = event.target.files[0];
            if (!file) return;

            // 检查文件大小
            if (file.size > 5 * 1024 * 1024) {
                alert('文件大小不能超过5MB');
                return;
            }

            // 检查文件类型
            if (!file.type.startsWith('image/')) {
                alert('请上传图片文件');
                return;
            }

            // 读取文件并显示预览
            const reader = new FileReader();
            reader.onload = function(e) {
                const uploadArea = document.getElementById(type + 'UploadArea');
                const preview = document.getElementById(type + 'Preview');
                const previewImg = document.getElementById(type + 'PreviewImg');

                uploadArea.style.display = 'none';
                preview.style.display = 'block';
                previewImg.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }

        // 删除上传的文件
        function removeUpload(type) {
            const uploadArea = document.getElementById(type + 'UploadArea');
            const preview = document.getElementById(type + 'Preview');
            const fileInput = document.getElementById(type + 'FileInput');

            uploadArea.style.display = 'block';
            preview.style.display = 'none';
            fileInput.value = '';
        }

        // 提交银行卡绑定
        function submitBankCard(event) {
            event.preventDefault();
            
            const bankId = document.querySelector('#bankForm-select').value;
            const realName = document.getElementById('realName').value.trim();
            const account =document.querySelector('#bankForm input[placeholder="请输入银行卡号"]').value.replace(/\s/g, '');


            // 验证
            if (!bankId) {
                showInfo('请选择银行');
                return;
            }
            if (!realName) {
                showInfo('请输入持卡人姓名');
                return;
            }
            if (!account) {
                showInfo('请输入银行卡号');
                return;
            }
            if (account.length < 16 || account.length > 30) {
                showInfo('请输入正确的银行卡号');
                return;
            }

            submitPaymentInfo(1, bankId, realName, account);
        }

        // 银行卡号格式化
        document.querySelector('#bankForm input[placeholder="请输入银行卡号"]').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            if (value.length > 0) {
                value = value.match(/.{1,4}/g).join(' ');
            }
            e.target.value = value;
        });

        // 提交支付宝绑定
        function submitAlipay(event) {
            event.preventDefault();
            
            const fileInput = document.getElementById('alipayFileInput');
            if (!fileInput.files || fileInput.files.length === 0) {
                showInfo('请上传支付宝收款码！');
                return;
            }

            const formData = new FormData();
                formData.append('image', fileInput.files[0]);

                fetch('/opers/forum/upload_image.html', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alipayPath = data.filePath;
                        submitPaymentInfo(2, '', '', alipayPath);
                    } else {
                        showInfo(data.message || '图片上传失败');
                    }
                })
                .catch(error => {
                    showInfo('图片上传失败，请重试');
                    console.error('Upload error:', error);
                });
        }

        // 提交USDT绑定
        function submitUsdt(event) {
            event.preventDefault();
            const usdtAddress = document.getElementById('usdtinp').value.trim();;
            if (!usdtAddress) {
                showInfo('请输入USDT钱包地址');
                return;
            }
      

            submitPaymentInfo(3, '', '', usdtAddress);
            // 实际项目中应该调用API并根据返回结果更新已绑定列表
        }


        function submitPaymentInfo(type, bankId, realName, account) {
            const formData = new FormData();
            formData.append('type', type);
            formData.append('bank_id', bankId);
            formData.append('real_name', realName);
            formData.append('account', account);

            fetch('/opers/member/bindpay.html', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.code === 200) {
                    showSuccess(data.msg || '绑定成功','操作成功',10000,'bind_pay.html');
                    
                } else {
                    showInfo(data.msg || '绑定失败');
                }
            })
            .catch(error => {
                showInfo('网络错误，请重试');
            });
        }

        
    </script>

<?php
include_once 'comm/alert_modal.php';
?>

</body>
</html>
