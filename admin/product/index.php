<?php
require_once('../../util/main.php');  // update include path
require_once('util/valid_admin.php'); // require admin user

require_once('util/images.php');

require_once('model/category.php');
require_once('model/category_db.php');
require_once('model/product.php');
require_once('model/product_db.php');

require_once('model/fields.php');
require_once('model/validate.php');

$action = strtolower(filter_input(INPUT_POST, 'action'));
if ($action == NULL) {
    $action = strtolower(filter_input(INPUT_GET, 'action'));
    if ($action == NULL) {        
        $action = 'list_products';
    }
}

// Set up all possible fields to validate
$validate = new Validate();
$fields = $validate->getFields();

// for the view product page
$fields->addField('quantity');

// for the add/edit product page
$fields->addField('category_id');
$fields->addField('code');
$fields->addField('name');
$fields->addField('price');
$fields->addField('discount_percent', required:FALSE);
$fields->addField('description');

switch ($action) {
    case 'list_products':
        // get categories and products
        $category_id = filter_input(INPUT_GET, 'category_id', 
                FILTER_VALIDATE_INT);
        if (empty($category_id)) {
            $category_id = 1;
        }
        $current_category = CategoryDB::getCategory($category_id);
        $categories = CategoryDB::getCategories();
        $products = ProductDB::getProductsByCategory($category_id);

        // display product list
        include('product_list.php');
        break;
    case 'view_product':
        $categories = CategoryDB::getCategories();
        $product_id = filter_input(INPUT_GET, 'product_id', 
                FILTER_VALIDATE_INT);
        $product = ProductDB::getProduct($product_id);
        $product_order_count = ProductDB::getProductOrderCount($product_id);
        $is_admin = TRUE;  
        include('product_view.php');
        break;
    case 'delete_product':
        $category_id = filter_input(INPUT_POST, 'category_id', 
                FILTER_VALIDATE_INT);
        $product_id = filter_input(INPUT_POST, 'product_id', 
                FILTER_VALIDATE_INT);
        ProductDB::deleteProduct($product_id);
        
        // Display the product list for the current category
        redirect(".?category_id=$category_id");
        break;
    case 'show_add_edit_form':
        $product_id = filter_input(INPUT_GET, 'product_id', 
                FILTER_VALIDATE_INT);
        if ($product_id === null) {
            $product_id = filter_input(INPUT_POST, 'product_id', 
                    FILTER_VALIDATE_INT);
        }
        
        // get selected product, or an empty Product object if no product selected
        $product = ProductDB::getProduct($product_id);
        if ($product === NULL) {
            $product = new Product(new Category());
        } 
        
        $categories = CategoryDB::getCategories();
        include('product_add_edit.php');
        break;
    case 'add_product':
        $category_id = filter_input(INPUT_POST, 'category_id', 
                FILTER_VALIDATE_INT);
        $category = new Category($category_id, '');
        
        // Get user input
        $product = new Product($category);
        $product->setCode(filter_input(INPUT_POST, 'code'));
        $product->setName(filter_input(INPUT_POST, 'name'));
        $product->setDescription(filter_input(INPUT_POST, 'description'));
        $product->setPrice(filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT));
        $product->setDiscountPercent(filter_input(INPUT_POST, 'discount_percent', FILTER_VALIDATE_FLOAT));

        // Validate user input
        $validate->text('code', $product->getCode());
        $validate->text('name', $product->getName());
        $validate->text('description', $product->getDescription(), max:1000);        
        $validate->number('price', $product->getPrice(), min:1);        
        $validate->number('discount_percent', $product->getDiscountPercent(), min:0);
        
        // If validation errors, redisplay add/edit page and exit controller
        if ($fields->hasErrors()) {
            $categories = CategoryDB::getCategories(); // for category drop-down
            include 'product_add_edit.php';
            break;
        }
        
        $product_id = ProductDB::addProduct($product);
        redirect(".?action=view_product&product_id=$product_id");
        break;
    case 'update_product':
        $product_id = filter_input(INPUT_POST, 'product_id', 
                FILTER_VALIDATE_INT);
        $category_id = filter_input(INPUT_POST, 'category_id', 
                FILTER_VALIDATE_INT);
        $category = new Category($category_id, '');
        
        // Get user input
        $product = new Product($category);
        $product->setID($product_id);
        $product->setCode(filter_input(INPUT_POST, 'code'));
        $product->setName(filter_input(INPUT_POST, 'name'));
        $product->setDescription(filter_input(INPUT_POST, 'description'));
        $product->setPrice(filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT));
        $product->setDiscountPercent(filter_input(INPUT_POST, 'discount_percent', FILTER_VALIDATE_FLOAT));

        // Validate user input
        $validate->text('code', $product->getCode());
        $validate->text('name', $product->getName());
        $validate->text('description', $product->getDescription(), max:1000);        
        $validate->number('price', $product->getPrice(), min:1);        
        $validate->number('discount_percent', $product->getDiscountPercent(), min:0);
        
        // If validation errors, redisplay add/edit page and exit controller
        if ($fields->hasErrors()) {
            $categories = CategoryDB::getCategories(); // for category drop-down
            include 'product_add_edit.php';
            break;
        }
        
        ProductDB::updateProduct($product);
        redirect(".?action=view_product&product_id=$product_id");
        break;
    case 'upload_image':
        $product_id = filter_input(INPUT_POST, 'product_id', 
                FILTER_VALIDATE_INT);
        $product = ProductDB::getProduct($product_id);
        $product_code = $product->getCode();

        $image_filename = $product_code . '.png';
        $image_dir = $doc_root . $app_path . 'images/';

        if (isset($_FILES['file1'])) {
            $source = $_FILES['file1']['tmp_name'];
            
            if (empty($source)) {
                display_error('You must select an image to upload. Please go back and try again.');
            } else {
                $target = $image_dir . DIRECTORY_SEPARATOR . $image_filename;

                // save uploaded file with correct filename
                move_uploaded_file($source, $target);

                // add code that creates the medium and small versions of the image
                process_image($image_dir, $image_filename);

                // display product with new image
                redirect(".?action=view_product&product_id=$product_id");
                }
        }
        break;
    default:
        display_error('Unknown product action: ' . $action);
        break;
}
?>