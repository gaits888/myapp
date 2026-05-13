
// 刷新验证码
function refreshCaptcha() {
  document.getElementById("captchaImg").src = "/lib/yzmcode.html?r=" + Math.random()
}

/**
 * 获取城市或区县列表
 * @param {number} pid 上级id
 * @param {number} type 2:获取城市 3:获取区县
 * @param {function} callback 回调函数
 */
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

/**
 * 更新城市下拉框
 * @param {number} provinceId 省份id
 * @param {function} callback 回调函数
 */
function updateCityOptions(provinceId, callback) {
  var citySelect = document.getElementById("city");
  var districtSelect = document.getElementById("district");

  if (!provinceId) {
    citySelect.innerHTML = '<option value="">请先选择省份</option>';
    districtSelect.innerHTML = '<option value="">请先选择城市</option>';
    if (callback) callback();
    return;
  }

  citySelect.innerHTML = '<option value="">加载中...</option>';
  districtSelect.innerHTML = '<option value="">请先选择城市</option>';

  getRegionList(provinceId, 2, function(cities) {
    citySelect.innerHTML = '<option value="">请选择城市</option>';
    for (var i = 0; i < cities.length; i++) {
      var city = cities[i];
      var option = document.createElement("option");
      option.value = city.id;
      option.textContent = city.fullname || city.name;
      citySelect.appendChild(option);
    }

    if (window.selectedCity) {
      citySelect.value = window.selectedCity;
      updateDistrictOptions(window.selectedCity, callback);
    } else {
      if (callback) callback();
    }
  });
}

/**
 * 更新区县下拉框
 * @param {number} cityId 城市id
 * @param {function} callback 回调函数
 */
function updateDistrictOptions(cityId, callback) {
  var districtSelect = document.getElementById("district");

  if (!cityId) {
    districtSelect.innerHTML = '<option value="">请先选择城市</option>';
    if (callback) callback();
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

    if (window.selectedDistrict) {
      districtSelect.value = window.selectedDistrict;
    }
    
    if (callback) callback();
  });
}

// 初始化地区选择器
function initCitySelector(callback) {
  var provinceSelect = document.getElementById("province");
  var citySelect = document.getElementById("city");
  var districtSelect = document.getElementById("district");

  // 如果已经有省份数据（PHP渲染），直接加载城市
  if (window.provinceSelect) {
    provinceSelect.value = window.provinceSelect;
    updateCityOptions(window.provinceSelect, function() {
      if (callback) callback();
    });
  } else {
    if (callback) callback();
  }

  provinceSelect.onchange = function() {
    var provinceId = this.value;
    window.provinceSelect = provinceId;
    window.selectedCity = "";
    window.selectedDistrict = "";
    updateCityOptions(provinceId);
  };

  citySelect.onchange = function() {
    var cityId = this.value;
    window.selectedCity = cityId;
    window.selectedDistrict = "";
    updateDistrictOptions(cityId);
  };

  districtSelect.onchange = function() {
    window.selectedDistrict = this.value;
  };
}

// 填充表单数据
function fillFormData() {
  if (!window.infoData) return;

  var data = window.infoData;

  var fields = [
    "title",
    "typeid",
    "laiyuan",
    "pj",
    "nums",
    "age",
    "wmtj",
    "price",
    "content",
    "uname",
    "mobile",
    "weixin",
    "qq",
    "yuni",
    "address"
  ];

  for (var i = 0; i < fields.length; i++) {
    var field = fields[i];
    var input = document.querySelector('[name="' + field + '"]');
    if (input && data[field]) {
      input.value = data[field];
    }
  }

  // 填充已上传的图片
  if (window.existingImages && window.existingImages.length > 0 && window.imageUploader) {
    for (var j = 0; j < window.existingImages.length; j++) {
      var imagePath = window.existingImages[j];
      window.imageUploader.files.push({
        file: null,
        preview: imagePath,
        type: "image",
        uploaded: true,
        path: imagePath
      });
    }
    window.imageUploader.render();
  }

  // 填充已上传的视频
  if (window.existingVideos && window.existingVideos.length > 0 && window.videoUploader) {
    for (var k = 0; k < window.existingVideos.length; k++) {
      var videoPath = window.existingVideos[k];
      window.videoUploader.files.push({
        file: null,
        preview: videoPath,
        type: "video",
        uploaded: true,
        path: videoPath
      });
    }
    window.videoUploader.render();
  }
}

