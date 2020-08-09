<?php
//Function filters input
function clean_input($input) {
   $input = trim($input);
   $input = stripslashes($input);
   $input = htmlspecialchars($input);
   return $input;

}
//function connects to database unless error occurs
function dbconnect() {
   $hostname = 'classmysql.engr.oregonstate.edu';
   $username = 'cs361_haggerto';
   $password = '=R:3.s*\'&MwVU-Qcbv#5)c^L)(]["~a}V%%';

   $con = mysqli_connect($hostname, $username, $password, $username);
//If connection cant be made it fails
   if (!$con) {
      die("Connection failed" . mysqli_connect_error());
   }
   return $con;
}
//Closes database connection
function dbclose($con) {
   mysqli_close($con);
}

?>
