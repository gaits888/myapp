/**
 * 发布页面表单验证和提交
 */

// 显示遮罩层
function showLoadingOverlay(text) {
    var overlay = document.getElementById('loadingOverlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'loadingOverlay';
        overlay.className = 'loading-overlay';
        overlay.innerHTML = '<div class="loading-spinner"></div><div class="loading-text">' + (text || '数据正在上传中，请稍等片刻...') + '</div>';
        document.body.appendChild(overlay);
    } else {
        var textEl = overlay.getElementsByClassName('loading-text')[0];
        if (textEl) {
            textEl.innerHTML = text || '数据正在上传中，请稍等片刻...';
        }
        overlay.style.display = 'flex';
    }
}

function showFieldError(fieldName, errorMessage) {
  let field = document.getElementById(fieldName)

  // 如果通过 id 找不到，尝试通过 name 属性查找
  if (!field) {
    field = document.querySelector(`[name="${fieldName}"]`)
  }

  if (!field) return

  const formItem = field.closest(".form-item")
  if (!formItem) return

  const errorElement = formItem.querySelector(`.form-error[data-field="${fieldName}"]`)
  if (errorElement) {
    errorElement.textContent = errorMessage
    errorElement.classList.add("show")
  }

  // 跳转到错误字段
  field.scrollIntoView({ behavior: "smooth", block: "center" })
  field.focus()
}

function hideFieldError(fieldId) {
  let field = document.getElementById(fieldId)

  // 如果通过 id 找不到，尝试通过 name 属性查找
  if (!field) {
    field = document.querySelector(`[name="${fieldId}"]`)
  }

  if (!field) return

  const formItem = field.closest(".form-item")
  if (!formItem) return

  const errorElement = formItem.querySelector(".form-error")
  if (errorElement) {
    errorElement.classList.remove("show")
  }
}

function showAlert(message, title = "提示", duration = 2000, redirectUrl = null) {
  // 简单的 alert 实现
  alert(message)
  if (redirectUrl) {
    setTimeout(() => {
      window.location.href = redirectUrl
    }, duration)
  }
}

function validateForm() {
  // 按页面顺序依次校验，遇到第一个未填/未选立即 alert + focus，返回 false
  var fields = [
    { name: "title",    isSelect: false, msg: "请输入信息标题" },
    { name: "province", isSelect: true,  msg: "请选择省份" },
    { name: "city",     isSelect: true,  msg: "请选择城市" },
    { name: "district", isSelect: true,  msg: "请选择区县" },
    { name: "typeid",   isSelect: true,  msg: "请选择发布类别" },
    { name: "laiyuan",  isSelect: true,  msg: "请选择信息来源" },
    { name: "pj",       isSelect: true,  msg: "请选择综合评价" },
    { name: "nums",     isSelect: true,  msg: "请选择场所人数" },
    { name: "age",      isSelect: true,  msg: "请选择年龄大小" },
    { name: "wmtj",     isSelect: false, msg: "请输入外貌形象" },
    { name: "price",    isSelect: false, msg: "请输入服务价格" },
    { name: "content",  isSelect: false, msg: "请填写详细内容" },
    { name: "uname",    isSelect: false, msg: "请输入联系人" },
    { name: "address",  isSelect: false, msg: "请输入详细地址" },
  ]

  for (var i = 0; i < fields.length; i++) {
    var f  = fields[i]
    var el = document.querySelector('[name="' + f.name + '"]')
    if (!el) continue
    var val   = el.value ? el.value.trim() : ""
    var empty = f.isSelect ? !val : !val
    if (empty) {
      alert(f.msg)
      el.focus()
      return false
    }
  }

  // 联系方式：手机、微信、QQ、与你号 至少填写一项
  var mobileEl = document.querySelector('input[name="mobile"]')
  var weixinEl = document.querySelector('input[name="weixin"]')
  var qqEl     = document.querySelector('input[name="qq"]')
  var yuniEl   = document.querySelector('input[name="yuni"]')
  var hasContact = (mobileEl && mobileEl.value.trim()) ||
                   (weixinEl && weixinEl.value.trim()) ||
                   (qqEl     && qqEl.value.trim())     ||
                   (yuniEl   && yuniEl.value.trim())
  if (!hasContact) {
    alert("手机号、微信、QQ、与你号 至少填写一项")
    mobileEl && mobileEl.focus()
    return false
  }

  // 图片：至少上传一张
  if (window.imageUploader) {
    if (!window.imageUploader.files || window.imageUploader.files.length === 0) {
      alert("请至少上传一张图片")
      var imgContainer = document.getElementById("imageUploadContainer")
      if (imgContainer) imgContainer.scrollIntoView({ behavior: "smooth", block: "center" })
      return false
    }
  }

  // 验证码
  var yzmEl = document.getElementById("yzm")
  if (!yzmEl || !yzmEl.value.trim()) {
    alert("请输入验证码")
    yzmEl && yzmEl.focus()
    return false
  }

  return true
}

