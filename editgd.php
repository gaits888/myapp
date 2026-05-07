<?php 
include_once 'loaduser.php';
include_once 'comm/alert_modal.php';
$page_title = "高端修改";

$info_id = intval($_GET['id'] ?? 0);
if ($info_id <= 0) {
    echo '<script>alert("参数错误");history.back();</script>';
    exit;
}

$info = db3('gdb')->where(['id' => $info_id, 'uid' => $user_id])->find();
if (!$info) {
    echo '<script>alert("信息不存在或无权编辑");history.back();</script>';
    exit;
}

$images = !empty($info['bdpics']) ? explode('|', $info['bdpics']) : [];
$videos = !empty($info['bdvideos']) ? explode('|', $info['bdvideos']) : [];


$citypid = db('areab')->where('id', $info['cityid'])->value('pid');
if ($citypid == 0) {
    $citypid = $info['cityid'];
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title><?php echo $page_title; ?></title>
<link rel="stylesheet" href="/css/imgvideo.css?t=<?php echo time(); ?>">
<link rel="stylesheet" href="/css/fb.css?t=<?php echo time(); ?>">
</head>
<body>
    <?php include 'comm/header.php'; ?>

    <div class="publish-container">
        <!-- 页面标题 -->
      
        <form id="publishForm">
            <!-- 基本信息部分 -->
            <div class="form-section">
                <h3 class="section-title">
                    基本信息
                    <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </h3>
                
                
                <div class="form-item">
                    <label class="form-label required">
                        <svg class="label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        所属地区
                    </label>
                    <select name="province" id="province" class="form-select">
                        <option value="">请选择省份</option>
                        <?php foreach ($provinceArr as $k => $v) { ?>
                           <option value="<?php echo $v['id']; ?>"><?php echo $v['fullname']; ?></option>
                        <?php } ?>
                    </select>
                    <div class="form-error" data-field="province">请选择省份</div>
                </div>
                
                <div class="form-item">
                    <label class="form-label required">所在城市</label>
                    <select name="city" id="city" class="form-select">
                        <option value="">请先选择省份</option>
                    </select>
                    <div class="form-error" data-field="city">请选择城市</div>
                </div>
                
                <div class="form-item" style="display:none;">
                    <label class="form-label required">所在区县</label>
                    <select name="district" id="district" class="form-select">
                        <option value="">请先选择城市</option>
                    </select>
                    <div class="form-error" data-field="district">请选择区县</div>
                </div>
                

                <div class="form-item">
                    <label class="form-label">
                        <svg class="label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        年龄大小
                    </label>
                    <select name="age" class="form-select">
                        <option value="0">请选择年龄大小</option>
                        <?php for($i = 18; $i <= 35; $i++): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?>岁</option>
                        <?php endfor; ?>
                    </select>
                    <div class="form-error" data-field="age">请选择年龄</div>
                </div>

                <div class="form-item">
                    <label class="form-label">
                        <svg class="label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        身高
                    </label>
                    <select name="sg" class="form-select">
                        <option value="0">请选择身高</option>
                        <?php for($i = 155; $i <= 180; $i++): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?>cm</option>
                        <?php endfor; ?>
                    </select>
                    <div class="form-error" data-field="sg">请选择身高</div>
                </div>

                <div class="form-item">
                    <label class="form-label">
                        <svg class="label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        体重
                    </label>
                    <select name="tz" class="form-select">
                        <option value="0">请选择体重</option>
                        <?php for($i = 35; $i <= 70; $i++): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?>Kg</option>
                        <?php endfor; ?>
                    </select>
                    <div class="form-error" data-field="tz">请选择体重</div>
                </div>
                <div class="form-item">
                    <label class="form-label">
                        <svg class="label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        学历
                    </label>
                    <select name="xl" class="form-select">
                        <option value="0">请选择学历</option>
                        <?php foreach ($xlArr as $k => $v) {?>
                               <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
                            <?php } ?>
                    </select>
                    <div class="form-error" data-field="xl">请选择学历</div>
                </div>


                <div class="form-item">
                    <label class="form-label">
                        <svg class="label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        职业
                    </label>
                    <select name="zy" class="form-select">
                        <option value="0">请选择职业</option>
                        <?php foreach ($zyArr as $key => $v): ?>
                        <option value="<?php echo $key;?>"><?php echo $v;?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-error" data-field="zy">请选择职业</div>
                </div>


                <div class="form-item">
                    <label class="form-label required">
                        <svg class="label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        约会价格
                    </label>
                    <input type="text" name="price" class="form-input" placeholder="填写如：次2000 , 夜5000 , 一天/1万 , 私聊">
                    <div class="form-error" data-field="price">请输入约会价格</div>
                </div>

            </div>

            

            <!-- 联系方式部分 -->
            <div class="form-section">
                <h3 class="section-title">
                    联系方式
                    <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                </h3>

                <div class="contact-hint">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    <span>手机号、微信、QQ 至少填写一项</span>
                </div>
                
                <div class="form-item">
                    <label class="form-label required">
                        <svg class="label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        昵称
                    </label>
                    <input type="text" name="uname" class="form-input" placeholder="请输入昵称">
                    <div class="form-error" data-field="uname">请输入昵称</div>
                </div>
                
                <div class="form-item">
                    <label class="form-label">
                        <svg class="label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        手机号码
                    </label>
                    <input type="tel" name="mobile" class="form-input" placeholder="请输入手机号码">
                </div>
                
                <div class="form-item">
                    <label class="form-label">
                        <svg class="label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        微信号
                    </label>
                    <input type="text" name="weixin" class="form-input" placeholder="请输入微信号">
                </div>
                
                <div class="form-item">
                    <label class="form-label">
                        <svg class="label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                        QQ号码
                    </label>
                    <input type="text" name="qq" class="form-input" placeholder="请输入QQ号码">
                </div>
              
                
                <div class="form-error" data-field="contact">请至少填写一项联系方式</div>
                
                
            </div>



            <!-- 图片上传 -->
            <div class="form-section upload-section">
                <h3 class="section-title">
                    图片上传
                    <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                </h3>
                <div id="imageUploadContainer" class="upload-container">
                    <div class="upload-grid"></div>
                    <div class="upload-hint">支持JPG、PNG格式，单张图片不超过5MB，最多上传<span class="upload-limit">9张</span></div>
                </div>
            </div>

            <!-- 视频上传 -->
            <div class="form-section upload-section">
                <h3 class="section-title">
                    视频上传
                    <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                </h3>
                <div id="videoUploadContainer" class="upload-container">
                    <div class="upload-grid"></div>
                    <div class="upload-hint">支持MP4格式，单个视频不超过50MB，最多上传<span class="upload-limit">3个</span></div>
                </div>
            </div>
            <!-- 验证码 -->
            <div class="form-section">
                <div class="form-item">
                    <!-- 标签、输入框、验证码图片同一行布局 -->
                    <div class="captcha-row">
                        <label class="form-label required">验证码:</label>

                        <div class="captcha-group">
                          <input type="text" name="captcha" id="yzm" class="form-inputyz" placeholder="请输入验证码">
                            <img id="captchaImg" src="/lib/yzmcode.html" alt="验证码" class="captcha-img" onclick="refreshCaptcha()">
                        </div>
                        
                    </div>
                    <div class="form-error" data-field="yzm">请输入验证码</div>
                    <div class="form-hint">点击验证码图片可刷新</div>
                </div>
            </div>

            <!-- 提交按钮 -->
            <button type="submit" class="submit-button">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                确认修改
            </button>
        </form>

        
    </div>

    <?php include 'comm/footer.php'; ?>


    <script>
        window.infoData = <?php echo json_encode($info); ?>;
        window.existingImages = <?php echo json_encode($images); ?>;
        window.existingVideos = <?php echo json_encode($videos); ?>;
        window.provinceSelect = <?php echo $citypid;?>;
        window.selectedCity = <?php echo $info['cityid']; ?>;
        window.selectedDistrict = 0;

// 刷新验证码
function refreshCaptcha() {
  document.getElementById("captchaImg").src = "/lib/yzmcode.html?r=" + Math.random()
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

  citySelect.innerHTML = '<option value="">加载中...</option>'
  districtSelect.innerHTML = '<option value="">请先选择城市</option>'

  const cities = await getRegionList(provinceId, 2)

  citySelect.innerHTML = '<option value="">请选择城市</option>'
  cities.forEach((city) => {
    const option = document.createElement("option")
    option.value = city.id
    option.textContent = city.fullname || city.name
    citySelect.appendChild(option)
  })

  if (window.selectedCity) {
    citySelect.value = window.selectedCity
    await updateDistrictOptions(window.selectedCity)
  }
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

  districtSelect.innerHTML = '<option value="">加载中...</option>'

  const districts = await getRegionList(cityId, 3)

  districtSelect.innerHTML = '<option value="">请选择区县</option>'
  districts.forEach((district) => {
    const option = document.createElement("option")
    option.value = district.id
    option.textContent = district.fullname || district.name
    districtSelect.appendChild(option)
  })

  if (window.selectedDistrict) {
    districtSelect.value = window.selectedDistrict
  }
}

// 初始化地区选择器
async function initCitySelector() {
  const provinceSelect = document.getElementById("province")
  const citySelect = document.getElementById("city")
  const districtSelect = document.getElementById("district")

  const provinces = await getRegionList(0, 1)

  if (provinces && provinces.length > 0) {
    provinceSelect.innerHTML = '<option value="">请选择省份</option>'
    provinces.forEach((province) => {
      const option = document.createElement("option")
      option.value = province.id
      option.textContent = province.fullname || province.name
      provinceSelect.appendChild(option)
    })

    if (window.provinceSelect) {
      provinceSelect.value = window.provinceSelect
      await updateCityOptions(window.provinceSelect)
    }
  }

  provinceSelect.addEventListener("change", async function () {
    const provinceId = this.value
    window.provinceSelect = provinceId
    window.selectedCity = ""
    window.selectedDistrict = ""
    await updateCityOptions(provinceId)
  })

  citySelect.addEventListener("change", async function () {
    const cityId = this.value
    window.selectedCity = cityId
    window.selectedDistrict = ""
    await updateDistrictOptions(cityId)
  })

  districtSelect.addEventListener("change", function () {
    window.selectedDistrict = this.value
  })
}

// 填充表单数据
function fillFormData() {
  if (!window.infoData) return

  const data = window.infoData

  const fields = [
    "age",
    "sg",
    "tz",
    "xl",
    "zy",
    "price",
    "uname",
    "mobile",
    "weixin",
    "qq",
  ]

  fields.forEach((field) => {
    const input = document.querySelector(`[name="${field}"]`)
    if (input && data[field]) {
      input.value = data[field]
    }
  })

  // 填充已上传的图片
  if (window.existingImages && window.existingImages.length > 0 && window.imageUploader) {
    window.existingImages.forEach((imagePath) => {
      window.imageUploader.files.push({
        file: null,
        preview: imagePath,
        type: "image",
        uploaded: true,
        path: imagePath,
      })
    })
    window.imageUploader.render()
  }

  // 填充已上传的视频
  if (window.existingVideos && window.existingVideos.length > 0 && window.videoUploader) {
    window.existingVideos.forEach((videoPath) => {
      window.videoUploader.files.push({
        file: null,
        preview: videoPath,
        type: "video",
        uploaded: true,
        path: videoPath,
      })
    })
    window.videoUploader.render()
  }
}

// 表单验证
function validateForm() {
  let isValid = true

  document.querySelectorAll(".form-error").forEach((el) => {
    el.classList.remove("show")
  })

  const requiredFields = {
    province: "请选择省份",
    city: "请选择城市",
    age: "请选择年龄",
    sg: "请选择身高",
    tz: "请选择体重",
    xl: "请选择学历",
    zy: "请选择职业",
    price: "请输入约会价格",
    uname: "请输入昵称",
    mobile: "请输入手机号码",
    weixin: "请输入微信号",
    qq: "请输入QQ号码",
    yzm: "请输入验证码",
  }

  for (const [field, message] of Object.entries(requiredFields)) {
    const input = document.querySelector(`[name="${field}"]`)
    const errorEl = document.querySelector(`.form-error[data-field="${field}"]`)

    if (input && !input.value.trim()) {
      if (errorEl) {
        errorEl.textContent = message
        errorEl.classList.add("show")
      }
      isValid = false
    }
  }

  const mobile = document.querySelector('[name="mobile"]').value.trim()
  const weixin = document.querySelector('[name="weixin"]').value.trim()
  const qq = document.querySelector('[name="qq"]').value.trim()

  if (!mobile && !weixin && !qq ) {
    const contactError = document.querySelector('.form-error[data-field="contact"]')
    if (contactError) {
      contactError.classList.add("show")
    }
    isValid = false
  }

  if (!isValid) {
    const firstError = document.querySelector(".form-error.show")
    if (firstError) {
      firstError.scrollIntoView({ behavior: "smooth", block: "center" })
    }
  }

  return isValid
}

// 表单提交处理
async function handleFormSubmit(e) {
  e.preventDefault()

  if (!validateForm()) {
    return
  }

  try {
    if (window.imageUploader) {
      const imageSuccess = await window.imageUploader.uploadAllFiles()
      if (!imageSuccess) {
        showInfo("图片上传失败，请重试")
        return
      }
    }

    if (window.videoUploader) {
      const videoSuccess = await window.videoUploader.uploadAllFiles()
      if (!videoSuccess) {
        showInfo("视频上传失败，请重试")
        return
      }
    }
  } catch (error) {
    showInfo("文件上传失败，请重试")
    return
  }

  const images = window.imageUploader ? window.imageUploader.getFiles() : []
  const videos = window.videoUploader ? window.videoUploader.getFiles() : []

  const formData = new FormData(document.getElementById("publishForm"))
  formData.append("id", window.infoData.id)
  formData.append("pics", images.join("|"))
  formData.append("videos", videos.join("|"))

  try {
    const response = await fetch("/opers/forum/highend_edit.html", {
      method: "POST",
      body: formData,
      credentials: "include",
    })

    const result = await response.json()

    if (result.code === 200) {
      showSuccess("修改成功，请等待审核！", "修改成功",100000,'member_publish.html')
    } else {
      showInfo(result.msg || "修改失败")
      if (result.msg && result.msg.includes("验证码")) {
        refreshCaptcha()
      }
    }
  } catch (error) {
    console.error("[v0] 提交错误:", error)
    showInfo("网络错误，请重试")
  }
}

document.addEventListener("DOMContentLoaded", async () => {
  // 先初始化地区选择器（会异步加载省份列表并设置默认值）
  await initCitySelector()

  // 再填充其他表单数据
  fillFormData()

  // 最后绑定表单提交事件
  const form = document.getElementById("publishForm")
  if (form) {
    form.addEventListener("submit", handleFormSubmit)
  }
})

      
    </script>
    <script src="/js/imgvideo.js?t=<?php time();?>"></script>
</body>
</html>
