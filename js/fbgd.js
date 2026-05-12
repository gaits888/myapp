
/**
 * 发布页面表单验证和提交
 */

// 防重复提交标志
var isSubmitting = false;

// 表单验证 - 使用alert()依次提示
function validateForm() {
  // 1. 验证省份（必填）
  var province = document.getElementById("province");
  if (!province || !province.value) {
    alert("请选择省份");
    province.focus();
    return false;
  }

  // 2. 验证城市（必填）
  var city = document.getElementById("city");
  if (!city || !city.value) {
    alert("请选择城市");
    city.focus();
    return false;
  }

  // 3. 验证年龄
  var age = document.querySelector('select[name="age"]');
  if (!age || !age.value || age.value === "0") {
    alert("请选择年龄");
    age.focus();
    return false;
  }

  // 4. 验证身高
  var sg = document.querySelector('select[name="sg"]');
  if (!sg || !sg.value || sg.value === "0") {
    alert("请选择身高");
    sg.focus();
    return false;
  }

  // 5. 验证体重
  var tz = document.querySelector('select[name="tz"]');
  if (!tz || !tz.value || tz.value === "0") {
    alert("请选择体重");
    tz.focus();
    return false;
  }

  // 6. 验证学历
  var xl = document.querySelector('select[name="xl"]');
  if (!xl || !xl.value || xl.value === "0") {
    alert("请选择学历");
    xl.focus();
    return false;
  }

  // 7. 验证职业
  var zy = document.querySelector('select[name="zy"]');
  if (!zy || !zy.value || zy.value === "0") {
    alert("请选择职业");
    zy.focus();
    return false;
  }

  // 8. 验证价格（必填）
  var price = document.querySelector('input[name="price"]');
  if (!price || !price.value.trim()) {
    alert("请输入约会价格");
    price.focus();
    return false;
  }

  // 9. 验证昵称（必填）
  var uname = document.querySelector('input[name="uname"]');
  if (!uname || !uname.value.trim()) {
    alert("请输入昵称");
    uname.focus();
    return false;
  }

  // 10. 验证联系方式（至少填写一项）
  var mobile = document.querySelector('input[name="mobile"]');
  var weixin = document.querySelector('input[name="weixin"]');
  var qq = document.querySelector('input[name="qq"]');

  var hasMobile = mobile && mobile.value.trim();
  var hasWeixin = weixin && weixin.value.trim();
  var hasQQ = qq && qq.value.trim();

  if (!hasMobile && !hasWeixin && !hasQQ) {
    alert("请至少填写一项联系方式（手机号、微信、QQ）");
    mobile.focus();
    return false;
  }

  // 11. 验证图片上传（至少一张）
  if (window.imageUploader) {
    var images = window.imageUploader.files;
    if (!images || images.length === 0) {
      alert("请至少上传一张图片");
      var imageSection = document.getElementById("imageUploadContainer");
      if (imageSection) {
        imageSection.scrollIntoView({ behavior: "smooth", block: "center" });
      }
      return false;
    }
  } else {
    alert("图片上传组件未初始化");
    return false;
  }

  // 12. 验证验证码（必填）
  var yzm = document.getElementById("yzm");
  if (!yzm || !yzm.value.trim()) {
    alert("请输入验证码");
    yzm.focus();
    return false;
  }

  if (yzm.value.length !== 4) {
    alert("验证码为4位");
    yzm.focus();
    return false;
  }

  return true;
}

// 刷新验证码
function refreshCaptcha() {
  var captchaImg = document.getElementById("captchaImg");
  if (captchaImg) {
    captchaImg.src = "/lib/yzmcode.html?r=" + Math.random();
  }
}

// 获取城市或区县列表（使用传统AJAX）
function getRegionList(pid, type, callback) {
  var postData = 'pid=' + encodeURIComponent(pid) + '&type=' + encodeURIComponent(type);
  
  var xhr = new XMLHttpRequest();
  xhr.open('POST', '/opers/city/getcity.html', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        try {
          var result = JSON.parse(xhr.responseText);
          if (result.code === 200) {
            callback(result.data || []);
          } else {
            callback([]);
          }
        } catch (e) {
          callback([]);
        }
      } else {
        callback([]);
      }
    }
  };
  
  xhr.send(postData);
}

