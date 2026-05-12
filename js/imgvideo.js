// 图片视频上传管理类
class MediaUploader {
  constructor(containerId, options = {}) {
    this.container = document.getElementById(containerId)
    this.type = options.type || "image" // 'image' or 'video'
    this.maxFiles = options.maxFiles || 9
    this.maxSize = options.maxSize || (this.type === "image" ? 5 : 50) // MB
    this.files = []

    this.init()
  }

  init() {
    this.render()
    this.bindEvents()
  }

  render() {
    const grid = this.container.querySelector(".upload-grid")
    grid.innerHTML = ""

    // 渲染已有文件
    this.files.forEach((file, index) => {
      grid.appendChild(this.createFileItem(file, index))
    })

    // 添加上传按钮
    if (this.files.length < this.maxFiles) {
      grid.appendChild(this.createUploadButton())
    }
  }

  createFileItem(file, index) {
    const item = document.createElement("div")
    item.className = "upload-item has-file"

    const preview =
      this.type === "image"
        ? `<img src="${file.preview}" class="upload-preview" alt="预览">`
        : `<div class="upload-video-preview">
                 <svg class="video-play-icon" fill="currentColor" viewBox="0 0 24 24">
                   <path d="M8 5v14l11-7z"/>
                 </svg>
               </div>`

    item.innerHTML = `
            ${preview}
            <button type="button" class="upload-remove" data-index="${index}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        `

    return item
  }

  createUploadButton() {
    const item = document.createElement("div")
    item.className = "upload-item"

    const icon =
      this.type === "image"
        ? `<svg class="upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
               </svg>`
        : `<svg class="upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
               </svg>`

    item.innerHTML = `
            <div class="upload-content">
                ${icon}
                <span class="upload-text">点击上传</span>
            </div>
        `

    item.onclick = () => this.triggerFileInput()

    return item
  }

  bindEvents() {
    this.container.addEventListener("click", (e) => {
      const videoPreview = e.target.closest(".upload-video-preview")
      if (videoPreview) {
        const item = videoPreview.closest(".upload-item")
        const index = Array.from(this.container.querySelectorAll(".upload-item.has-file")).indexOf(item)
        if (index !== -1 && this.files[index]) {
          this.showVideoModal(this.files[index])
        }
        return
      }

      const removeBtn = e.target.closest(".upload-remove")
      if (removeBtn) {
        const index = Number.parseInt(removeBtn.dataset.index)
        this.removeFile(index)
      }
    })
  }

  showVideoModal(fileItem) {
    // 创建模态框
    const modal = document.createElement("div")
    modal.className = "video-modal"
    modal.style.cssText = `
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.9);
      z-index: 10000;
      display: flex;
      align-items: center;
      justify-content: center;
    `

    const videoUrl = fileItem.path || (fileItem.file ? URL.createObjectURL(fileItem.file) : null)

    if (!videoUrl) {
      alert("视频文件不存在")
      return
    }

    modal.innerHTML = `
      <div style="position: relative; width: 90%; max-width: 800px;">
        <video controls autoplay style="width: 100%; max-height: 80vh;">
          <source src="${videoUrl}" type="video/mp4">
          您的浏览器不支持视频播放
        </video>
        <button class="close-modal" style="
          position: absolute;
          top: -40px;
          right: 0;
          background: transparent;
          border: none;
          color: white;
          font-size: 32px;
          cursor: pointer;
          width: 40px;
          height: 40px;
        ">×</button>
      </div>
    `

    document.body.appendChild(modal)

    // 关闭模态框
    const closeModal = () => {
      const video = modal.querySelector("video")
      if (video) {
        video.pause()
      }
      modal.remove()
    }

    modal.querySelector(".close-modal").addEventListener("click", closeModal)
    modal.addEventListener("click", (e) => {
      if (e.target === modal) {
        closeModal()
      }
    })
  }

  triggerFileInput() {
    const input = document.createElement("input")
    input.type = "file"
    input.accept = this.type === "image" ? "image/*" : "video/*"
    input.multiple = true

    input.onchange = (e) => this.handleFileSelect(e)
    input.click()
  }

  async handleFileSelect(e) {
    const files = Array.from(e.target.files)
    const remainingSlots = this.maxFiles - this.files.length
    const filesToAdd = files.slice(0, remainingSlots)

    for (const file of filesToAdd) {
      if (file.size > this.maxSize * 1024 * 1024) {
        alert(`文件 ${file.name} 超过 ${this.maxSize}MB 限制`)
        continue
      }

      // 只做本地预览，不立即上传
      if (this.type === "image") {
        const compressed = await this.compressImage(file)
        this.files.push({
          file: compressed.file,
          preview: compressed.preview,
          type: "image",
          uploaded: false, // 标记未上传
          path: null,
        })
      } else {
        const preview = await this.createVideoPreview(file)
        this.files.push({
          file,
          preview,
          type: "video",
          uploaded: false, // 标记未上传
          path: null,
        })
      }
    }

    this.render()
  }

