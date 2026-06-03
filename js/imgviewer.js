/**
 * 图片全屏查看器 - 支持PC端和移动端
 * 功能：点击放大、左右滑动、手势支持
 */
(function($) {
  'use strict';

  var ImageViewer = {
    isOpen: false,
    currentIndex: 0,
    images: [],
    $overlay: null,
    touchStartX: 0,
    touchEndX: 0,

    // 初始化
    init: function(selector) {
      var self = this;
      
      // 创建查看器DOM
      this.createViewer();
      
      // 绑定图片点击事件
      $(document).on('click', selector || '.lazy', function(e) {
        e.preventDefault();
        self.collectImages($(this));
        self.open($(this).index(selector || '.lazy'));
      });

      // 绑定关闭事件
      this.$overlay.on('click', function(e) {
        if ($(e.target).hasClass('img-viewer-overlay')) {
          self.close();
        }
      });
      this.$overlay.on('click', '.img-viewer-close, .img-viewer-close span, .img-viewer-close svg', function(e) {
        e.stopPropagation();
        self.close();
      });

      // 绑定左右箭头点击
      this.$overlay.find('.img-viewer-prev').on('click', function(e) {
        e.stopPropagation();
        self.prev();
      });
      this.$overlay.find('.img-viewer-next').on('click', function(e) {
        e.stopPropagation();
        self.next();
      });

      // 绑定键盘事件
      $(document).on('keydown', function(e) {
        if (!self.isOpen) return;
        if (e.keyCode === 37) self.prev();      // 左箭头
        if (e.keyCode === 39) self.next();      // 右箭头
        if (e.keyCode === 27) self.close();     // ESC
      });

      // 绑定触摸事件（移动端滑动）
      this.$overlay[0].addEventListener('touchstart', function(e) {
        self.touchStartX = e.changedTouches[0].screenX;
      }, { passive: true });

      this.$overlay[0].addEventListener('touchend', function(e) {
        self.touchEndX = e.changedTouches[0].screenX;
        self.handleSwipe();
      }, { passive: true });
    },

    // 创建查看器DOM结构
    createViewer: function() {
      var html = 
        '<div class="img-viewer-overlay">' +
          '<div class="img-viewer-close"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></div>' +
          '<div class="img-viewer-prev"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg></div>' +
          '<div class="img-viewer-next"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg></div>' +
          '<div class="img-viewer-content">' +
            '<img class="img-viewer-image" src="" alt="">' +
          '</div>' +
          '<div class="img-viewer-counter"></div>' +
        '</div>';
      
      $('body').append(html);
      this.$overlay = $('.img-viewer-overlay');

      // 添加样式
      if ($('#img-viewer-style').length === 0) {
        var css = 
          '<style id="img-viewer-style">' +
          '.img-viewer-overlay{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.92);z-index:99999;justify-content:center;align-items:center;}' +
          '.img-viewer-overlay.active{display:flex;}' +
          '.img-viewer-close{position:absolute;top:20px;right:20px;width:46px;height:46px;background:rgba(80,80,80,0.85);border-radius:50%;cursor:pointer;z-index:10;display:flex;justify-content:center;align-items:center;transition:all 0.2s ease;}' +
          '.img-viewer-close:hover{background:rgba(100,100,100,0.9);}' +
          '.img-viewer-close svg{width:22px;height:22px;color:#fff;}' +
          '.img-viewer-prev,.img-viewer-next{position:absolute;top:50%;transform:translateY(-50%);width:46px;height:46px;background:rgba(80,80,80,0.85);border-radius:50%;cursor:pointer;z-index:10;display:flex;justify-content:center;align-items:center;transition:all 0.2s ease;user-select:none;}' +
          '.img-viewer-prev:hover,.img-viewer-next:hover{background:rgba(100,100,100,0.9);}' +
          '.img-viewer-prev svg,.img-viewer-next svg{width:22px;height:22px;color:#fff;}' +
          '.img-viewer-prev{left:20px;}' +
          '.img-viewer-next{right:20px;}' +
          '.img-viewer-content{max-width:90%;max-height:85%;display:flex;justify-content:center;align-items:center;}' +
          '.img-viewer-image{max-width:100%;max-height:85vh;object-fit:contain;transition:opacity 0.3s;}' +
          '.img-viewer-counter{position:absolute;bottom:30px;left:50%;transform:translateX(-50%);color:rgba(255,255,255,0.8);font-size:14px;}' +
          '@media(max-width:768px){' +
            '.img-viewer-prev,.img-viewer-next{width:40px;height:40px;}' +
            '.img-viewer-prev svg,.img-viewer-next svg{width:18px;height:18px;}' +
            '.img-viewer-prev{left:15px;}' +
            '.img-viewer-next{right:15px;}' +
            '.img-viewer-close{width:40px;height:40px;top:15px;right:15px;}' +
            '.img-viewer-close svg{width:18px;height:18px;}' +
          '}' +
          '</style>';
        $('head').append(css);
      }
    },

    // 收集同组图片
    collectImages: function($clickedImg) {
      var self = this;
      this.images = [];
      
      // 获取同一容器内的所有懒加载图片
      var $container = $clickedImg.closest('div').parent();
      var $imgs = $container.find('.lazy');
      
      if ($imgs.length <= 1) {
        // 如果容器内只有一张或没有，则收集页面所有懒加载图片
        $imgs = $('.lazy');
      }
      
      $imgs.each(function() {
        var src = $(this).attr('data-original') || $(this).attr('src');
        if (src && src.indexOf('data:image') === -1) {
          self.images.push(src);
        }
      });

      // 找到当前点击图片的索引
      var clickedSrc = $clickedImg.attr('data-original') || $clickedImg.attr('src');
      this.currentIndex = this.images.indexOf(clickedSrc);
      if (this.currentIndex === -1) this.currentIndex = 0;
    },

    // 打开查看器
    open: function(index) {
      if (typeof index === 'number' && index >= 0) {
        this.currentIndex = index;
      }
      this.isOpen = true;
      this.$overlay.addClass('active');
      this.showImage();
      $('body').css('overflow', 'hidden');
    },

    // 关闭查看器
    close: function() {
      this.isOpen = false;
      this.$overlay.removeClass('active');
      $('body').css('overflow', '');
    },

    // 显示当前图片
    showImage: function() {
      if (this.images.length === 0) return;
      
      var src = this.images[this.currentIndex];
      var $img = this.$overlay.find('.img-viewer-image');
      
      $img.css('opacity', 0.5);
      $img.attr('src', src).on('load', function() {
        $(this).css('opacity', 1);
      });

      // 更新计数器
      this.$overlay.find('.img-viewer-counter').text(
        (this.currentIndex + 1) + ' / ' + this.images.length
      );

      // 更新箭头显示
      this.$overlay.find('.img-viewer-prev').toggle(this.images.length > 1);
      this.$overlay.find('.img-viewer-next').toggle(this.images.length > 1);
    },

    // 上一张
    prev: function() {
      if (this.images.length <= 1) return;
      this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
      this.showImage();
    },

    // 下一张
    next: function() {
      if (this.images.length <= 1) return;
      this.currentIndex = (this.currentIndex + 1) % this.images.length;
      this.showImage();
    },

    // 处理滑动手势
    handleSwipe: function() {
      var diff = this.touchStartX - this.touchEndX;
      var threshold = 50; // 滑动阈值

      if (Math.abs(diff) < threshold) return;

      if (diff > 0) {
        this.next(); // 左滑 -> 下一张
      } else {
        this.prev(); // 右滑 -> 上一张
      }
    }
  };

  // 暴露到全局
  window.ImageViewer = ImageViewer;

  // jQuery插件
  $.fn.imageViewer = function() {
    ImageViewer.init(this.selector || '.lazy');
    return this;
  };

  // 自动初始化
  $(document).ready(function() {
    ImageViewer.init('.lazy');
  });

})(jQuery);
