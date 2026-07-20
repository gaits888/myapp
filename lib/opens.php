<style>
.contact-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}
  /* Toast 提示 */
        .toast-message {
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
        } 
        .hidden {
            display: none;
        }


.contact-modal-content {
  position: relative;
  background: #fff;
  border-radius: 12px;
  padding: 10px;
/*  width: 100%;*/

  overflow-y: auto;
/*  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);*/
  box-shadow: var(--shadow-md);
  z-index: 1;
}

.contact-modal-title {
  color: #333;
  margin: 0 0 10px;
  text-align: left;
  font-size: 16px;
  font-weight: 600;
}

.contact-list {
/*  margin-bottom: 20px;*/
}


.contact-item {
    display: flex;
    align-items: center;
    padding: 12px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    margin-bottom: 10px;
    gap: 12px;
    border: 1px solid rgb(193 193 193 / 24%);
}

/*.contact-item:last-child {
  margin-bottom: 0;
}*/

.contact-icon {
  color: #FF1493;
  flex-shrink: 0;
  display: flex;
  align-items: center;
}

.contact-text {
  flex: 1;
  min-width: 0;
}

.contact-label {
  color: #888;
  font-size: 13px;
  margin-bottom: 0px;
}

.contact-value {
  color: #333;
  font-size: 14px;
  font-weight: 500;
  word-break: break-all;
}

.contact-copy-btn {
  padding: 6px 12px;
  background: rgba(49 160 243 / 60%);
  color: #fff;
  border: none;
  border-radius: 6px;
  font-size: 12px;
  cursor: pointer;
  flex-shrink: 0;
  transition: transform 0.2s;
}

.contact-copy-btn:active {
  transform: scale(0.95);
}

.contact-copy-btn.hidden {
  display: none;
}

.contact-close-btn {
  width: 100%;
  padding: 12px;
  background: #f5f5f5;
  color: #666;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  cursor: pointer;
  transition: background 0.2s;
}

.contact-close-btn:hover {
  background: #e0e0e0;
}
</style>

    

  <div class="contact-modal-content">
    <div class="contact-list">
<h3 class="contact-modal-title">联系方式</h3>
      <!-- 手机 -->
      <div class="contact-item">
        <div class="contact-icon">
<svg t="1774018781077" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="112371" width="32" height="32"><path d="M512 0C230.4 0 0 230.4 0 512s230.4 512 512 512 512-230.4 512-512-230.4-512-512-512z m243.2 704l-25.6 25.6-6.4 6.4-6.4 6.4c-32 32-140.8 76.8-332.8-108.8-179.2-179.2-128-288-102.4-320l44.8-44.8c19.2-19.2 51.2-19.2 70.4 0l64 64c19.2 19.2 19.2 51.2 0 70.4l-38.4 32-6.4 12.8c-19.2 19.2-19.2 25.6 6.4 51.2l96 89.6c44.8 38.4 51.2 38.4 70.4 25.6l32-38.4c19.2-19.2 51.2-25.6 70.4-6.4l64 64c19.2 19.2 19.2 51.2 0 70.4z" fill="#ff7da7" p-id="112372"></path></svg>
        </div>
        <div class="contact-text">
          <div class="contact-label">手机</div>
          <div class="contact-value" id="contactMobile"><?php echo htmlspecialchars($contact_infos['mobile'] ?? '无'); ?></div>
        </div>
      

         <?php if (!empty($contact_infos['mobile'])): ?>
                <button class="contact-copy-btn" onclick="copyText('<?php echo htmlspecialchars($contact_infos['mobile']); ?>')">复制</button>
                <?php else: ?>
                <button class="contact-copy-btn hidden">复制</button>
                <?php endif; ?>

      </div>

      <!-- 微信 -->
      <div class="contact-item">
        <div class="contact-icon">