  async compressImage(file) {
    return new Promise((resolve) => {
      const reader = new FileReader()
      reader.onload = (e) => {
        const img = new Image()
        img.onload = () => {
          const canvas = document.createElement("canvas")
          let width = img.width
          let height = img.height
          const maxSize = 1920

          if (width > height && width > maxSize) {
            height = (height * maxSize) / width
            width = maxSize
          } else if (height > maxSize) {
            width = (width * maxSize) / height
            height = maxSize
          }

          canvas.width = width
          canvas.height = height

          const ctx = canvas.getContext("2d")
          ctx.drawImage(img, 0, 0, width, height)

          canvas.toBlob(
            (blob) => {
              resolve({
                file: new File([blob], file.name, { type: "image/jpeg" }),
                preview: canvas.toDataURL("image/jpeg", 0.8),
                type: "image",
              })
            },
            "image/jpeg",
            0.8,
          )
        }
        img.src = e.target.result
      }
      reader.readAsDataURL(file)
    })
  }

  async createVideoPreview(file) {
    return new Promise((resolve) => {
      const video = document.createElement("video")
      video.preload = "metadata"
      video.onloadedmetadata = () => {
        video.currentTime = 1
      }
      video.onseeked = () => {
        const canvas = document.createElement("canvas")
        canvas.width = video.videoWidth
        canvas.height = video.videoHeight
        canvas.getContext("2d").drawImage(video, 0, 0)
        resolve(canvas.toDataURL())
      }
      video.src = URL.createObjectURL(file)
    })
  }

  async removeFile(index) {
    const item = this.files[index]

    // 如果文件已上传到服务器，需要调用删除接口
    if (item.uploaded && item.path) {
      const deleteUrl = item.type === "image" ? "/opers/forum/delete_image.html" : "/opers/forum/delete_video.html"

      try {
        const response = await fetch(deleteUrl, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: `filePath=${encodeURIComponent(item.path)}`,
          credentials: "include",
        })

        const result = await response.json()

        if (!result.success) {
          // showInfo("删除失败:", result.message)
          // 即使服务器删除失败，也继续删除本地预览
        }
      } catch (error) {
        console.error("删除请求失败:", error)
        // 网络错误也继续删除本地预览
      }
    }

    // 从数组中移除文件
    this.files.splice(index, 1)
    this.render()
  }

  getFiles() {
    // 返回已上传文件的路径，如果未上传返回空数组
    return this.files.filter((item) => item.uploaded && item.path).map((item) => item.path)
  }

  // 新增方法：获取所有文件对象（包括未上传的）
  getAllFileObjects() {
    return this.files
  }

  async uploadAllFiles() {
    const uploadPromises = []

    for (let i = 0; i < this.files.length; i++) {
      const item = this.files[i]

      // 如果已经上传过，跳过
      if (item.uploaded && item.path) {
        continue
      }

      // 上传文件
      const uploadPromise = this.uploadFile(item.file, item.type).then((result) => {
        if (result && result.code == 200) {
          this.files[i].path = result.data.url
          this.files[i].uploaded = true
          return result
        } else {
          alert(result.msg || "上传失败")
          return null
        }
      })
      uploadPromises.push(uploadPromise)
    }

    // 等待所有上传完成
    try {
      await Promise.all(uploadPromises)
      return true
    } catch (error) {
      console.error("文件上传失败:", error)
      alert(error.message)
      return false
    }
  }

  async uploadFile(file, type) {
    const formData = new FormData()
    // formData.append(type === "image" ? "image" : "video", file)

    // const uploadUrl = type === "image" ? "/opers/forum/upload_image.html" : "/opers/forum/upload_video.html"
    if(type === "image"){
      formData.append('type', 'image');
    }else{
      formData.append('type', 'video');
    }
    formData.append('file', file);
    const uploadUrl = '/uploads_api.html';


    try {
      const response = await fetch(uploadUrl, {
        method: "POST",
        body: formData,
        credentials: "include",
      })

      const result = await response.json()

      if (result.code == 200) {
        return result
      } else {
        alert(result.msg || "上传失败")
        return null
      }
    } catch (error) {
      console.error("上传错误:", error)
      alert("网络错误，上传失败")
      return null
    }
  }
}

// 初始化上传组件
window.imageUploader = null
window.videoUploader = null

document.addEventListener("DOMContentLoaded", () => {
  if (document.getElementById("imageUploadContainer")) {
    window.imageUploader = new MediaUploader("imageUploadContainer", {
      type: "image",
      maxFiles: 9,
      maxSize: 5,
    })
  }

  if (document.getElementById("videoUploadContainer")) {
    window.videoUploader = new MediaUploader("videoUploadContainer", {
      type: "video",
      maxFiles: 3,
      maxSize: 50,
    })
  }
})
