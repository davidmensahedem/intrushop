<?php include 'view/header.php'; ?>
<?php include 'view/sidebar.php'; ?>
<main>
    <h1>My Account</h1>
    <p><?php echo $customer->getName() . ' (' . $customer->getEmail() . ')'; ?></p>
    <form action="." method="post">
        <input type="hidden" name="action" value="view_account_edit">
        <input type="submit" value="Edit Account">
    </form>
    <h2>Shipping Address</h2>
    <p><?php echo htmlspecialchars($shipping_address->getLine1()); ?><br>
        <?php if ( $shipping_address->hasLine2() ) : ?>
            <?php echo htmlspecialchars($shipping_address->getLine2()); ?><br>
        <?php endif; ?>
        <?php echo htmlspecialchars($shipping_address->getCity()); ?>, <?php 
              echo htmlspecialchars($shipping_address->getState()); ?>
        <?php echo htmlspecialchars($shipping_address->getZipCode()); ?><br>
        <?php echo htmlspecialchars($shipping_address->getPhone()); ?>
    </p>
    <form action="." method="post">
        <input type="hidden" name="action" value="view_address_edit">
        <input type="hidden" name="address_type" value="shipping">
        <input type="submit" value="Edit Shipping Address">
    </form>
    <h2>Billing Address</h2>
    <p><?php echo htmlspecialchars($billing_address->getLine1()); ?><br>
        <?php if ( $billing_address->hasLine2() ) : ?>
            <?php echo htmlspecialchars($billing_address->getLine2()); ?><br>
        <?php endif; ?>
        <?php echo htmlspecialchars($billing_address->getCity()); ?>, <?php 
              echo htmlspecialchars($billing_address->getState()); ?>
        <?php echo htmlspecialchars($billing_address->getZipCode()); ?><br>
        <?php echo htmlspecialchars($billing_address->getPhone()); ?>
    </p>
    <form action="." method="post">
        <input type="hidden" name="action" value="view_address_edit">
        <input type="hidden" name="address_type" value="billing">
        <input type="submit" value="Edit Billing Address">
    </form>
    <?php if (count($orders) > 0 ) : ?>
        <h2>Your Orders</h2>
        <ul>
            <?php foreach($orders as $order) :
                $url = $app_path . 'account' .
                       '?action=view_order&order_id=' . $order->getID();
                ?>
                <li>
                    Order # <a href="<?php echo $url; ?>">
                    <?php echo $order->getID(); ?></a> placed on
                    <?php echo $order->getOrderDateFormatted(); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</main>
<?php include 'view/footer.php'; ?>
