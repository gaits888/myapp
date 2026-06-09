<?php
include_once 'loaduser.php';
include_once 'config.php';

// 当前用户信息（积分、邀请码）
$user_info = db('userb')->field('id,uname,jifen,invite_code,headpic')->where('id', $user_id)->find();

// 若没有邀请码则生成一个并写回（基于用户ID + 随机，保证唯一）
if (empty($user_info['invite_code'])) {
    $newCode = strtoupper(substr(md5($user_id . time() . mt_rand()), 0, 8));
    db('userb')->where('id', $user_id)->update(['invite_code' => $newCode]);
    $user_info['invite_code'] = $newCode;
}

$inviteCode = $user_info['invite_code'];
$myJifen    = intval($user_info['jifen']);

// 邀请奖励积分（可在 fl_config 配置 invite_jifen，未配置默认 100）
$rewardJifen = db('fl_config')->where('cname', 'invite_jifen')->value('value');
$rewardJifen = $rewardJifen ? intval($rewardJifen) : 100;

// 邀请统计：统计 invite_code 等于我的邀请码的注册用户数
$inviteCount = db('userb')->where('reg_invite', $inviteCode)->count();

// 邀请记录列表（最近20条）
$inviteList = db('userb')
    ->field('id,uname,headpic,addtime')
    ->where('reg_invite', $inviteCode)
    ->order('id desc')
    ->limit(20)
    ->select();

