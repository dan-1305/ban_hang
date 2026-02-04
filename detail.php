<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết sản phẩm - Silent Architect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        .main-img { width: 100%; height: 400px; object-fit: contain; background: #fff; border: 1px solid #ddd; padding: 10px; border-radius: 8px; }
        .thumb-img { width: 80px; height: 80px; object-fit: cover; cursor: pointer; border: 2px solid #ddd; margin-right: 5px; transition: 0.2s; border-radius: 4px; }
        .thumb-img:hover, .thumb-img.active { border-color: #0d6efd; }
        
        /* CSS Đánh giá 5 sao chuẩn Bài 14 */
        .star-rating { color: #ccc; cursor: pointer; font-size: 1.5rem; display: inline-block; }
        .star-rating .fa-star.active { color: #f1c40f; transition: color 0.2s; }
        .star-rating .fa-star:hover { color: #f39c12; }
        
        .price-text { color: #e74c3c; font-size: 2rem; font-weight: 800; }
        .qty-input { max-width: 120px; }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row bg-white p-4 shadow-sm rounded">
        <div class="col-md-6">
            <img id="mainImage" src="uploads/loading.gif" class="main-img mb-3" alt="Ảnh sản phẩm">
            <div id="imageGallery" class="d-flex flex-wrap gap-2"></div>
        </div>

        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="product.html">Cửa hàng</a></li>
                    <li class="breadcrumb-item active" id="breadcrumbCat">Đang tải...</li>
                </ol>
            </nav>
            
            <h1 id="pName" class="fw-bold text-dark">...</h1>
            
            <div class="mb-3">
                <div class="star-rating" id="starRating">
                    <i class="fa-star fas" data-value="1"></i>
                    <i class="fa-star fas" data-value="2"></i>
                    <i class="fa-star fas" data-value="3"></i>
                    <i class="fa-star fas" data-value="4"></i>
                    <i class="fa-star fas" data-value="5"></i>
                </div>
                <span class="text-muted small ms-2" id="ratingStats">(Chưa có đánh giá)</span>
            </div>

            <h3 id="pPrice" class="price-text my-3"></h3>
            <p id="pDesc" class="text-secondary lead" style="min-height: 100px;"></p>
            <hr>
            
            <div class="d-flex align-items-center gap-3 mb-4">
                <span class="fw-bold">Số lượng:</span>
                <div class="input-group qty-input shadow-sm">
                    <button class="btn btn-outline-dark" onclick="changeQty(-1)">-</button>
                    <input type="number" id="quantity" class="form-control text-center fw-bold" value="1" min="1">
                    <button class="btn btn-outline-dark" onclick="changeQty(1)">+</button>
                </div>
            </div>

            <button class="btn btn-primary btn-lg w-100 py-3 fw-bold shadow" onclick="addToCart()">
                <i class="fas fa-cart-shopping me-2"></i>THÊM VÀO GIỎ HÀNG
            </button>
        </div>
    </div>

    <div class="mt-5 mb-5">
        <h4 class="fw-bold mb-4 text-uppercase border-start border-primary border-4 ps-3">Sản phẩm tương tự</h4>
        <div class="row g-4" id="similarContainer"></div>
    </div>
</div>

<script>
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('id');

    $(document).ready(function() {
        if (!productId) {
            alert("ID sản phẩm không tồn tại!");
            window.location.href = 'product.html';
            return;
        }

        // 1. Load dữ liệu chi tiết sản phẩm
        $.ajax({
            url: 'api/product_detail.php',
            type: 'GET',
            data: { id: productId },
            success: function(res) {
                let data = JSON.parse(res);
                if(data.error) { alert(data.error); return; }

                let p = data.product;
                $('#pName').text(p.product_name);
                $('#pPrice').text(Number(p.price).toLocaleString() + ' VNĐ');
                $('#pDesc').text(p.description || 'Sản phẩm chính hãng dành cho dân Tech.');
                $('#breadcrumbCat').text(p.category_name);

                // Load Gallery ảnh
                let galleryHtml = '';
                data.images.forEach((img, index) => {
                    if (index === 0) $('#mainImage').attr('src', img.image_url);
                    galleryHtml += `<img src="${img.image_url}" class="thumb-img ${index===0?'active':''}" onclick="updateMainImage(this, '${img.image_url}')">`;
                });
                $('#imageGallery').html(galleryHtml);

                // Load Sản phẩm tương tự
                let simHtml = '';
                data.similar.forEach(s => {
                    simHtml += `
                        <div class="col-6 col-md-3">
                            <div class="card h-100 border-0 shadow-sm overflow-hidden">
                                <a href="detail.php?id=${s.id}">
                                    <img src="${s.image_url || 'uploads/default.jpg'}" class="card-img-top p-3" style="height:150px; object-fit:contain">
                                </a>
                                <div class="card-body text-center">
                                    <h6 class="fw-bold text-truncate">${s.product_name}</h6>
                                    <p class="text-danger fw-bold">${Number(s.price).toLocaleString()}đ</p>
                                    <a href="detail.php?id=${s.id}" class="btn btn-sm btn-primary">Xem chi tiết</a>
                                </div>
                            </div>
                        </div>`;
                });
                $('#similarContainer').html(simHtml);
            }
        });

        // 2. Xử lý Đánh giá 5 sao bằng AJAX
        $('.star-rating .fa-star').on('click', function() {
            let ratingValue = $(this).data('value');
            
            // Cập nhật giao diện sao
            $('.star-rating .fa-star').removeClass('active');
            $('.star-rating .fa-star').each(function() {
                if ($(this).data('value') <= ratingValue) $(this).addClass('active');
            });

            // Gửi dữ liệu về Server để lưu vào CSDL
            $.ajax({
                url: 'api/save_rating.php',
                type: 'POST',
                data: { 
                    product_id: productId, 
                    rating: ratingValue 
                },
                success: function(res) {
                    try {
                        let result = JSON.parse(res);
                        if(result.status === 'success') {
                            alert("Cảm ơn ông Danh! Hệ thống đã ghi nhận " + ratingValue + " sao.");
                        } else {
                            alert("Lỗi lưu đánh giá: " + result.message);
                        }
                    } catch(e) { console.error("Lỗi parse JSON:", e); }
                }
            });
        });
    });

    // Cập nhật ảnh chính khi click ảnh thu nhỏ
    function updateMainImage(el, url) {
        $('#mainImage').attr('src', url);
        $('.thumb-img').removeClass('active');
        $(el).addClass('active');
    }

    // Thay đổi số lượng mua
    function changeQty(amt) {
        let qtyInput = $('#quantity');
        let newQty = parseInt(qtyInput.val()) + amt;
        if (newQty >= 1) qtyInput.val(newQty);
    }

    function addToCart() {
        let qty = $('#quantity').val();
        let name = $('#pName').text();
        alert(`Đã thêm ${qty} ${name} vào giỏ hàng của ông Danh thành công!`);
        // Bài 15 thầy Nhã sẽ dạy xử lý Session giỏ hàng
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>