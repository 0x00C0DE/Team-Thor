<?php

function clean_input($input) {
   $input = trim($input);
   $input = stripslashes($input);
   $input = htmlspecialchars($input);
   return $input;

}

function dbconnect() {
   $hostname = 'classmysql.engr.oregonstate.edu';
   $username = 'cs361_leebrad';
   $password = 'temp102';

   $con = mysqli_connect($hostname, $username, $password, $username);

   if (!$con) {
      die("Connection failed" . mysqli_connect_error());

   }

   return $con;



}

function dbclose($con) {
   mysqli_close($con);

}

?>
