<nav id="editNav">

    <ol>
        <?php
        //For tblUsers
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

        
        //For tblDogs
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
        
        
        
        //For tblShelters
        if ($path_parts['filename'] == "shelterList") {
            print '<li class="activePage">Current Shelters</li>';
        } else {
            print '<li><a href="shelterList.php">Current Shelters</a></li>';
        }
        if ($path_parts['filename'] == "shelterAddForm") {
            print '<li class="activePage">Add a Shelter</li>';
        } else {
            print '<li><a href="shelterAddForm.php">Add a Shelter</a></li>';
        }
        ?>
    </ol>

</nav> 