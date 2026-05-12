
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

// 隐藏加载层（关闭 showLoadingOverlay 创建的遮罩）
function hideLoadingLayer() {
  var overlay = document.getElementById("loadingOverlay");
  if (overlay) overlay.style.display = "none";
}

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
function submitFormData(formData) {
  var xhr = new XMLHttpRequest();
  xhr.open('POST', '/opers/forum/fbgd.html', true);

  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4) {
      // 后端返回后关闭加载层
      hideLoadingLayer();
      if (xhr.status === 200) {
        try {
          var result = JSON.parse(xhr.responseText);
          if (result.code === 200) {
            alert('发布成功，等待审核！');
            window.location.href = 'user.html';
          } else {
            // 失败后刷新验证码（此时 Session 更新不影响本次请求）
            refreshCaptcha();
            alert(result.msg || '发布失败，请重试');
          }
        } catch (e) {
          alert('数据解析错误，请重试');
        }
      } else {
        alert('网络错误，请重试');
      }
    }
  };

  xhr.onerror = function() {
    hideLoadingLayer();
    alert('网络错误，请重试');
  };

  xhr.send(formData);
}

// 提交表单
function submitForm(event) {
  event.preventDefault();

  if (!validateForm()) {
    return false;
  }

  // 验证通过：显示加载层，不可手动关闭
  // 不在此刷新验证码，refreshCaptcha() 会让后端重新生成 Session 值导致校验失败
  showLoadingOverlay("数据正在飞速上传中，请稍等...");

  // 收集并提交表单数据
  function collectAndSubmit() {
    var formData = new FormData();

    formData.append("province", document.getElementById("province").value);
    formData.append("city",     document.getElementById("city").value);

    var ageField = document.querySelector('select[name="age"]');
    if (ageField && ageField.value && ageField.value !== "0") formData.append("age", ageField.value);

    var sgField = document.querySelector('select[name="sg"]');
    if (sgField && sgField.value && sgField.value !== "0") formData.append("sg", sgField.value);

    var tzField = document.querySelector('select[name="tz"]');
    if (tzField && tzField.value && tzField.value !== "0") formData.append("tz", tzField.value);

    var xlField = document.querySelector('select[name="xl"]');
    if (xlField && xlField.value && xlField.value !== "0") formData.append("xl", xlField.value);

    var zyField = document.querySelector('select[name="zy"]');
    if (zyField && zyField.value && zyField.value !== "0") formData.append("zy", zyField.value);

    formData.append("price", document.querySelector('input[name="price"]').value.trim());
    formData.append("uname", document.querySelector('input[name="uname"]').value.trim());
    formData.append("captcha", document.getElementById("yzm").value.trim());

    var mobile = document.querySelector('input[name="mobile"]');
    if (mobile && mobile.value.trim()) formData.append("mobile", mobile.value.trim());

    var weixin = document.querySelector('input[name="weixin"]');
    if (weixin && weixin.value.trim()) formData.append("weixin", weixin.value.trim());

    var qq = document.querySelector('input[name="qq"]');
    if (qq && qq.value.trim()) formData.append("qq", qq.value.trim());

    if (window.imageUploader) formData.append("images", JSON.stringify(window.imageUploader.getFiles()));
    if (window.videoUploader) formData.append("videos", JSON.stringify(window.videoUploader.getFiles()));

    submitFormData(formData);
  }

  // 先上传图片，再上传视频，最后提交
  if (window.imageUploader && window.imageUploader.uploadAllFiles) {
    window.imageUploader.uploadAllFiles().then(function(imageResult) {
      if (!imageResult) {
        hideLoadingLayer();
        alert('图片上传失败，请重试');
        return;
      }
      if (window.videoUploader && window.videoUploader.uploadAllFiles) {
        window.videoUploader.uploadAllFiles().then(function(videoResult) {
          if (!videoResult) {
            hideLoadingLayer();
            alert('视频上传失败，请重试');
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
