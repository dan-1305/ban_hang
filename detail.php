<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết sản phẩm - Silent Architect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        .main-img { width: 100%; height: 400px; object-fit: contain; background: #fff; border: 1px solid #ddd; }
        .thumb-img { width: 80px; height: 80px; object-fit: cover; cursor: pointer; border: 1px solid #ddd; margin-right: 5px; }
        .thumb-img:hover { border-color: #0d6efd; }
        
        /* CSS Đánh giá 5 sao */
        .star-rating { color: #ddd; cursor: pointer; font-size: 1.2rem; }
        .star-rating .fa-star.active { color: #f1c40f; }
        .star-rating .fa-star:hover { color: #f39c12; }
        
        /* Tinh chỉnh ô số lượng */
        .qty-input { max-width: 100px; text-align: center; font-weight: bold; }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row bg-white p-4 shadow-sm rounded">
        <div class="col-md-6">
            <img id="mainImage" src="" class="main-img mb-3" alt="Ảnh sản phẩm">
            <div id="imageGallery" class="d-flex"></div>
        </div>

        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="product.html">Sản phẩm</a></li>
                    <li class="breadcrumb-item active" id="breadcrumbCat">...</li>
                </ol>
            </nav>
            
            <h1 id="pName" class="fw-bold">Đang tải...</h1>
            
            <div class="star-rating mb-2" id="starRating">
                <i class="fa-star fas active" data-value="1"></i>
                <i class="fa-star fas active" data-value="2"></i>
                <i class="fa-star fas active" data-value="3"></i>
                <i class="fa-star fas active" data-value="4"></i>
                <i class="fa-star fas" data-value="5"></i>
                <span class="text-muted small ms-2">(4.0/5 - 128 đánh giá)</span>
            </div>

            <h3 id="pPrice" class="text-danger fw-bold my-3"></h3>
            <p id="pDesc" class="text-muted"></p>
            <hr>
            
            <div class="d-flex align-items-center gap-3 mb-4">
                <span class="fw-bold">Số lượng:</span>
                <div class="input-group qty-input">
                    <button class="btn btn-outline-secondary btn-sm" onclick="changeQty(-1)">-</button>
                    <input type="number" id="quantity" class="form-control form-control-sm text-center border-secondary" value="1" min="1">
                    <button class="btn btn-outline-secondary btn-sm" onclick="changeQty(1)">+</button>
                </div>
            </div>

            <button class="btn btn-primary btn-lg w-100 shadow-sm" onclick="addToCart()">
                <i class="fas fa-cart-plus me-2"></i>Thêm vào giỏ hàng
            </button>
        </div>
    </div>

    <div class="mt-5 mb-5">
        <h4 class="fw-bold mb-4">SẢN PHẨM TƯƠNG TỰ</h4>
        <div class="row g-4" id="similarContainer"></div>
    </div>
</div>

<script>
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('id');

    $(document).ready(function() {
        if (!productId) { alert("Không thấy ID!"); return; }

        // Load dữ liệu sản phẩm
        $.ajax({
            url: 'api/product_detail.php',
            type: 'GET',
            data: { id: productId },
            success: function(res) {
                let data = JSON.parse(res);
                let p = data.product;

                $('#pName').text(p.product_name);
                $('#pPrice').text(Number(p.price).toLocaleString() + ' VNĐ');
                $('#pDesc').text(p.description || 'Siêu phẩm này chưa có mô tả chi tiết.');
                $('#breadcrumbCat').text(p.category_name);

                let galleryHtml = '';
                data.images.forEach((img, index) => {
                    if (index === 0) $('#mainImage').attr('src', img.image_url);
                    galleryHtml += `<img src="${img.image_url}" class="thumb-img" onclick="$('#mainImage').attr('src', '${img.image_url}')">`;
                });
                $('#imageGallery').html(galleryHtml);

                let simHtml = '';
                data.similar.forEach(s => {
                    simHtml += `
                        <div class="col-md-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <img src="${s.image_url || 'uploads/default.jpg'}" class="card-img-top p-3" style="height:150px; object-fit:contain">
                                <div class="card-body text-center">
                                    <h6 class="fw-bold">${s.product_name}</h6>
                                    <p class="text-danger small">${Number(s.price).toLocaleString()}đ</p>
                                    <a href="detail.php?id=${s.id}" class="btn btn-sm btn-outline-primary">Xem</a>
                                </div>
                            </div>
                        </div>`;
                });
                $('#similarContainer').html(simHtml);
            }
        });

        // Xử lý click chọn sao
        $('.star-rating .fa-star').on('click', function() {
            let val = $(this).data('value');
            $('.star-rating .fa-star').removeClass('active');
            $('.star-rating .fa-star').each(function() {
                if ($(this).data('value') <= val) $(this).addClass('active');
            });
            console.log("Ông Danh vừa đánh giá " + val + " sao!");
        });
    });

    // Hàm thay đổi số lượng
    function changeQty(amt) {
        let qty = parseInt($('#quantity').val()) + amt;
        if (qty < 1) qty = 1;
        $('#quantity').val(qty);
    }

    function addToCart() {
        alert("Đã thêm " + $('#quantity').val() + " " + $('#pName').text() + " vào giỏ hàng của ông Danh!");
    }
</script>
</body>
</html>