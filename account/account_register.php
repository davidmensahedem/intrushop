<?php include 'view/header.php'; ?>
<?php include 'view/sidebar.php'; ?>

<main>
    <h1>Register</h1>
    <form action="." method="post" id="register_form">

        <h2>Customer Information</h2>
        <input type="hidden" name="action" value="register">

        <label>E-Mail:</label>
        <input type="text" name="email"
               value="<?php echo htmlspecialchars($customer->getEmail()); ?>" size="30">
        <?php echo $fields->getField('email')->getHTML(); ?>
        <span class="error"><?php echo htmlspecialchars($email_message); ?></span><br>

        <label>Password:</label>
        <input type="password" name="password_1" size="30">
        <?php echo $fields->getField('password_1')->getHTML(); ?><br>

        <label>Retype Password:</label>
        <input type="password" name="password_2" size="30">
        <?php echo $fields->getField('password_2')->getHTML(); ?><br>

        <label>First Name:</label>
        <input type="text" name="first_name"
               value="<?php echo htmlspecialchars($customer->getFirstName()); ?>" 
               size="30">
        <?php echo $fields->getField('first_name')->getHTML(); ?><br>

        <label>Last Name:</label>
        <input type="text" name="last_name"
               value="<?php echo htmlspecialchars($customer->getLastName()); ?>"
               size="30">
        <?php echo $fields->getField('last_name')->getHTML(); ?><br>

        <h2>Shipping Address</h2>
        <label>Address:</label>
        <input type="text" name="ship_line1"
               value="<?php echo htmlspecialchars($shipping_address->getLine1()); ?>"
               size="30">
        <?php echo $fields->getField('ship_line1')->getHTML(); ?><br>

        <label>Line 2:</label>
        <input type="text" name="ship_line2"
               value="<?php echo htmlspecialchars($shipping_address->getLine2()); ?>"
               size="30">
        <?php echo $fields->getField('ship_line2')->getHTML(); ?><br>

        <label>City:</label>
        <input type="text" name="ship_city"
               value="<?php echo htmlspecialchars($shipping_address->getCity()); ?>"
               size="30">
        <?php echo $fields->getField('ship_city')->getHTML(); ?><br>

        <label>State:</label>
        <input type="text" name="ship_state"
               value="<?php echo htmlspecialchars($shipping_address->getState()); ?>"
               size="30">
        <?php echo $fields->getField('ship_state')->getHTML(); ?><br>

        <label>Zip Code:</label>
        <input type="text" name="ship_zip"
               value="<?php echo htmlspecialchars($shipping_address->getZipCode()); ?>"
               size="30">
        <?php echo $fields->getField('ship_zip')->getHTML(); ?><br>

        <label>Phone:</label>
        <input type="text" name="ship_phone"
               value="<?php echo htmlspecialchars($shipping_address->getPhone()); ?>"
               size="30">
        <?php echo $fields->getField('ship_phone')->getHTML(); ?><br>

        <h2>Billing Address</h2>
        <label>&nbsp;</label>
        <input type="checkbox" name="use_shipping"
               <?php if ($use_shipping) : ?>checked<?php endif; ?>
               size="30"> Use shipping address
        <br>

        <label>Address:</label>
        <input type="text" name="bill_line1"
               value="<?php echo htmlspecialchars($billing_address->getLine1()); ?>"
               size="30">
        <?php echo $fields->getField('bill_line1')->getHTML(); ?><br>

        <label>Line 2:</label>
        <input type="text" name="bill_line2"
               value="<?php echo htmlspecialchars($billing_address->getLine2()); ?>"
               size="30">
        <?php echo $fields->getField('bill_line2')->getHTML(); ?><br>

        <label>City:</label>
        <input type="text" name="bill_city"
               value="<?php echo htmlspecialchars($billing_address->getCity()); ?>"
               size="30">
        <?php echo $fields->getField('bill_city')->getHTML(); ?><br>

        <label>State:</label>
        <input type="text" name="bill_state"
               value="<?php echo htmlspecialchars($billing_address->getState()); ?>"
               size="30">
        <?php echo $fields->getField('bill_state')->getHTML(); ?><br>

        <label>Zip Code:</label>
        <input type="text" name="bill_zip"
               value="<?php echo htmlspecialchars($billing_address->getZipCode()); ?>"
               size="30">
        <?php echo $fields->getField('bill_zip')->getHTML(); ?><br>

        <label>Phone:</label>
        <input type="text" name="bill_phone"
               value="<?php echo htmlspecialchars($billing_address->getPhone()); ?>"
               size="30">
        <?php echo $fields->getField('bill_phone')->getHTML(); ?><br>

        <label>&nbsp;</label>
        <input type="submit" value="Register">
    </form>
</main>
<?php include 'view/footer.php'; ?>