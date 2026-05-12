/**
 * 发布页面表单验证和提交
 */

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
  // 清除所有错误提示
  document.querySelectorAll(".form-error").forEach((el) => el.classList.remove("show"))

  // 1. 验证标题（必填）
  const title = document.querySelector('input[name="title"]')
  if (!title || !title.value.trim()) {
    showFieldError("title", "请输入信息标题")
    return false
  }

  // 2. 验证省份（必填）
  const province = document.getElementById("province")
  if (!province || !province.value) {
    showFieldError("province", "请选择省份")
    return false
  }

  // 3. 验证城市（必填）
  const city = document.getElementById("city")
  if (!city || !city.value) {
    showFieldError("city", "请选择城市")
    return false
  }

  // 4. 验证区县（必填）
  const district = document.getElementById("district")
  if (!district || !district.value) {
    showFieldError("district", "请选择区县")
    return false
  }

  // 5. 验证发布类别（必填）
  const typeid = document.querySelector('select[name="typeid"]')
  if (!typeid || !typeid.value) {
    showFieldError("typeid", "请选择发布类别")
    return false
  }

  // 6. 验证信息来源（必填）
  const laiyuan = document.querySelector('select[name="laiyuan"]')
  if (!laiyuan || !laiyuan.value) {
    showFieldError("laiyuan", "请选择信息来源")
    return false
  }

  // 7. 验证综合评价（必填）
  const pj = document.querySelector('select[name="pj"]')
  if (!pj || !pj.value) {
    showFieldError("pj", "请选择综合评价")
    return false
  }

  // 8. 验证详细内容（必填）
  const content = document.querySelector('textarea[name="content"]')
  if (!content || !content.value.trim()) {
    showFieldError("content", "请填写详细内容")
    return false
  } else if (content.value.trim().length < 10) {
    showFieldError("content", "详细内容至少需要10个字符")
    return false
  }

  // 9. 验证联系人（必填）
  const uname = document.querySelector('input[name="uname"]')
  if (!uname || !uname.value.trim()) {
    showFieldError("uname", "请输入联系人姓名")
    return false
  }

  // 10. 验证联系方式（至少填写一项）
  const mobile = document.querySelector('input[name="mobile"]')
  const weixin = document.querySelector('input[name="weixin"]')
  const qq = document.querySelector('input[name="qq"]')
  const yuni = document.querySelector('input[name="yuni"]')

  const hasContact =
    (mobile && mobile.value.trim()) ||
    (weixin && weixin.value.trim()) ||
    (qq && qq.value.trim()) ||
    (yuni && yuni.value.trim())

  if (!hasContact) {
    // 显示联系方式区域的错误提示
    const contactError = document.querySelector('.form-error[data-field="contact"]')
    if (contactError) {
      contactError.classList.add("show")
      contactError.scrollIntoView({ behavior: "smooth", block: "center" })
    }
    return false
  }

  // 11. 验证详细地址（必填）
  const address = document.querySelector('input[name="address"]')
  if (!address || !address.value.trim()) {
    showFieldError("address", "请输入详细地址")
    return false
  }

  // 12. 验证图片上传（至少一张）
  if (window.imageUploader) {
    const images = window.imageUploader.files
    if (!images || images.length === 0) {
      showAlert("请至少上传一张图片")
      // 滚动到图片上传区域
      const imageSection = document.getElementById("imageUploadContainer")
      if (imageSection) {
        imageSection.scrollIntoView({ behavior: "smooth", block: "center" })
      }
      return false
    }
  } else {
    showAlert("图片上传组件未初始化")
    return false
  }

  // 13. 验证验证码（必填）
  const yzm = document.getElementById("yzm")
  if (!yzm || !yzm.value.trim()) {
    showFieldError("yzm", "请输入验证码")
    return false
  }

  return true
}

