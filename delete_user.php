<?php
    require_once "./.dblogin.php";
    $con = dbconnect();

    $postID = $_GET['lid'];
    $msg = "test";
    $sqldelete = "DELETE FROM Account WHERE lid='$postID'";

    $deletePost = mysqli_query($con, $sqldelete);
    if($deletePost)
    {
      header("refresh:0; url=userlist.php");
    }
    else
    {
      echo "Not deleted";
      echo " lid:" . $postID . "";
    }
    dbclose($con)
?>