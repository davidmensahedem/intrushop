<?php include 'view/header.php'; ?>
<?php include 'view/sidebar_admin.php'; ?>
<main>
    <?php
    // set add or edit text and action
    if (isset($product_id)) {
        $heading_text = 'Edit Product';
        $action = 'update_product';
    } else {
        $heading_text = 'Add Product';
        $action = 'add_product';
    }
    
    // get product values for display
    $category_id = ($product->getCategory()->hasID()) ? $product->getCategory()->getID() : '' ;
    $code = htmlspecialchars($product->getCode());
    $name = htmlspecialchars($product->getName());
    $price = ($product->hasPrice()) ? $product->getPrice() : '';
    $discount = ($product->hasDiscountPercent()) ? $product->getDiscountPercent() : '';
    $description = htmlspecialchars($product->getDescription());
    
    ?>
    <h1>Product Manager - <?php echo $heading_text; ?></h1>
    <form action="index.php" method="post" id="add_product_form">
        <input type="hidden" name="action" 
               value="<?php echo $action; ?>" />
        <input type="hidden" name="category_id" 
               value="<?php echo $category_id; ?>" />
        <input type="hidden" name="product_id"
               value="<?php echo $product_id; ?>" />

        <label>Category:</label>
        <select name="category_id">
        <?php foreach ($categories as $category) : 
            $selected = '';
            if ($category->getID() == $category_id) {
                $selected = 'selected';
            }
        ?>
            <option value="<?php echo $category->getID(); ?>" 
                <?php echo $selected; ?>><?php echo $category->getName(); ?>
            </option>
            <?php echo $fields->getField('category_id')->getHTML(); ?>
        <?php endforeach; ?>
        </select><br>

        <label>Code:</label>
        <input type="text" name="code"
               value="<?php echo $code; ?>">
        <?php echo $fields->getField('code')->getHTML(); ?>
        <br>

        <label>Name:</label>
        <input type="text" name="name" 
               value="<?php echo $name; ?>" 
               size="50">
        <?php echo $fields->getField('name')->getHTML(); ?>
        <br>

        <label>List Price:</label>
        <input type="text" name="price" 
               value="<?php echo $price; ?>">
        <?php echo $fields->getField('price')->getHTML(); ?>
        <br>

        <label>Discount Percent:</label>
        <input type="text" name="discount_percent" 
               value="<?php echo $discount; ?>">
        <?php echo $fields->getField('discount_percent')->getHTML(); ?>
        <br>

        <label>Description:</label>
        <textarea name="description" rows="10"
                  cols="50"><?php echo $description; ?></textarea>
        <?php echo $fields->getField('description')->getHTML(); ?>
        <br>

        <label>&nbsp;</label>
        <input type="submit" value="Submit">
        
    </form>
    <div id="formatting_directions">
        <h2>How to work with the description</h2>
        <ul>
            <li>Use two returns to start a new paragraph.</li>
            <li>Use an asterisk to mark items in a bulleted list.</li>
            <li>Use one return between items in a bulleted list.</li>
            <li>Use standard HMTL tags for bold and italics.</li>
        </ul>
    </div>

</main>
<?php include 'view/footer.php'; ?>