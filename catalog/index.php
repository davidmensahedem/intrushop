<?php
require_once('../util/main.php');      // update include path

require_once('model/category.php');
require_once('model/category_db.php');
require_once('model/product.php');
require_once('model/product_db.php');

require_once('model/fields.php');
require_once('model/validate.php');

$category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
$product_id = filter_input(INPUT_GET, 'product_id', FILTER_VALIDATE_INT);
if ($category_id !== NULL) {
    $action = 'category';
} elseif ($product_id !== NULL) {
    $action = 'product';
} else {
    $action = 'category';
    $category_id = 1;
}

switch ($action) {
    // Display the specified category
    case 'category':
        // Get category data
        $categories = CategoryDB::getCategories();
        $category = CategoryDB::getCategory($category_id);
        $category_name = $category->getName();
        
        // Get product data
        $products = ProductDB::getProductsByCategory($category_id);

        // Display category
        include('category_view.php');
        break;
    
    // Display the specified product
    case 'product':
        // Get category data
        $categories = CategoryDB::getCategories();
        
        // Get product data
        $product = ProductDB::getProduct($product_id);
        
        // Set up validation
        $validate = new Validate();
        $fields = $validate->getFields();
        $fields->addField('quantity');

        // Display product
        include('product_view.php');
        break;
    
    default:
        display_error("Unknown catalog action: $action");
        break;
}
?>