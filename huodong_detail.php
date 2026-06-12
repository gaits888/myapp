<?php
include_once 'loaduser.php';
include_once 'config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: /huodong.php');
    exit;
}

$hd = db('fl_huodong')->where('id', $id)->where('status', 1)->find();
if (empty($hd)) {
    header('Location: /huodong.php');
    exit;
}

// 浏览量 +1
db('fl_huodong')->where('id', $id)->setInc('views');

// 是否已报名
$hasJoined = false;
if (!empty($user_id)) {
    $joinCount = db('fl_huodong_join')->where('hd_id', $id)->where('user_id', $user_id)->count();
    $hasJoined = $joinCount > 0;
}

// 活动状态
$now = time();
$isEnded = ($hd['end_time'] > 0 && $now > $hd['end_time']);
$isFull = ($hd['max_count'] > 0 && $hd['join_count'] >= $hd['max_count']);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="keywords" content="<?php echo htmlspecialchars($hd['tag'] . '_' . $hd['city'] . '同城活动'); ?>_<?php echo $webname; ?>">
<meta name="description" content="<?php echo htmlspecialchars($hd['subtitle'] ?: $hd['title']); ?>_<?php echo $webname; ?>">
<title><?php echo htmlspecialchars($hd['title']); ?>_<?php echo $webname; ?></title>
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
    body { margin: 0; background: #f7f7f8; color: var(--text-main); font-family: -apple-system, BlinkMacSystemFont, "PingFang SC", "Helvetica Neue", Arial, sans-serif; padding-bottom: 80px; }
    .detail-container { max-width: 640px; margin: 0 auto; }

    .detail-cover { position: relative; width: 100%; padding-top: 52%; overflow: hidden; background: linear-gradient(135deg, #ffd9e1, #ffeef2); }
    .detail-cover img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; }
    .detail-badge {
        position: absolute; top: 14px; left: 14px; padding: 5px 14px; border-radius: 20px;
        font-size: 12px; font-weight: 600; color: #fff;
    }
    .detail-badge.ongoing { background: #ff5e7b; }
    .detail-badge.upcoming { background: #ff9f43; }
    .detail-badge.ended { background: #aaa; }

    .detail-body { background: #fff; padding: 20px 16px; }
    .detail-title { font-size: 21px; font-weight: 700; margin: 0 0 10px; line-height: 1.4; }
    .detail-sub { font-size: 14px; color: var(--text-sub); margin: 0 0 16px; line-height: 1.5; }

    .info-list { border-top: 1px solid var(--border); padding-top: 16px; }
    .info-row { display: -webkit-box; display: -webkit-flex; display: -ms-flexbox; display: flex; -webkit-box-align: center; -webkit-align-items: center; -ms-flex-align: center; align-items: center; margin-bottom: 14px; font-size: 14px; }
    .info-row:last-child { margin-bottom: 0; }
    .info-row .label { color: var(--text-sub); width: 72px; flex: 0 0 auto; }
    .info-row .value { color: var(--text-main); flex: 1; font-weight: 500; }
    .info-row .value.city-label { color: var(--primary); font-weight: 600; }

    .section { background: #fff; margin-top: 12px; padding: 20px 16px; }
    .section-title { font-size: 16px; font-weight: 700; margin: 0 0 14px; display: flex; align-items: center; }
    .section-title::before { content: ''; width: 4px; height: 16px; background: var(--primary); border-radius: 2px; margin-right: 8px; }
    .detail-content { font-size: 14px; line-height: 1.8; color: #555; }
    .detail-content img { max-width: 100%; height: auto; border-radius: 8px; }

    /* 底部报名栏 */
    .join-bar {
        position: fixed; bottom: 0; left: 0; right: 0; background: #fff;
        box-shadow: 0 -2px 12px rgba(0,0,0,0.08); padding: 12px 16px;
        display: -webkit-box; display: -webkit-flex; display: -ms-flexbox; display: flex;
        -webkit-box-align: center; -webkit-align-items: center; -ms-flex-align: center; align-items: center; gap: 14px; z-index: 100;
    }
    .join-bar .count-info { flex: 0 0 auto; }
    .join-bar .count-info .num { font-size: 18px; font-weight: 800; color: var(--primary); }
    .join-bar .count-info .txt { font-size: 12px; color: var(--text-sub); }
    .join-bar .join-btn {
        flex: 1; background: var(--primary); color: #fff; border: none; border-radius: 24px;
        padding: 13px; font-size: 16px; font-weight: 600; cursor: pointer;
    }
    .join-bar .join-btn:active { background: var(--primary-dark); }
    .join-bar .join-btn.disabled { background: #ccc; cursor: not-allowed; }

    /* 报名弹层 */
    .join-mask { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 9999; -webkit-box-align: end; -webkit-align-items: flex-end; -ms-flex-align: end; align-items: flex-end; }
    .join-mask.active { display: -webkit-box; display: -webkit-flex; display: -ms-flexbox; display: flex; }
    .join-sheet { background: #fff; width: 100%; border-radius: 16px 16px 0 0; padding: 24px 18px; }
    .join-sheet h3 { font-size: 18px; font-weight: 700; margin: 0 0 18px; text-align: center; }
    .join-field { margin-bottom: 14px; }
    .join-field label { display: block; font-size: 13px; color: var(--text-sub); margin-bottom: 6px; }
    .join-field input {
        width: 100%; border: 1px solid var(--border); border-radius: 10px; padding: 12px; font-size: 14px; background: #fafafa;
    }
    .join-sheet .submit-btn { width: 100%; background: var(--primary); color: #fff; border: none; border-radius: 24px; padding: 13px; font-size: 16px; font-weight: 600; margin-top: 8px; cursor: pointer; }
    .join-sheet .cancel-btn { width: 100%; background: none; color: var(--text-sub); border: none; padding: 12px; font-size: 14px; margin-top: 4px; cursor: pointer; }
</style>
</head>
<body>
    <?php include 'comm/header.php'; ?>

    <div class="detail-container">
        <div class="detail-cover">
            <img src="<?php echo $hd['cover'] ?: '/images/hd_default.jpg'; ?>" alt="<?php echo htmlspecialchars($hd['title']); ?>" onerror="this.src='/images/hd_default.jpg'">
            <?php
            if ($isEnded) { echo '<span class="detail-badge ended">已结束</span>'; }
            elseif ($hd['start_time'] > 0 && $now < $hd['start_time']) { echo '<span class="detail-badge upcoming">报名中</span>'; }
            else { echo '<span class="detail-badge ongoing">进行中</span>'; }
            ?>
        </div>

        <div class="detail-body">
            <h1 class="detail-title"><?php echo htmlspecialchars($hd['title']); ?></h1>
            <p class="detail-sub"><?php echo htmlspecialchars($hd['subtitle']); ?></p>
            <div class="info-list">
                <div class="info-row">
                    <span class="label">活动时间</span>
                    <span class="value"><?php echo $hd['start_time'] > 0 ? date('Y年m月d日 H:i', $hd['start_time']) : '待定'; ?><?php echo $hd['end_time'] > 0 ? ' - ' . date('H:i', $hd['end_time']) : ''; ?></span>
                </div>
                <div class="info-row">
                    <span class="label">活动城市</span>
                    <span class="value city-label"><?php echo htmlspecialchars($hd['city']); ?></span>
                </div>
                <div class="info-row">
                    <span class="label">活动地点</span>
                    <span class="value"><?php echo htmlspecialchars($hd['address'] ?: '报名后通知'); ?></span>
                </div>
                <?php if (!empty($hd['tag'])): ?>
                <div class="info-row">
                    <span class="label">活动标签</span>
                    <span class="value"><?php echo htmlspecialchars($hd['tag']); ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">活动详情</h2>
            <div class="detail-content"><?php echo $hd['content']; ?></div>
        </div>
    </div>

    <!-- 底部报名栏 -->
    <div class="join-bar">
        <div class="count-info">
            <div><span class="num"><?php echo intval($hd['join_count']); ?></span><span class="txt">/<?php echo $hd['max_count'] > 0 ? $hd['max_count'] : '不限'; ?>人</span></div>
            <div class="txt">已报名</div>
        </div>
        <?php if ($isEnded): ?>
            <button class="join-btn disabled" disabled>活动已结束</button>
        <?php elseif ($hasJoined): ?>
            <button class="join-btn disabled" disabled>您已报名</button>
        <?php elseif ($isFull): ?>
            <button class="join-btn disabled" disabled>名额已满</button>
        <?php else: ?>
            <button class="join-btn" onclick="openJoin()">立即报名</button>
        <?php endif; ?>
    </div>

    <!-- 报名弹层 -->
    <div class="join-mask" id="joinMask">
        <div class="join-sheet">
            <h3>活动报名</h3>
            <div class="join-field">
                <label>昵称</label>
                <input type="text" id="joinName" placeholder="请输入您的昵称" maxlength="20">
            </div>
            <div class="join-field">
                <label>联系电话</label>
                <input type="tel" id="joinMobile" placeholder="请输入联系电话" maxlength="11">
            </div>
            <div class="join-field">
                <label>备注（选填）</label>
                <input type="text" id="joinRemark" placeholder="如有特殊需求可备注" maxlength="100">
            </div>
            <button class="submit-btn" id="joinSubmit" onclick="submitJoin()">确认报名</button>
            <button class="cancel-btn" onclick="closeJoin()">取消</button>
        </div>
    </div>

    <?php include 'comm/footer.php'; ?>
    <?php include 'comm/alert_modal.php'; ?>

    <script src="/js/jquery-3.5.1.min.js"></script>
    <script>
        var hdId = <?php echo $id; ?>;
        var isLogin = <?php echo !empty($user_id) ? 'true' : 'false'; ?>;

        function notify(msg) {
            if (typeof showInfo === 'function') { showInfo(msg); }
            else { alert(msg); }
        }

        function openJoin() {
            if (!isLogin) {
                notify('请先登录后再报名');
                setTimeout(function() { location.href = '/login.php'; }, 1200);
                return;
            }
            document.getElementById('joinMask').classList.add('active');
        }

        function closeJoin() {
            document.getElementById('joinMask').classList.remove('active');
        }

        document.getElementById('joinMask').addEventListener('click', function(e) {
            if (e.target === this) closeJoin();
        });

        function submitJoin() {
            var name = $('#joinName').val().trim();
            var mobile = $('#joinMobile').val().trim();
            var remark = $('#joinRemark').val().trim();

            if (!name) { notify('请输入昵称'); return; }
            if (!/^1[3-9]\d{9}$/.test(mobile)) { notify('请输入正确的手机号'); return; }

            var $btn = $('#joinSubmit');
            $btn.prop('disabled', true).text('提交中...');

            $.ajax({
                url: '/opers/huodong/join.html',
                type: 'POST',
                dataType: 'json',
                data: { hd_id: hdId, uname: name, mobile: mobile, remark: remark },
                success: function(res) {
                    if (res.code === 200) {
                        if (typeof showSuccess === 'function') {
                            showSuccess(res.msg || '报名成功！', '报名成功', 2000);
                        } else { alert('报名成功！'); }
                        closeJoin();
                        setTimeout(function() { location.reload(); }, 1500);
                    } else {
                        notify(res.msg || '报名失败');
                        $btn.prop('disabled', false).text('确认报名');
                    }
                },
                error: function() {
                    notify('网络错误，请重试');
                    $btn.prop('disabled', false).text('确认报名');
                }
            });
        }
    </script>
</body>
</html>
