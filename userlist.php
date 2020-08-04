<!DOCTYPE html>
<html lang="en">
<head> 
   <?php
   session_save_path("./sessionDir");
   session_start();
   require_once "./.dblogin.php";
   ?>

</head>
<body>
    <div class="content">
      <h1>List of users</h1>

      <div class= "account-info-box">
      <div class="element-list">
        <?php
        $conn = dbconnect();
        
        $sql = "SELECT * FROM `Account`";
  
        echo '----------------------';
  
        $result = mysqli_query($conn, $sql);
        
        if(mysqli_num_rows($result) > 0) {
          while($row = mysqli_fetch_array($result)) {
            echo "<div class='example-element'>";
            echo "<div> Username: " . $row['username'] . "</div>";
            echo "<div> Password: " . $row['psswrd'] . "</div>";
            echo "<div> aid: " . $row['aid'] . "</div>";
            echo "<div> email: " . $row['email'] . "</div>";
            echo "<div> Birth date: " . $row['b_date'] . "</div>";
            echo "<div> Name: " . $row['name'] . "</div>";
            echo "<div> lid: " . $row['lid'] . "</div>";
            echo "<div> Max saveable locations: " . $row['locMax'] . "</div>";
            
  
            echo "<a href=./delete_user.php?lid=".$row['lid'].">Delete</a><br>";
            echo "<a href=./edit_user.php?id=".$row['lid'].">edit</a>";
            echo "</div>";
          }
        }
        dbclose($conn);
        ?>
      </div>
    </div>
</body>

</html>
