<nav id="dogNav">
    
    <ol>
        <?php
        if ($path_parts['filename'] == "dogList") {
            print '<li class="activePage">Current Dogs</li>';
        } else {
            print '<li><a href="dogList.php">Current Dogs</a></li>';
        }
      
        if ($path_parts['filename'] == "dogAddForm") {
            print '<li class="activePage">Add a Dog</li>';
        } else {
            print '<li><a href="dogAddForm.php">Add a Dog</a></li>';
        }
        ?>
        </ol>
    
</nav> 