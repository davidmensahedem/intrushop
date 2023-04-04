<?php
require_once('../../util/main.php');  // update include path
/* 
 * Don't require util/valid_admin.php file to require admin user
 * because need to allow non-logged in users to get to login page
 */

require_once('model/user.php');
require_once('model/admin.php');
require_once('model/admin_db.php');

require_once('model/fields.php');
require_once('model/validate.php');

$action = filter_input(INPUT_POST, 'action');
if (AdminDB::getAdminCount() == 0) {
    if ($action != 'create') {
        $action = 'view_account';
    }
} elseif (isset($_SESSION['admin_id'])) {
    if ($action == NULL) {
        $action = filter_input(INPUT_GET, 'action');
        if ($action == NULL ) {
            $action = 'view_account';            
        }
    }
} elseif ($action == 'login') {
    $action = 'login';
} else {
    $action = 'view_login';
}

// Set up all possible fields to validate
$validate = new Validate();
$fields = $validate->getFields();

// for the Add Account page and other pages
$fields->addField('email', 'Must be valid email.');
$fields->addField('password_1');
$fields->addField('password_2');
$fields->addField('first_name');
$fields->addField('last_name');

// for the Login page
$fields->addField('password');

switch ($action) {
    case 'view_login':
        // Clear login data
        $email = '';
        $password = '';
        $password_message = '';
        
        include 'account_login.php';
        break;
    case 'login':
        // Get username/password
        $email = filter_input(INPUT_POST, 'email');
        $password = filter_input(INPUT_POST, 'password');
        
        // Validate user data       
        $validate->email('email', $email);
        $validate->text('password', $password, min:6);        

        // If validation errors, redisplay Login page and exit controller
        if ($fields->hasErrors()) {
            include 'admin/account/account_login.php';
            break;
        }
        
        // Check database - if valid username/password, log in
        if (AdminDB::isValidAdminLogin($email, $password)) {
            $admin = AdminDB::getAdminByEmail($email);
            $_SESSION['admin_id'] = $admin->getID();
        } else {
            $password_message = 'Login failed. Invalid email or password.';
            include 'admin/account/account_login.php';
            break;
        }

        // Display Admin Menu page
        redirect($app_path . 'admin');
        break;
    case 'view_account':
        // Get all accounts and current admin from database
        $admins = AdminDB::getAllAdmins();
        $current_admin = AdminDB::getAdmin($_SESSION['admin_id']);

        // Set up variables for add form
        $new_admin = new Admin();
        if (!isset($email_message)) { 
            $email_message = '';             
        }

        // View admin accounts
        include 'account_view.php';
        break;
    case 'create':
        // Get admin user data
        $new_admin = new Admin();
        $new_admin->setEmail(filter_input(INPUT_POST, 'email'));
        $new_admin->setFirstName(filter_input(INPUT_POST, 'first_name'));
        $new_admin->setLastName(filter_input(INPUT_POST, 'last_name'));
        $new_admin->setPassword(filter_input(INPUT_POST, 'password_1'));
        $confirm_password = filter_input(INPUT_POST, 'password_2');

        // Validate admin user data
        $validate->email('email', $new_admin->getEmail());
        $validate->text('first_name', $new_admin->getFirstName());
        $validate->text('last_name', $new_admin->getLastName());        
        $validate->text('password_1', $new_admin->getPassword(), min:6);
        $validate->verify('password_2', $confirm_password, $new_admin->getPassword());     
        
        // Validate unique email 
        $email_message = '';
        if (AdminDB::isValidAdminEmail($new_admin->getEmail())) {
            $email_message = 'This email is already in use.';
        }
        
        // If validation errors, redisplay account page and exit controller
        if ($fields->hasErrors() || !empty($email_message)) {
            $admins = AdminDB::getAllAdmins();
            $current_admin = AdminDB::getAdmin($_SESSION['admin_id']);
            include 'admin/account/account_view.php';
            break;
        }

        // Add admin user
        $admin_id = AdminDB::addAdmin($new_admin);

        // Set admin user in session
        if (!isset($_SESSION['admin_id'])) {
            $_SESSION['admin_id'] = $admin_id;
        }

        redirect('.');
        break;
    case 'view_edit':
        // Get admin user data
        $admin_id = filter_input(INPUT_POST, 'admin_id', FILTER_VALIDATE_INT);
        $admin = AdminDB::getAdmin($admin_id);

        // Display Edit page
        include 'account_edit.php';
        break;
    case 'update':
        $admin = new Admin();
        $admin->setID(filter_input(INPUT_POST, 'admin_id', FILTER_VALIDATE_INT));
        $admin->setFirstName(filter_input(INPUT_POST, 'first_name'));
        $admin->setLastName(filter_input(INPUT_POST, 'last_name'));
        $admin->setPassword(filter_input(INPUT_POST, 'password_1'));
        $confirm_password = filter_input(INPUT_POST, 'password_2');
        
        // allow password and confirm password to be blank
        $fields->getField('password_1')->setRequired(FALSE);
        $fields->getField('password_2')->setRequired(FALSE);
        
        // Validate admin user data
        $validate->text('first_name', $admin->getFirstName());
        $validate->text('last_name', $admin->getLastName());        
        $validate->text('password_1', $admin->getPassword(), min:6);
        $validate->verify('password_2', $admin->getPassword(), $confirm_password);   
        
        // If validation errors, redisplay edit page and exit controller
        if ($fields->hasErrors()) {
            include 'admin/account/account_edit.php';
            break;
        }

        AdminDB::updateAdmin($admin);
        if ($admin->hasPassword()) {
            AdminDB::changePassword($admin);
        }
        
        redirect('.');
        break;
    case 'view_delete_confirm':
        $admin_id = filter_input(INPUT_POST, 'admin_id', FILTER_VALIDATE_INT);
        if ($admin_id == $_SESSION['admin_id']) {
            display_error('You cannot delete your own account.');
        }
        $admin = AdminDB::getAdmin($admin_id);
        include 'account_delete.php';
        break;
    case 'delete':
        $admin_id = filter_input(INPUT_POST, 'admin_id', FILTER_VALIDATE_INT);
        AdminDB::deleteAdmin($admin_id);
        redirect('.');
        break;
    case 'logout':
        unset($_SESSION['admin_id']);
        redirect('.');
        break;
    default:
        display_error('Unknown account action: ' . $action);
        break;
}
?>