// 更新城市下拉框
function updateCityOptions(provinceId) {
  var citySelect = document.getElementById("city");
  var districtSelect = document.getElementById("district");

  if (!provinceId) {
    citySelect.innerHTML = '<option value="">请先选择省份</option>';
    if (districtSelect) {
      districtSelect.innerHTML = '<option value="">请先选择城市</option>';
    }
    return;
  }

  citySelect.innerHTML = '<option value="">加载中...</option>';
  if (districtSelect) {
    districtSelect.innerHTML = '<option value="">请先选择城市</option>';
  }

  getRegionList(provinceId, 2, function(cities) {
    citySelect.innerHTML = '<option value="">请选择城市</option>';
    for (var i = 0; i < cities.length; i++) {
      var city = cities[i];
      var option = document.createElement("option");
      option.value = city.id;
      option.textContent = city.fullname || city.name;
      citySelect.appendChild(option);
    }
  });
}

// 更新区县下拉框
function updateDistrictOptions(cityId) {
  var districtSelect = document.getElementById("district");

  if (!districtSelect) return;

  if (!cityId) {
    districtSelect.innerHTML = '<option value="">请先选择城市</option>';
    return;
  }

  districtSelect.innerHTML = '<option value="">加载中...</option>';

  getRegionList(cityId, 3, function(districts) {
    districtSelect.innerHTML = '<option value="">请选择区县</option>';
    for (var i = 0; i < districts.length; i++) {
      var district = districts[i];
      var option = document.createElement("option");
      option.value = district.id;
      option.textContent = district.fullname || district.name;
      districtSelect.appendChild(option);
    }
  });
}

// 提交表单数据（使用传统AJAX）
function submitFormData(formData, submitBtn, originalHTML) {
  var xhr = new XMLHttpRequest();
  xhr.open('POST', '/opers/forum/fbgd.html', true);

  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        try {
          var result = JSON.parse(xhr.responseText);
          if (result.code === 200) {
            alert('发布成功，等待审核！');
            window.location.href = 'user.html';
          } else {
            alert(result.msg || '发布失败，请重试');
            refreshCaptcha();
            isSubmitting = false;
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHTML;
          }
        } catch (e) {
          alert('数据解析错误');
          isSubmitting = false;
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalHTML;
        }
      } else {
        alert('网络错误，请重试');
        isSubmitting = false;
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalHTML;
      }
    }
  };

  xhr.onerror = function() {
    alert('网络错误，请重试');
    isSubmitting = false;
    submitBtn.disabled = false;
    submitBtn.innerHTML = originalHTML;
  };

  xhr.send(formData);
}

