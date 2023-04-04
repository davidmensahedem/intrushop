<?php
require_once('../util/main.php');      // update include path

require_once('model/cart.php');
require_once('model/card.php');
require_once('model/category.php');
require_once('model/product.php');
require_once('model/product_db.php');
require_once('model/orderItem.php');
require_once('model/order_db.php');
require_once('model/user.php');
require_once('model/customer.php');
require_once('model/customer_db.php');
require_once('model/address.php');
require_once('model/address_db.php');

require_once('model/fields.php');
require_once('model/validate.php');

if (!isset($_SESSION['user_id'])) {
    $_SESSION['checkout'] = TRUE;
    redirect('../account');
    exit();
}

$action = filter_input(INPUT_POST, 'action');
if ($action == NULL) {
    $action = filter_input(INPUT_GET, 'action');
    if ($action == NULL) {        
        $action = 'confirm';
    }
}

// Set up validation
$validate = new Validate();
$fields = $validate->getFields();
$fields->addField('card_type');
$fields->addField('card_number', 'No dashes or spaces.');
$fields->addField('card_cvv');
$fields->addField('card_expires', 'MM/YYYY');

// get card and cart objects
$cart = new Cart();
$card = new Card();

// get customer data
$customer = CustomerDB::getCustomer($_SESSION['user_id']);
$shipping_address = AddressDB::getAddress($customer->getShippingAddressID());
$billing_address = AddressDB::getAddress($customer->getBillingAddressID());

switch ($action) {
    case 'confirm':
        if ($cart->getProductCount() == 0) {
            redirect($app_path . 'cart');
        }
        $cart_items = $cart->getItems();
        $subtotal = $cart->getSubtotal();
        $item_shipping = 5;
        $shipping_cost = OrderDB::getShippingCost($cart->getItemCount());
        $tax = OrderDB::getTaxAmount($subtotal, $shipping_address);    
        $total = $subtotal + $tax + $shipping_cost;
        
        include 'checkout_confirm.php';
        break;
    case 'payment':
        if ($cart->getProductCount() == 0) {
            redirect($app_path . 'cart');
        }
        include 'checkout_payment.php';
        break;
    case 'process':
        if ($cart->getProductCount() == 0) {
            redirect($app_path . 'cart');
        }
        $cart_items = $cart->getItems();
        $card->setType(filter_input(INPUT_POST, 'card_type'));
        $card->setNumber(filter_input(INPUT_POST, 'card_number'));
        $card->setCvv(filter_input(INPUT_POST, 'card_cvv'));
        $card->setExpires(filter_input(INPUT_POST, 'card_expires'));

        // Validate user data
        $validate->cardType('card_type', $card->getType(), Card::codes());
        $validate->cardNumber('card_number', $card->getNumber(), $card->getType());
        $validate->cardCvv('card_cvv', $card->getCvv(), $card->getType());
        $validate->cardExpDate('card_expires', $card->getExpires());
        
        // If validation errors, redisplay Checkout page and exit controller
        if ($fields->hasErrors()) {
            include 'checkout/checkout_payment.php';
            break;
        }

        // add order to database
        $order_id = OrderDB::addOrder($customer, $cart, $shipping_address, $card);
        
        // add order items to database
        foreach($cart_items as $product_id => $item) {
            $order_item = $item['order_item'];
            $order_item->setOrderID($order_id);
            OrderDB::addOrderItem($order_item);
        }
        
        $cart->clear();
        
        redirect($app_path . 'account?action=view_order&order_id=' . $order_id);
        break;
    default:
        display_error('Unknown cart action: ' . $action);
        break;
}
?>