<svg t="1774018494151" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="99382" width="32" height="32"><path d="M337.387283 341.82659c-17.757225 0-35.514451 11.83815-35.514451 29.595375s17.757225 29.595376 35.514451 29.595376 29.595376-11.83815 29.595376-29.595376c0-18.49711-11.83815-29.595376-29.595376-29.595375zM577.849711 513.479769c-11.83815 0-22.936416 12.578035-22.936416 23.6763 0 12.578035 11.83815 23.676301 22.936416 23.676301 17.757225 0 29.595376-11.83815 29.595376-23.676301s-11.83815-23.676301-29.595376-23.6763zM501.641618 401.017341c17.757225 0 29.595376-12.578035 29.595376-29.595376 0-17.757225-11.83815-29.595376-29.595376-29.595375s-35.514451 11.83815-35.51445 29.595375 17.757225 29.595376 35.51445 29.595376zM706.589595 513.479769c-11.83815 0-22.936416 12.578035-22.936416 23.6763 0 12.578035 11.83815 23.676301 22.936416 23.676301 17.757225 0 29.595376-11.83815 29.595376-23.676301s-11.83815-23.676301-29.595376-23.6763z" fill="#ff7da7" p-id="99383"></path><path d="M510.520231 2.959538C228.624277 2.959538 0 231.583815 0 513.479769s228.624277 510.520231 510.520231 510.520231 510.520231-228.624277 510.520231-510.520231-228.624277-510.520231-510.520231-510.520231zM413.595376 644.439306c-29.595376 0-53.271676-5.919075-81.387284-12.578034l-81.387283 41.433526 22.936416-71.768786c-58.450867-41.433526-93.965318-95.445087-93.965317-159.815029 0-113.202312 105.803468-201.988439 233.803468-201.98844 114.682081 0 216.046243 71.028902 236.023121 166.473989-7.398844-0.739884-14.797688-1.479769-22.196532-1.479769-110.982659 1.479769-198.289017 85.086705-198.289017 188.67052 0 17.017341 2.959538 33.294798 7.398844 49.572255-7.398844 0.739884-15.537572 1.479769-22.936416 1.479768z m346.265896 82.867052l17.757225 59.190752-63.630058-35.514451c-22.936416 5.919075-46.612717 11.83815-70.289017 11.83815-111.722543 0-199.768786-76.947977-199.768786-172.393063-0.739884-94.705202 87.306358-171.653179 198.289017-171.65318 105.803468 0 199.028902 77.687861 199.028902 172.393064 0 53.271676-34.774566 100.624277-81.387283 136.138728z" fill="#ff7da7" p-id="99384"></path></svg>
        </div>
        <div class="contact-text">
          <div class="contact-label">微信</div>
          <div class="contact-value" id="contactWeixin"><?php echo htmlspecialchars($contact_infos['weixin'] ?? '无'); ?></div>
        </div>
         <?php if (!empty($contact_infos['weixin'])): ?>
                <button class="contact-copy-btn" onclick="copyText('<?php echo htmlspecialchars($contact_infos['weixin']); ?>')">复制</button>
                <?php else: ?>
                <button class="contact-copy-btn hidden">复制</button>
                <?php endif; ?>
      </div>

      <!-- QQ -->
      <div class="contact-item">
        <div class="contact-icon">
<svg t="1774018557735" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="100655" width="32" height="32"><path d="M512 1024C229.226667 1024 0 794.773333 0 512 0 229.226667 229.226667 0 512 0 794.773333 0 1024 229.226667 1024 512 1024 794.773333 794.773333 1024 512 1024ZM693.333333 458.666667C689.024 438.634667 682.666667 426.666667 682.666667 426.666667 682.666667 426.666667 682.944 397.333333 650.666667 330.666667 618.389333 264 522.666667 256 522.666667 256L512 256C512 256 416.277333 264 384 330.666667 351.722667 397.333333 352 426.666667 352 426.666667 352 426.666667 345.642667 438.634667 341.333333 458.666667 337.024 478.698667 341.333333 490.666667 341.333333 490.666667 341.333333 490.666667 286.656 546.421333 288 586.666667 289.344 626.912 299.221333 652.544 320 629.333333 340.778667 606.122667 341.333333 608 341.333333 608 341.333333 608 340.970667 625.333333 352 640 363.029333 654.666667 373.333333 661.333333 373.333333 661.333333 373.333333 661.333333 317.386667 689.034667 330.666667 725.333333 343.946667 761.632 384.874667 768.288 416 768 447.125333 767.712 512 746.666667 512 746.666667L522.666667 746.666667C522.666667 746.666667 587.541333 767.712 618.666667 768 649.792 768.288 690.72 761.632 704 725.333333 717.28 689.034667 661.333333 661.333333 661.333333 661.333333 661.333333 661.333333 671.637333 654.666667 682.666667 640 693.696 625.333333 693.333333 608 693.333333 608 693.333333 608 693.888 606.122667 714.666667 629.333333 735.445333 652.544 745.322667 626.912 746.666667 586.666667 748.010667 546.421333 693.333333 490.666667 693.333333 490.666667 693.333333 490.666667 697.642667 478.698667 693.333333 458.666667Z" fill="#ff7da7" p-id="100656"></path></svg>
        </div>
        <div class="contact-text">
          <div class="contact-label">QQ</div>
          <div class="contact-value" id="contactQQ"><?php echo htmlspecialchars($contact_infos['qq'] ?? '无'); ?></div>
        </div>
         <?php if (!empty($contact_infos['qq'])): ?>
                <button class="contact-copy-btn" onclick="copyText('<?php echo htmlspecialchars($contact_infos['qq']); ?>')">复制</button>
                <?php else: ?>
                <button class="contact-copy-btn hidden">复制</button>
                <?php endif; ?>
      </div>