// 表单验证 - 按顺序依次判断必填项，使用alert()提示
function validateForm() {
  // 验证信息标题
  var title = document.querySelector('[name="title"]');
  if (!title.value.trim()) {
    alert('请输入信息标题');
    title.focus();
    return false;
  }

  // 验证省份
  var province = document.getElementById('province');
  if (!province.value) {
    alert('请选择省份');
    province.focus();
    return false;
  }

  // 验证城市
  var city = document.getElementById('city');
  if (!city.value) {
    alert('请选择城市');
    city.focus();
    return false;
  }

  // 验证区县
  var district = document.getElementById('district');
  if (!district.value) {
    alert('请选择区县');
    district.focus();
    return false;
  }

  // 验证发布类别
  var typeid = document.querySelector('[name="typeid"]');
  if (!typeid.value) {
    alert('请选择发布类别');
    typeid.focus();
    return false;
  }

  // 验证信息来源
  var laiyuan = document.querySelector('[name="laiyuan"]');
  if (!laiyuan.value) {
    alert('请选择信息来源');
    laiyuan.focus();
    return false;
  }

  // 验证综合评价
  var pj = document.querySelector('[name="pj"]');
  if (!pj.value) {
    alert('请选择综合评价');
    pj.focus();
    return false;
  }

  // 验证详细内容
  var content = document.querySelector('[name="content"]');
  if (!content.value.trim()) {
    alert('请填写详细内容');
    content.focus();
    return false;
  }

  // 验证联系人
  var uname = document.querySelector('[name="uname"]');
  if (!uname.value.trim()) {
    alert('请输入联系人');
    uname.focus();
    return false;
  }

  // 验证联系方式（至少填写一项）
  var mobile = document.querySelector('[name="mobile"]').value.trim();
  var weixin = document.querySelector('[name="weixin"]').value.trim();
  var qq = document.querySelector('[name="qq"]').value.trim();
  var yuni = document.querySelector('[name="yuni"]').value.trim();

  if (!mobile && !weixin && !qq && !yuni) {
    alert('请至少填写一项联系方式（手机号、微信、QQ、与你号）');
    document.querySelector('[name="mobile"]').focus();
    return false;
  }

  // 验证详细地址
  var address = document.querySelector('[name="address"]');
  if (!address.value.trim()) {
    alert('请输入详细地址');
    address.focus();
    return false;
  }

  // 验证验证码
  var yzm = document.getElementById('yzm');
  if (!yzm.value.trim()) {
    alert('请输入验证码');
    yzm.focus();
    return false;
  }

  if (yzm.value.length !== 4) {
    alert('验证码为4位');
    yzm.focus();
    return false;
  }

  return true;
}

// 隐藏加载层（关闭 showLoadingOverlay 创建的遮罩）
function hideLoadingLayer() {
  var overlay = document.getElementById('loadingOverlay');
  if (overlay) overlay.style.display = 'none';
}

// 表单提交处理
function handleFormSubmit(e) {
  e.preventDefault();

  if (!validateForm()) {
    return false;
  }

  // 验证通过：调用统一加载层，不可手动关闭
  // 不在此刷新验证码，refreshCaptcha() 会让后端重新生成 Session 值导致校验失败
  showLoadingOverlay('数据正在飞速上传中，请稍等...');

  // 提交表单数据到后端
  function submitFormData() {
    var images   = window.imageUploader ? window.imageUploader.getFiles() : [];
    var videos   = window.videoUploader ? window.videoUploader.getFiles() : [];
    var form     = document.getElementById('publishForm');
    var formData = new FormData(form);
    formData.append('id',     window.infoData.id);
    formData.append('pics',   images.join('|'));
    formData.append('videos', videos.join('|'));

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/opers/forum/publish_edit.html', true);

    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4) {
        // 后端返回后关闭加载层
        hideLoadingLayer();
        if (xhr.status === 200) {
          try {
            var result = JSON.parse(xhr.responseText);
            if (result.code === 200) {
              alert('修改成功，请等待审核！');
              window.location.href = 'member_publish.html';
            } else {
              // 后端返回后统一刷新验证码（此时 Session 更新不影响本次已失败的请求）
              refreshCaptcha();
              alert(result.msg || '修改失败');
            }
          } catch (err) {
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

  // 先上传图片，再上传视频，最后提交表单
  if (window.imageUploader && window.imageUploader.uploadAllFiles) {
    window.imageUploader.uploadAllFiles().then(function(imageSuccess) {
      if (!imageSuccess) {
        hideLoadingLayer();
        alert('图片上传失败，请重试');
        return;
      }
      if (window.videoUploader && window.videoUploader.uploadAllFiles) {
        window.videoUploader.uploadAllFiles().then(function(videoSuccess) {
          if (!videoSuccess) {
            hideLoadingLayer();
            alert('视频上传失败，请重试');
            return;
          }
          submitFormData();
        });
      } else {
        submitFormData();
      }
    });
  } else {
    submitFormData();
  }

  return false;
}

document.addEventListener("DOMContentLoaded", function() {
  // 先初始化地区选择器
  initCitySelector(function() {
    // 再填充其他表单数据
    fillFormData();
  });

  // 绑定表单提交事件
  var form = document.getElementById("publishForm");
  if (form) {
    form.onsubmit = handleFormSubmit;
  }
});
