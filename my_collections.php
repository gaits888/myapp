<?php 
include_once 'loaduser.php';
include_once 'config.php';
$pageTitle = "我的收藏";
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="author" content="<?php echo $webname; ?>" />
<meta name="keywords" content="我的收藏_<?php echo $webname; ?>">
<meta name="description" content="我的收藏_<?php echo $webname; ?>">
<title>我的收藏_<?php echo $webname; ?></title>
<link rel="stylesheet" href="/css/comm.css">
<link rel="stylesheet" href="/css/my_collections.css">
</head>
<body>
<?php include_once 'comm/header.php'; ?>

  <!-- 大分类标签 -->
  <div class="main-category-tabs">
    <div class="main-category-btn active" data-category="forum">论坛</div>
    <div class="main-category-btn" data-category="highend">高端</div>
    <div class="main-category-btn" data-category="travel">伴游</div>
    <div class="main-category-btn" data-category="friend">包养</div>
  </div>

  <!-- 收藏卡片容器 -->
  <div class="collections-container">
    <div class="cards-grid" id="cardsGrid">
      <!-- 加载中提示 -->
      <div class="empty-state">
        <div class="empty-state-icon">
          <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
          </svg>
        </div>
        <div class="empty-state-text">加载中...</div>
      </div>
    </div>

    <!-- 分页 -->
    <div class="pagination" id="pagination" style="display: none;">
    </div>
  </div>

  <!-- 底部导航 -->
<?php include_once 'comm/footer.php'; ?>
<script>
// 当前状态
let currentPage = 1
let currentCategory = 1 // 1:论坛, 2:高端, 3:伴游, 4:伴友
const pageSize = 4
let totalPages = 1
let seeUrl = ""

// 分类映射
const categoryMap = {
  forum: { value: 1, name: "论坛" },
  highend: { value: 2, name: "高端" },
  travel: { value: 3, name: "伴游" },
  friend: { value: 4, name: "包养" },
}

// 页面加载完成后初始化
document.addEventListener("DOMContentLoaded", () => {
  // 绑定分类切换
  const categoryBtns = document.querySelectorAll(".main-category-btn")
  categoryBtns.forEach((btn) => {
    btn.addEventListener("click", function () {
      categoryBtns.forEach((b) => b.classList.remove("active"))
      this.classList.add("active")

      const category = this.dataset.category
      currentCategory = categoryMap[category].value
      currentPage = 1

      loadCollections()
    })
  })

  // 加载初始数据
  loadCollections()
})

// 加载收藏数据
function loadCollections() {
  const cardsGrid = document.getElementById("cardsGrid")

  // 显示加载中
  cardsGrid.innerHTML = `
    <div class="empty-state">
      <div class="empty-state-icon">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
        </svg>
      </div>
      <div class="empty-state-text">加载中...</div>
    </div>
  `

  // 发送POST请求
  fetch("/opers/info/member_collections.html", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `page=${currentPage}&pageSize=${pageSize}&infotype=${currentCategory}`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.code === 200) {
        const list = data.data.list || []
        seeUrl = data.data.seeurl || "publish_detail"
        totalPages = data.data.totalPages || 1

        if (list.length === 0) {
          // 显示空状态
          cardsGrid.innerHTML = `
          <div class="empty-state">
            <div class="empty-state-icon">
              <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H4V8l8 5 8-5v10zm-8-7L4 6h16l-8 5z"/>
              </svg>
            </div>
            <div class="empty-state-text">暂无收藏</div>
            <div class="empty-state-desc">快去收藏喜欢的内容吧</div>
          </div>
        `
          document.getElementById("pagination").style.display = "none"
        } else {
          // 渲染卡片
          renderCards(list)
          // 渲染分页
          renderPagination()
        }
      } else {
        // 显示错误信息
        cardsGrid.innerHTML = `
        <div class="empty-state">
          <div class="empty-state-icon">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2V7h2v6z"/>
            </svg>
          </div>
          <div class="empty-state-text">${data.msg || "加载失败"}</div>
        </div>
      `
        document.getElementById("pagination").style.display = "none"
      }
    })
    .catch((error) => {
      console.error("加载失败:", error)
      cardsGrid.innerHTML = `
      <div class="empty-state">
        <div class="empty-state-icon">
          <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
          </svg>
        </div>
        <div class="empty-state-text">网络错误，请稍后重试</div>
      </div>
    `

      document.getElementById("pagination").style.display = "none"
    })
}

// 渲染卡片
function renderCards(list) {
  const cardsGrid = document.getElementById("cardsGrid")
  const categoryNames = ["", "论坛", "高端", "伴游", "包养"]

  cardsGrid.innerHTML = list
    .map(
      (item) => `
    <a href="/${seeUrl}/${item.id}.html" class="collection-card-link">
      <div class="collection-card">
        <div class="card-image-wrapper">
          <img src="${item.pic}" alt="${item.title}" class="card-image" onerror="this.src='upload/default_avatar.png';">
        </div>
        <div class="card-content">
          <div class="card-title">${item.title}<span>${item.city_name || "未知地区"}</span></div>
          
          <div class="card-meta">
            <span>浏览 ${item.times || 0}</span>
            <span>${formatTime(item.collect_time)}</span>
          </div>
        </div>
      </div>
    </a>
  `,
    )
    .join("")
}

// 渲染分页
function renderPagination() {
  const pagination = document.getElementById("pagination")

  if (totalPages <= 1) {
    pagination.style.display = "none"
    return
  }

  pagination.style.display = "flex"

  let paginationHTML = `
    <button class="page-btn" id="prevBtn" ${currentPage === 1 ? "disabled" : ""}>上一页</button>
  `

  // 生成页码按钮
  for (let i = 1; i <= totalPages; i++) {
    if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
      paginationHTML += `
        <button class="page-btn ${i === currentPage ? "active" : ""}" data-page="${i}">${i}</button>
      `
    } else if (i === currentPage - 3 || i === currentPage + 3) {
      paginationHTML += `<span style="color: var(--text-tertiary);">...</span>`
    }
  }

  paginationHTML += `
    <button class="page-btn" id="nextBtn" ${currentPage === totalPages ? "disabled" : ""}>下一页</button>
  `

  pagination.innerHTML = paginationHTML

  // 绑定分页事件
  document.getElementById("prevBtn")?.addEventListener("click", () => {
    if (currentPage > 1) {
      currentPage--
      loadCollections()
    }
  })

  document.getElementById("nextBtn")?.addEventListener("click", () => {
    if (currentPage < totalPages) {
      currentPage++
      loadCollections()
    }
  })

  document.querySelectorAll(".page-btn[data-page]").forEach((btn) => {
    btn.addEventListener("click", function () {
      currentPage = Number.parseInt(this.dataset.page)
      loadCollections()
    })
  })
}

// 格式化时间
function formatTime(timestamp) {
  if (!timestamp) return ""

  const date = new Date(timestamp * 1000)
  const now = new Date()
  const diff = Math.floor((now - date) / 1000)

  if (diff < 60) return "刚刚"
  if (diff < 3600) return Math.floor(diff / 60) + "分钟前"
  if (diff < 86400) return Math.floor(diff / 3600) + "小时前"
  if (diff < 2592000) return Math.floor(diff / 86400) + "天前"

  return (
    date.getFullYear() +
    "-" +
    String(date.getMonth() + 1).padStart(2, "0") +
    "-" +
    String(date.getDate()).padStart(2, "0")
  )
}
    
</script>
</body>
</html>
