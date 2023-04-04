<?php include 'view/header.php'; ?>
<main>
    <h2>Billing Address</h2>
    <p><?php echo htmlspecialchars($billing_address->getLine1()); ?><br>
        <?php if ( $billing_address->hasLine2() ) : ?>
            <?php echo htmlspecialchars($billing_address->getLine2()); ?><br>
        <?php endif; ?>
        <?php echo htmlspecialchars($billing_address->getFullAddress()); ?><br>
        <?php echo htmlspecialchars($billing_address->getPhone()); ?>
    </p>
    <form action="../account" method="post">
        <input type="hidden" name="action" value="edit_billing">
        <input type="submit" value="Edit Billing Address">
    </form>
    <h2>Payment Information</h2>
    <form action="." method="post" id="payment_form">
        <input type="hidden" name="action" value="process">
        <label>Card Type:</label>
        <select name="card_type">
            <?php foreach (Card::list() as $code => $name) : ?>
                <option value='<?php echo $code ?>' 
                    <?php echo ($card->getType() === $code) ? 'selected' : ''; ?>><?php echo $name ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php echo $fields->getField('card_type')->getHTML(); ?>
        <br>

        <label>Card Number:</label>
        <input type="text" name="card_number" 
               value="<?php echo htmlspecialchars($card->getNumber()); ?>">
        <?php echo $fields->getField('card_number')->getHTML(); ?>
        <br>

        <label>CVV:</label>
        <input type="text" name="card_cvv" 
               value="<?php echo htmlspecialchars($card->getCvv()); ?>">
        <?php echo $fields->getField('card_cvv')->getHTML(); ?>
        <br>

        <label>Expiration:</label>
        <input type="text" name="card_expires" 
               value="<?php echo htmlspecialchars($card->getExpires()); ?>">
        <?php echo $fields->getField('card_expires')->getHTML(); ?>
        <br>

        <label>&nbsp;</label>
        <input type="submit" value="Place Order">
    </form>
    <form action="../cart" method="post" >
        <input type="submit" value="Cancel Payment Entry">
    </form>
</main>
<?php include 'view/footer.php'; ?>