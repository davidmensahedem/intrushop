<?php
require_once '../util/main.php';      // update include path

require_once 'model/cart.php';
require_once 'model/category.php';
require_once 'model/category_db.php';
require_once 'model/product.php';
require_once 'model/orderItem.php';
require_once 'model/product_db.php';

require_once('model/fields.php');
require_once('model/validate.php');

$action = filter_input(INPUT_POST, 'action');
if ($action == NULL) {
    $action = filter_input(INPUT_GET, 'action');
    if ($action == NULL) {        
        $action = 'view';
    }
}

// Set up validation
$validate = new Validate();
$fields = $validate->getFields();
$fields->addField('quantity');

// get Cart object
$cart = new Cart();

switch ($action) {
    case 'view':
        $cart_items = $cart->getItems();
        
        include ('cart_view.php');
        break;
    case 'add':
        $product_id = filter_input(INPUT_GET, 'product_id', FILTER_VALIDATE_INT);
        $quantity = filter_input(INPUT_GET, 'quantity', FILTER_VALIDATE_INT);
        $is_admin = filter_input(INPUT_GET, 'admin', FILTER_VALIDATE_BOOLEAN);
        $product = ProductDB::getProduct($product_id);
        
        // Validate user data
        $validate->number('quantity', $quantity, min:1);
        
        // If validation errors, redisplay product page and exit controller
        if ($fields->hasErrors()) { 
            $categories = CategoryDB::getCategories(); // for the view product sidebar links
            if($is_admin) {
                include 'admin/product/product_view.php';
            } else {
                include 'catalog/product_view.php';
            }
            break;
        }

        $cart->addItem($product_id, $quantity);
        
        redirect('.');
        break;
    case 'update':
        $items = filter_input(INPUT_POST, 'items', FILTER_DEFAULT, 
                FILTER_REQUIRE_ARRAY);
        foreach ( $items as $product_id => $quantity ) {
            if ($quantity == 0) {
                $cart->removeItem($product_id);
            } else {
                $cart->updateItem($product_id, $quantity);
            }
        }
        
        redirect('.');
        break;
    default:
        display_error("Unknown cart action: " . $action);
        break;
}

?>