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
		
		//get user lid and current subscription status
		$user = mysqli_real_escape_string($con,$_SESSION["user"]);
		$location = mysqli_real_escape_string($con,$_POST["location_name"]);
		$query = "SELECT * FROM Account ";
		$query .= "JOIN locations ON Account.lid=locations.user_lid ";
		$query .= "WHERE Account.username='".$user."' ";
		$query .= "AND locations.name='".$location."'";
		$res = mysqli_query($con,$query);
		if($res && mysqli_num_rows($res)==1){
			$data = mysqli_fetch_array($res);
			$userLid = $data["user_lid"];
			$newSubStatus = "YES";
			if($data["is_subscribed"]=="YES") $newSubStatus = "NO";

			//send update query
			$update = "UPDATE locations ";
			$update .= "SET is_subscribed='".$newSubStatus."' ";
			$update .= "WHERE name='".$location."' ";
			$update .= "AND user_lid='".$userLid."'";
			$res = mysqli_query($con,$update);
			if($res){
				//redirect back to homepage
				header("location: ../index.php",TRUE);
				exit;
			}
			else{	//update failed
				echo '</h2>Failed to update subscription status<h2>';
				echo '<h4>'.mysqli_error($con).'<h4>';
			}
		}
		else{
			echo '<h2>Falied to fetch user information</h2>';
			echo '<h4>'.mysqli_error($con).'<h4>';
		}
	}
	else{
		echo '<h2>Not logged in</h2>';
	}
}
else{
	echo '<h2>Error</h2><h4>'.$_SERVER["REQUEST_METHOD"].' method not allowed</h4>';
}
?>
