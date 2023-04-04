<aside>
    <h2>Links</h2>
    <ul>
        <li>
            <a href="<?php echo $app_path . 'cart'; ?>">View Cart</a>
        </li>
            <?php
            // Check if user is logged in and
            // display appropriate account links
            $account_url = $app_path . 'account';
            $logout_url = $account_url . '?action=logout';
            if (isset($_SESSION['user_id'])) :
            ?>
                <li><a href="<?php echo $account_url; ?>">My Account</a></li>
                <li><a href="<?php echo $logout_url; ?>">Logout</a>
            <?php else: ?>
                <li><a href="<?php echo $account_url; ?>">Login/Register</a></li>
            <?php endif; ?>
        <li>
            <a href="<?php echo $app_path; ?>">Home</a>
        </li>
    </ul>
        
    <h2>Categories</h2>
    <ul>
        <!-- display links for all categories -->
        <?php foreach($categories as $category) :
                $url = $app_path . 'catalog?category_id=' . $category->getID();
        ?>
        <li>
            <a href="<?php echo $url; ?>">
               <?php echo htmlspecialchars($category->getName()); ?>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
    
    <h2>Temp Link</h2>
    <ul>
        <li>
            <!-- This link is for testing only.
                 Remove it from a production application. -->
            <a href="<?php echo $app_path; ?>admin">Admin</a>
        </li>        
    </ul>
</aside>
