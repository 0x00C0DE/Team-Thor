<?php
session_save_path("../sessionDir");
session_start();
//show errors
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["location_name"])){
	if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]){	//check if user is logged in
		//connect to db
		require_once "../.dblogin.php";
		$con = dbconnect();
		
		//delete location
		$location_name = mysqli_real_escape_string($con,$_POST["location_name"]);
		$username = mysqli_real_escape_string($con,$_SESSION["user"]);
		$query = "DELETE FROM locations WHERE name='".$location_name."' AND user_lid=(";
		$query .= "SELECT lid FROM Account WHERE username='".$username."')";
		$res = mysqli_query($con,$query);
		if($res){
			//redirect back to homepage
			header("location: ../index.php",TRUE);
			exit;
		}
		else{	//delete failed
			echo '<h2>Falied to fetch delete location</h2>';
			echo '<h4>'.mysqli_error($con).'<h4>';
		}
	}
	else{
		echo '<h2>Error: Not logged in</h2>';
	}
}
else{
	echo '<h2>Error</h2><h4>'.$_SERVER["REQUEST_METHOD"].' method not allowed</h4>';
}
?>
