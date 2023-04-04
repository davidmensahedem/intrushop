<?php
require_once('../util/main.php');     // update include path

require_once('model/user.php');
require_once('model/customer.php');
require_once('model/customer_db.php');
require_once('model/address.php');
require_once('model/address_db.php');
require_once('model/card.php');
require_once('model/order.php');
require_once('model/orderItem.php');
require_once('model/order_db.php');
require_once('model/category.php');
require_once('model/category_db.php');
require_once('model/product.php');
require_once('model/product_db.php');

require_once('model/fields.php');
require_once('model/validate.php');

$action = filter_input(INPUT_POST, 'action');
if ($action == NULL) {
    $action = filter_input(INPUT_GET, 'action');
    if ($action == NULL) {        
        $action = 'view_login';
        if (isset($_SESSION['user_id'])) {
            $action = 'view_account';
        }
    }
}

// Set up all possible fields to validate
$validate = new Validate();
$fields = $validate->getFields();

// for the Registration page and other pages
$fields->addField('email', 'Must be valid email.');
$fields->addField('password_1');
$fields->addField('password_2');
$fields->addField('first_name');
$fields->addField('last_name');
$fields->addField('ship_line1');
$fields->addField('ship_line2', required:FALSE);
$fields->addField('ship_city');
$fields->addField('ship_state');
$fields->addField('ship_zip');
$fields->addField('ship_phone', required:FALSE);
$fields->addField('bill_line1');
$fields->addField('bill_line2', required:FALSE);
$fields->addField('bill_city');
$fields->addField('bill_state');
$fields->addField('bill_zip');
$fields->addField('bill_phone', required:FALSE);

// for the Login page
$fields->addField('password');

// for the Edit Address page
$fields->addField('line1');
$fields->addField('line2', required:FALSE);
$fields->addField('city');
$fields->addField('state');
$fields->addField('zip');
$fields->addField('phone', required:FALSE);

