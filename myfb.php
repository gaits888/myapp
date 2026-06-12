<?php 
include_once 'config.php';
$pageTitle = "我的发布";
$history_url = 'user.html';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="author" content="<?php echo $webname; ?>" />
<meta name="keywords" content="用户登录_<?php echo $webname; ?>">
<meta name="description" content="用户登录_<?php echo $webname; ?>">
<title>用户登录_<?php echo $webname; ?></title>
<link rel="stylesheet" href="/css/footer.css">
<link rel="stylesheet" href="/css/comm.css">
<!--<link rel="stylesheet" href="/css/member_publish.css?t=22">-->
<style>
/* 全局样式重置 */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'PingFang SC', 'Hiragino Sans GB', 'Microsoft YaHei', sans-serif;
  background: #f5f5f5;
  color: #333;
  overflow-x: hidden;
  padding-top: 50px;
  padding-bottom: 70px;
}

/* 添加大分类标签栏 */
.main-category-section {
  background: #fff;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  position: sticky;
  top: 50px;
  z-index: 100;
  margin: 10px 10px 0 10px;
  border-radius: 10px;
}

.main-category-tabs {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
  padding: 10px;
  background: #f9f9f9;
  border-radius: 12px;
}

.main-category-tabs::-webkit-scrollbar {
  display: none;
}

.main-category-btn {
  border: 2px solid transparent;
  border-radius: 50px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  background: white;
  color: #666;
  white-space: nowrap;
  position: relative;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  -webkit-box-flex: 1;
  -webkit-flex: 1 1 0;
  -ms-flex: 1 1 0px;
  flex: 1 1 0;
  margin-right: 10px;
  text-align: center;
  height: 35px;
  line-height: 31px;
}

.main-category-btn:last-child {
  margin-right: 0;
}

/* 发布容器 */
.publish-container {
  padding: 10px 10px 10px 10px;
  background: transparent;
}

