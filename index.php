<?php
require_once('util/main.php');         // update include path

require_once('model/category.php');
require_once('model/category_db.php');
require_once('model/product.php');
require_once('model/product_db.php');

// Set the featured product IDs in an array
$product_ids = [1, 7, 9];
// Note: You could also store a list of featured products in the database

// Get the products from the database
$products = [];
foreach ($product_ids as $product_id) {
    $products[] = ProductDB::getProduct($product_id);  // add product to array
}

// Get the categories from the database
$categories = CategoryDB::getCategories();

// Display the home page
include('home_view.php');
?>