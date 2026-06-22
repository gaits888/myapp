// 全局变量定义
let currentPage = 1
const pageSize = 4 // 默认每页显示4条
let currentStatus = -1 // 默认显示全部状态
let currentTypeInfo = 1 // 添加主分类参数，默认论坛(1)
let totalPages = 1

// 查询参数对象
const queryParams = {
  page: currentPage,
  pageSize: pageSize,
  status: currentStatus,
  typeinfo: currentTypeInfo, // 添加typeinfo参数
}

// HTML转义，防止拒绝原因中的特殊字符破坏页面或XSS
function escapeHtml(str) {
  return String(str)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;")
}

// 渲染发布列表函数
function renderPublishList(data,editurl,seeurl) {
  const publishListContainer = document.querySelector(".publish-list")
  if (!publishListContainer) return

  // 清空现有内容
  publishListContainer.innerHTML = ""

  // 检查数据是否存在且为数组
  if (!data || !Array.isArray(data)) {
    console.error("无效的数据格式")
    return
  }

  // 如果没有数据，显示空状态
  if (data.length === 0) {
    const emptyElement = document.createElement("div")
    emptyElement.className = "empty-state"
    emptyElement.style.cssText = `
            grid-column: 1 / -1;
            padding: 40px 20px;
            text-align: center;
            color: #95a5a6;
            font-size: 14px;
        `
    emptyElement.textContent = "暂无发布内容"
    publishListContainer.appendChild(emptyElement)
    return
  }

  // 遍历数据渲染列表项
  data.forEach((item) => {
    const publishItem = document.createElement("div")
    publishItem.className = "publish-item"

    // 根据flag字段设置对应的状态
    let statusClass = ""
    let statusText = ""

    if (item.flag === 1) {
      statusClass = "status-approved"
      statusText = "审核通过"
    } else if (item.flag === 0) {
      statusClass = "status-reviewing"
      statusText = "待审核"
    } else if (item.flag === 2) {
      statusClass = "status-rejected"
      statusText = "审核失败"
    }

    // 审核拒绝(flag==2)且ly有值时，显示拒绝原因（ly已由后端转为文字），并做HTML转义防XSS
    let reasonHtml = ""
    if (item.flag === 2 && item.ly && String(item.ly).replace(/^\s+|\s+$/g, "") !== "") {
      reasonHtml = `<div class="publish-reason"><span class="publish-reason-label">拒绝原因：</span>${escapeHtml(item.ly)}</div>`
    }

    let zdText, zdClass
    if (item.iszd == 1) {
      zdText = "已置顶"
      zdClass = 'publish-izd-badge';
    } else {
      zdText = "置顶"
      zdClass = 'publish-zd-badge';
    }
    publishItem.innerHTML = `
            <div class="publish-image-container">
                <a href="javascript:void(0);" onclick="userzdUrl('${item.infotype}',${item.id},${item.iszd})" class="${zdClass}">${zdText}</a>
                <a href="javascript:void(0);" onclick="userfbUrl('${editurl}',${item.id},${item.vipclass},${item.flag})" class="publish-edit-badge">修改</a>
                <a href="${seeurl}.html?id=${item.id}">
                    <img src="${item.pic || "upload/default_avatar.png"}" alt="${item.title || ""}" class="publish-image" onerror="this.src='upload/default_avatar.png';">
                </a>
            </div>
            <a href="${seeurl}.html?id=${item.id}" class="publish-link">
                <div class="publish-info">
                    <div class="publish-title">${item.title || ""}</div>
                    <div class="publish-meta">
                        <span class="publish-view-count">浏览 ${item.times || 0} 次</span>
                        <span class="publish-region">${item.city_name || ""}</span>
                        <span style="display: none;" class="publish-status ${statusClass}">${statusText}</span>
                    </div>
                    ${reasonHtml}
                </div>
            </a>
        `

    publishListContainer.appendChild(publishItem)
  })
}

function userfbUrl(editurl,ids,vipnum,flag){
    if (flag>0 && vipnum<2) {
      showInfo('修改需要季度VIP！', '提示');
      return;
    }else{
      window.location.href=editurl+'.html?id='+ids;
      return;
    }
}




function userzdUrl(infotype, ids, iszd) {
  if (iszd == 0) {
    // 弹出置顶消费选项
    openZhidingModal(ids,infotype)
    return
  } else {
    // window.location.href = seeurl + ".html?id=" + ids
    return
  }
}


