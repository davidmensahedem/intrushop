<?php
require_once('../../util/main.php');  // update include path
require_once('util/valid_admin.php'); // require admin user

require_once('model/admin_db.php');
require_once('model/category.php');
require_once('model/category_db.php');
require_once('model/product_db.php');
require_once('model/cart.php');

require_once('model/fields.php');
require_once('model/validate.php');

$action = strtolower(filter_input(INPUT_POST, 'action'));
if ($action == NULL) {
    $action = strtolower(filter_input(INPUT_GET, 'action'));
    if ($action == NULL) {        
        $action = 'list_categories';
    }
}

// set up validation for static input to add a category
// and dynamically generated inputs to update a category 
$validate = new Validate();
$fields = $validate->getFields();
$fields->addField('name');                 // static add input

$categories = CategoryDB::getCategories();
foreach($categories as $category) {        // dynamic update inputs
    $fields->addField('name' . $category->getID());
}

switch ($action) {
    case 'list_categories':
        include('category_list.php');
        break;
    case 'delete_category':
        $category_id = filter_input(INPUT_POST, 'category_id', 
                FILTER_VALIDATE_INT);
        
        // remove category from cart and from database
        Cart::clearLastCategory($category_id);
        CategoryDB::deleteCategory($category_id);

        redirect('.');
        break;
    case 'add_category':
        $name = filter_input(INPUT_POST, 'name');
        
        $validate->text('name', $name);
        
        // If validation errors, redisplay category page and exit controller
        if ($fields->hasErrors()) {
            include('category_list.php');
            break;
        }

        CategoryDB::addCategory($name);
        
        redirect('.');
        break;
    case 'update_category':
        $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
        $field_name = 'name' . $category_id;
        $name = filter_input(INPUT_POST, $field_name);

        $validate->text($field_name, $name);
        
        // If validation errors, redisplay category page and exit controller
        if ($fields->hasErrors()) {
            include('category_list.php');
            break;
        }
            
        $category = new Category($category_id, $name);
        CategoryDB::updateCategory($category);
        
        redirect('.');
        break;
    default:
        display_error("Unknown category action: " . $action);
        break;
}

?>