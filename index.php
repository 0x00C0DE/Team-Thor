<!DOCTYPE html>
<html lang="en">
<head>
   <?php
   session_start();
   require_once "./.dblogin.php";
   ?>

</head>
<body>

  <div class="header">
 
     <?php
      if(isset($_SESSION["loggedin"])) echo '<br><br><br>';

      ?>
     <h1>Weather App</h1>
     <?php
      if(!isset($_SESSION["loggedin"])) {
         echo '<div class="header-link"><a href="./create_account.php">Create Account</a></div>';
         echo '<div class="header-link"><a href="./login.php">Login</a></div>';
      }
      ?>
  </div>
   <div class="content">
      <div class="user-listing-preview">
      <?php
      if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
        echo "<li class='push-right'><a href='./profile.php'>";
        $con = dbconnect();
        $sql = "SELECT name FROM Account WHERE username='" . $_SESSION['user'] . "'";
        $result = mysqli_query($con, $sql);

        if(mysqli_num_rows($result) == 1) {
          $row = mysqli_fetch_array($result);
          echo $row['name'];
        }
        echo "</a></li>";
        echo "<li><a href='./logout.php'>Log Out</a></li>";

      }
      ?>
         <h3>Job Listings</h3>
         <?php
          $conn = dbconnect();

          $sql = "SELECT * FROM `Account`";

          $result = mysqli_query($conn, $sql);

          if(mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_array($result)) {
               echo '<div class="user-listing-preview-item">';
               echo '<h4>' . $row['username'] . '</h4>';
               echo '<p>' . substr($row['psswrd'], 0, min(strlen($row['psswrd']), 30)) . '... </p>';
               echo '<p>Salary: $' .$row['email'] . '. </p>';
               echo '<p>Posted by ' . $row['name'] . ' on ' . date("l F j, Y", strtotime($row['date'])) . '.</p>';
               echo "</div>";
            }
          }

          ?>
      </div>

   </div>
</body>

</html>
