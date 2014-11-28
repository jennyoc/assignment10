<nav id="shelterNav">
    
    <ol>
        <?php
        if ($path_parts['filename'] == "shelterList") {
            print '<li class="activePage">Current Users</li>';
        } else {
            print '<li><a href="shelterList.php">Current Users</a></li>';
        }
      
        if ($path_parts['filename'] == "shelterAddForm") {
            print '<li class="activePage">Add a Shelter</li>';
        } else {
            print '<li><a href="shelterAddForm.php">Add a Shelter</a></li>';
        }
        ?>
        </ol>
    
</nav> 