<?php if (isset($contact_infos['yuli'])): ?>
       <!-- yuni -->
      <div class="contact-item">
        <div class="contact-icon">
          

<svg t="1774018843355" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="114495" width="32" height="32"><path d="M512 260.266667c-149.333333 0-268.8 102.4-268.8 234.666666 0 51.2 21.333333 102.4 55.466667 140.8l8.533333 8.533334v110.933333l136.533333-34.133333h8.533334c21.333333 4.266667 38.4 4.266667 59.733333 4.266666 149.333333 0 268.8-102.4 268.8-230.4 0-128-119.466667-234.666667-268.8-234.666666z m187.733333 226.133333l-119.466666 102.4c-4.266667 4.266667-12.8 8.533333-21.333334 8.533333s-17.066667-4.266667-21.333333-8.533333l-76.8-85.333333-93.866667 81.066666c-12.8 12.8-34.133333 8.533333-46.933333-4.266666-12.8-12.8-8.533333-34.133333 4.266667-46.933334l119.466666-98.133333c12.8-12.8 34.133333-8.533333 42.666667 4.266667l76.8 85.333333 98.133333-85.333333c12.8-12.8 34.133333-8.533333 46.933334 4.266666 8.533333 12.8 8.533333 29.866667-8.533334 42.666667z" fill="#ff7da7" p-id="114496"></path><path d="M512 0C230.4 0 0 230.4 0 512s230.4 512 512 512 512-230.4 512-512S793.6 0 512 0z m0 789.333333c-21.333333 0-42.666667 0-64-4.266666l-204.8 51.2v-166.4c-42.666667-51.2-64-110.933333-64-174.933334 0-162.133333 149.333333-298.666667 332.8-298.666666s332.8 132.266667 332.8 298.666666c0 162.133333-149.333333 294.4-332.8 294.4z" fill="#ff7da7" p-id="114497"></path></svg>

        </div>
        <div class="contact-text">
          <div class="contact-label">与你</div>
          <div class="contact-value" id="contactQQ"><?php echo htmlspecialchars($contact_infos['yuli'] ?? '无'); ?></div>
        </div>
         <?php if (!empty($contact_infos['yuli'])): ?>
                <button class="contact-copy-btn" onclick="copyText('<?php echo htmlspecialchars($contact_infos['yuli']); ?>')">复制</button>
                <?php else: ?>
                <button class="contact-copy-btn hidden">复制</button>
                <?php endif; ?>
      </div>
<?php endif ?>



    </div>

    <?php include_once 'comm/detail_collections.php'; ?>
  </div>


<script src="/js/clipboard.min.js"></script>
<script>
/*
        function showMessage(message) {
            const existingToast = document.querySelector('.toast-message');
            if (existingToast) existingToast.remove();

            const toast = document.createElement('div');
            toast.className = 'toast-message';
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => toast.remove(), 2000);
        }
        */

 
 /*
function copyText(textToCopy) {
    var success = false;

    // 优先使用现代 Clipboard API
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(textToCopy)
            .then(function() {
                alert('复制成功');
            })
            .catch(function() {
                // Clipboard API 失败，回退到传统方法
                fallbackCopy(textToCopy);
            });
        return;
    }
    */

</script>
