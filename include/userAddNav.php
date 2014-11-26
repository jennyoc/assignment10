<nav id="userNav">
    
    <ol>
        <?php
        if ($path_parts['filename'] == "userList") {
            print '<li class="activePage">Current Users</li>';
        } else {
            print '<li><a href="userList.php">Current Users</a></li>';
        }
      
        if ($path_parts['filename'] == "userAddForm") {
            print '<li class="activePage">Add a User</li>';
        } else {
            print '<li><a href="userAddForm.php">Add a User</a></li>';
        }
        ?>
        </ol>
    
</nav> 