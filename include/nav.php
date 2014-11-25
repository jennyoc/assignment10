<header>
<a href="https://jmagie.w3.uvm.edu/cs148/assignment10/index.php"><img src="images/logo.png" alt="Puppy LoVermont">
</header>

<nav>
    <ol>
        <?php
        
        if ($path_parts['filename'] == "index") {
            print '<li class="activePage">Home</li>';
        } else {
            print '<li><a href="index.php">Home</a></li>';
        }
      
        if ($path_parts['filename'] == "about-us") {
            print '<li class="activePage">About Us</li>';
        } else {
            print '<li><a href="about-us.php">About Us</a></li>';
        }
        
        if ($path_parts['filename'] == "adopt") {
            print '<li class="activePage">Why Adopt?</li>';
        } else {
            print '<li><a href="adopt.php">Why Adopt?</a></li>';
        }
        
        if ($path_parts['filename'] == "search") {
            print '<li class="activePage">Search</li>';
        } else {
            print '<li><a href="search.php">Search</a></li>';
        }
        
        if ($path_parts['filename'] == "register") {
            print '<li class="activePage">Register</li>';
        } else {
            print '<li><a href="register.php">Register</a></li>';
        }
        
        if ($path_parts['filename'] == "locations") {
            print '<li class="activePage">Locations</li>';
        } else {
            print '<li><a href="locations.php">Locations</a></li>';
        }
        
        if ($path_parts['filename'] == "userForm") {
            print '<li class="activePage">Add a User</li>';
        } else {
            print '<li><a href="userForm.php">Add a User</a></li>';
        }
        ?>
    </ol>
</nav>