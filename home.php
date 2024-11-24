<?php 
include 'admin/db_connect.php';

// Default limit and pagination
$limit = 10;
$page = (isset($_GET['_page']) && $_GET['_page'] > 0) ? $_GET['_page'] - 1 : 0;
$offset = $page > 0 ? $page * $limit : 0;

// Get search parameter
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Update status to 'unavailable' for products with 0 stock
$conn->query("UPDATE product_list SET status = 'unavailable' WHERE stock = 0");

// Modify query based on search parameter
$search_query = $search ? "WHERE name LIKE '%$search%' OR description LIKE '%$search%'" : '';
$qry = $conn->query("
    SELECT 
        id, 
        name, 
        description, 
        img_path, 
        size, 
        size_unit, 
        price, 
        stock, 
        CASE 
            WHEN stock = 0 THEN 'unavailable'
            ELSE status
        END AS status 
    FROM product_list 
    $search_query 
    ORDER BY name ASC 
    LIMIT $limit OFFSET $offset
");

// Get total count of items based on search
$total_count_query = $conn->query("SELECT id FROM product_list $search_query");
$all_menu = $total_count_query->num_rows;
$page_btn_count = ceil($all_menu / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Menu Page</title>
    <link rel="stylesheet" href="path/to/bootstrap.min.css">
    <link rel="stylesheet" href="path/to/font-awesome.min.css">
    <link rel="stylesheet" href="path/to/custom-styles.css">
    <script src="path/to/jquery.min.js"></script>
    <script src="path/to/bootstrap.min.js"></script>
    <script src="path/to/sweetalert2.all.min.js"></script>
    <style>
        .steps {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .step-item {
            text-align: center;
            color: white;
            opacity: 0;
            transform: translateY(20px);
            animation: slideIn 0.5s forwards;
            margin: 0 15px;
        }
        .step-item h4 {
            font-size: 2rem;
            margin-top: 10px;
        }
        .step-item i {
            font-size: 3rem;
            color: white;
            margin-bottom: 10px;
            transition: transform 0.3s;
        }
        .step-item i:hover {
            transform: scale(1.1);
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fa-bounce { animation: bounce 2s infinite; }
        .fa-beat { animation: beat 2s infinite; }
        .fa-spin { animation: spin 2s infinite; }
        body, html { overflow-x: hidden; }
        .container, .steps, .input-group, .card-deck { width: 100%; margin: 0 auto; padding: 0 10px; }
        @media (max-width: 768px) {
            .masthead h1 { font-size: 2rem; }
            .step-item h4 { font-size: 1.5rem; }
            .step-item i { font-size: 2.5rem; }
            .btn-group .btn { font-size: 0.9rem; padding: 6px; }
            .card-deck .col-lg-3 { width: 100%; margin-bottom: 1rem; }
        }
    </style>
</head>
<body>
    <header class="masthead">
        <div class="container h-100">
            <div class="row h-100 align-items-center justify-content-center text-center">
                <div class="col-lg-10 align-self-center mb-4 page-title">
                    <h1 class="text-white">Welcome to <?php echo htmlspecialchars(isset($_SESSION['setting_name']) ? $_SESSION['setting_name'] : 'M&M Cake Ordering System'); ?></h1>
                    <div class="steps d-flex justify-content-around mt-5">
                        <div class="step-item"><i class="fas fa-search"></i><h4>Browse</h4></div>
                        <div class="step-item"><i class="fas fa-shopping-cart"></i><h4>Order</h4></div>
                        <div class="step-item"><i class="fas fa-truck"></i><h4>Deliver</h4></div>
                    </div>
                    <hr class="divider my-4 bg-dark" />
                    <a class="btn btn-dark bg-black btn-xl js-scroll-trigger" href="#menu">Order Now</a>
                </div>
            </div>
        </div>
    </header>

    <section class="page-section" id="menu">
        <h1 class="text-center text-cursive" style="font-size:3em"><b>Menu</b></h1>
        <div class="d-flex justify-content-center">
            <hr class="border-dark" width="5%">
        </div>

        <div class="container">
            <form method="GET" action="">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Search for cakes..." name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <div class="input-group-append">
                        <button class="btn btn-dark" type="submit">Search for Cakes</button>
                    </div>
                </div>
            </form>
        </div>

        <div id="menu-field" class="card-deck mt-2">
            <?php while ($row = $qry->fetch_assoc()): ?>
            <div class="col-lg-3 mb-3">
                <div class="card menu-item rounded-0">
                    <div class="position-relative overflow-hidden" id="item-img-holder">
                        <img src="assets/img/<?php echo htmlspecialchars($row['img_path']); ?>" class="card-img-top" alt="Cake Image">
                    </div>
                    <div class="card-body rounded-0">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                        <p class="card-text truncate"><?php echo htmlspecialchars($row['description']); ?></p>
                        <p class="card-text">Size: <?php echo htmlspecialchars($row['size']) . ' ' . htmlspecialchars($row['size_unit']); ?></p>
                        <p class="card-text">Price: <?php echo htmlspecialchars($row['price']); ?></p>
                        <p class="card-text">Availability: <?php echo htmlspecialchars($row['status']); ?><br>Stock: <?php echo htmlspecialchars($row['stock']); ?></p>
                        <div class="text-center">
                            <button class="btn btn-sm btn-outline-dark view_prod btn-block" data-id="<?php echo htmlspecialchars($row['id']); ?>"><i class="fa fa-eye"></i> View</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <div class="w-100 mx-4 d-flex justify-content-center">
            <div class="btn-group paginate-btns">
                <a class="btn btn-default border border-dark" <?php echo ($page == 0) ? 'disabled' : ''; ?> href="./?_page=<?php echo ($page); ?>&search=<?php echo urlencode($search); ?>">Prev.</a>
                <?php for ($i = 1; $i <= $page_btn_count; $i++): ?>
                    <a class="btn btn-default border border-dark <?php echo ($i == ($page + 1)) ? 'active' : ''; ?>" href="./?_page=<?php echo $i ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
                <a class="btn btn-default border border-dark" <?php echo (($page + 1) == $page_btn_count) ? 'disabled' : ''; ?> href="./?_page=<?php echo ($page + 2); ?>&search=<?php echo urlencode($search); ?>">Next</a>
            </div>
        </div>
    </section>

    <script>
        $(document).ready(function() {
            $('.step-item').each(function(index) {
                $(this).css('animation-delay', (index * 0.1) + 's');
            });

            $('.view_prod').click(function() {
                uni_modal_right('Product Details', 'view_prod.php?id=' + $(this).attr('data-id'));
            });

            <?php if (isset($_GET['_page'])): ?>
                $(function() {
                    document.querySelector('html').scrollTop = $('#menu').offset().top - 100;
                });
            <?php endif; ?>
        });
    </script>
</body>
</html>
