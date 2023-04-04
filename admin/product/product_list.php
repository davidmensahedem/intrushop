<?php include 'view/header.php'; ?>
<?php include 'view/sidebar_admin.php'; ?>
<main>
    <h1 class="top">Product Manager - List Products</h1>
    <p>To view, edit, or delete a product, select the product.</p>
    <p>To add a product, select the "Add Product" link.</p>
    <h1>
        <?php echo htmlspecialchars($current_category->getName()); ?>
    </h1>
    <?php if (count($products) == 0) : ?>
        <p>There are no products for this category.</p>
    <?php else : ?>
            <?php foreach ($products as $product) : ?>
            <p>
                <a href=".?action=view_product&amp;product_id=<?php
                          echo $product->getID(); ?>">
                    <?php echo htmlspecialchars($product->getName()); ?>
                </a>
            </p>
            <?php endforeach; ?>
    <?php endif; ?>

    <h1>Links</h1>
    <p><a href=".?action=show_add_edit_form">Add Product</a></p>

</main>
<?php include 'view/footer.php'; ?>