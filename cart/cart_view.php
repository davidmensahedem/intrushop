<?php include 'view/header.php'; ?>
<main>
    <h1>Your Cart</h1>
    <?php if ($cart->getProductCount() == 0) : ?>
        <p>There are no products in your cart.</p>
    <?php else: ?>
        <p>To remove an item from your cart, change its quantity to 0.</p>
        <form action="." method="post">
            <input type="hidden" name="action" value="update">
            <table id="cart">
            <tr id="cart_header">
                <th class="left">Item</th>
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
                    <?php echo sprintf('$%.2f', $product->getDiscountPrice()); ?>
                </td>
                <td class="right">
                    <input type="text" size="3" class="right"
                           name="items[<?php echo $product_id; ?>]"
                           value="<?php echo $order_item->getQuantity(); ?>">
                </td>
                <td class="right">
                    <?php echo sprintf('$%.2f', $order_item->getLineTotal()); ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <tr id="cart_footer" >
                <td colspan="3" class="right" ><b>Subtotal</b></td>
                <td class="right">
                    <?php echo sprintf('$%.2f', $cart->getSubtotal()); ?>
                </td>
            </tr>
            <tr>
                <td colspan="4" class="right">
                    <input type="submit" value="Update Cart">
                </td>
            </tr>
            </table>
        </form>
        
    <?php endif; ?>

    <p>Return to: <a href="../">Home</a></p>

    <!-- display most recent category -->
    <?php if ($cart->hasLastCategory()) :
            $category = $cart->getLastCategory();
            $category_url = $app_path . 'catalog' .
                '?category_id=' . $category->getID();
        ?>
        <p>Return to: <a href="<?php echo $category_url; ?>">
            <?php echo $category->getName(); ?></a></p>
    <?php endif; ?>

    <!-- if cart has items, display the Checkout link -->
    <?php if ($cart->getProductCount() > 0) : ?>
        <p>
            Proceed to: <a href="<?php echo $app_path . 'checkout'; ?>">Checkout</a>
        </p>
    <?php endif; ?>
</main>
<?php include 'view/footer.php'; ?>