// 提交表单
function submitForm(event) {
  event.preventDefault();

  // 防重复提交检查
  if (isSubmitting) {
    alert('正在提交中，请勿重复点击');
    return false;
  }

  if (!validateForm()) {
    return false;
  }

  var submitBtn = document.querySelector(".submit-button");
  if (!submitBtn) return false;

  // 设置提交锁
  isSubmitting = true;

  var originalHTML = submitBtn.innerHTML;
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>上传文件中...';

  // 收集表单数据的函数
  function collectAndSubmit() {
    submitBtn.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>提交数据中...';

    var formData = new FormData();

    // 基本信息
    formData.append("province", document.getElementById("province").value);
    formData.append("city", document.getElementById("city").value);

    // 年龄、身高、体重、学历、职业
    var ageField = document.querySelector('select[name="age"]');
    if (ageField && ageField.value && ageField.value !== "0") {
      formData.append("age", ageField.value);
    }

    var sgField = document.querySelector('select[name="sg"]');
    if (sgField && sgField.value && sgField.value !== "0") {
      formData.append("sg", sgField.value);
    }

    var tzField = document.querySelector('select[name="tz"]');
    if (tzField && tzField.value && tzField.value !== "0") {
      formData.append("tz", tzField.value);
    }

    var xlField = document.querySelector('select[name="xl"]');
    if (xlField && xlField.value && xlField.value !== "0") {
      formData.append("xl", xlField.value);
    }

    var zyField = document.querySelector('select[name="zy"]');
    if (zyField && zyField.value && zyField.value !== "0") {
      formData.append("zy", zyField.value);
    }

    // 价格
    formData.append("price", document.querySelector('input[name="price"]').value.trim());

    // 联系方式
    formData.append("uname", document.querySelector('input[name="uname"]').value.trim());

    var mobile = document.querySelector('input[name="mobile"]');
    if (mobile && mobile.value.trim()) formData.append("mobile", mobile.value.trim());

    var weixin = document.querySelector('input[name="weixin"]');
    if (weixin && weixin.value.trim()) formData.append("weixin", weixin.value.trim());

    var qq = document.querySelector('input[name="qq"]');
    if (qq && qq.value.trim()) formData.append("qq", qq.value.trim());

    // 验证码
    formData.append("captcha", document.getElementById("yzm").value.trim());

    // 图片和视频
    if (window.imageUploader) {
      var images = window.imageUploader.getFiles();
      formData.append("images", JSON.stringify(images));
    }

    if (window.videoUploader) {
      var videos = window.videoUploader.getFiles();
      formData.append("videos", JSON.stringify(videos));
    }

    submitFormData(formData, submitBtn, originalHTML);
  }

  // 上传图片
  if (window.imageUploader && window.imageUploader.uploadAllFiles) {
    window.imageUploader.uploadAllFiles().then(function(imageResult) {
      if (!imageResult) {
        alert('图片上传失败');
        isSubmitting = false;
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalHTML;
        return;
      }

      // 上传视频
      if (window.videoUploader && window.videoUploader.uploadAllFiles) {
        window.videoUploader.uploadAllFiles().then(function(videoResult) {
          if (!videoResult) {
            alert('视频上传失败');
            isSubmitting = false;
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHTML;
            return;
          }
          collectAndSubmit();
        });
      } else {
        collectAndSubmit();
      }
    });
  } else {
    collectAndSubmit();
  }

  return false;
}

// 页面加载完成后初始化
document.addEventListener("DOMContentLoaded", function() {
  // 绑定表单提交事件
  var publishForm = document.getElementById("publishForm");
  if (publishForm) {
    publishForm.onsubmit = submitForm;
  }

  // 验证码图片点击刷新
  var captchaImg = document.getElementById("captchaImg");
  if (captchaImg) {
    captchaImg.onclick = refreshCaptcha;
    captchaImg.style.cursor = "pointer";
  }

  // 省份选择变化
  var provinceSelect = document.getElementById("province");
  if (provinceSelect) {
    provinceSelect.onchange = function() {
      var provinceId = this.value;
      updateCityOptions(provinceId);
    };
  }

  // 城市选择变化
  var citySelect = document.getElementById("city");
  if (citySelect) {
    citySelect.onchange = function() {
      var cityId = this.value;
      updateDistrictOptions(cityId);
    };
  }

  // 初始化图片上传组件
  var imageContainer = document.getElementById("imageUploadContainer");
  if (imageContainer && typeof window.MediaUploader !== "undefined") {
    window.imageUploader = new window.MediaUploader(imageContainer, {
      type: "image",
      maxFiles: 9,
      maxSize: 5 * 1024 * 1024
    });
  }

  // 初始化视频上传组件
  var videoContainer = document.getElementById("videoUploadContainer");
  if (videoContainer && typeof window.MediaUploader !== "undefined") {
    window.videoUploader = new window.MediaUploader(videoContainer, {
      type: "video",
      maxFiles: 3,
      maxSize: 50 * 1024 * 1024
    });
  }
});