// 提交表单
async function submitForm(event) {
  event.preventDefault()

  if (!validateForm()) {
    return
  }

  // 获取提交按钮
  const submitBtn = document.querySelector(".submit-button")
  if (!submitBtn) return

  const originalHTML = submitBtn.innerHTML
  submitBtn.disabled = true
  submitBtn.innerHTML =
    '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>提交中...'

  try {
    // 先上传所有图片和视频
    submitBtn.innerHTML =
      '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>上传文件中...'

    // 上传图片
    if (window.imageUploader) {
      const imageResult = await window.imageUploader.uploadAllFiles()
      if (!imageResult) {
        throw new Error("图片上传失败")
      }
    }

    // 上传视频
    if (window.videoUploader) {
      const videoResult = await window.videoUploader.uploadAllFiles()
      if (!videoResult) {
        throw new Error("视频上传失败")
      }
    }

    submitBtn.innerHTML =
      '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>提交数据中...'

    // 收集表单数据
    const formData = new FormData()

    // 基本信息
    formData.append("title", document.querySelector('input[name="title"]').value.trim())
    formData.append("province", document.getElementById("province").value)
    formData.append("city", document.getElementById("city").value)
    formData.append("district", document.getElementById("district").value)
    formData.append("typeid", document.querySelector('select[name="typeid"]').value)
    formData.append("laiyuan", document.querySelector('select[name="laiyuan"]').value)
    formData.append("pj", document.querySelector('select[name="pj"]').value)

    // 详细信息（可选字段）
    const nums = document.querySelector('select[name="nums"]')
    if (nums && nums.value) formData.append("nums", nums.value)

    const age = document.querySelector('select[name="age"]')
    if (age && age.value) formData.append("age", age.value)

    const wmtj = document.querySelector('input[name="wmtj"]')
    if (wmtj && wmtj.value.trim()) formData.append("wmtj", wmtj.value.trim())

    const price = document.querySelector('input[name="price"]')
    if (price && price.value.trim()) formData.append("price", price.value.trim())

    formData.append("content", document.querySelector('textarea[name="content"]').value.trim())

    // 联系方式
    formData.append("uname", document.querySelector('input[name="uname"]').value.trim())

    const mobile = document.querySelector('input[name="mobile"]')
    if (mobile && mobile.value.trim()) formData.append("mobile", mobile.value.trim())

    const weixin = document.querySelector('input[name="weixin"]')
    if (weixin && weixin.value.trim()) formData.append("weixin", weixin.value.trim())

    const qq = document.querySelector('input[name="qq"]')
    if (qq && qq.value.trim()) formData.append("qq", qq.value.trim())

    const yuni = document.querySelector('input[name="yuni"]')
    if (yuni && yuni.value.trim()) formData.append("yuni", yuni.value.trim())

    formData.append("address", document.querySelector('input[name="address"]').value.trim())

    // 验证码
    formData.append("captcha", document.getElementById("yzm").value.trim())

    // 图片和视频（从上传组件获取已上传的文件路径）
    if (window.imageUploader) {
      const images = window.imageUploader.getFiles()
      formData.append("images", JSON.stringify(images))
    }

    if (window.videoUploader) {
      const videos = window.videoUploader.getFiles()
      formData.append("videos", JSON.stringify(videos))
    }

    // 发送请求
    const response = await fetch("/opers/forum/publish.html", {
      method: "POST",
      body: formData,
      credentials: "include",
    })

    const result = await response.json()

    if (result.code === 200) {
      showAlert("发布成功！", "发布成功", 3000, "user.php")
    } else {
      showAlert(result.msg || "发布失败，请重试")
      // 刷新验证码
      refreshCaptcha()
      submitBtn.disabled = false
      submitBtn.innerHTML = originalHTML
    }
  } catch (error) {
    console.error("提交错误:", error)
    showAlert(error.message || "网络错误，请稍后重试")
    submitBtn.disabled = false
    submitBtn.innerHTML = originalHTML
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