// 渲染分页控件函数
function renderPagination(currentPage, totalPages) {
  const paginationContainer = document.querySelector(".pagination")
  if (!paginationContainer) return

  // 清空现有内容
  paginationContainer.innerHTML = ""

  // 创建上一页按钮
  const prevButton = document.createElement("button")
  prevButton.className = "page-btn prev"
  prevButton.textContent = "上一页"
  prevButton.disabled = currentPage === 1
  prevButton.addEventListener("click", () => {
    if (currentPage > 1) {
      loadPublishData(currentPage - 1, currentStatus)
    }
  })
  paginationContainer.appendChild(prevButton)

  // 计算显示的页码范围（最多显示3个数字按钮）
  let startPage, endPage

  if (totalPages <= 3) {
    startPage = 1
    endPage = totalPages
  } else {
    if (currentPage <= 2) {
      startPage = 1
      endPage = 3
    } else if (currentPage >= totalPages - 1) {
      startPage = totalPages - 2
      endPage = totalPages
    } else {
      startPage = currentPage - 1
      endPage = currentPage + 1
    }
  }

  // 添加省略号（如果开始页码大于1）
  if (startPage > 1) {
    const firstPageButton = document.createElement("button")
    firstPageButton.className = "page-btn"
    firstPageButton.textContent = "1"
    firstPageButton.addEventListener("click", () => {
      loadPublishData(1, currentStatus)
    })
    paginationContainer.appendChild(firstPageButton)

    if (startPage > 2) {
      const dots = document.createElement("span")
      dots.className = "page-btn dots"
      dots.textContent = "..."
      paginationContainer.appendChild(dots)
    }
  }

  // 添加数字页码按钮
  for (let i = startPage; i <= endPage; i++) {
    const pageButton = document.createElement("button")
    pageButton.className = `page-btn ${i === currentPage ? "active" : ""}`
    pageButton.textContent = i
    pageButton.addEventListener("click", () => {
      loadPublishData(i, currentStatus)
    })
    paginationContainer.appendChild(pageButton)
  }

  // 添加省略号（如果结束页码小于总页数）
  if (endPage < totalPages) {
    if (endPage < totalPages - 1) {
      const dots = document.createElement("span")
      dots.className = "page-btn dots"
      dots.textContent = "..."
      paginationContainer.appendChild(dots)
    }

    const lastPageButton = document.createElement("button")
    lastPageButton.className = "page-btn"
    lastPageButton.textContent = totalPages
    lastPageButton.addEventListener("click", () => {
      loadPublishData(totalPages, currentStatus)
    })
    paginationContainer.appendChild(lastPageButton)
  }

  // 创建下一页按钮
  const nextButton = document.createElement("button")
  nextButton.className = "page-btn next"
  nextButton.textContent = "下一页"
  nextButton.disabled = currentPage === totalPages
  nextButton.addEventListener("click", () => {
    if (currentPage < totalPages) {
      loadPublishData(currentPage + 1, currentStatus)
    }
  })
  paginationContainer.appendChild(nextButton)
}

function setupTypeFilterButtons() {
  const typeTabs = document.querySelectorAll(".filter-tabs-type .filter-tab")
  const statusTabs = document.querySelectorAll(".filter-tabs .filter-tab")
  if (!typeTabs.length) return

  typeTabs.forEach((tab) => {
    tab.addEventListener("click", () => {
      typeTabs.forEach((t) => t.classList.remove("active"))
      tab.classList.add("active")

      // 获取typeinfo值
      const typeinfo = tab.getAttribute("data-typeinfo")
      currentTypeInfo = Number.parseInt(typeinfo)

      currentStatus = -1
      statusTabs.forEach((t) => t.classList.remove("active"))
      if (statusTabs.length > 0) {
        statusTabs[0].classList.add("active")
      }

      // 重置页码并重新加载数据
      currentPage = 1
      loadPublishData(currentPage, currentStatus)
    })
  })
}

// 设置状态筛选按钮事件
function setupFilterButtons() {
  const filterTabs = document.querySelectorAll(".filter-tabs .filter-tab")
  if (!filterTabs.length) return

  filterTabs.forEach((tab) => {
    tab.addEventListener("click", () => {
      filterTabs.forEach((t) => t.classList.remove("active"))
      tab.classList.add("active")

      // 获取状态值
      const status = tab.getAttribute("data-status")
      currentStatus = Number.parseInt(status)

      currentPage = 1
      loadPublishData(currentPage, currentStatus)
    })
  })
}

// 异步加载发布数据
function loadPublishData(page, status) {
  // 更新当前页码和状态
  currentPage = page
  currentStatus = status

  // 更新查询参数
  queryParams.page = page
  queryParams.status = status
  queryParams.typeinfo = currentTypeInfo // 添加typeinfo参数

  // 显示加载状态
  const publishListContainer = document.querySelector(".publish-list")
  if (publishListContainer) {
    publishListContainer.innerHTML =
      '<div style="grid-column: 1 / -1; padding: 40px; text-align: center; color: #95a5a6;">加载中...</div>'
  }

  // 发送AJAX请求 - 使用POST方法，使用表单格式发送数据
  const formData = new URLSearchParams()
  formData.append("page", page)
  formData.append("pageSize", pageSize)
  formData.append("status", status)
  formData.append("typeinfo", currentTypeInfo) // 添加typeinfo参数

  fetch("oper/info/member_publish.html", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: formData,
  })
    .then((response) => {
      return response.json()
    })
    .then((responseData) => {
      // 检查响应结构
      if (!responseData || responseData.code !== 200 || !responseData.data) {
        if (responseData && responseData.msg) {
          throw new Error(responseData.msg)
        }
        throw new Error("接口返回格式不正确")
      }

      const data = responseData.data

      // 更新当前页码和总页数
      if (data.page) {
        currentPage = data.page
      }

      if (data.totalPages) {
        totalPages = data.totalPages
      } else if (data.total && pageSize) {
        totalPages = Math.ceil(data.total / pageSize)
      }

      // 渲染列表
      renderPublishList(data.list || [],data.editurl,data.seeurl)

      // 渲染分页
      renderPagination(currentPage, totalPages)
    })
    .catch((error) => {
      console.error("加载数据失败:", error)
      if (publishListContainer) {
        const errorMessage = error.message || "加载失败，请重试"
        publishListContainer.innerHTML = `<div style="grid-column: 1 / -1; padding: 40px; text-align: center; color: #e74c3c;">${errorMessage}</div>`
      }
    })
}







// 初始化应用
function initializeApp() {
  setupTypeFilterButtons()

  // 设置状态筛选按钮事件
  setupFilterButtons()

  // 初始加载数据
  loadPublishData(currentPage, currentStatus)
}

// 页面加载完成后初始化
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initializeApp)
} else {
  initializeApp()
}
