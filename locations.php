
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
	<?php
	session_save_path("./sessionDir");
	session_start();
	require_once "./.dblogin.php";

	require_once "./php/meta.php";
	
	//the minimum distance in kilometers between locations to be considered a neighbor
	const MIN_DIST = 25;

	//the radius of earth in kilometers
	const EARTH_RAD = 6371;
	?>
	<link rel="stylesheet" href="./css/locationStyle.css">
</head>
<body>
	<?php
	//create database connection
	$conn = dbconnect();

	//include header
	require_once './php/header.php';

	//function to calculate distance between two locations
	//formulae taken from https://edwilliams.org/avform.htm
	function distanceBetween($loc0,$loc1){
		//convert degrees to radians because the trig functions only take radians
		$lat0 = deg2rad($loc0["lat"]);
		$lon0 = deg2rad($loc0["lon"]);
		$lat1 = deg2rad($loc1["lat"]);
		$lon1 = deg2rad($loc1["lon"]);
		//calculate the great circle distance between the points
		$latDiff = sin($lat0 - $lat1);
		$cosProd = cos($lat0) * cos($lat1);
		$lonDiff = sin(($lon0 - $lon1)/2);
		$part0 = ($latDiff/2)**2;
		$part1 = $cosProd * ($lonDiff ** 2);
		$ang = 2*asin(sqrt($part0 + $part1));	//angular difference between locations in radians
		$dist = $ang * EARTH_RAD;	//distance between locations in kilometers
		return $dist;
	}

	//
	function neighborDiff($a,$b){
		if(!isset($a["neighbors"])) $numA = 0;
		else $numA = sizeof($a["neighbors"]);

		if(!isset($b["neighbors"])) $numB = 0;
		else $numB = sizeof($b["neighbors"]);

		return $numB - $numA;
	}
	?>

	<div class="container-md mb-3">
		<div class="card shadow bg-light container-fluid my-2 py-2 overflow-auto">
			<?php
			function showError($title,$body){
				echo '<div class="jumbotron">';
				echo '<h1 class="display-3">'.$title.'</h1>';
				echo '<p class="lead">'.$body.'</p>';
				echo '</div>';
			}

			//check if user is an admin
			if(userIsLoggedIn()){
				$query = 'SELECT * FROM Account WHERE username=\''.$_SESSION["user"].'\'';
				$result = mysqli_query($conn,$query);

				if(!$result || mysqli_num_rows($result)!=1){
					//query failed
					showError('Query Failed','Query to fetch account information failed');
					die();
				}
				$userData = mysqli_fetch_array($result);
				if($userData["aid"] == null){
					//user is not an admin
					showError('Forbidden','You must be an admin to view this page');
					die();
				}

				//get locations
				$locQuery = 'SELECT locations.*,username FROM locations JOIN Account ON user_lid=lid';
				$res = mysqli_query($conn,$locQuery);
				if(!$res || mysqli_num_rows($res)<1){
					//query failed
					showError('Query Failed','Query to fetch location data failed');
					die();
				}
				
				//collect locations into an array
				$locations = Array();
				while($row = mysqli_fetch_array($res)){
					$row["neighbors"] = array();	//initialize neighbors array
					$locations[] = $row;	//push row into locations
				}
				for($i=0; $i<sizeof($locations); $i++){
					for($j=$i+1; $j<sizeof($locations); $j++){
						$dist = distanceBetween($locations[$i],$locations[$j]);
						if($dist <= MIN_DIST){
							//add location[$i] to the neighbors of location[$j] and vica versa
							array_push(
								$locations[$i]["neighbors"],
								array($locations[$j]["location_id"],$locations[$j]["username"])
							);
							array_push(
								$locations[$j]["neighbors"],
								array($locations[$i]["location_id"],$locations[$i]["username"])
							);
						}
					}
				}
				//sort locations by number of neighbors
				usort($locations,"neighborDiff");

				/*Create an array of locations which have been accounted for by being a neighbor of
				another already shown location*/
				$accountedFor = Array();
				//display locations
				echo '<h2 class="text-center">Locations</h2>';
				echo '<p class="text-muted text-center">Locations are grouped together if they are ';
				echo MIN_DIST.'km apart or closer</p>';
				echo '<table class="table table-sm table-bordered">';
				echo '<tr>';
				echo '<th>Name</th>';
				echo '<th>Number of Users</th>';
				echo '<th>Latitude</th>';
				echo '<th>Longitude</th>';
				echo '<th>Users</th>';
				echo '</tr>';
				for($i=0; $i<sizeof($locations); $i++){
					$loc = $locations[$i];
					if(in_array($loc["location_id"],$accountedFor)){
						continue;	//skip location if a neighbor has already been displayed
					}
					//show data on location
					echo '<tr>';
					echo '<td>'.$loc["name"].'</td>';
					echo '<td>'.(sizeof($loc["neighbors"])+1).'</td>';
					echo '<td>'.$loc["lat"].'</td>';
					echo '<td>'.$loc["lon"].'</td>';
					//show collapsable list of users
					echo '<td class="text-center">';
					$userListId = 'userList'.$loc["location_id"];
					echo '<button class="btn btn-info px-2 py-0 collapsed userListToggleButton" ';
					echo 'data-toggle="collapse" data-target="#'.$userListId.'" type="button"></button>';
					echo '<ul class="collapse text-left" id="'.$userListId.'">';
					echo '<li>'.$loc["username"].'</li>';
					foreach($loc["neighbors"] as $neighbor){
						echo '<li>'.$neighbor[1].'</li>';
						$accountedFor[] = $neighbor[0];	//add neighbors to accountedFor
					}
					echo '</ul>';
					echo '</td>';
					echo '</tr>';
				}
				echo '</table>';
			}
			dbclose($conn);
			?>
		</div>
	</div>
</body>
