function showMessage(message) {
  const existingToast = document.querySelector(".toast-message")
  if (existingToast) {
    existingToast.remove()
  }

  const toast = document.createElement("div")
  toast.className = "toast-message"
  toast.textContent = message
  toast.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        z-index: 9999;
        font-size: 14px;
        max-width: 80%;
        text-align: center;
    `

  document.body.appendChild(toast)

  setTimeout(() => {
    toast.remove()
  }, 2000)
}

function fetchContacts(infoId, type, infotype = 1) {
  let api_urls // Declare api_urls variable
  if (infotype === 1) {
    api_urls = "/opers/info/seeinfop.html"
  } else if (infotype === 2) {
    api_urls = "/opers/info/seeinfog.html"
  } else if (infotype === 3 || infotype === 4) {
    api_urls = "/opers/info/seeinfob.html"
  } else {
    return
  }
  const xhr = new XMLHttpRequest()
  xhr.open("POST", api_urls, true)
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded")

  xhr.onreadystatechange = () => {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        const response = JSON.parse(xhr.responseText)

        if (response.code === 200) {
          showInfo(response.msg || "操作成功")
          showContactModal(response.data)
          if (infotype === 1) {
            document.getElementById("pointsContactBtn").style.display = "none"
            document.getElementById("memberContactBtn").style.display = "none"
            document.getElementById("viewedContactBtn").style.display = "block"
          } else{
            document.getElementById("detailContactspan").innerHTML = "查看联系方式";
          }
          isJs = 1;
        } else if (response.code === 401){
            showInfo(response.msg || "操作失败，请重试",'提示','10000','/login.html')
        } else {
          showInfo(response.msg || "操作失败，请重试")
        }
      } else {
        showInfo("网络请求失败，请重试")
      }
    }
  }

  xhr.send("info_id=" + infoId + "&type=" + type + "&infotype=" + infotype)
}

function showContactModal(data) {
  const modal = document.createElement("div")
  modal.className = "contact-modal"
  modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    `

  const modalContent = document.createElement("div")
  modalContent.style.cssText = `
        background: white;
        border-radius: 12px;
        padding: 24px;
        width: 90%;
        max-width: 400px;
        max-height: 80vh;
        overflow-y: auto;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    `

  const modalTitle = document.createElement("h3")
  modalTitle.textContent = "联系方式"
  modalTitle.style.cssText = `
        color: #333;
        margin-bottom: 20px;
        text-align: center;
        font-size: 18px;
        font-weight: 600;
    `

  const contactList = document.createElement("div")
  contactList.style.cssText = "margin-bottom: 20px;"

  function addContactItem(container, label, value, iconHtml) {
    const item = document.createElement("div")
    item.style.cssText = `
            display: flex;
            align-items: center;
            padding: 12px;
            background: #fff5f8;
            border-radius: 8px;
            margin-bottom: 10px;
            border: 1px solid #ffe0eb;
        `

    const icon = document.createElement("div")
    icon.innerHTML = iconHtml
    icon.style.cssText = "color: #FF1493; flex-shrink: 0;"

    const textContainer = document.createElement("div")
    textContainer.style.cssText = "flex: 1;"

    const labelElem = document.createElement("div")
    labelElem.textContent = label
    labelElem.style.cssText = "color: #888; font-size: 12px; margin-bottom: 4px;"

    const valueElem = document.createElement("div")
    valueElem.textContent = value
    valueElem.style.cssText = "color: #333; font-size: 14px; font-weight: 500;"

    const copyBtn = document.createElement("button")
    copyBtn.textContent = "复制"

    if (value === "") {
      copyBtn.style.cssText = `
            padding: 6px 12px;
            background: linear-gradient(135deg, rgba(248 144 10 / 78%) 0%, rgb(255, 105, 180) 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            flex-shrink: 0;
            display: none;
        `
    } else {
      copyBtn.style.cssText = `
            padding: 6px 12px;
            background: linear-gradient(135deg, rgba(248 144 10 / 78%) 0%, rgb(255, 105, 180) 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            flex-shrink: 0;
            transition: transform 0.2s;
        `
    }

    copyBtn.onclick = () => {
      copyText(value)
    }

    // 添加点击效果
    copyBtn.onmousedown = () => {
      copyBtn.style.transform = "scale(0.95)"
    }
    copyBtn.onmouseup = () => {
      copyBtn.style.transform = "scale(1)"
    }

    textContainer.appendChild(labelElem)
    textContainer.appendChild(valueElem)
    item.appendChild(icon)
    item.appendChild(textContainer)
    item.appendChild(copyBtn)
    container.appendChild(item)
  }

  const phoneIcon ='<svg t="1775061072658" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="41374" width="28" height="28" style="margin-right:10px;"><path d="M256 85.333333h512a42.666667 42.666667 0 0 1 42.666667 42.666667v768a42.666667 42.666667 0 0 1-42.666667 42.666667H256a42.666667 42.666667 0 0 1-42.666667-42.666667V128a42.666667 42.666667 0 0 1 42.666667-42.666667z m256 640a42.666667 42.666667 0 1 0 0 85.333334 42.666667 42.666667 0 0 0 0-85.333334z" fill="#f9a31a" p-id="41375"></path></svg>'
  const weixinIcon = '<svg t="1775060440836" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="34583" width="28" height="28" style="margin-right:10px;"><path d="M669.3 369.4c9.8 0 19.6 0 29.4 1.6C671 245.2 536.9 152 383.2 152 211.6 152 71 269.7 71 416.8c0 85 45.8 156.9 124.2 210.9l-31.1 93.2L273.6 667c39.2 8.2 70.3 16.3 109.5 16.3 9.8 0 19.6 0 31.1-1.6-6.5-21.3-9.8-42.5-9.8-65.4 0.1-135.7 116.2-246.9 264.9-246.9z m-168.4-85c24.5 0 39.2 16.3 39.2 39.2 0 22.9-16.3 39.2-39.2 39.2-24.5 0-47.4-16.4-47.4-39.2 0-24.5 24.6-39.2 47.4-39.2z m-216.3 73.1c-24.7 0-47.8-16.2-47.8-38.8 0-24.3 24.7-38.8 47.8-38.8s39.5 16.2 39.5 38.8c0.1 22.7-16.4 38.8-39.5 38.8z" fill="#f9a31a" p-id="34584"></path><path d="M953.8 613c0-125.9-124.2-227.2-264.8-227.2-148.8 0-266.5 103-266.5 227.2 0 125.9 117.7 227.2 266.5 227.2 31.1 0 62.1-8.2 93.2-16.3l85 47.4-22.9-78.5c62.1-47.4 109.5-109.5 109.5-179.8z m-351.5-39.2c-14.7 0-31.1-14.7-31.1-31.1 0-14.7 16.3-31.1 31.1-31.1 22.9 0 39.2 16.3 39.2 31.1 0 16.4-14.7 31.1-39.2 31.1z m178-7.6c-14.8 0-31.3-14.6-31.3-30.7 0-14.6 16.5-30.7 31.3-30.7 23.1 0 39.5 16.2 39.5 30.7 0 16.2-16.4 30.7-39.5 30.7z" fill="#f9a31a" p-id="34585"></path></svg>'
  const qqIcon =
    '<svg t="1775060498876" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="35757" width="28" height="28" style="margin-right:10px;"><path d="M162.797568 576.497664c-30.287872 75.442176-35.29728 147.37408-10.903552 160.73728 16.87552 9.275392 43.149312-11.943936 67.883008-50.542592 9.814016 42.274816 34.000896 80.203776 68.589568 110.866432-36.21888 14.116864-59.94496 37.175296-59.94496 63.24224 0 42.944512 64.079872 77.613056 143.112192 77.613056 71.305216 0 130.373632-28.153856 141.273088-65.247232 2.885632 0 14.209024 0 16.961536 0 11.114496 37.093376 70.053888 65.247232 141.441024 65.247232 79.120384 0 143.11424-34.670592 143.11424-77.613056 0-26.066944-23.683072-48.955392-59.98592-63.24224 34.463744-30.662656 58.81856-68.591616 68.548608-110.866432 24.727552 38.598656 50.880512 59.817984 67.84 50.542592 24.518656-13.3632 19.632128-85.295104-10.94656-160.73728-23.891968-59.068416-56.266752-102.67648-80.953344-112.449536 0.333824-3.592192 0.626688-7.563264 0.626688-11.364352 0-22.892544-6.098944-44.027904-16.498688-61.239296 0.210944-1.376256 0.210944-2.67264 0.210944-4.050944 0-10.569728-2.381824-20.385792-6.475776-28.86656-6.223872-153.76384-101.339136-276.02944-255.35488-276.02944-153.974784 0-249.217024 122.267648-255.440896 276.02944-4.009984 8.605696-6.473728 18.466816-6.473728 28.993536 0 1.378304 0 2.67264 0.167936 4.052992-10.190848 17.084416-16.29184 38.219776-16.29184 61.19424 0 3.844096 0.206848 7.686144 0.4608 11.446272C219.148288 473.905152 186.650624 517.431296 162.797568 576.497664L162.797568 576.497664z" fill="#f9a31a" p-id="35758"></path></svg>'

  const ylIcon ='<svg t="1775061344508" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="47260" width="28" height="28" style="margin-right:10px;"><path d="M512 938.666667c235.648 0 426.666667-191.018667 426.666667-426.666667S747.648 85.333333 512 85.333333 85.333333 276.352 85.333333 512v426.666667h426.666667zM298.666667 352h384a32 32 0 0 1 0 64H298.666667a32 32 0 0 1 0-64zM266.666667 554.666667a32 32 0 0 1 32-32h384a32 32 0 0 1 0 64H298.666667a32 32 0 0 1-32-32zM298.666667 693.333333h213.333333a32 32 0 0 1 0 64H298.666667a32 32 0 0 1 0-64z" p-id="47261" fill="#f9a31a"></path></svg>'
    

  if (data.mobile) {
    addContactItem(contactList, "手机", data.mobile, phoneIcon)
  } else {
    addContactItem(contactList, "手机", "", phoneIcon)
  }
  if (data.weixin) {
    addContactItem(contactList, "微信", data.weixin, weixinIcon)
  } else {
    addContactItem(contactList, "微信", "", weixinIcon)
  }
  if (data.qq) {
    addContactItem(contactList, "QQ", data.qq, qqIcon)
  } else {
    addContactItem(contactList, "QQ", "", qqIcon)
  }
  if (data.yuli) {
    addContactItem(contactList, "与你", data.yuli, ylIcon)
  } else {
    addContactItem(contactList, "与你", "", ylIcon)
  }

  const closeBtn = document.createElement("button")
  closeBtn.textContent = "关闭"
  closeBtn.style.cssText = `
        width: 100%;
        padding: 12px;
        background: #f5f5f5;
        color: #666;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        cursor: pointer;
        transition: background 0.2s;
    `
  closeBtn.onclick = () => modal.remove()

  // 添加hover效果
  closeBtn.onmouseenter = () => {
    closeBtn.style.background = "#e0e0e0"
  }
  closeBtn.onmouseleave = () => {
    closeBtn.style.background = "#f5f5f5"
  }

  modalContent.appendChild(modalTitle)
  modalContent.appendChild(contactList)
  modalContent.appendChild(closeBtn)
  modal.appendChild(modalContent)
  document.body.appendChild(modal)

  modal.addEventListener("click", (e) => {
    if (e.target === modal) {
      modal.remove()
    }
  })
}




function copyText(textToCopy) {
    // navigator clipboard 需要https等安全上下文
    if (navigator.clipboard && window.isSecureContext) {
        // navigator clipboard 向剪贴板写文本
        navigator.clipboard.writeText(textToCopy).then(function() {
            alert("复制成功");
        }).catch(function() {
            // clipboard API 失败，回退到传统方法
            fallbackCopy(textToCopy);
        });
    } else {
        // 不支持 clipboard API，使用传统方法
        fallbackCopy(textToCopy);
    }

    function fallbackCopy(text) {
        var input = document.createElement("input");
        input.style.position = "fixed";
        input.style.top = "-10000px";
        input.style.zIndex = "-999";
        document.body.appendChild(input);
        input.value = text;
        input.focus();
        input.select();
        try {
            var result = document.execCommand("copy");
            document.body.removeChild(input);
            if (!result || result === "unsuccessful") {
                alert("复制失败");
            } else {
                alert("复制成功");
            }
        } catch (e) {
            document.body.removeChild(input);
            alert("当前浏览器不支持复制功能，请检查更新或更换其他浏览器操作");
        }
    }
}

