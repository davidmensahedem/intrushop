<?php include 'view/header.php'; ?>
<?php include 'view/sidebar_admin.php'; ?>
<main>
    <h1>Edit Account</h1>
    <div id="edit_account_form">
    <form action="." method="post">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="admin_id"
               value="<?php echo $admin_id; ?>">
        
        <label>First Name:</label>
        <input type="text" name="first_name" 
               value="<?php echo htmlspecialchars($admin->getFirstName()); ?>">
        <?php echo $fields->getField('first_name')->getHTML(); ?><br>
        
        <label>Last Name:</label>
        <input type="text" name="last_name" 
               value="<?php echo htmlspecialchars($admin->getLastName()); ?>">
        <?php echo $fields->getField('last_name')->getHTML(); ?><br>
        
        <label>New Password:</label>
        <input type="password" name="password_1">
        <span>Leave blank to leave unchanged</span>
        <?php echo $fields->getField('password_1')->getHTML(); ?><br>
        
        
        <label>Retype Password:</label>
        <input type="password" name="password_2">
        <?php echo $fields->getField('password_2')->getHTML(); ?>
        <br>
        
        <label>&nbsp;</label>
        <input type="submit" value="Update Account"><br>
    </form>
    <form action="." method="post">
        <label>&nbsp;</label>
        <input type="submit" value="Cancel">
    </form>
    </div>
</main>
<?php include 'view/footer.php'; ?>