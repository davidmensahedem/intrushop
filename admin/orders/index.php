<?php
require_once('../../util/main.php');  // update include path
require_once('util/valid_admin.php'); // require admin user

require_once('model/category.php');
require_once('model/user.php');
require_once('model/customer.php');
require_once('model/customer_db.php');
require_once('model/address.php');
require_once('model/address_db.php');
require_once('model/card.php');
require_once('model/order.php');
require_once('model/orderItem.php');
require_once('model/order_db.php');
require_once('model/product.php');
require_once('model/product_db.php');

$action = filter_input(INPUT_POST, 'action');
if ($action == NULL) {
    $action = filter_input(INPUT_GET, 'action');
    if ($action == NULL) {        
        $action = 'view_orders';
    }
}

switch($action) {
    case 'view_orders':
        $new_orders = OrderDB::getUnfilledOrders();
        $old_orders = OrderDB::getFilledOrders();
        include 'orders.php';
        break;
    case 'view_order':
        $order_id = filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT);

        // Get order data
        $order = orderDB::getOrder($order_id);
        $order_items = orderDB::getOrderItems($order_id);

        // Get customer data
        $customer = CustomerDB::getCustomer($order->getCustomerID());
        
        // Get address data
        $shipping_address = AddressDB::getAddress($order->getShippingAddressID());
        $billing_address = AddressDB::getAddress($order->getBillingAddressID());

        include 'order.php';
        break;
    case 'set_ship_date':
        $order_id = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
        OrderDB::setShipDate($order_id);
        redirect(".?action=view_order&order_id=$order_id");
    case 'confirm_delete':
        // Get order data
        $order_id = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
        $order = OrderDB::getOrder($order_id);

        // Get customer data
        $customer = CustomerDB::getCustomer($order->getCustomerID());

        include 'confirm_delete.php';
        break;
    case 'delete':
        $order_id = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
        OrderDB::deleteOrder($order_id);
        redirect('.');
        break;
    default:
        display_error("Unknown order action: " . $action);
        break;
}
?>