// 隐藏加载层（关闭 showLoadingOverlay 创建的遮罩）
function hideLoadingLayer() {
  var overlay = document.getElementById("loadingOverlay")
  if (overlay) overlay.style.display = "none"
}

// 提交表单
async function submitForm(event) {
  event.preventDefault()

  if (!validateForm()) {
    return
  }

  // 验证通过：调用统一的加载层函数，不可手动关闭
  // 不在此刷新验证码，refreshCaptcha() 会让后端重新生成 Session 值导致校验失败
  showLoadingOverlay("数据正在飞速上传中，请稍等...")

  try {
    // 上传图片
    if (window.imageUploader) {
      var imageResult = await window.imageUploader.uploadAllFiles()
      if (!imageResult) {
        hideLoadingLayer()
        alert("图片上传失败，请重试")
        return
      }
    }

    // 上传视频
    if (window.videoUploader) {
      var videoResult = await window.videoUploader.uploadAllFiles()
      if (!videoResult) {
        hideLoadingLayer()
        alert("视频上传失败，请重试")
        return
      }
    }

    // 收集表单数据
    var formData = new FormData()
    formData.append("title",    document.querySelector('input[name="title"]').value.trim())
    formData.append("province", document.getElementById("province").value)
    formData.append("city",     document.getElementById("city").value)
    formData.append("district", document.getElementById("district").value)
    formData.append("typeid",   document.querySelector('select[name="typeid"]').value)
    formData.append("laiyuan",  document.querySelector('select[name="laiyuan"]').value)
    formData.append("pj",       document.querySelector('select[name="pj"]').value)
    formData.append("content",  document.querySelector('textarea[name="content"]').value.trim())
    formData.append("uname",    document.querySelector('input[name="uname"]').value.trim())
    formData.append("address",  document.querySelector('input[name="address"]').value.trim())
    formData.append("captcha",  document.getElementById("yzm").value.trim())

    // 必填字段（已在 validateForm 校验过，此处直接 append）
    formData.append("nums",  document.querySelector('select[name="nums"]').value)
    formData.append("age",   document.querySelector('select[name="age"]').value)
    formData.append("wmtj",  document.querySelector('input[name="wmtj"]').value.trim())
    formData.append("price", document.querySelector('input[name="price"]').value.trim())
    var mob   = document.querySelector('input[name="mobile"]');  if (mob   && mob.value.trim())  formData.append("mobile", mob.value.trim())
    var wx    = document.querySelector('input[name="weixin"]');  if (wx    && wx.value.trim())   formData.append("weixin", wx.value.trim())
    var qq    = document.querySelector('input[name="qq"]');      if (qq    && qq.value.trim())   formData.append("qq",     qq.value.trim())
    var yuni  = document.querySelector('input[name="yuni"]');    if (yuni  && yuni.value.trim()) formData.append("yuni",   yuni.value.trim())

    // 已上传文件路径
    if (window.imageUploader) formData.append("images", JSON.stringify(window.imageUploader.getFiles()))
    if (window.videoUploader) formData.append("videos", JSON.stringify(window.videoUploader.getFiles()))

    var response = await fetch("/opers/forum/publish.html", {
      method: "POST",
      body: formData,
      credentials: "include",
    })

    var result = await response.json()

    // 后端返回后关闭加载层
    hideLoadingLayer()

    if (result.code === 200) {
      alert("发布成功")
      window.location.href = "user.html"
    } else {
      // 后端返回失败后刷新验证码（此时 Session 更新不影响本次请求）
      refreshCaptcha()
      showAlert(result.msg || "发布失败，请重试")
    }
  } catch (error) {
    hideLoadingLayer()
    refreshCaptcha()
    showAlert(error.message || "网络错误，请稍后重试")
  }
}

