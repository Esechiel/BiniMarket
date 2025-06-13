<?php
require_once 'php/db.php';

header('Content-Type: application/json');

$per_page = 6;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$start = ($page - 1) * $per_page;

$filters = [
    'category' => $_GET['category'] ?? [],
    'location' => $_GET['location'] ?? '',
    'condition' => $_GET['condition'] ?? [],
    'type' => $_GET['type'] ?? [],
    'minPrice' => isset($_GET['minPrice']) ? intval($_GET['minPrice']) : 0,
    'maxPrice' => isset($_GET['maxPrice']) ? intval($_GET['maxPrice']) : 100000,
    'sort' => $_GET['sort'] ?? 'recent'
];

$where = [];

if (!empty($filters['category'])) {
    $cat_list = implode("','", array_map('mysqli_real_escape_string', array_map(fn($c) => $conn, $filters['category'])));
    $where[] = "c.name IN ('$cat_list')";
}

if (!empty($filters['location'])) {
    $location = mysqli_real_escape_string($conn, $filters['location']);
    $where[] = "l.location_id = '$location'";
}

if (!empty($filters['condition'])) {
    $cond_list = implode("','", array_map('mysqli_real_escape_string', array_map(fn($c) => $conn, $filters['condition'])));
    $where[] = "l.condition IN ('$cond_list')";
}

if (!empty($filters['type'])) {
    $type_list = implode("','", array_map('mysqli_real_escape_string', array_map(fn($c) => $conn, $filters['type'])));
    $where[] = "l.type IN ('$type_list')";
}

$where[] = "l.price BETWEEN {$filters['minPrice']} AND {$filters['maxPrice']}";

$where_sql = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

switch ($filters['sort']) {
    case 'price-asc':
        $order_by = "ORDER BY l.price ASC";
        break;
    case 'price-desc':
        $order_by = "ORDER BY l.price DESC";
        break;
    case 'popularity':
        $order_by = "ORDER BY l.views_count DESC";
        break;
    default:
        $order_by = "ORDER BY l.creation_date DESC";
        break;
}

$sql = "SELECT l.* FROM listings l JOIN categories c ON l.category_id = c.category_id $where_sql $order_by LIMIT $start, $per_page";
$count_sql = "SELECT COUNT(*) FROM listings l JOIN categories c ON l.category_id = c.category_id $where_sql";

$res = mysqli_query($conn, $sql);
$count_res = mysqli_query($conn, $count_sql);

$total = mysqli_fetch_row($count_res)[0];
$total_pages = ceil($total / $per_page);

$listings = [];
if ($res && mysqli_num_rows($res) > 0) {
    $listings = mysqli_fetch_all($res, MYSQLI_ASSOC);
    $ids = array_column($listings, 'listing_id');
    $id_list = implode(',', $ids);
    $img_sql = "SELECT listing_id, image_path FROM listing_images WHERE is_primary = 1 AND listing_id IN ($id_list)";
    $img_res = mysqli_query($conn, $img_sql);
    $img_map = [];
    while ($row = mysqli_fetch_assoc($img_res)) {
        $img_map[$row['listing_id']] = $row['image_path'];
    }

    foreach ($listings as &$listing) {
        $listing['image'] = $img_map[$listing['listing_id']] ?? null;
    }
}

echo json_encode([
    'total_results' => $total,
    'total_pages' => $total_pages,
    'page' => $page,
    'listings' => $listings
]);
