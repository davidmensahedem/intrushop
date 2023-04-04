<?php include 'view/header.php'; ?>
<?php include 'view/sidebar_admin.php'; ?>
<main>
    <h1>Order Information</h1>
    <p>Order Number: <?php echo $order->getID(); ?></p>
    <p>Order Date: <?php echo $order->getOrderDateFormatted(); ?></p>
    <p>Customer: <?php echo htmlspecialchars($customer->getName()) . ' (' . 
            htmlspecialchars($customer->getEmail()) . ')'; ?></p>
    <h2>Shipping</h2>
    <?php if ($order->hasShipDate()) : ?>
        <p>Ship Date: <?php echo $order->getShipDateFormatted(); ?></p>
    <?php else: ?>
        <p>Ship Date: Not yet shipped</p>
        <form action="." method="post" >
            <input type="hidden" name="action" value="set_ship_date">
            <input type="hidden" name="order_id"
                   value="<?php echo $order->getID(); ?>">
            <input type="submit" value="Ship Order">
        </form>
        <form action="." method="post" >
            <input type="hidden" name="action" value="confirm_delete">
            <input type="hidden" name="order_id"
                   value="<?php echo $order->getID(); ?>">
            <input type="submit" value="Delete Order">
        </form>
    <?php endif; ?>
    <p><?php echo htmlspecialchars($shipping_address->getLine1()); ?><br>
        <?php if ( $shipping_address->hasLine2() > 0 ) : ?>
            <?php echo htmlspecialchars($shipping_address->getLine2()); ?><br>
        <?php endif; ?>
        <?php echo htmlspecialchars($shipping_address->getCity()); ?>, <?php 
              echo htmlspecialchars($shipping_address->getState()); ?>
        <?php echo htmlspecialchars($shipping_address->getZipCode()); ?><br>
        <?php echo htmlspecialchars($shipping_address->getPhone()); ?>
    </p>
    <h2>Billing</h2>
    <p>Card Number: <?php echo htmlspecialchars($order->getCard()->getNumber()) . ' (' . 
            htmlspecialchars($order->getCard()->getName()) . ')'; ?></p>
    <p>Card Expires: <?php echo htmlspecialchars($order->getCard()->getExpires()); ?></p>
    <p><?php echo htmlspecialchars($billing_address->getLine1()); ?><br>
        <?php if ( $billing_address->hasLine2() ) : ?>
            <?php echo htmlspecialchars($billing_address->getLine2()); ?><br>
        <?php endif; ?>
        <?php echo htmlspecialchars($billing_address->getCity()); ?>, <?php 
              echo htmlspecialchars($billing_address->getState()); ?>
        <?php echo htmlspecialchars($billing_address->getZipCode()); ?><br>
        <?php echo htmlspecialchars($billing_address->getPhone()); ?>
    </p>
    <h2>Order Items</h2>
    <table id="cart">
        <tr id="cart_header">
            <th class="left">Item</th>
            <th class="right">List Price</th>
            <th class="right">Savings</th>
            <th class="right">Your Cost</th>
            <th class="right">Quantity</th>
            <th class="right">Line Total</th>
        </tr>
        <?php
        $subtotal = 0;
        foreach ($order_items as $item) :
            $product = ProductDB::getProduct($item->getProductID());
            $subtotal += $item->getLineTotal();
            ?>
            <tr>
                <td><?php echo htmlspecialchars($product->getName()); ?></td>
                <td class="right">
                    <?php echo sprintf('$%.2f', $item->getPrice()); ?>
                </td>
                <td class="right">
                    <?php echo sprintf('$%.2f', $item->getDiscountAmount()); ?>
                </td>
                <td class="right">
                    <?php echo sprintf('$%.2f', $item->getCost()); ?>
                </td>
                <td class="right">
                    <?php echo $item->getQuantity(); ?>
                </td>
                <td class="right">
                    <?php echo sprintf('$%.2f', $item->getLineTotal()); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr id="cart_footer">
            <td colspan="5" class="right">Subtotal:</td>
            <td class="right">
                <?php echo sprintf('$%.2f', $subtotal); ?>
            </td>
        </tr>
        <tr>
            <td colspan="5" class="right">
                <?php echo htmlspecialchars($shipping_address->getState()); ?> Tax:
            </td>
            <td class="right">
                <?php echo sprintf('$%.2f', $order->getTaxAmount()); ?>
            </td>
        </tr>
        <tr>
            <td colspan="5" class="right">Shipping:</td>
            <td class="right">
                <?php echo sprintf('$%.2f', $order->getShipAmount()); ?>
            </td>
        </tr>
            <tr>
            <td colspan="5" class="right">Total:</td>
            <td class="right">
                <?php
                    $total = $order->getTotal($subtotal);
                    echo sprintf('$%.2f', $total);
                ?>
            </td>
        </tr>
</table>
</main>
<?php include 'view/footer.php'; ?>