<?php include 'view/header.php'; ?>
<?php include 'view/sidebar.php'; ?>
<main class="nofloat">
    <h1>Featured products</h1>
    <p>We have a great selection of musical instruments including
        guitars, basses, and drums. And we're constantly adding more to give
        you the best selection possible!
    </p>
    <table>
    <?php foreach ($products as $product) : ?>
        <tr>
            <td class="product_image_column" >
                <img src="images/<?php echo htmlspecialchars($product->getCode()); ?>_s.png"
                     alt="&nbsp;">
            </td>
            <td>
                <p>
                    <a href="catalog?product_id=<?php echo
                           $product->getID(); ?>">
                        <?php echo htmlspecialchars($product->getName()); ?>
                    </a>
                </p>
                <p>
                    <b>Your price:</b>
                    $<?php echo $product->getDiscountPriceFormatted(); ?>
                </p>
                <p>
                    <?php echo get_first_paragraph($product->getDescription()); ?>
                </p>
            </td>
        </tr>
    <?php endforeach; ?>
    </table>
</main>
<?php include 'view/footer.php'; ?>