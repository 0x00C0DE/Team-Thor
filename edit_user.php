<?php
 session_start();
 require_once "./.dblogin.php";
 
 $postid = "";
 $success = false;
 $error = false;
 $err = array();
 
 if($_SERVER["REQUEST_METHOD"] == "POST") {

     $username = clean_input($_POST["username"]);
     if(empty($username)) {
       array_push($err, "usernameempty");
     }

     $email = clean_input($_POST["email"]);
     if(empty($email)) {
        array_push($err, "emailempty");
     }

     $locMax = clean_input($_POST["locMax"]);
     if(empty($locMax)) {
        array_push($err, "locMaxempty");
     }
     
     $postID = $_POST['id'];

     if(!sizeof($err)) {
        $conn = dbconnect();

        $stmt = $conn->prepare("UPDATE Account SET username=?, email=?, locMax=? WHERE lid='$postID';");
        $stmt->bind_param("sss", $username, $email, $locMax);

        if($stmt->execute()) {
           $success = true;
        }

        $stmt->close();
        dbclose($conn);

     }
 } 
else if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
   $postid = $_GET["id"];


}

 if($success) {
    header('location: userlist.php');
    exit;
 }




?>
<!DOCTYPE html>
<html lang="en">
<head>
 

</head>
<body>
 
   <div class="content">
      <div class="test-example">
         <h1>Update a Job Post</h1>
         <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <div class="create-test-input">
               <?php $error = in_array("usernameempty", $err); ?>
               <label>username:</label><br>
               <input type="text" name="username" <?php if($error) echo 'class="input-error"'; ?>>
            </div>
            <div class="create-test-input">
               <?php $error = in_array("emailempty", $err); ?>
               <label>email:</label><br>
               <input type="text" name="email" <?php if($error) echo 'class="input-error"'; ?>>
            </div>
            <div class="create-test-input">
               <?php $error = in_array("locMaxempty", $err); ?>
               <label>Max saveable locations:</label><br>
               <input type="text" name="locMax" <?php if($error) echo 'class="input-error"'; ?>>
            </div>
           
	    <input type="hidden" name="id" value="<?php echo $postid; ?>">
            <input type="submit" value="Edit Users" id="edit-users-submit">
         </form>
      </div>
   </div>

</body>

</html>