// 刷新验证码
function refreshCaptcha() {
  const captchaImg = document.getElementById("captchaImg")
  if (captchaImg) {
    captchaImg.src = "/lib/yzmcode.html?r=" + Math.random()
  }
}

/**
 * 获取城市或区县列表
 * @param {number} pid 上级id
 * @param {number} type 2:获取城市 3:获取区县
 * @returns {Promise<Array>} 城市或区县列表
 */
async function getRegionList(pid, type) {
  try {
    const formData = new FormData()
    formData.append("pid", pid)
    formData.append("type", type)

    const response = await fetch("/opers/city/getcity.html", {
      method: "POST",
      body: formData,
      credentials: "include",
    })

    const result = await response.json()

    if (result.code === 200) {
      return result.data || []
    } else {
      console.error("获取地区列表失败:", result.msg)
      return []
    }
  } catch (error) {
    console.error("获取地区列表错误:", error)
    return []
  }
}

/**
 * 更新城市下拉框
 * @param {number} provinceId 省份id
 */
async function updateCityOptions(provinceId) {
  const citySelect = document.getElementById("city")
  const districtSelect = document.getElementById("district")

  if (!provinceId) {
    citySelect.innerHTML = '<option value="">请先选择省份</option>'
    districtSelect.innerHTML = '<option value="">请先选择城市</option>'
    return
  }

  // 显示加载状态
  citySelect.innerHTML = '<option value="">加载中...</option>'
  districtSelect.innerHTML = '<option value="">请先选择城市</option>'

  // 获取城市列表
  const cities = await getRegionList(provinceId, 2)

  // 更新城市下拉框
  citySelect.innerHTML = '<option value="">请选择城市</option>'
  cities.forEach((city) => {
    const option = document.createElement("option")
    option.value = city.id
    option.textContent = city.fullname || city.name
    citySelect.appendChild(option)
  })
}

/**
 * 更新区县下拉框
 * @param {number} cityId 城市id
 */
async function updateDistrictOptions(cityId) {
  const districtSelect = document.getElementById("district")

  if (!cityId) {
    districtSelect.innerHTML = '<option value="">请先选择城市</option>'
    return
  }

  // 显示加载状态
  districtSelect.innerHTML = '<option value="">加载中...</option>'

  // 获取区县列表
  const districts = await getRegionList(cityId, 3)

  // 更新区县下拉框
  districtSelect.innerHTML = '<option value="">请选择区县</option>'
  districts.forEach((district) => {
    const option = document.createElement("option")
    option.value = district.id
    option.textContent = district.fullname || district.name
    districtSelect.appendChild(option)
  })
}

// 页面加载完成后初始化
document.addEventListener("DOMContentLoaded", () => {
  // 绑定表单提交事件
  const publishForm = document.getElementById("publishForm")
  if (publishForm) {
    publishForm.addEventListener("submit", submitForm)
  }

  // 验证码图片点击刷新
  const captchaImg = document.getElementById("captchaImg")
  if (captchaImg) {
    captchaImg.addEventListener("click", refreshCaptcha)
    captchaImg.style.cursor = "pointer"
  }

  // 为所有表单元素添加输入监听
  const formInputs = document.querySelectorAll(".form-input, .form-select, .form-textarea, .form-inputyz")
  formInputs.forEach((input) => {
    const fieldName = input.id || input.name

    input.addEventListener("input", () => {
      hideFieldError(fieldName)
    })

    input.addEventListener("change", () => {
      hideFieldError(fieldName)
    })
  })

  // 省份选择变化
  const provinceSelect = document.getElementById("province")
  if (provinceSelect) {
    provinceSelect.addEventListener("change", async (e) => {
      const provinceId = e.target.value
      await updateCityOptions(provinceId)
      hideFieldError("province")
    })
  }

  // 城市选择变化
  const citySelect = document.getElementById("city")
  if (citySelect) {
    citySelect.addEventListener("change", async (e) => {
      const cityId = e.target.value
      await updateDistrictOptions(cityId)
      hideFieldError("city")
    })
  }
})
