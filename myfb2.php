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
<meta name="keywords" content="我的发布_<?php echo $webname; ?>">
<meta name="description" content="我的发布_<?php echo $webname; ?>">
<title>我的发布_<?php echo $webname; ?></title>
<link rel="stylesheet" href="/css/footer.css">
<link rel="stylesheet" href="/css/comm.css">
<link rel="stylesheet" href="/css/member_publish.css?t=123">
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
