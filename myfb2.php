<?php 
include_once 'loaduser.php';
include_once 'config.php';

$page_title = "我的发布";
$history_url = '/user.html';

$zdPackages = [
    ['duration' => '一个月', 'price' => 298, 'months' => 1],
    ['duration' => '一季度', 'price' => 498, 'months' => 3],
    ['duration' => '半年', 'price' => 798, 'months' => 6],
    ['duration' => '一年', 'price' => 998, 'months' => 12]
];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title><?php echo $webname.'-'.$page_title; ?></title>
<meta name="author" content="<?php echo $webname; ?>" />
<meta name="keywords" content="<?php echo $webname.'-'.$page_title; ?>">
<meta name="description" content="<?php echo $webname.'-'.$page_title; ?>">
<link href="/favicon.ico" rel="shortcut icon"/>
<link rel="stylesheet" href="/css/footer.css">
<style>
    /* 全局样式重置 */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'PingFang SC', 'Hiragino Sans GB', 'Microsoft YaHei', sans-serif;
      background: linear-gradient(135deg, #0d0d19 0%, #1a1a2e 50%, #16213e 100%);
      color: #fff;
/*      min-height: 100vh;*/
      overflow-x: hidden;
      padding-top: 50px;
    }

    /* 页面头部导航 */
    
    /* 添加大分类标签栏 */
    .main-category-section {
/*      padding: 10px;*/
      background: rgba(15, 20, 25, 0.6);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .main-category-tabs {
      display: flex;
      justify-content: center;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      scrollbar-width: none;
      padding: 10px;
      background: linear-gradient(135deg, rgba(30, 30, 50, 0.6), rgba(20, 20, 35, 0.6));
      border-radius: 5px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }

    .main-category-tabs::-webkit-scrollbar {
      display: none;
    }

    .main-category-btn {
      width: 23%;
      padding: 8px 10px;
      border: none;
      border-radius: 5px;
      font-size: 14px;
      cursor: pointer;
      transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
      background: linear-gradient(145deg, rgba(45, 45, 65, 0.8), rgba(30, 30, 45, 0.8));
      color: rgba(255, 255, 255, 0.7);
      white-space: nowrap;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.1);
      letter-spacing: 0.5px;
      margin-left: 5px;
      margin-right:5px;
      text-align: center;
    }

    .main-category-btn:hover {
      background: linear-gradient(145deg, rgba(60, 60, 85, 0.9), rgba(45, 45, 65, 0.9));
      border-color: rgba(139, 92, 246, 0.4);
      box-shadow: 0 4px 16px rgba(139, 92, 246, 0.25);
    }

    .main-category-btn.active {
    background: linear-gradient(135deg, rgb(246 92 239 / 50%) 0%, rgb(124 58 237 / 0%) 100%);
    border: 0;
      color: #fff;
      border: 0;
/*      box-shadow: 0 6px 24px rgba(139, 92, 246, 0.5), 0 0 0 3px rgba(139, 92, 246, 0.15);*/
      border-color: rgba(139, 92, 246, 0.6);
/*      transform: translateY(-2px) scale(1.05);*/
    }

    /* 发布容器 */
    .publish-container {
/*      padding: 16px;*/
      background: transparent;
      min-height: calc(100vh - 56px - 70px);
    }

    /* 重新调整筛选标签样式，保持与大分类一致的风格 */
    .filter-tabs {
      display: flex;
      justify-content: center;
      padding: 10px;
/*      margin-bottom: 10px;*/
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      scrollbar-width: none;
      backdrop-filter: blur(10px);
      border-radius: 5px;
      box-shadow: 0 3px 15px rgba(0, 0, 0, 0.25);
    }

    .filter-tabs::-webkit-scrollbar {
      display: none;
    }

    .filter-tab {
      width: 23%;
      text-align: center;
      padding: 8px 0px 8px 0px;
      color: rgba(255, 255, 255, 0.7);
      font-size: 13px;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      white-space: nowrap;
      border-radius: 5px;
      background: linear-gradient(145deg, rgba(40, 40, 60, 0.6), rgba(30, 30, 45, 0.6));
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.08);
      letter-spacing: 0.3px;
      margin-left: 5px;
      margin-right: 5px;
    }

    .filter-tab:hover {
      background: linear-gradient(145deg, rgba(55, 55, 75, 0.7), rgba(40, 40, 60, 0.7));
      border-color: rgba(139, 92, 246, 0.35);
      box-shadow: 0 3px 12px rgba(139, 92, 246, 0.2);
    }

    .filter-tab.active {
      background: linear-gradient(135deg, rgb(246 92 239 / 50%) 0%, rgb(124 58 237 / 0%) 100%);
      border: none;
      color: #fff;
      border-color: rgba(139, 92, 246, 0.5);
    }

    /* 发布列表 - 2列网格布局 */
    .publish-list {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 10px;
      margin-bottom: 10px;
    }

    /* 发布项 */
    .publish-item {
      background: linear-gradient(135deg, rgba(26, 26, 46, 0.9), rgba(45, 45, 68, 0.9));
      backdrop-filter: blur(10px);
      border-radius: 5px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
      transition: transform 0.3s, box-shadow 0.3s;
      position: relative;
      border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .publish-item:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.5);
    }

    /* 发布图片容器 */
    .publish-image-container {
      position: relative;
      width: 100%;
      padding-top: 100%; /* 1:1 宽高比 */
      overflow: hidden;
    }

    .publish-image {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s;
    }

    .publish-item:hover .publish-image {
      transform: scale(1.08);
    }

    /* 修改按钮 */
    .publish-edit-badge {
      position: absolute;
      top: 8px;
      right: 8px;
      background: rgba(246 92 239 / 50%);
      color: white;
      padding: 4px 10px;
      border-radius: 5px;
      font-size: 11px;
      font-weight: 500;
      text-decoration: none;
      z-index: 10;
      transition: all 0.3s;
      backdrop-filter: blur(10px);
      box-shadow: 0 2px 8px rgba(139, 92, 246, 0.4);
    }

    .publish-edit-badge:hover {
      background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
      transform: scale(1.05);
      box-shadow: 0 4px 12px rgba(139, 92, 246, 0.6);
    }

    /* 置顶按钮 */
    .publish-pin-badge {
      opacity:1;
      position: absolute;
      top: 8px;
      left: 8px;
      background: rgba(233 89 43 / 80%);
      color: white;
      padding: 4px 10px;
      border-radius: 5px;
      font-size: 11px;
      font-weight: 500;
      cursor: pointer;
      z-index: 10;
      transition: all 0.3s;
      backdrop-filter: blur(10px);
/*      box-shadow: 0 2px 8px rgba(255, 107, 107, 0.5);*/
      border: none;
    }

    .publish-pin-badgeo {
      opacity:1;
      position: absolute;
      top: 8px;
      left: 8px;
      background:linear-gradient(135deg, #ee5a6f 0%, rgb(255 107 107 / 76%) 100%);
      color: white;
      padding: 4px 10px;
      border-radius: 5px;
      font-size: 11px;
      font-weight: 500;
      cursor: pointer;
      z-index: 10;
      transition: all 0.3s;
      backdrop-filter: blur(10px);
/*      box-shadow: 0 2px 8px rgba(255, 107, 107, 0.5);*/
      border: none;
    }

    .publish-pin-badge:hover {
      background: linear-gradient(135deg, #ee5a6f 0%, #6d28d9 100%);
      transform: scale(1.05);
      box-shadow: 0 4px 12px rgba(255, 107, 107, 0.7);
    }

    .publish-pin-badge.active {
      cursor: not-allowed;
      opacity: 0.7;
    }

    /* 发布信息 */
    .publish-info {
      padding: 14px;
      display: flex;
      flex-direction: column;
      background: rgba(15, 20, 25, 0.5);
    }

    .publish-link {
      text-decoration: none;
      color: inherit;
    }

    .publish-title {
      font-size: 14px;
      color: #fff;
      line-height: 1.4;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      margin-bottom: 5px;
    }

    .publish-price {
      font-size: 14px;
/*      font-weight: 600;*/
      background: linear-gradient(135deg, #8b5cf6, #6d28d9);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      line-height: 1;
    }

    .publish-meta {
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-size: 12px;
      color: rgba(255, 255, 255, 0.6);
      margin-top: auto;
    }

    .publish-date {
      font-weight: 400;
    }

    .publish-status {
      padding: 2px 8px;
      border-radius: 3px;
      font-size: 11px;
    }

    /* 不同状态的样式 */
    .status-approved {
      background: rgba(34, 197, 94, 0.2);
      color: #22c55e;
      border: 1px solid rgba(34, 197, 94, 0.3);
    }

    .status-reviewing {
      background: rgba(249, 115, 22, 0.2);
      color: #f97316;
      border: 1px solid rgba(249, 115, 22, 0.3);
    }

    .status-rejected {
      background: rgba(239, 68, 68, 0.2);
      color: #ef4444;
      border: 1px solid rgba(239, 68, 68, 0.3);
    }

    /* 拒绝理由 */
    .publish-reason {
      margin-top: 8px;
      padding: 6px 8px;
      background: rgba(239, 68, 68, 0.12);
      border: 1px solid rgba(239, 68, 68, 0.25);
      border-radius: 4px;
      font-size: 11px;
      line-height: 1.4;
      color: #fca5a5;
      word-break: break-all;
    }

    .publish-reason-label {
      color: #ef4444;
      font-weight: 600;
      margin-right: 2px;
    }

    /* 分页样式 */
    .pagination {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 10px 0;
    }

    .page-btn {
      min-width: 36px;
      height: 36px;
      padding: 0 12px;
      background: rgba(26, 32, 44, 0.8);
      backdrop-filter: blur(10px);
      border: 1.5px solid rgba(255, 255, 255, 0.1);
      border-radius: 5px;
      font-size: 14px;
      font-weight: 500;
      color: rgba(255, 255, 255, 0.8);
      cursor: pointer;
      transition: all 0.25s;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-left: 5px;
      margin-right: 5px;
    }

    .page-btn:hover:not(:disabled) {
      border-color: #8b5cf6;
      color: #8b5cf6;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
    }

    .page-btn.active {
      background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 50%, #6d28d9 100%);
      color: white;
      border-color: transparent;
      box-shadow: 0 4px 12px rgba(139, 92, 246, 0.5);
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
      color: rgba(255, 255, 255, 0.4);
      min-width: 24px;
    }

    .page-btn.prev,
    .page-btn.next {
      padding: 0 10px;
      font-size: 14px;
    }

    .page-btn:disabled {
      opacity: 0.4;
      cursor: not-allowed;
      pointer-events: none;
    }

    /* 无数据状态 */
    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: rgba(255, 255, 255, 0.5);
      grid-column: 1 / -1;
    }

    .empty-state-icon {
      width: 60px;
      height: 60px;
      margin: 0 auto 2px;
      opacity: 0.4;
    }

    .empty-state-text {
      font-size: 15px;
      margin-bottom: 16px;
    }

    .empty-state-button {
      background: linear-gradient(135deg, #6366f1, #4f46e5);
      color: white;
      border: none;
      padding: 10px 24px;
      border-radius: 5px;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s;
    }

    .empty-state-button:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(99, 102, 241, 0.5);
    }

    .page-info {
      text-align: center;
      color: rgba(255, 255, 255, 0.5);
      margin-top: 10px;
      font-size: 13px;
    }

    /* 加载状态样式 */
    .loading {
      text-align: center;
      padding: 40px 0;
      color: rgba(255, 255, 255, 0.5);
      font-size: 14px;
      grid-column: 1 / -1;
    }

    /* 置顶弹窗样式 */
    /* 置顶弹窗遮罩层 */
    .zd-modal-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.7);
      backdrop-filter: blur(5px);
      z-index: 9998;
      animation: fadeIn 0.3s ease;
    }

    .zd-modal-overlay.active {
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* 置顶弹窗容器 */
    .zd-modal {
      background: linear-gradient(135deg, rgba(26, 26, 46, 0.95), rgba(45, 45, 68, 0.95));
      backdrop-filter: blur(20px);
      border-radius: 5px;
      padding: 10px;
      width: 90%;
      max-width: 400px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
      border: 1px solid rgba(139, 92, 246, 0.3);
      animation: slideUp 0.3s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .zd-modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .zd-modal-title {
      font-size: 18px;
      font-weight: 600;
      color: #fff;
      background: linear-gradient(135deg, #8b5cf6, #a78bfa);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .zd-modal-close {
      background: none;
      border: none;
      color: rgba(255, 255, 255, 0.6);
      font-size: 24px;
      cursor: pointer;
      padding: 0;
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      transition: all 0.3s;
    }

    .zd-modal-close:hover {
      background: rgba(255, 255, 255, 0.1);
      color: #fff;
    }

    .zd-packages {
      display: flex;
      flex-direction: column;
      margin-bottom: 10px;
    }

    .zd-package-item {
      background: linear-gradient(135deg, rgba(30, 30, 50, 0.6), rgba(45, 45, 68, 0.6));
      border: 2px solid rgba(139, 92, 246, 0.2);
      border-radius: 5px;
      padding: 16px;
      cursor: pointer;
      transition: all 0.3s;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 10px;
    }

    .zd-package-item:hover {
      border-color: rgba(139, 92, 246, 0.5);
      background: linear-gradient(135deg, rgba(40, 40, 60, 0.7), rgba(55, 55, 78, 0.7));
      transform: translateX(4px);
    }

    .zd-package-item.selected {
      border-color: #8b5cf6;
      background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(124, 58, 237, 0.2));
      box-shadow: 0 0 20px rgba(139, 92, 246, 0.3);
    }

    .zd-package-duration {
      font-size: 16px;
      font-weight: 600;
      color: #fff;
    }

    .zd-package-price {
      font-size: 18px;
      font-weight: 700;
      background: linear-gradient(135deg, #8b5cf6, #a78bfa);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .zd-modal-footer {
      display: flex;
    }

    .zd-btn {
      flex: 1;
      padding: 12px;
      border: none;
      border-radius: 5px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
      
    }

    .zd-btn-cancel {
      background: rgba(255, 255, 255, 0.1);
      color: rgba(255, 255, 255, 0.8);
      margin-right: 10px;
    }

    .zd-btn-cancel:hover {
      background: rgba(255, 255, 255, 0.15);
    }

    .zd-btn-confirm {
      background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 50%, #6d28d9 100%);
      color: #fff;
      box-shadow: 0 4px 12px rgba(139, 92, 246, 0.4);
    }

    .zd-btn-confirm:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(139, 92, 246, 0.6);
    }

    .zd-btn-confirm:disabled {
      opacity: 0.5;
      cursor: not-allowed;
      transform: none;
    }

    /* 响应式设计 */
    @media (max-width: 375px) {
      .main-category-tabs,
      .filter-tabs {
      }
      
      .publish-list {
        gap: 10px;
      }
      
      .publish-info {
        padding: 8px;
      }
      
      .publish-title {
        font-size: 13px;
      }
      
      .publish-price {
        font-size: 15px;
      }
    }
  </style>
</head>
<body>
  <!-- 页面头部 -->
 <?php include_once 'comm/header.php'; ?>

  <!-- 添加大分类选择区 -->
  <div class="main-category-section">
    <div class="main-category-tabs">
      <div class="main-category-btn active" data-category="1">论坛</div>
      <div class="main-category-btn" data-category="2">高端</div>
      <div class="main-category-btn" data-category="3">伴游</div>
      <div class="main-category-btn" data-category="4">包养</div>
    </div>
  </div>

  <!-- 主要内容区域 -->
  <div class="publish-container">
    <!-- 筛选标签 -->
    <div class="filter-tabs">
      <div class="filter-tab active" data-status="all">全部</div>
      <div class="filter-tab" data-status="reviewing">审核中</div>
      <div class="filter-tab" data-status="approved">审核通过</div>
      <div class="filter-tab" data-status="rejected">审核拒绝</div>
    </div>

    <!-- 发布列表 - 初始为空，通过JavaScript异步加载 -->
    <div class="publish-list">
    </div>

    <!-- 分页组件 - 初始为空，通过JavaScript异步加载后生成 -->
    <div class="pagination">
    </div>
  </div>

  <!-- 置顶弹窗 -->
  <div class="zd-modal-overlay" id="zdModal">
    <div class="zd-modal">
      <div class="zd-modal-header">
        <h3 class="zd-modal-title">选择置顶套餐</h3>
        <button class="zd-modal-close" onclick="closeZdModal()">&times;</button>
      </div>
      <div class="zd-packages">
        <?php foreach ($zdPackages as $index => $package): ?>
          <div class="zd-package-item" data-price="<?php echo $package['price']; ?>" data-months="<?php echo $package['months']; ?>">
            <span class="zd-package-duration"><?php echo $package['duration']; ?></span>
            <span class="zd-package-price">¥<?php echo $package['price']; ?></span>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="zd-modal-footer">
        <button class="zd-btn zd-btn-cancel" onclick="closeZdModal()">取消</button>
        <button class="zd-btn zd-btn-confirm" id="zdConfirmBtn" disabled>确认置顶</button>
      </div>
    </div>
  </div>

  <script src="/js/jquery-3.5.1.min.js"></script>
  <script>
    var currentZdInfoId = null;
    var currentZdInfoType = null;
    var selectedZdPrice = null;

    // 打开置顶弹窗
    function openZdModal(infoId, infoType) {
      currentZdInfoId = infoId;
      currentZdInfoType = infoType;
      selectedZdPrice = null;
      $('.zd-package-item').removeClass('selected');
      $('#zdConfirmBtn').prop('disabled', true);
      $('#zdModal').addClass('active');
    }

    // 关闭置顶弹窗
    function closeZdModal() {
      $('#zdModal').removeClass('active');
      currentZdInfoId = null;
      currentZdInfoType = null;
      selectedZdPrice = null;
    }

    // 套餐选择事件
    $(document).on('click', '.zd-package-item', function() {
      $('.zd-package-item').removeClass('selected');
      $(this).addClass('selected');
      selectedZdPrice = $(this).data('price');
      $('#zdConfirmBtn').prop('disabled', false);
    });

    // 确认置顶按钮
    $('#zdConfirmBtn').click(function() {
      if (!currentZdInfoId || !selectedZdPrice) {
        alert('请选择置顶套餐');
        return;
      }

      var $btn = $(this);
      $btn.prop('disabled', true).text('提交中...');

      $.ajax({
        url: '/opers/user/zd.html',
        type: 'POST',
        dataType: 'json',
        data: {
          id: currentZdInfoId,
          infotype: currentZdInfoType,
          zd_price: selectedZdPrice
        },
        success: function(response) {
          if (response.code === 200) {
            alert(response.msg || '置顶成功');
            closeZdModal();
            loadData(); // 重新加载列表
          } else {
            alert(response.msg || '置顶失败');
          }
        },
        error: function() {
          alert('网络错误，请重试');
        },
        complete: function() {
          $btn.prop('disabled', false).text('确认置顶');
        }
      });
    });

    $(document).ready(function() {
      // 默认参数
      var currentPage = 1;
      var pageSize = 4;
      var currentStatus = -1;
      var currentCategory = '1';
      
      // 初始化加载数据
      loadData();
      
      $('.main-category-btn').click(function() {
        $(this).addClass('active').siblings().removeClass('active');
        currentCategory = $(this).data('category');
        currentPage = 1;
        loadData();
      });
      
      // 筛选标签点击事件
      $('.filter-tab').click(function() {
        $(this).addClass('active').siblings().removeClass('active');
        var statusVal = $(this).data('status');
        switch(statusVal) {
          case 'all':
            currentStatus = -1;
            break;
          case 'approved':
            currentStatus = 1;
            break;
          case 'reviewing':
            currentStatus = 0;
            break;
          case 'rejected':
            currentStatus = 2;
            break;
          default:
            currentStatus = -1;
        }
        currentPage = 1;
        loadData();
      });
      
      // 分页按钮点击事件
      $('.pagination').on('click', '.page-btn', function() {
        if ($(this).hasClass('prev')) {
          if (currentPage > 1) {
            currentPage--;
            loadData();
          }
        } else if ($(this).hasClass('next')) {
          currentPage++;
          loadData();
        } else if (!$(this).hasClass('dots') && !$(this).prop('disabled')) {
          currentPage = parseInt($(this).text());
          loadData();
        }
      });
      
      // HTML转义，防止拒绝理由中的特殊字符破坏页面或XSS
      function escapeHtml(str) {
        return String(str)
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#39;');
      }

      // 异步加载数据函数
      function loadData() {
        $.ajax({
          url: '/opers/info/member_publish.php',
          type: 'POST',
          dataType: 'json',
          data: {
            page: currentPage,
            pageSize: pageSize,
            status: currentStatus,
            infotype: currentCategory
          },
          beforeSend: function() {
            $('.publish-list').html('<div class="loading">加载中...</div>');
          },
          success: function(response) {
            if (response.code === 200) {
              if (response.data && Array.isArray(response.data.list)) {
                renderList(response.data.list,response.data.editurl,response.data.seeurl);
                updatePagination(response.data.total || 0, response.data.totalPages || 1);
              } else {
                renderEmptyState('数据格式错误');
              }
            } else {
              console.error('加载数据失败:', response.msg);
              renderEmptyState(response.msg || '加载数据失败');
            }
          },
          error: function(xhr, status, error) {
            console.error('请求错误:', error);
            renderEmptyState('网络错误，请重试');
          }
        });
      }
      
      // 渲染列表函数
      function renderList(dataList,editurl,seeurl) {
        var $listGrid = $('.publish-list');
        $listGrid.empty();
        
        if (!Array.isArray(dataList) || dataList.length === 0) {
          renderEmptyState('暂无相关数据');
          return;
        }
        
        $.each(dataList, function(index, item) {
          var statusClass = '';
          var statusText = '';
          
          switch (item.flag) {
            case 1:
              statusClass = 'status-approved';
              statusText = '审核通过';
              break;
            case 0:
              statusClass = 'status-reviewing';
              statusText = '审核中';
              break;
            case 2:
              statusClass = 'status-rejected';
              statusText = '审核拒绝';
              break;
            default:
              statusClass = '';
              statusText = '未知状态';
          }
          
          var pinBadgeHtml = '';
          if (item.iszd == 1) {
            pinBadgeHtml = '<span class="publish-pin-badgeo" >已置顶</span>';
          } else {
            pinBadgeHtml = `<button class="publish-pin-badge" onclick="openZdModal(${item.id}, ${currentCategory})">置顶</button>`;
          }

          // 审核拒绝时显示拒绝理由（ly字段），并做HTML转义防止XSS
          var reasonHtml = '';
          if (item.flag == 2 && item.ly && String(item.ly).replace(/^\s+|\s+$/g, '') !== '') {
            reasonHtml = `<div class="publish-reason"><span class="publish-reason-label">拒绝理由：</span>${escapeHtml(item.ly)}</div>`;
          }

          var cardHtml = `
            <div class="publish-item" data-status="${item.flag}">
              <div class="publish-image-container">
                ${pinBadgeHtml}
                <a href="${editurl}.html?id=${item.id}" class="publish-edit-badge">修改</a>
                <a href="${seeurl}${item.id}.html">
                  <img src="${item.pic}" onerror="this.src='upload/default_avatar.png';" alt="${item.title}" class="publish-image">
                </a>
              </div>
              <a href="${seeurl}.html?id=${item.id}" class="publish-link">
                <div class="publish-info">
                  <div class="publish-title">${item.title}</div>
                 <!-- <div class="publish-price">${item.price}</div> -->
                  <div class="publish-meta">
                    <span class="publish-date">${item.city_name}</span>
                    <span class="publish-status ${statusClass}">${statusText}</span>
                  </div>
                  ${reasonHtml}
                </div>
              </a>
            </div>
          `;
          
          $listGrid.append(cardHtml);
        });
      }
      
      // 更新分页函数
      function updatePagination(total, totalPages) {
        var $pagination = $('.pagination');
        $pagination.empty();
        
        var prevDisabled = currentPage === 1 ? 'disabled' : '';
        $pagination.append(`<button class="page-btn prev" ${prevDisabled}>上一页</button>`);
        
        var startPage = Math.max(1, currentPage - 2);
        var endPage = Math.min(totalPages, startPage + 4);
        
        if (endPage - startPage < 4 && startPage > 1) {
          startPage = Math.max(1, endPage - 4);
        }
        
        if (startPage > 1) {
          $pagination.append(`<button class="page-btn">1</button>`);
          if (startPage > 2) {
            $pagination.append(`<span class="page-btn dots">...</span>`);
          }
        }
        
        for (var i = startPage; i <= endPage; i++) {
          var activeClass = i === currentPage ? 'active' : '';
          $pagination.append(`<button class="page-btn ${activeClass}">${i}</button>`);
        }
        
        if (endPage < totalPages) {
          if (endPage < totalPages - 1) {
            $pagination.append(`<span class="page-btn dots">...</span>`);
          }
          $pagination.append(`<button class="page-btn">${totalPages}</button>`);
        }
        
        var nextDisabled = currentPage >= totalPages ? 'disabled' : '';
        $pagination.append(`<button class="page-btn next" ${nextDisabled}>下一页</button>`);
        
        if (!$('.page-info').length) {
          $pagination.after('<div class="page-info"></div>');
        }
        
      }
      
      // 渲染空状态
      function renderEmptyState(message = '暂无相关数据') {
        var emptyHtml = `
          <div class="empty-state">
            <svg class="empty-state-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
              <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
              <circle cx="8.5" cy="8.5" r="1.5"></circle>
              <polyline points="21 15 16 10 5 21"></polyline>
            </svg>
            <div class="empty-state-text">${message}</div>
          </div>
        `;
        $('.publish-list').html(emptyHtml);
      }
    });
  </script>

</body>
</html>
