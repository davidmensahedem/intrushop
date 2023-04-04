<?php include 'view/header.php'; ?>
<?php include 'view/sidebar_admin.php'; ?>
<main>
    <h1>Outstanding Orders</h1>
    <?php if (count($new_orders) > 0 ) : ?>
        <ul>
            <?php foreach($new_orders as $order) :
                $url = $app_path . 'admin/orders' .
                       '?action=view_order&amp;order_id=' . $order->getID();
                ?>
                <li>
                    <a href="<?php echo $url; ?>">Order # 
                    <?php echo $order->getID(); ?></a> for
                    <?php echo htmlspecialchars($order->getCustomer()->getName()); ?> placed on
                    <?php echo $order->getOrderDateFormatted(); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>There are no shipped orders.</p>
    <?php endif; ?>
    <h1>Shipped Orders</h1>
    <?php if (count($old_orders) > 0 ) : ?>
        <ul>
            <?php foreach($old_orders as $order) :
                $url = $app_path . 'admin/orders' .
                       '?action=view_order&amp;order_id=' . $order->getID();
                ?>
                <li>
                    <a href="<?php echo $url; ?>">Order #
                    <?php echo $order->getID(); ?></a> for
                    <?php echo htmlspecialchars($order->getCustomer()->getName()); ?> placed on
                    <?php echo $order->getOrderDateFormatted(); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>There are no shipped orders.</p>
    <?php endif; ?>
</main>
<?php include 'view/footer.php'; ?>