/* 筛选标签样式 */
.filter-tabs {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
  padding: 10px;
  margin-bottom: 10px;
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.main-category-btn.active {
  background: linear-gradient(135deg, #00d9a3 0%, #2ded5c 100%);
  color: #fff;
  border: 0;
  box-shadow: 0 4px 12px rgba(0, 217, 163, 0.3);
  font-weight: 600;
  height:35px;
  line-height: 35px;
}

.filter-tab {
  -webkit-box-flex: 1;
  -webkit-flex: 1 1 0;
  -ms-flex: 1 1 0px;
  flex: 1 1 0;
  padding: 6px 10px;
  color: #666;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  white-space: nowrap;
  border-radius: 50px;
  background: transparent;
  border: none;
  margin-right: 8px;
  text-align: center;
}

.filter-tab:last-child {
  margin-right: 0;
}

.filter-tab.active {
  background:linear-gradient(135deg, #00d9a3 0%, #2ded5c 100%);
  color: #fff;
  font-weight: 600;
}

/* 发布列表 - 2列网格布局 */
.publish-list {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  margin-bottom: 10px;
}

/* 发布项 */
.publish-item {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  position: relative;
  border: 1px solid #e0e0e0;
  margin-right: 12px;
  margin-bottom: 12px;
}

.publish-item:nth-child(2n) {
  margin-right: 0;
}

/* 发布图片容器 */
.publish-image-container {
  position: relative;
  width: 100%;
  padding-top: 130%;
  overflow: hidden;
  background: linear-gradient(135deg, #f9f9f9, #f0f0f0);
}

.publish-image {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.publish-item:active .publish-image {
  transform: scale(1.03);
}

/* 修改按钮 */
.publish-edit-badge {
  position: absolute;
  top: 8px;
  right: 8px;
  background: linear-gradient(135deg, #00d9a3, #00c090);
  color: white;
  padding: 5px 12px;
  border-radius: 50px;
  font-size: 11px;
  font-weight: 500;
  text-decoration: none;
  z-index: 10;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 217, 163, 0.4);
}

.publish-edit-badge:active {
  opacity: 0.85;
  transform: scale(0.95);
}

/* 置顶按钮 */
.publish-pin-badge {
  position: absolute;
  top: 8px;
  left: 8px;
  background: linear-gradient(135deg, #ff6b6b, #ff8787);
  color: white;
  padding: 5px 12px;
  border-radius: 50px;
  font-size: 11px;
  font-weight: 500;
  cursor: pointer;
  z-index: 10;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(255, 107, 107, 0.4);
  border: none;
}

.publish-pin-badgeo {
  position: absolute;
  top: 8px;
  left: 8px;
  background: rgba(255, 255, 255, 0.95);
  color: #00d9a3;
  padding: 5px 12px;
  border-radius: 50px;
  font-size: 11px;
  font-weight: 500;
  cursor: pointer;
  z-index: 10;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  border: 1px solid #e0e0e0;
}

.publish-pin-badge:active,
.publish-pin-badgeo:active {
  opacity: 0.85;
  transform: scale(0.95);
}

/* 发布信息 */
.publish-info {
  padding: 12px;
  display: flex;
  flex-direction: column;
  background: #fff;
}

.publish-link {
  text-decoration: none;
  color: inherit;
}

.publish-title {
  font-size: 12px;
  color: #333;
  line-height: 1;
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  margin-bottom: 8px;
}

.publish-price {
  font-size: 16px;
  font-weight: 700;
  background: linear-gradient(135deg, #00d9a3, #00c090);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  line-height: 1.2;
  margin-bottom: 8px;
}

.publish-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 11px;
  color: #999;
  margin-top: 4px;
}

.publish-date {
  font-weight: 400;
}

.publish-status {
  padding: 4px 10px;
  border-radius: 50px;
  font-size: 11px;
  font-weight: 600;
  white-space: nowrap;
}

/* 不同状态的样式 */
.status-approved {
  background: #e8f5e9;
  color: #2e7d32;
  border: 1px solid #a5d6a7;
}

.status-reviewing {
  background: #fff3e0;
  color: #e65100;
  border: 1px solid #ffcc80;
}

.status-rejected {
  background: #ffebee;
  color: #c62828;
  border: 1px solid #ef9a9a;
}

/* 分页样式 */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 20px 0;
}

.page-btn {
  min-width: 36px;
  height: 36px;
  padding: 0 12px;
  background: #fff;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 500;
  color: #333;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 8px;
}

.page-btn:last-child {
  margin-right: 0;
}

.page-btn.active {
  background: linear-gradient(135deg, #00d9a3, #00c090);
  color: #fff;
  border-color: transparent;
}

.page-btn:hover:not(:disabled):not(.dots) {
  border-color: #00d9a3;
  color: #00d9a3;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-btn.dots {
  border: none;
  cursor: default;
}

/* 无数据状态 */
.empty-state {
  text-align: center;
  padding: 80px 20px;
  color: #999;
  grid-column: 1 / -1;
}

.empty-state-icon {
  width: 80px;
  height: 80px;
  margin: 0 auto 20px;
  opacity: 0.3;
}

.empty-state-text {
  font-size: 16px;
  margin-bottom: 20px;
  color: #666;
}

.empty-state-button {
  background: linear-gradient(135deg, #00d9a3, #00c090);
  color: white;
  border: none;
  padding: 12px 32px;
  border-radius: 50px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(0, 217, 163, 0.3);
}

.empty-state-button:active {
  opacity: 0.9;
  transform: translateY(0) scale(0.96);
}

/* 加载状态样式 */
.loading {
  text-align: center;
  padding: 40px 0;
  color: #999;
  font-size: 13px;
  grid-column: 1 / -1;
}

/* 置顶弹窗样式 */
.zd-modal-overlay {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
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
  background: #fff;
  border-radius: 16px;
  padding: 24px;
  width: 90%;
  max-width: 400px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
  border: 1px solid #e0e0e0;
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
  padding-bottom: 16px;
  border-bottom: 1px solid #e0e0e0;
}

.zd-modal-title {
  font-size: 18px;
  font-weight: 600;
  background: linear-gradient(135deg, #00d9a3, #00c090);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.zd-modal-close {
  background: #f5f5f5;
  border: 1px solid #e0e0e0;
  color: #666;
  font-size: 20px;
  cursor: pointer;
  padding: 0;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: all 0.3s ease;
}

.zd-modal-close:hover {
  background: rgba(0, 217, 163, 0.1);
  border-color: #00d9a3;
  color: #00d9a3;
}

.zd-packages {
  display: flex;
  flex-direction: column;
  margin-bottom: 20px;
}

.zd-package-item {
  background: #f9f9f9;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  padding: 16px;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.zd-package-item:last-child {
  margin-bottom: 0;
}

.zd-package-item.selected {
  background: rgba(0, 217, 163, 0.1);
  border-color: #00d9a3;
}

.zd-package-duration {
  font-size: 16px;
  font-weight: 600;
  color: #333;
}

.zd-package-price {
  font-size: 18px;
  font-weight: 700;
  background: linear-gradient(135deg, #00d9a3, #00c090);
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
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-right: 12px;
}

.zd-btn:last-child {
  margin-right: 0;
}

.zd-btn-cancel {
  background: #f5f5f5;
  color: #333;
  border: 1px solid #e0e0e0;
}

.zd-btn-cancel:hover {
  background: #fff;
  border-color: #d0d0d0;
}

.zd-btn-confirm {
  background: linear-gradient(135deg, #00d9a3, #00c090);
  color: #fff;
  box-shadow: 0 4px 12px rgba(0, 217, 163, 0.3);
}

.zd-btn-confirm:hover {
  opacity: 0.9;
  transform: translateY(-2px);
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
    padding: 10px 12px;
  }
  
  .publish-list {
    gap: 10px;
  }
  
  .publish-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
  }
  
  .publish-item {
    margin-right: 10px;
    margin-bottom: 10px;
  }
  
  .publish-item:nth-child(2n) {
    margin-right: 0;
  }
  
  .publish-info {
    padding: 10px;
  }
  
  .publish-title {
    font-size: 13px;
    min-height: 2.6em;
    margin-bottom: 6px;
  }
  
  .publish-price {
    margin-bottom: 6px;
  }
  
  .publish-edit-badge,
  .publish-pin-badge,
  .publish-pin-badgeo {
    padding: 4px 10px;
    font-size: 11px;
  }
}

@media (min-width: 768px) {
  .publish-list {
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
  }
  
  .publish-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
  }
  
  .publish-item {
    margin-right: 16px;
    margin-bottom: 16px;
  }
  
  .publish-item:nth-child(2n) {
    margin-right: 16px;
  }
  
  .publish-item:nth-child(3n) {
    margin-right: 0;
  }
}

@media (min-width: 1024px) {
  .publish-list {
    grid-template-columns: repeat(4, 1fr);
  }
  
  .publish-item:nth-child(3n) {
    margin-right: 16px;
  }
  
  .publish-item:nth-child(4n) {
    margin-right: 0;
  }
}

</style>
</head>
<body>
<?php include_once 'webphp/zd_money.php'; ?>
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

  <div class="publish-container">
    <!-- 筛选标签 -->
    <div class="filter-tabs">
      <div class="filter-tab active" data-status="all">全部</div>
      <div class="filter-tab" data-status="approved">审核通过</div>
      <div class="filter-tab" data-status="reviewing">审核中</div>
      <div class="filter-tab" data-status="rejected">审核拒绝</div>
    </div>

    <div class="publish-list">
    </div>

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
            location.reload();
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
      
      // 异步加载数据函数
      function loadData() {
        $.ajax({
          url: '/opers/info/member_publish.html',
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
            pinBadgeHtml = `<div class="publish-pin-badge" onclick="openZdModal(${item.id}, ${currentCategory})">置顶</div>`;
          }
          
          var cardHtml = `
            <div class="publish-item" data-status="${item.flag}">
              <div class="publish-image-container">
                ${pinBadgeHtml}
                <a href="/${editurl}.html?id=${item.id}" class="publish-edit-badge">修改</a>
                <a href="/${seeurl}/${item.id}.html">
                  <img src="${item.pic}" onerror="this.src='upload/default_avatar.png';" alt="${item.title}" class="publish-image">
                </a>
              </div>
              <a href="/${seeurl}.html?id=${item.id}" class="publish-link">
                <div class="publish-info">
                  <div class="publish-title"><div style="float:left;white-space: nowrap;overflow:hidden;text-overflow:ellipsis;">${item.title}</div></div>
                  <div class="publish-meta">
                    <span class="publish-date">${item.city_name}</span>
                    <span class="publish-status ${statusClass}">${statusText}</span>
                  </div>
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
