<?php
session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../src/Database/Database.php';
require_once __DIR__ . '/../src/Models/Release.php';
require_once __DIR__ . '/../src/Models/Order.php';
require_once __DIR__ . '/../src/Models/User.php';
require_once __DIR__ . '/../src/Models/Wishlist.php';
require_once __DIR__ . '/../src/Models/Subscription.php';
require_once __DIR__ . '/../src/Models/Blog.php';
require_once __DIR__ . '/../src/Models/Rating.php';
require_once __DIR__ . '/../src/Helpers/functions.php';

use VinylHive\Database\Database;
use VinylHive\Models\Release;
use VinylHive\Models\Order;
use VinylHive\Models\User;
use VinylHive\Models\Wishlist;
use VinylHive\Models\Subscription;
use VinylHive\Models\Blog;
use VinylHive\Models\Rating;

$route = $_GET['route'] ?? 'home';
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER['SCRIPT_NAME'];
$path = str_replace(dirname($scriptName), '', $requestUri);
$path = parse_url($path, PHP_URL_PATH);
$path = trim($path, '/');

// Маршрутизация
if ($path === '' || $path === 'index.php') {
    // Главная
    $releaseModel = new Release();
    $newReleases = $releaseModel->getLatest(8);
    $preorders = $releaseModel->getPreorders(6);
    $hits = $releaseModel->getPopular(5);
    $genres = ['Rock', 'Jazz', 'Electronic', 'Classical'];
    require_once __DIR__ . '/../src/Views/partials/header.php';
    require_once __DIR__ . '/index_content.php';
    require_once __DIR__ . '/../src/Views/partials/footer.php';
    
} elseif ($path === 'catalog') {
    // Каталог
    $releaseModel = new Release();
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $perPage = 24;
    $offset = ($page - 1) * $perPage;
    
    $filters = [
        'genres' => isset($_GET['genres']) ? explode(',', $_GET['genres']) : [],
        'year_min' => $_GET['year_min'] ?? null,
        'year_max' => $_GET['year_max'] ?? null,
        'countries' => isset($_GET['countries']) ? explode(',', $_GET['countries']) : [],
        'vinyl_types' => isset($_GET['vinyl_types']) ? explode(',', $_GET['vinyl_types']) : [],
        'price_min' => $_GET['price_min'] ?? null,
        'price_max' => $_GET['price_max'] ?? null,
        'search' => $_GET['search'] ?? null
    ];
    
    $releases = $releaseModel->filter($filters, $perPage, $offset);
    $totalCount = $releaseModel->getFilterCount($filters);
    $totalPages = ceil($totalCount / $perPage);
    $countries = $releaseModel->getUniqueCountries();
    $priceRange = $releaseModel->getMinMaxPrice();
    $yearRange = $releaseModel->getMinMaxYear();
    
    require_once __DIR__ . '/../src/Views/partials/header.php';
    require_once __DIR__ . '/../src/Views/catalog.php';
    require_once __DIR__ . '/../src/Views/partials/footer.php';
    
} elseif (preg_match('/^product\/(\d+)$/', $path, $matches)) {
    // Карточка товара по ID
    $releaseModel = new Release();
    $release = $releaseModel->getById((int)$matches[1]);
    
    if (!$release) {
        http_response_code(404);
        require_once __DIR__ . '/../src/Views/404.php';
        exit;
    }
    
    $alsoBought = $releaseModel->getAlsoBought($release['id']);
    $ratingModel = new Rating();
    $avgRating = null;
    $userRating = null;
    
    foreach ($release['pressings'] as $pressing) {
        $avg = $ratingModel->getAverageForPressing($pressing['id']);
        if ($avg && $avg['avg']) {
            $avgRating = $avg;
        }
        if (isset($_SESSION['user_id'])) {
            $userRating = $ratingModel->getUserRating($pressing['id'], $_SESSION['user_id']);
        }
    }
    
    require_once __DIR__ . '/../src/Views/partials/header.php';
    require_once __DIR__ . '/../src/Views/product.php';
    require_once __DIR__ . '/../src/Views/partials/footer.php';
    
} elseif (preg_match('/^artist\/([^\/]+)\/album\/([^\/]+)$/', $path, $matches)) {
    // ЧПУ /artist/beatles/album/abbey-road
    $releaseModel = new Release();
    $release = $releaseModel->getBySlug($matches[1], $matches[2]);
    
    if (!$release) {
        http_response_code(404);
        require_once __DIR__ . '/../src/Views/404.php';
        exit;
    }
    
    $alsoBought = $releaseModel->getAlsoBought($release['id']);
    
    require_once __DIR__ . '/../src/Views/partials/header.php';
    require_once __DIR__ . '/../src/Views/product.php';
    require_once __DIR__ . '/../src/Views/partials/footer.php';
    
} elseif ($path === 'cart') {
    require_once __DIR__ . '/../src/Views/partials/header.php';
    require_once __DIR__ . '/../src/Views/cart.php';
    require_once __DIR__ . '/../src/Views/partials/footer.php';
    
} elseif ($path === 'blog') {
    $blogModel = new Blog();
    $category = $_GET['category'] ?? null;
    
    if ($category && in_array($category, ['cleaning', 'symbols', 'reviews', 'guides'])) {
        $posts = $blogModel->getByCategory($category);
    } else {
        $posts = $blogModel->getAllPublished();
    }
    
    require_once __DIR__ . '/../src/Views/partials/header.php';
    require_once __DIR__ . '/../src/Views/blog.php';
    require_once __DIR__ . '/../src/Views/partials/footer.php';
    
} elseif (preg_match('/^blog\/([a-z0-9-]+)$/', $path, $matches)) {
    $blogModel = new Blog();
    $post = $blogModel->getBySlug($matches[1]);
    
    if (!$post) {
        http_response_code(404);
        require_once __DIR__ . '/../src/Views/404.php';
        exit;
    }
    
    require_once __DIR__ . '/../src/Views/partials/header.php';
    require_once __DIR__ . '/../src/Views/article.php';
    require_once __DIR__ . '/../src/Views/partials/footer.php';
    
} elseif ($path === 'login') {
    require_once __DIR__ . '/../src/Views/partials/header.php';
    require_once __DIR__ . '/../src/Views/login.php';
    require_once __DIR__ . '/../src/Views/partials/footer.php';
    
} elseif ($path === 'register') {
    require_once __DIR__ . '/../src/Views/partials/header.php';
    require_once __DIR__ . '/../src/Views/register.php';
    require_once __DIR__ . '/../src/Views/partials/footer.php';
    
} elseif ($path === 'logout') {
    $_SESSION = [];
    session_destroy();
    redirect('/');
    
} elseif ($path === 'account') {
    if (!isset($_SESSION['user_id'])) {
        redirect('/login');
    }
    
    $userModel = new User();
    $user = $userModel->findById($_SESSION['user_id']);
    $orderModel = new Order();
    $orders = $orderModel->getUserOrders($_SESSION['user_id']);
    $wishlistModel = new Wishlist();
    $wishlist = $wishlistModel->getUserWishlist($_SESSION['user_id']);
    $subscriptionModel = new Subscription();
    $subscriptions = $subscriptionModel->getUserSubscriptions($_SESSION['user_id']);
    
    require_once __DIR__ . '/../src/Views/partials/header.php';
    require_once __DIR__ . '/../src/Views/account.php';
    require_once __DIR__ . '/../src/Views/partials/footer.php';
    
} elseif ($path === 'wishlist') {
    require_once __DIR__ . '/../src/Views/partials/header.php';
    require_once __DIR__ . '/../src/Views/wishlist.php';
    require_once __DIR__ . '/../src/Views/partials/footer.php';
    
} elseif (strpos($path, 'api/') === 0) {
    if ($path === 'api/filter') {
        require_once __DIR__ . '/../src/Controllers/FilterController.php';
    } elseif ($path === 'api/cart/add') {
        require_once __DIR__ . '/../src/Controllers/CartController.php';
        $cart = new CartController();
        $cart->add();
    } elseif ($path === 'api/cart/get') {
        require_once __DIR__ . '/../src/Controllers/CartController.php';
        $cart = new CartController();
        $cart->get();
    } elseif ($path === 'api/rating') {
        require_once __DIR__ . '/../src/Controllers/RatingController.php';
    } elseif ($path === 'api/wishlist') {
        require_once __DIR__ . '/../src/Controllers/WishlistController.php';
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'API not found']);
    }
    
} else {
    http_response_code(404);
    require_once __DIR__ . '/../src/Views/partials/header.php';
    require_once __DIR__ . '/../src/Views/404.php';
    require_once __DIR__ . '/../src/Views/partials/footer.php';
}
