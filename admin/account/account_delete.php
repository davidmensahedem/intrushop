<?php include 'view/header.php'; ?>
<?php include 'view/sidebar_admin.php'; ?>
<main class="nofloat">
    <h1>Delete Account</h1>
    <p>Are you sure you want to delete the account for
       <?php echo htmlspecialchars($admin->getName()) .
                  ' (' . htmlspecialchars($admin->getEmail()) . ')'; ?>?</p>
    <form action="." method="post">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="admin_id"
               value="<?php echo $admin->getID(); ?>">
        <input type="submit" value="Delete Account">
    </form>
    <form action="." method="post">
        <input type="submit" value="Cancel">
    </form>
</main>
<?php include 'view/footer.php'; ?>