switch ($action) {
    case 'view_register':
        $categories = CategoryDB::getCategories();
        
        // Clear user data
        $customer = new Customer();
        $billing_address = new Address();
        $shipping_address = new Address();
        $use_shipping = '';
        $email_message = '';
        
        include 'account_register.php';
        break;
    case 'register':
        // Get user data 
        $customer = new Customer();
        $customer->setEmail(filter_input(INPUT_POST, 'email'));
        $customer->setPassword(filter_input(INPUT_POST, 'password_1'));
        $customer->setFirstName(filter_input(INPUT_POST, 'first_name'));
        $customer->setLastName(filter_input(INPUT_POST, 'last_name'));
        $confirm_password = filter_input(INPUT_POST, 'password_2');
        
        // Get shipping data
        $shipping_address = new Address();
        $shipping_address->setLine1(filter_input(INPUT_POST, 'ship_line1'));
        $shipping_address->setLine2(filter_input(INPUT_POST, 'ship_line2'));
        $shipping_address->setCity(filter_input(INPUT_POST, 'ship_city'));
        $shipping_address->setState(filter_input(INPUT_POST, 'ship_state'));
        $shipping_address->setZipCode(filter_input(INPUT_POST, 'ship_zip'));
        $shipping_address->setPhone(filter_input(INPUT_POST, 'ship_phone'));
        
        $use_shipping = isset($_POST['use_shipping']);
        
        $billing_address = new Address();
        if ($use_shipping) {
            $billing_address->setLine1($shipping_address->getLine1());
            $billing_address->setLine2($shipping_address->getLine2());
            $billing_address->setCity($shipping_address->getCity());
            $billing_address->setState($shipping_address->getState());
            $billing_address->setZipCode($shipping_address->getZipCode());
            $billing_address->setPhone($shipping_address->getPhone());
        } else {
            $billing_address->setLine1(filter_input(INPUT_POST, 'bill_line1'));
            $billing_address->setLine2(filter_input(INPUT_POST, 'bill_line2'));
            $billing_address->setCity(filter_input(INPUT_POST, 'bill_city'));
            $billing_address->setState(filter_input(INPUT_POST, 'bill_state'));
            $billing_address->setZipCode(filter_input(INPUT_POST, 'bill_zip'));
            $billing_address->setPhone(filter_input(INPUT_POST, 'bill_phone'));
        }
        
        // Validate user data       
        $validate->email('email', $customer->getEmail());
        $validate->password('password_1', $customer->getPassword());
        $validate->verify('password_2', $customer->getPassword(), $confirm_password);        
        $validate->text('first_name', $customer->getFirstName());
        $validate->text('last_name', $customer->getLastName());
        $validate->text('ship_line1', $shipping_address->getLine1());        
        $validate->text('ship_line2', $shipping_address->getLine2());        
        $validate->text('ship_city', $shipping_address->getCity());        
        $validate->state('ship_state', $shipping_address->getState());        
        $validate->zip('ship_zip', $shipping_address->getZipCode());        
        $validate->phone('ship_phone', $shipping_address->getPhone());        
        if (!$use_shipping) {
            $validate->text('bill_line1', $billing_address->getLine1());        
            $validate->text('bill_line2', $billing_address->getLine2());        
            $validate->text('bill_city', $billing_address->getCity());        
            $validate->state('bill_state', $billing_address->getState());        
            $validate->zip('bill_zip', $billing_address->getZipCode());        
            $validate->phone('bill_phone', $billing_address->getPhone());
        }
        
        // Check if email is in use
        $email_message = '';
        if (CustomerDB::hasCustomerEmail($customer->getEmail())) {
            $email_message = 'Email already in use.';
        }

        // If validation errors, redisplay Register page and exit controller
        if ($fields->hasErrors() || !empty($email_message)) {
            $categories = CategoryDB::getCategories();
            include 'account/account_register.php';
            break;
        }
        
        // Add the customer data to the database
        $customer_id = CustomerDB::addCustomer($customer);
        
        // Add the shipping address to the database
        $ship_id = AddressDB::addAddress($customer_id, $shipping_address);
        CustomerDB::customerChangeShippingID($customer_id, $ship_id);
        
        // Add the billing address to the database
        $bill_id = AddressDB::addAddress($customer_id, $billing_address);
        CustomerDB::customerChangeBillingID($customer_id, $bill_id);

        // Store user id in session
        $_SESSION['user_id'] = $customer_id;
        
        // Redirect to the Checkout application if necessary
        if (isset($_SESSION['checkout'])) {
            unset($_SESSION['checkout']);
            redirect('../checkout');
        } else {
            redirect('.');
        }        
        break;
    case 'view_login':
        $categories = CategoryDB::getCategories();
        
        // Clear login data
        $email = '';
        $password = '';
        $password_message = '';
        
        include 'account_login_register.php';
        break;
    case 'login':
        $email = filter_input(INPUT_POST, 'email');
        $password = filter_input(INPUT_POST, 'password');
        
        // Validate user data
        $validate->email('email', $email);
        $validate->password('password', $password);        

        // If validation errors, redisplay Login page and exit controller
        if ($fields->hasErrors()) {
            $categories = CategoryDB::getCategories();
            include 'account/account_login_register.php';
            break;
        }
        
        // Check email and password in database
        if (CustomerDB::isValidCustomerLogin($email, $password)) {
            $customer = CustomerDB::getCustomerByEmail($email);
            $_SESSION['user_id'] = $customer->getID();
        } else {
            $categories = CategoryDB::getCategories();
            $password_message = 'Login failed. Invalid email or password.';
            include 'account/account_login_register.php';
            break;
        }

        // If necessary, redirect to the Checkout app
        // Redirect to the Checkout application
        if (isset($_SESSION['checkout'])) {
            unset($_SESSION['checkout']);
            redirect('../checkout');
        } else {
            redirect('.');
        }        
        break;
    case 'view_account':
        $categories = CategoryDB::getCategories();
        $customer = CustomerDB::getCustomer($_SESSION['user_id']);

        $shipping_address = AddressDB::getAddress($customer->getShippingAddressID());
        $billing_address = AddressDB::getAddress($customer->getBillingAddressID());
        $orders = OrderDB::getOrdersByCustomerId($customer->getID());
        
        include 'account_view.php';
        break;
    case 'view_order':
        $categories = CategoryDB::getCategories();
        $order_id = filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT);
        $order = OrderDB::getOrder($order_id);
        $order_items = OrderDB::getOrderItems($order_id);

        $shipping_address = AddressDB::getAddress($order->getShippingAddressID());
        $billing_address = AddressDB::getAddress($order->getBillingAddressID());
        
        include 'account_view_order.php';
        break;
    case 'view_account_edit':
        $categories = CategoryDB::getCategories();
        $customer = CustomerDB::getCustomer($_SESSION['user_id']);     

        include 'account_edit.php';
        break;
    case 'update_account':
        // Get the customer data
        $customer = new Customer($_SESSION['user_id']);
        $customer->setFirstName(filter_input(INPUT_POST, 'first_name'));
        $customer->setLastName(filter_input(INPUT_POST, 'last_name'));
        $customer->setPassword(filter_input(INPUT_POST, 'password_1'));
        $confirm_password = filter_input(INPUT_POST, 'password_2');
        
        // allow password and confirm password to be blank
        $fields->getField('password_1')->setRequired(FALSE);
        $fields->getField('password_2')->setRequired(FALSE);
        
        // Validate user data
        $validate->password('password_1', $customer->getPassword());
        $validate->verify('password_2', $customer->getPassword(), $confirm_password);        
        $validate->text('first_name', $customer->getFirstName());
        $validate->text('last_name', $customer->getLastName());   

        // If validation errors, redisplay account edit page and exit controller
        if ($fields->hasErrors()) {
            $categories = CategoryDB::getCategories();
            include 'account/account_edit.php';
            break;
        }

        // Update the customer data
        CustomerDB::updateCustomer($customer);
        if (!empty($customer->getPassword())) {
            CustomerDB::customerChangePassword($customer);
        }

        redirect('.');
        break;
    case 'view_address_edit':
        $categories = CategoryDB::getCategories();
        $customer = CustomerDB::getCustomer($_SESSION['user_id']);
        
        // Set up variables for address type
        $address_type = filter_input(INPUT_POST, 'address_type');
        if ($address_type == 'billing') {
            $address_id = $customer->getBillingAddressID();
            $heading = 'Update Billing Address';
        } else {
            $address_id = $customer->getShippingAddressID();
            $heading = 'Update Shipping Address';
        }

        // Get the data for the address
        $address = AddressDB::getAddress($address_id);

        // Display the data on the page
        include 'address_edit.php';
        break;
    case 'update_address':
        $customer = CustomerDB::getCustomer($_SESSION['user_id']);
    
        // Set up variables for address type
        $address_type = filter_input(INPUT_POST, 'address_type');
        if ($address_type == 'billing') {
            $address = new Address($customer->getBillingAddressID());
            $heading = 'Update Billing Address';
        } else {
            $address = new Address($customer->getShippingAddressID());
            $heading = 'Update Shipping Address';
        }

        // Get the post data
        $address->setLine1(filter_input(INPUT_POST, 'line1'));
        $address->setLine2(filter_input(INPUT_POST, 'line2'));
        $address->setCity(filter_input(INPUT_POST, 'city'));
        $address->setState(filter_input(INPUT_POST, 'state'));
        $address->setZipCode(filter_input(INPUT_POST, 'zip'));
        $address->setPhone(filter_input(INPUT_POST, 'phone'));

        // Validate the data
        $validate->text('line1', $address->getLine1());        
        $validate->text('line2', $address->getLine2());        
        $validate->text('city', $address->getCity());        
        $validate->state('state', $address->getState());        
        $validate->zip('zip', $address->getZipCode());        
        $validate->phone('phone', $address->getPhone());      

        // If validation errors, redisplay Login page and exit controller
        if ($fields->hasErrors()) {
            $categories = CategoryDB::getCategories();
            include 'account/address_edit.php';
            break;
        }
        
        // If the old address has orders, disable it
        // Otherwise, delete it
        AddressDB::disableOrDeleteAddress($address->getID());

        // Add the new address
        $address_id = AddressDB::addAddress($customer->getID(), $address);
        $address->setID($address_id);

        // Relate the address to the customer account
        if ($address_type == 'billing') {
            CustomerDB::customerChangeBillingID($customer->getID(), $address_id);
        } else {
            CustomerDB::customerChangeShippingID($customer->getID(), $address_id);
        }

        redirect('.');
        break;
    case 'logout':
        unset($_SESSION['user_id']);
        redirect($app_path);
        break;
    default:
        display_error("Unknown account action: " . $action);
        break;
}
?>