// 完整邀请链接
$inviteUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
    . '://' . $_SERVER['HTTP_HOST'] . '/reg.php?code=' . $inviteCode;
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="keywords" content="邀请好友_赚积分_<?php echo $webname; ?>">
<meta name="description" content="邀请好友注册即可获得积分奖励_<?php echo $webname; ?>">
<title>邀请好友赚积分_<?php echo $webname; ?></title>
<link rel="stylesheet" href="/css/comm.css">
<style>
    :root {
        --primary: #ff5e7b;
        --primary-dark: #e84968;
        --primary-light: #fff0f3;
        --text-main: #333;
        --text-sub: #888;
        --border: #f0f0f0;
    }
    * { box-sizing: border-box; }
    body { margin: 0; background: #f7f7f8; color: var(--text-main); font-family: -apple-system, BlinkMacSystemFont, "PingFang SC", "Helvetica Neue", Arial, sans-serif; }
    .share-container { max-width: 640px; margin: 0 auto; padding: 16px; }

    /* 顶部奖励卡片 */
    .reward-hero {
        background: linear-gradient(135deg, #ff7a93 0%, #ff5e7b 100%);
        border-radius: 16px;
        padding: 28px 24px;
        color: #fff;
        text-align: center;
        box-shadow: 0 8px 24px rgba(255, 94, 123, 0.25);
    }
    .reward-hero .hero-title { font-size: 22px; font-weight: 700; margin: 0 0 8px; }
    .reward-hero .hero-sub { font-size: 14px; opacity: 0.92; margin: 0 0 20px; line-height: 1.5; }
    .reward-hero .reward-amount { font-size: 40px; font-weight: 800; line-height: 1; }
    .reward-hero .reward-amount small { font-size: 16px; font-weight: 500; margin-left: 4px; }
    .reward-hero .reward-label { font-size: 13px; opacity: 0.9; margin-top: 6px; }

    /* 数据统计 */
    .stat-row { display: flex; gap: 12px; margin-top: -20px; position: relative; z-index: 2; }
    .stat-card {
        flex: 1; background: #fff; border-radius: 14px; padding: 18px 12px; text-align: center;
        box-shadow: 0 4px 16px rgba(0,0,0,0.05);
    }
    .stat-card .num { font-size: 26px; font-weight: 800; color: var(--primary); }
    .stat-card .label { font-size: 13px; color: var(--text-sub); margin-top: 4px; }

    /* 通用卡片 */
    .card { background: #fff; border-radius: 14px; padding: 20px; margin-top: 16px; box-shadow: 0 4px 16px rgba(0,0,0,0.04); }
    .card-title { font-size: 16px; font-weight: 700; margin: 0 0 16px; display: flex; align-items: center; }
    .card-title::before { content: ''; width: 4px; height: 16px; background: var(--primary); border-radius: 2px; margin-right: 8px; }

    /* 邀请码 */
    .invite-code-box {
        display: flex; align-items: center; justify-content: space-between;
        background: var(--primary-light); border: 1px dashed var(--primary);
        border-radius: 10px; padding: 14px 16px;
    }
    .invite-code-box .code { font-size: 24px; font-weight: 800; color: var(--primary); letter-spacing: 3px; }
    .invite-code-box .copy-btn {
        background: var(--primary); color: #fff; border: none; border-radius: 20px;
        padding: 8px 18px; font-size: 14px; font-weight: 600; cursor: pointer;
    }
    .invite-code-box .copy-btn:active { background: var(--primary-dark); }

    /* 邀请链接 */
    .invite-link-box { display: flex; gap: 8px; margin-top: 14px; }
    .invite-link-box input {
        flex: 1; border: 1px solid var(--border); border-radius: 10px; padding: 12px;
        font-size: 13px; color: var(--text-sub); background: #fafafa; min-width: 0;
    }
    .invite-link-box .copy-btn {
        background: #fff; color: var(--primary); border: 1px solid var(--primary);
        border-radius: 10px; padding: 0 16px; font-size: 14px; font-weight: 600; cursor: pointer; white-space: nowrap;
    }

    /* 海报区 */
    .poster-actions { display: flex; gap: 12px; margin-top: 16px; }
    .btn-main, .btn-ghost {
        flex: 1; border-radius: 24px; padding: 13px; font-size: 15px; font-weight: 600;
        cursor: pointer; text-align: center; border: none;
    }
    .btn-main { background: var(--primary); color: #fff; }
    .btn-main:active { background: var(--primary-dark); }
    .btn-ghost { background: #fff; color: var(--primary); border: 1px solid var(--primary); }

    /* 二维码弹层 */
    .qr-mask {
        display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7);
        z-index: 9999; justify-content: center; align-items: center; padding: 24px;
    }
    .qr-mask.active { display: flex; }
    .qr-poster {
        background: #fff; border-radius: 16px; padding: 24px; text-align: center; max-width: 320px; width: 100%;
    }
    .qr-poster .poster-head { font-size: 18px; font-weight: 700; color: var(--primary); margin-bottom: 6px; }
    .qr-poster .poster-desc { font-size: 13px; color: var(--text-sub); margin-bottom: 16px; }
    .qr-poster #qrcode { display: flex; justify-content: center; margin: 0 auto 14px; }
    .qr-poster #qrcode img, .qr-poster #qrcode canvas { border-radius: 8px; }
    .qr-poster .poster-tip { font-size: 12px; color: var(--text-sub); }
    .qr-close { margin-top: 16px; color: var(--text-sub); font-size: 14px; cursor: pointer; }

    /* 规则说明 */
    .rule-list { margin: 0; padding-left: 20px; color: var(--text-sub); font-size: 13px; line-height: 1.9; }

    /* 邀请记录 */
    .invite-item { display: flex; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--border); }
    .invite-item:last-child { border-bottom: none; }
    .invite-item .avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; background: #eee; margin-right: 12px; }
    .invite-item .info { flex: 1; min-width: 0; }
    .invite-item .name { font-size: 14px; font-weight: 600; }
    .invite-item .time { font-size: 12px; color: var(--text-sub); margin-top: 2px; }
    .invite-item .reward { font-size: 14px; font-weight: 700; color: var(--primary); }
    .empty-tip { text-align: center; color: var(--text-sub); font-size: 14px; padding: 24px 0; }
</style>
</head>
<body>
    <?php include 'comm/header.php'; ?>

    <div class="share-container">
        <!-- 奖励主视觉 -->
        <div class="reward-hero">
            <h2 class="hero-title">邀请好友 一起赚积分</h2>
            <p class="hero-sub">每成功邀请一位好友注册<br>你即可获得丰厚积分奖励</p>
            <div class="reward-amount">+<?php echo $rewardJifen; ?><small>积分/人</small></div>
            <div class="reward-label">好友越多，积分越多，多邀多得</div>
        </div>

        <!-- 统计 -->
        <div class="stat-row">
            <div class="stat-card">
                <div class="num"><?php echo $inviteCount; ?></div>
                <div class="label">已邀请好友</div>
            </div>
            <div class="stat-card">
                <div class="num"><?php echo $myJifen; ?></div>
                <div class="label">当前积分</div>
            </div>
        </div>

        <!-- 邀请码 + 链接 -->
        <div class="card">
            <h3 class="card-title">我的专属邀请</h3>
            <div class="invite-code-box">
                <span>邀请码 <span class="code" id="inviteCode"><?php echo $inviteCode; ?></span></span>
                <button class="copy-btn" onclick="copyText('<?php echo $inviteCode; ?>', '邀请码已复制')">复制</button>
            </div>
            <div class="invite-link-box">
                <input type="text" id="inviteUrl" value="<?php echo $inviteUrl; ?>" readonly>
                <button class="copy-btn" onclick="copyText('<?php echo $inviteUrl; ?>', '邀请链接已复制')">复制链接</button>
            </div>
            <div class="poster-actions">
                <button class="btn-main" onclick="showPoster()">生成分享海报</button>
                <button class="btn-ghost" onclick="copyText('<?php echo $inviteUrl; ?>', '邀请链接已复制，去粘贴给好友吧')">分享链接</button>
            </div>
        </div>

        <!-- 规则说明 -->
        <div class="card">
            <h3 class="card-title">活动规则</h3>
            <ol class="rule-list">
                <li>将你的专属邀请码或链接分享给好友。</li>
                <li>好友通过你的链接（或填写邀请码）完成注册。</li>
                <li>好友注册成功后，你立即获得 <?php echo $rewardJifen; ?> 积分奖励。</li>
                <li>邀请人数不限，邀请越多，积分越多。</li>
                <li>积分可用于平台内消费、解锁权益等。</li>
            </ol>
        </div>

        <!-- 邀请记录 -->
        <div class="card">
            <h3 class="card-title">邀请记录</h3>
            <?php if (!empty($inviteList)): ?>
                <?php foreach ($inviteList as $item): ?>
                <div class="invite-item">
                    <img class="avatar" src="<?php echo $item['headpic'] ?: '/images/default_avatar.png'; ?>" alt="头像" onerror="this.src='/images/default_avatar.png'">
                    <div class="info">
                        <div class="name"><?php echo htmlspecialchars($item['uname'] ?: ('用户' . $item['id'])); ?></div>
                        <div class="time"><?php echo !empty($item['addtime']) ? date('Y-m-d H:i', is_numeric($item['addtime']) ? $item['addtime'] : strtotime($item['addtime'])) : ''; ?></div>
                    </div>
                    <div class="reward">+<?php echo $rewardJifen; ?></div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-tip">还没有邀请记录，快去邀请好友吧～</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- 海报二维码弹层 -->
    <div class="qr-mask" id="qrMask">
        <div class="qr-poster">
            <div class="poster-head"><?php echo $webname; ?></div>
            <div class="poster-desc">扫码注册，立即开启同城交友</div>
            <div id="qrcode"></div>
            <div class="poster-tip">长按二维码保存图片分享给好友</div>
            <div class="qr-close" onclick="hidePoster()">关闭</div>
        </div>
    </div>

    <?php include 'comm/footer.php'; ?>
    <?php include 'comm/alert_modal.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script>
        var inviteUrl = '<?php echo $inviteUrl; ?>';
        var qrInited = false;

        // 复制文本（兼容移动端）
        function copyText(text, tip) {
            var input = document.createElement('input');
            input.value = text;
            input.style.position = 'fixed';
            input.style.opacity = '0';
            document.body.appendChild(input);
            input.select();
            input.setSelectionRange(0, 99999);
            var ok = false;
            try { ok = document.execCommand('copy'); } catch (e) { ok = false; }
            document.body.removeChild(input);

            if (navigator.clipboard && !ok) {
                navigator.clipboard.writeText(text).then(function() {
                    notify(tip || '复制成功');
                });
            } else {
                notify(tip || '复制成功');
            }
        }

        // 统一提示（优先用项目已有的 showInfo/showSuccess，否则 alert）
        function notify(msg) {
            if (typeof showSuccess === 'function') { showSuccess(msg, '提示', 1500); }
            else if (typeof showInfo === 'function') { showInfo(msg); }
            else { alert(msg); }
        }

        // 生成并显示海报二维码
        function showPoster() {
            if (!qrInited) {
                new QRCode(document.getElementById('qrcode'), {
                    text: inviteUrl,
                    width: 200,
                    height: 200,
                    colorDark: '#333333',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.H
                });
                qrInited = true;
            }
            document.getElementById('qrMask').classList.add('active');
        }

        function hidePoster() {
            document.getElementById('qrMask').classList.remove('active');
        }

        // 点击遮罩关闭
        document.getElementById('qrMask').addEventListener('click', function(e) {
            if (e.target === this) hidePoster();
        });
    </script>
</body>
</html>
