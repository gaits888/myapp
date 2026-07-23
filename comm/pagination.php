<?php
// 确保当前页不超过总页数
$page = min($page, $totalPages);
?>
<link rel="stylesheet" href="/css/pagination.css?t=1.123982739">
<!-- 分页 -->
<div class="pagination">
  <div class="pagination-btn" onclick="prevPage()" id="prevBtn" <?php echo $page <= 1 ? 'disabled' : ''; ?>>
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <polyline points="15 18 9 12 15 6"></polyline>
    </svg>
    <span>上一页</span>
  </div>
  
  <select id="pageSelector" onchange="goToPage(this.value)" class="pagination-select">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <option value="<?php echo $i; ?>" <?php echo $i == $page ? 'selected' : ''; ?>>
        <?php echo $i; ?> / <?php echo $totalPages; ?>
      </option>
    <?php endfor; ?>
  </select>

  <div class="pagination-btn" onclick="nextPage()" id="nextBtn" <?php echo $page >= $totalPages ? 'disabled' : ''; ?>>
    <span>下一页</span>
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <polyline points="9 18 15 12 9 6"></polyline>
    </svg>
  </div>
</div>


<script>
<?php
echo "var currentPageStrs = '$currentPageStrs';var doamin_url = '$doamin_url';";
?>
  var currentPages = <?php echo $page; ?>;
  var totalPages = <?php echo $totalPages; ?>;

  // 读取当前排序条件，保证翻页时同步导航筛选（伪静态用查询串携带）
  function getSortParam() {
    var sort = new URLSearchParams(window.location.search).get('sort') || 'new';
    if (['new', 'rz', 'tj'].indexOf(sort) === -1) sort = 'new';
    // 默认(最新信息)不带参数，保持 URL 干净
    return sort === 'new' ? '' : ('?sort=' + sort);
  }

  function prevPage() {
    if (currentPages > 1) {
      goToPage(currentPages - 1);
    }
  }

  function nextPage() {
    if (currentPages < totalPages) {
      goToPage(currentPages + 1);
    }
  }

  function goToPage(page) {
    page = parseInt(page);
    var sortQuery = getSortParam();
    if (currentPageStrs === 'home') {
      window.location.href = '/' + page + '.html' + sortQuery;
    } else {
      window.location.href = doamin_url + page + '.html' + sortQuery;
    }
  }
</script>
