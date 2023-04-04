<?php include 'view/header.php'; ?>
<main>
    <h1>Confirm Order</h1>
    <table id="cart">
        <tr id="cart_header">
            <th class="left" >Item</th>
            <th class="right">Price</th>
            <th class="right">Quantity</th>
            <th class="right">Total</th>
        </tr>
        <?php foreach ($cart_items as $product_id => $item) : 
            $product = $item['product'];
            $order_item = $item['order_item'];
        ?>
            <tr>
                <td><?php echo htmlspecialchars($product->getName()); ?></td>
                <td class="right">
                    <?php echo sprintf('$%.2f', $order_item->getPrice()); ?>
                </td>
                <td class="right">
                    <?php echo $order_item->getQuantity(); ?>
                </td>
                <td class="right">
                    <?php echo sprintf('$%.2f', $order_item->getLineTotal()); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr id="cart_footer">
            <td colspan="3" class="right"><b>Subtotal</b></td>
            <td class="right">
                <?php echo sprintf('$%.2f', $subtotal); ?>
            </td>
        </tr>
        <tr>
            <td colspan="3" class="right"><?php echo $shipping_address->getState(); ?> Tax</td>
            <td class="right">
                <?php echo sprintf('$%.2f', $tax); ?>
            </td>
        </tr>
        <tr>
            <td colspan="3" class="right">Shipping</td>
            <td class="right">
                <?php echo sprintf('$%.2f', $shipping_cost); ?>
            </td>
        </tr>
            <tr>
            <td colspan="3" class="right"><b>Total</b></td>
            <td class="right">
                <?php echo sprintf('$%.2f', $total); ?>
            </td>
        </tr>
</table>
    <p>
        Proceed to: <a href="<?php echo '?action=payment'; ?>">Payment</a>
    </p>
</main>
<?php include 'view/footer.php'; ?>