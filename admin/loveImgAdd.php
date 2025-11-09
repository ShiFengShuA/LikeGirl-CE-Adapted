<?php
session_start();
include_once 'Nav.php';
$inv_date = date("Y-m-d");
?>

<link rel="stylesheet" href="/admin/assets/css/photo.select.css">
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3 size_18">新增图片</h4>
                <form class="needs-validation" action="loveImgAddPost.php" method="post" onsubmit="return check()"
                      novalidate>
                    <div class="form-group mb-3">
                        <label for="validationCustom01">日期</label>
                        <input class="form-control col-sm-4" id="example-date" type="date" name="imgDatd" class="form-control" placeholder="日期" value="<?php echo $inv_date ?>" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="validationCustom01">图片描述<span class="margin_left badge badge-success-lighten">尽量控制在25个字符以内 </span></label>
                        <input name="imgText" type="text" class="form-control" placeholder="请输入图片描述" value="" required>
                    </div>
                    <div class="form-group mb-3" id="img_url">
                        <label for="validationCustom01">图片URL (支持相对与绝对路径 多个URL用分号;分隔)</label>
                        <div class="d-flex align-items-center">
                            <input class="form-control flex-fill mr-2" type="text" name="imgUrl" placeholder="请输入图片URL地址" value="" required>
                            <div class="btn-group" style="width: 220px; display: flex; gap: 10px;">
                                <button class="btn btn-outline-success rounded-8" type="button" data-toggle="modal" data-target="#uploadModal">
                                    <i class="fa fa-upload"></i> 上传
                                </button>
                                <button class="btn btn-outline-danger rounded-8" type="button" data-toggle="modal" data-target="#galleryModal">
                                    <i class="fa fa-images"></i> 图库
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3 text_right">
                        <button class="btn btn-primary"  type="button" id="ImgAddPost">新增相册</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--上传图片-->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="uploadModalLabel">图片上传</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body d-flex flex-column align-items-center">
        <div class="upload-area d-flex flex-column align-items-center mb-3" id="uploadArea">
          <div id="thumbnailContainer" class="relative">
            <img src="" alt="图片缩略图" id="thumbnailImage" class="thumbnail-image" style="display: none; width: 150px; height: 150px; object-fit: cover; border-radius: 10px; position: relative; z-index: 1;">
            <div class="overlay d-flex flex-column align-items-center justify-content-center" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); border-radius: 10px; opacity: 0; transition: opacity 0.3s ease;">
            </div>
          </div>
          <input id="fileName" type="text" class="text-center mt-2 no-border" placeholder="拖放文件或点击选择文件" value="" required>
          <span class="text-center mt-2">选择后建议重命名(保留后缀如.png/.jpg)，便于图库查找</span>
        </div>
        <button class="btn btn-primary" type="submit" id="uploadImg">上传图片</button>
      </div>
    </div>
  </div>
</div>
<input type="file" name="imageFile" id="imageFile" accept="image/*" required class="d-none">

<!--图库选择-->
<div class="modal fade" id="galleryModal" tabindex="-1" role="dialog" aria-labelledby="galleryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="galleryModalLabel">我的图库</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <nav class="navbar navbar-expand-lg navbar-custom-i">
            <div class="container-fluid">
                <div class="navbar-search d-flex align-items-center">
                    <input type="text" class="form-control" placeholder="输入关键字..." id="keywords">
                    <button class="btn btn-outline-success rounded-8" style="width: 50px" id="btn-search">
                        <i class="fas fa-search"></i> 
                    </button>
                </div>
    
                <div class="navbar-function ms-auto d-flex align-items-center">
                    <button class="btn btn-outline-success rounded-8" id="prevPage">
                        <i class="fas fa-chevron-left"></i> 
                    </button>
                    <span class="mx-2"><span id="currentPage">0</span> / <span id="totalPages">0</span>, 共 <span id="totalCount">0</span></span>
                    <button class="btn btn-outline-success rounded-8" id="nextPage">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </nav>
        <div class="row" id="imageContainer"></div>
      </div>
    </div>
  </div>
</div>

<script>
    function check() {
        let title = document.getElementsByName('imgText')[0].value.trim();
        if (title.length == 0) {
            alert("描述不能为空");
            return false;
        }
        
        if (title.length > 200) {
            alert("描述长度不能超过200个字符");
            return false;
        }
        
        let imgUrl = document.getElementsByName('imgUrl')[0].value.trim();
        if (imgUrl.length == 0) {
            alert("图片URL不能为空");
            return false;
        }
        
        // 验证URL格式（基本验证）
        let urls = imgUrl.split(';').map(url => url.trim()).filter(url => url !== '');
        if (urls.length === 0) {
            alert("请输入有效的图片URL");
            return false;
        }
        
        for (let i = 0; i < urls.length; i++) {
            if (!isValidUrl(urls[i])) {
                alert("第" + (i + 1) + "个URL格式不正确: " + urls[i]);
                return false;
            }
        }
        
        return true;
    }
    
    function isValidUrl(string) {
        try {
            // 允许相对路径和绝对路径
            if (string.startsWith('/') || string.startsWith('./') || string.startsWith('../') || string.startsWith('http://') || string.startsWith('https://')) {
                return true;
            }
            
            // 检查是否是有效的URL
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }

    // AJAX提交表单
    document.addEventListener('DOMContentLoaded', function() {
        const addButton = document.getElementById('ImgAddPost');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const messageAlert = document.getElementById('messageAlert');
        const form = document.getElementById('addForm');
        
        if (addButton && form) {
            // 阻止表单的默认提交行为
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (check()) {
                    // 显示加载中
                    addButton.disabled = true;
                    loadingSpinner.style.display = 'inline-block';
                    messageAlert.style.display = 'none';
                    
                    const formData = new FormData(form);
                    
                    fetch('ImgAddPost.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        // 首先检查响应状态
                        if (!response.ok) {
                            throw new Error('网络响应不正常: ' + response.status);
                        }
                        return response.text();
                    })
                    .then(data => {
                        console.log('服务器返回:', data); // 调试信息
                        
                        // 去除可能的空白字符
                        data = data.trim();
                        
                        if (data === '1') {
                            showAlert('添加成功！', 'success');
                            
                        } else {
                            showAlert('添加失败，请重试！错误: ' + data, 'danger');
                            addButton.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('网络错误，请重试！', 'danger');
                        addButton.disabled = false;
                    })
                    .finally(() => {
                        loadingSpinner.style.display = 'none';
                    });
                }
            });
        }
    });
    
    function showAlert(message, type) {
        const messageAlert = document.getElementById('messageAlert');
        messageAlert.textContent = message;
        messageAlert.className = `alert alert-${type}`;
        messageAlert.style.display = 'block';
        
        // 5秒后自动隐藏提示（错误信息）
        if (type === 'danger') {
            setTimeout(() => {
                messageAlert.style.display = 'none';
            }, 5000);
        }
    }
</script>

<style>
    .spinner-border {
        width: 1.5rem;
        height: 1.5rem;
    }
    
    .ml-2 {
        margin-left: 0.5rem;
    }
</style>

<?php
include_once 'Footer.php';
?>