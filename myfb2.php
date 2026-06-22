<?php 
include_once 'loaduser.php';
include_once 'config.php';

$where = [];
$where[] = "(cname = 'is_by' OR cname = 'is_by2')";

$re = $db->table('fl_config')
    ->where($where)
    ->field('id,value,cname')
    ->select();

$re_data =[];
foreach ($re as $k => $v) {
    $re_data[$v['cname']] = $v['value'];
}

//置顶金额配置
$zd_money_config = $db->table('fl_config')
    ->where(['cname'=>'zd_money'])
    ->field('id,value,cname')
    ->find();
$zd_money_arr = explode('|', $zd_money_config['value']);

// var_dump($zd_money_arr);
$is_a = $re_data['is_by']; // 设为1显示伴游
$is_b = $re_data['is_by2']; // 设为1显示伴友
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>我的发布-<?php echo $webname; ?></title>
<meta name="keywords" content="我的发布-<?php echo $webname; ?>">
<meta name="description" content="我的发布-<?php echo $webname; ?>">
<link href="/css/header.css" type="text/css" rel="stylesheet" media="all" />
<style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* 防止页面左右滑动 */
        html, body {
            max-width: 100%;
            overflow-x: hidden;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "SF Pro Text", "Segoe UI", Roboto, "Helvetica Neue", Arial, "PingFang SC", "Hiragino Sans GB", "Microsoft YaHei UI", "Microsoft YaHei", sans-serif;
            color: #2c3e50;
            background: #fafafa;
            line-height: 1.6;
            padding-top: 56px; /* 调整顶部内边距以适应固定的header */
            padding-bottom: 60px; /* 调整底部内边距以适应固定的footer */
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
            font-feature-settings: "kern" 1;
            font-weight: 400;
        }

        /* 头部导航样式 */
        .header {
            background: linear-gradient(135deg, #ff6b9d 0%, #ffa8c5 100%);
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            box-shadow: 0 2px 12px rgba(255, 107, 157, 0.2);
        }

        .location {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 13px;
            font-weight: 500;
            letter-spacing: 0.2px;
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 20px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(255, 255, 255, 0.2);
        }

        .location:active {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(0.96);
        }

        .location svg {
            width: 16px;
            height: 16px;
        }

        .header-title {
            font-size: 17px;
            font-weight: 600;
            flex: 1;
            text-align: center;
            letter-spacing: 1px;
            color: white;
        }

        .user-icon {
            cursor: pointer;
            padding: 6px;
            border-radius: 50%;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(255, 255, 255, 0.2);
        }

        .user-icon:active {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(0.96);
        }

        .user-icon svg {
            width: 22px;
            height: 22px;
            display: block;
        }

        /* 右侧侧边栏样式 */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.45);
            backdrop-filter: blur(2px);
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: flex-end;
            justify-content: center;
        }

        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .sidebar {
            position: fixed;
            top: 0;
            right: -100%;
            width: 80%;
            max-width: 320px;
            height: 100vh;
            background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
            z-index: 1001;
            transition: right 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: -8px 0 32px rgba(0, 0, 0, 0.12), -4px 0 16px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .sidebar.show {
            right: 0;
        }

        .user-profile {
            padding: 18px 20px;
            background: linear-gradient(135deg, #ff6b9d 0%, #ffa8c5 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
            position: relative;
        }

        .user-profile-close {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 30px;
            height: 30px;
            border: none;
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border-radius: 50%;
            font-size: 18px;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .user-profile-close:hover {
            background: rgba(255, 255, 255, 0.35);
            transform: rotate(90deg);
        }

        .user-profile-close:active {
            background: rgba(255, 255, 255, 0.4);
            transform: rotate(90deg) scale(0.92);
        }

        .user-avatar {
            width: 56px;
            height: 56px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ff6b9d;
            font-size: 24px;
            font-weight: 700;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
            position: relative;
        }

        .user-avatar::after {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            padding: 4px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.6) 0%, rgba(255, 255, 255, 0.3) 100%);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            opacity: 0.5;
        }

        .user-name {
            font-size: 16px;
            font-weight: 600;
            color: white;
            letter-spacing: 0.3px;
        }

        .user-hint {
            font-size: 12px;
            color: white;
            cursor: pointer;
            padding: 5px 16px;
            background: rgba(255, 255, 255, 0.25);
            border-radius: 20px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            font-weight: 500;
            letter-spacing: 0.2px;
        }

        /* 用户提示框hover/active状态样式 */
        .user-hint:hover {
            background: rgba(255, 255, 255, 0.35);
            transform: translateY(-1px);
        }

        .user-hint:active {
            background: rgba(255, 255, 255, 0.45);
            transform: translateY(0);
        }

        /* VIP等级样式 */
        .vip-normal,
        .vip-quarterly,
        .vip-annual,
        .vip-supreme {
            position: relative;
            overflow: hidden;
            padding: 5px 12px;
            border-radius: 16px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.2px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .vip-normal {
            background: linear-gradient(135deg, #a0a0a0 0%, #757575 100%);
            color: white;
        }

        .vip-normal::after {
            content: '';
            position: absolute;
            top: -100%;
            left: -100%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transform: rotate(30deg);
            animation: shine 3s infinite;
        }

        .vip-quarterly {
            background: linear-gradient(135deg, #4caf50 0%, #2e7d32 100%);
            color: white;
        }

        .vip-quarterly::after {
            content: '';
            position: absolute;
            top: -100%;
            left: -100%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transform: rotate(30deg);
            animation: shine 2.5s infinite;
        }

        .vip-annual {
            background: linear-gradient(135deg, #ff9800 0%, #e65100 100%);
            color: white;
        }

        .vip-annual::after {
            content: '';
            position: absolute;
            top: -100%;
            left: -100%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transform: rotate(30deg);
            animation: shine 2s infinite;
        }

        .vip-supreme {
            background: linear-gradient(135deg, #9c27b0 0%, #6a1b9a 100%);
            color: white;
            box-shadow: 0 4px 16px rgba(156, 39, 176, 0.4);
        }

        .vip-supreme::after {
            content: '';
            position: absolute;
            top: -100%;
            left: -100%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transform: rotate(30deg);
            animation: shine 1.5s infinite;
        }

        @keyframes shine {
            0% {
                transform: translateX(-100%) rotate(30deg);
            }
            100% {
                transform: translateX(100%) rotate(30deg);
            }
        }

        /* 侧边栏菜单样式 */
        .sidebar-menu {
            flex: 1;
            overflow-y: auto;
            padding: 16px 0;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            gap: 12px;
            color: #4a4a4a;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .menu-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 0;
            background: linear-gradient(90deg, rgba(255, 107, 157, 0.1) 0%, rgba(255, 107, 157, 0) 100%);
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .menu-item:hover {
            background: rgba(255, 107, 157, 0.05);
            color: #ff6b9d;
            transform: translateX(4px);
        }

        .menu-item:hover::before {
            width: 6px;
        }

        .menu-item svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .menu-item:hover svg {
            transform: scale(1.15);
        }

        .menu-item span {
            font-size: 15px;
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        .menu-item.logout {
            color: #e53935;
            margin-top: 16px;
            border-top: 1px solid rgba(0, 0, 0, 0.08);
        }

        .menu-item.logout:hover {
            background: rgba(229, 57, 53, 0.05);
            color: #c62828;
        }

        /* 区域选择模态框样式 */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.45);
            backdrop-filter: blur(2px);
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: flex-end;
            justify-content: center;
        }

        .modal-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background: white;
            width: 100%;
            max-height: 80vh;
            border-radius: 20px 20px 0 0;
            box-shadow: 0 -8px 32px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            animation: slideUp 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes slideUp {
            from {
                transform: translateY(100%);
            }
            to {
                transform: translateY(0);
            }
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            flex-shrink: 0;
        }

        .modal-header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .modal-back {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.05);
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            color: #4a4a4a;
            opacity: 0;
            visibility: hidden;
            transform: translateX(-8px);
        }

        .modal-back.show {
            opacity: 1;
            visibility: visible;
            transform: translateX(0);
        }

        .modal-back:active {
            background: rgba(0, 0, 0, 0.1);
            transform: scale(0.92);
        }

        .modal-title {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
            flex: 1;
        }

        .modal-close {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.05);
            font-size: 20px;
            color: #7f8c8d;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .modal-close:hover {
            background: rgba(0, 0, 0, 0.1);
            color: #2c3e50;
        }

        .modal-close:active {
            background: rgba(0, 0, 0, 0.15);
            transform: scale(0.92);
        }

        .area-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px 12px;
            padding: 20px;
            overflow-y: auto;
            flex: 1;
        }

        .area-item {
            padding: 10px 8px;
            text-align: center;
            font-size: 14px;
            color: #4a4a4a;
            background: rgba(0, 0, 0, 0.03);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .area-item:hover {
            background: rgba(255, 107, 157, 0.08);
            border-color: rgba(255, 107, 157, 0.3);
            transform: translateY(-1px);
        }

        .area-item:active {
            transform: translateY(0);
        }

        .area-item.active {
            background: linear-gradient(135deg, #ff6b9d 0%, #ff8fb3 100%);
            color: white;
            font-weight: 600;
            border-color: #ff6b9d;
        }

        /* 退出登录确认弹窗样式 */
        .logout-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.45);
            backdrop-filter: blur(2px);
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logout-modal-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .logout-modal-content {
            background: white;
            border-radius: 20px;
            padding: 24px;
            width: 85%;
            max-width: 360px;
            box-shadow: 0 12px 48px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
        }

        .logout-modal-icon {
            width: 64px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logout-modal-icon svg {
            width: 100%;
            height: 100%;
        }

        .logout-modal-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }

        .logout-modal-text {
            font-size: 14px;
            color: #7f8c8d;
            line-height: 1.6;
            text-align: center;
            margin: 0;
        }

        .logout-modal-actions {
            display: flex;
            gap: 12px;
            width: 100%;
        }

        .logout-modal-btn {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0, 0, 0.2, 1);
            letter-spacing: 0.3px;
        }

        .logout-modal-cancel {
            background: #f5f5f5;
            color: #7f8c8d;
        }

        .logout-modal-cancel:hover {
            background: #e8e8e8;
        }

        .logout-modal-cancel:active {
            transform: scale(0.96);
            background: #ddd;
        }

        .logout-modal-confirm {
            background: linear-gradient(135deg, #ff6b9d 0%, #ff8fb3 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(255, 107, 157, 0.3);
        }

        .logout-modal-confirm:hover {
            box-shadow: 0 6px 16px rgba(255, 107, 157, 0.4);
            transform: translateY(-1px);
        }

        .logout-modal-confirm:active {
            transform: translateY(0) scale(0.96);
            box-shadow: 0 2px 8px rgba(255, 107, 157, 0.2);
        }

        /* 底部导航样式 */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, #ffffff 0%, #fefefe 100%);
            border-top: 1px solid rgba(0, 0, 0, 0.06);
            display: flex;
            justify-content: space-around;
            padding: 6px 0 calc(6px + env(safe-area-inset-bottom));
            z-index: 100;
            box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.04),
                        0 -2px 8px rgba(0, 0, 0, 0.02);
            backdrop-filter: blur(10px);
            transform: translateZ(0);
            -webkit-transform: translateZ(0);
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
            cursor: pointer;
            flex: 1;
            color: #95a5a6;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 4px 0;
            position: relative;
            border-radius: 12px;
            margin: 0 4px;
        }

        .nav-item::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 107, 157, 0.08) 0%, rgba(255, 143, 179, 0.05) 100%);
            border-radius: 12px;
            opacity: 0;
            transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-item.active {
            color: #ff6b9d;
            transform: translateY(-2px);
        }

        .nav-item:active {
            transform: translateY(0) scale(0.96);
        }

        .nav-item svg {
            width: 24px;
            height: 24px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            z-index: 1;
            filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0));
        }

        .nav-item.active svg {
            transform: scale(1.1);
            filter: drop-shadow(0 2px 4px rgba(255, 107, 157, 0.25));
        }

        .nav-item span {
            font-size: 10px;
            font-weight: 500;
            position: relative;
            z-index: 1;
            letter-spacing: 0.3px;
        }

        .nav-item.active span {
            font-weight: 600;
            letter-spacing: 0.4px;
        }

        @media (min-width: 769px) {
            .bottom-nav {
                display: none;
            }
        }

        /* 我的发布页面特有样式 */
        .publish-container {
            padding: 10px;
            background: #f8f9fa;
            min-height: calc(100vh - 116px);
        }

        /* 优化筛选标签布局 */
        /* 类型筛选标签放在上方 */
        .filter-tabs-type {
            display: flex;
            gap: 8px;
            margin-bottom: 10px;
            padding: 10px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .filter-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 10px;
            padding: 10px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        /* 添加 filter-tab 完整样式 */
        .filter-tab {
            flex: 1;
            padding: 6px 8px;
            text-align: center;
            font-size: 12px;
            font-weight: 500;
            color: #7f8c8d;
            background: transparent;
            border-radius: 0px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            position: relative;
            white-space: nowrap;
        }

        .filter-tab::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%) scaleX(0);
            width: 60%;
            height: 2px;
            background: linear-gradient(90deg, #ff6b9d 0%, #ff8fb3 100%);
            border-radius: 2px;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .filter-tab.active {
            background: rgba(255, 107, 157, 0.08);
            color: #ff6b9d;
            font-weight: 600;
        }

        .filter-tab.active::after {
/*            transform: translateX(-50%) scaleX(1);*/
        }

        .filter-tab:active {
            transform: scale(0.96);
        }

        /* 重新设计发布列表为图文卡片 */
        /* 改为2列网格布局 */
        .publish-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 10px;
        }

        .publish-item {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .publish-item:active {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 107, 157, 0.15);
        }

        /* 图片容器改为占据卡片顶部 */
        .publish-image-container {
            position: relative;
            width: 100%;
            padding-top: 133.33%; /* 3:4 aspect ratio */
            overflow: hidden;
        }

        .publish-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* 修改按钮改为徽章样式，覆盖在图片右上角 */
        .publish-edit-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background: linear-gradient(135deg, #ff6b9d 0%, #ff8fb3 100%);
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(255, 107, 157, 0.4);
            z-index: 10;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .publish-edit-badge:active {
            transform: scale(0.95);
        }


        /* 置顶按钮改为徽章样式，覆盖在图片右上角 */
        .publish-zd-badge {
            position: absolute;
            top: 8px;
            left: 8px;
            background: linear-gradient(135deg, #ffd700 0%, #ff6b9d 100%);
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(255, 107, 157, 0.4);
            z-index: 10;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .publish-zd-badge:active {
            transform: scale(0.95);
        }


        .publish-izd-badge {
            position: absolute;
            top: 8px;
            left: 8px;
            background: linear-gradient(135deg, #ff6b9d 0%, #ffd700 100%);
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(255, 107, 157, 0.4);
            z-index: 10;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .publish-izd-badge:active {
            transform: scale(0.95);
        }



        /* a标签包裹内容区域 */
        .publish-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .publish-info {
            padding: 10px;
            display: flex;
            flex-direction: column;
        }

        .publish-title {
            font-size: 12px;
            font-weight: 500;
            color: #2c3e50;
            line-height: 1.4;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            margin-bottom: 6px;
        }

        .publish-price {
            font-size: 16px;
            font-weight: 700;
            color: #ff6b9d;
            line-height: 1.3;
        }

        .publish-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11px;
            color: #95a5a6;
            margin-top: auto;
        }

        .publish-date {
            font-weight: 400;
        }

        .publish-status {
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 600;
        }

        .status-approved {
            background: rgba(76, 175, 80, 0.1);
            color: #4caf50;
        }

        .status-reviewing {
            background: rgba(33, 150, 243, 0.1);
            color: #2196f3;
        }

        .status-rejected {
            background: rgba(244, 67, 54, 0.1);
            color: #f44336;
        }

        /* 复制list.html的分页样式 */
        /* 简化分页，只显示上一页、当前页码、下一页 */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px 0;
            margin-top: 10px;
        }

        .page-btn {
            margin-right:10px;
            min-width: 36px;
            height: 36px;
            padding: 0 12px;
            background: white;
            border: 1.5px solid #e8eaed;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #5f6368;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .page-btn:hover {
            border-color: #ff6b9d;
            color: #ff6b9d;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(255, 107, 157, 0.1);
        }

        .page-btn.active {
            background: linear-gradient(135deg, #ff6b9d 0%, #ff8fb3 100%);
            color: white;
            border-color: transparent;
            box-shadow: 0 2px 8px rgba(255, 107, 157, 0.25);
            font-weight: 600;
        }

        .page-btn:active {
            transform: translateY(0) scale(0.96);
        }

        .page-btn.dots {
            border: none;
            background: transparent;
            cursor: default;
            pointer-events: none;
            color: #bdc3c7;
            min-width: 24px;
        }

        .page-btn.prev,
        .page-btn.next {
            padding: 0 10px;
            font-size: 12px;
        }

        .page-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* 置顶弹窗样式 */
        /* 优化弹窗整体样式，增加动画和精致感 */
        .zhiding-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .zhiding-modal-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .zhiding-modal-content {
            background: linear-gradient(180deg, #ffffff 0%, #fef9fa 100%);
            border-radius: 24px;
            padding: 0;
            width: 85%;
            max-width: 360px;
            box-shadow: 0 20px 60px rgba(255, 107, 157, 0.2), 0 8px 24px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transform: scale(0.9) translateY(20px);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .zhiding-modal-overlay.show .zhiding-modal-content {
            transform: scale(1) translateY(0);
        }

        /* 添加渐变头部装饰 */
        .zhiding-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 20px 16px;
            background: linear-gradient(135deg, #ff6b9d 0%, #ffa8c5 100%);
            position: relative;
        }

        .zhiding-modal-header::after {
            content: '';
            position: absolute;
            bottom: -12px;
            left: 0;
            right: 0;
            height: 24px;
            background: linear-gradient(180deg, #ffffff 0%, #fef9fa 100%);
            border-radius: 24px 24px 0 0;
        }

        .zhiding-modal-title {
            font-size: 18px;
            font-weight: 600;
            color: #ffffff;
            margin: 0;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            letter-spacing: 0.5px;
        }

        .zhiding-modal-close {
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.25);
            font-size: 18px;
            color: #ffffff;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            line-height: 1;
        }

        .zhiding-modal-close:hover {
            background: rgba(255, 255, 255, 0.4);
            transform: rotate(90deg);
        }

        .zhiding-modal-close:active {
            transform: rotate(90deg) scale(0.9);
        }

        /* 优化选项卡片样式 */
        .zhiding-options {
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        .zhiding-option {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            background: #ffffff;
            border: 2px solid #f0f0f0;
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .zhiding-option::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 107, 157, 0.08) 0%, rgba(255, 168, 197, 0.08) 100%);
            opacity: 0;
            transition: opacity 0.25s ease;
        }

        .zhiding-option:hover {
            border-color: rgba(255, 107, 157, 0.4);
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(255, 107, 157, 0.15);
        }

        .zhiding-option:hover::before {
            opacity: 1;
        }

        .zhiding-option:active {
            transform: translateX(4px) scale(0.98);
        }

        .zhiding-option.selected {
            border-color: #ff6b9d;
            background: linear-gradient(135deg, rgba(255, 107, 157, 0.1) 0%, rgba(255, 168, 197, 0.1) 100%);
            box-shadow: 0 4px 16px rgba(255, 107, 157, 0.2);
        }

        .zhiding-option.selected::after {
            content: '✓';
            position: absolute;
            top: 8px;
            right: 8px;
            width: 18px;
            height: 18px;
            background: linear-gradient(135deg, #ff6b9d 0%, #ffa8c5 100%);
            border-radius: 50%;
            color: white;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .zhiding-option-left {
            display: flex;
            flex-direction: column;
            gap: 2px;
            position: relative;
            z-index: 1;
        }

        .zhiding-option-duration {
            font-size: 15px;
            font-weight: 600;
            color: #2c3e50;
        }

        .zhiding-option-desc {
            font-size: 11px;
            color: #95a5a6;
        }

        .zhiding-option-price {
            font-size: 22px;
            font-weight: 700;
            background: linear-gradient(135deg, #e4e313  0%, #ff6b9d 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
            z-index: 1;
        }

        .zhiding-option-price::before {
            content: '¥';
            font-size: 13px;
            margin-right: 1px;
        }

        /* 优化提交按钮样式，与页面主色调一致 */
        .zhiding-submit-btn {
            width: calc(100% - 40px);
            margin: 0 20px 20px;
            padding: 14px 20px;
            background: linear-gradient(135deg, #ff6b9d 0%, #ffa8c5 100%);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 16px rgba(255, 107, 157, 0.3);
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
        }

        .zhiding-submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .zhiding-submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(255, 107, 157, 0.4);
        }

        .zhiding-submit-btn:hover::before {
            left: 100%;
        }

        .zhiding-submit-btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(255, 107, 157, 0.3);
        }

        .zhiding-submit-btn:disabled {
            background: linear-gradient(135deg, #bdc3c7 0%, #dde1e3 100%);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
    </style>
</head>
<body>
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
    </style>
    <header class="header">
        <div class="back-btn" onclick="window.location.href='/user.html';">
            <svg fill="currentColor" viewBox="0 0 24 24">
                <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
            </svg>
        </div>
        <h1 class="header-title">我的发布</h1>
        <div class="header-spacer"></div>
    </header>

    <!-- 主要内容区域 -->
    <div class="publish-container">
        <!-- 类型筛选标签移到上方 - 论坛、高端、伴游、伴友 -->
        <div class="filter-tabs-type">
            <div class="filter-tab active" data-typeinfo="1">论坛</div>
            <div class="filter-tab" data-typeinfo="2">高端</div>
            <?php if($is_a == 1): ?>
            <div class="filter-tab" data-typeinfo="3">伴游</div>
            <?php endif; ?>
            <?php if($is_b == 1): ?>
            <div class="filter-tab" data-typeinfo="4">包养</div>
            <?php endif; ?>
        </div>

        <!-- 状态筛选标签 -->
        <div class="filter-tabs">
            <div class="filter-tab active" data-status="-1">全部</div>
            <div class="filter-tab" data-status="0">审核中</div>
            <div class="filter-tab" data-status="1">审核通过</div>
            
            <div class="filter-tab" data-status="2">审核拒绝</div>
        </div>

        <!-- 发布列表改为2列网格布局 -->
        <div class="publish-list">
          
        </div>

        <!-- 简化分页组件 -->
        <div class="pagination">
            <button class="page-btn prev" disabled>上一页</button>
            <button class="page-btn active">1</button>
            <button class="page-btn next">下一页</button>
        </div>
    </div>

    <?php //include_once 'comm/footer.php'; ?>

    <!-- 引入JavaScript文件 -->
    <script src="/js/member_publish.js?t=123"></script>

    <!-- 置顶包月弹窗 -->
    <div class="zhiding-modal-overlay" id="zhidingModal">
        <div class="zhiding-modal-content">
            <div class="zhiding-modal-header">
                <h3 class="zhiding-modal-title">置顶包月</h3>
                <button class="zhiding-modal-close" onclick="closeZhidingModal()">&times;</button>
            </div>
            <!-- 添加隐藏字段存储信息id -->
            <input type="hidden" id="zhidingInfoId" value="">
            <input type="hidden" id="zhidinginfotype" value="">

            
            <div class="zhiding-options">
                <div class="zhiding-option" data-duration="1" data-price="<?php echo $zd_money_arr[0]; ?>">
                    <div class="zhiding-option-left">
                        <span class="zhiding-option-duration">一个月</span>
                        <span class="zhiding-option-desc">置顶30天</span>
                    </div>
                    <span class="zhiding-option-price"><?php echo $zd_money_arr[0]; ?></span>
                </div>
                <div class="zhiding-option" data-duration="3" data-price="<?php echo $zd_money_arr[1]; ?>">
                    <div class="zhiding-option-left">
                        <span class="zhiding-option-duration">一季度</span>
                        <span class="zhiding-option-desc">置顶90天</span>
                    </div>
                    <span class="zhiding-option-price"><?php echo $zd_money_arr[1]; ?></span>
                </div>
                <div class="zhiding-option" data-duration="6" data-price="<?php echo $zd_money_arr[2]; ?>">
                    <div class="zhiding-option-left">
                        <span class="zhiding-option-duration">半年</span>
                        <span class="zhiding-option-desc">置顶180天</span>
                    </div>
                    <span class="zhiding-option-price"><?php echo $zd_money_arr[2]; ?></span>
                </div>
                <div class="zhiding-option" data-duration="12" data-price="<?php echo $zd_money_arr[3]; ?>">
                    <div class="zhiding-option-left">
                        <span class="zhiding-option-duration">一年</span>
                        <span class="zhiding-option-desc">置顶365天</span>
                    </div>
                    <span class="zhiding-option-price"><?php echo $zd_money_arr[3]; ?></span>
                </div>
            </div>
            <!-- 添加提交按钮 -->
            <button class="zhiding-submit-btn" id="zhidingSubmitBtn" onclick="submitZhiding()">确认提交</button>
        </div>
    </div>

    <script>
    // 置顶弹窗相关变量
    var selectedZhidingOption = null;
    
    function openZhidingModal(infoId,infotype) {
        document.getElementById('zhidingInfoId').value = infoId;
        document.getElementById('zhidinginfotype').value = infotype;
        document.getElementById('zhidingModal').classList.add('show');
        // 重置选项状态
        selectedZhidingOption = null;
        var options = document.querySelectorAll('.zhiding-option');
        options.forEach(function(opt) {
            opt.classList.remove('selected');
        });
    }
    
    function closeZhidingModal() {
        document.getElementById('zhidingModal').classList.remove('show');
    }
    
    // 点击遮罩层关闭弹窗
    document.getElementById('zhidingModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeZhidingModal();
        }
    });
    
    document.querySelectorAll('.zhiding-option').forEach(function(option) {
        option.addEventListener('click', function() {
            // 移除其他选项的选中状态
            document.querySelectorAll('.zhiding-option').forEach(function(opt) {
                opt.classList.remove('selected');
            });
            // 添加当前选项的选中状态
            this.classList.add('selected');
            selectedZhidingOption = {
                duration: this.getAttribute('data-duration'),
                price: this.getAttribute('data-price')
            };
        });
    });
    
    function submitZhiding() {
        if (!selectedZhidingOption) {
            showInfo('请选择置顶时长');
            return;
        }
        var infoId = document.getElementById('zhidingInfoId').value;
        var infotype = document.getElementById('zhidinginfotype').value;
        
        var submitBtn = document.getElementById('zhidingSubmitBtn');
        
        // 禁用按钮防止重复提交
        submitBtn.disabled = true;
        submitBtn.innerText = '提交中...';
        
        // 使用异步POST请求提交到 oper/user/zd.php 接口
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'oper/user/zd.html', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                submitBtn.disabled = false;
                submitBtn.innerText = '确认提交';
                
                if (xhr.status === 200) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.code === 200 || response.success) {
                            showSuccess(response.data.message || '置顶成功','置顶成功',40000,'member_publish.html');
                            closeZhidingModal();
                            // 刷新页面或更新状态
                            // location.reload();
                        } else {
                            showInfo(response.msg ||response.message|| '操作失败');
                        }
                    } catch (e) {
                        // 如果返回不是JSON，直接显示结果
                        showInfo(xhr.responseText || '操作完成');
                        closeZhidingModal();
                        // location.reload();
                    }
                } else {
                    showInfo('请求失败，请稍后重试');
                }
            }
        };
        
        xhr.onerror = function() {
            submitBtn.disabled = false;
            submitBtn.innerText = '确认提交';
            alert('网络错误，请稍后重试');
        };
        
        // 发送POST数据
        var postData = 'id=' + encodeURIComponent(infoId) + 
                        '&infotype=' + encodeURIComponent(infotype) + 
                       '&duration=' + encodeURIComponent(selectedZhidingOption.duration) + 
                       '&price=' + encodeURIComponent(selectedZhidingOption.price);
        xhr.send(postData);
    }
    </script>

<?php 
include_once 'comm/alert_modal.php';
?>

</body>
</html>
