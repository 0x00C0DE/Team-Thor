<?php

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["location_name"])){
	if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]){	//check if user is logged in
		$success = false;
		$error = false;
		$err = array();
		//connect to db
		require_once "../.dblogin.php";
		$con = dbconnect();
		if($_SERVER["REQUEST_METHOD"] == "POST"){
			/* cleans the input of name, latitude, longitude when the user enters in invalid input into one of those several fields. If one of the field are empty upon submission,
			all fields will be cleaned of input. The way I envision this working is an XMLHttp request is passed to the api returning a json file
			With Name, lat, and lon recorded. a secondary XMLHttp request then occurs passing that data to the php variables */
			$name = clean_input($_POST["name"]);
			if(empty($name)) {
				array_push($err, "nameempty");
			}
			$lat = clean_input($_POST["lat"]);
			if(empty($lat)) {
				array_push($err, "latempty")
			}
			$lon =clean_input($_POST["lon"]);
			if(empty($lon)) {
				array_push($err, "lonempty");
			}
			$username = mysqli_real_escape_string($con,$_SESSION["user"]);
			$query = SELECT lid FROM Account WHERE username='".$username."')
			$user_lid = mysql_query( $sql, $conn );
			//If all inputs are valid, continue to package the variables into a query to be sent and stored in the database.
			$stmt = $conn->prepare("INSERT INTO locations (name, user_lid, lat, lon) VALUES (?, ?, ?, ?)");
			$stmt->bind_param("ssss", $name, $user_lid, $lat, $lon);
			if($stmt->execute()) {
				$success = true;
			}
			$stmt->close();
			dbclose($conn);
		}
		if($success){
			header('location: index.php');
			exit;
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php
	require_once "./php/meta.php";
	?>
	<script type="text/javascript"> 
		document.addEventListener("DOMContentLoaded",start)
		function start(){
			document.getElementById("latlon-location-submit").addEventListener("click", function(event){
				var apiKey = "f10f1cf3299eadede2bbce70765881e2";
				var lat = document.getElementById('lat').value;
				var lon = document.getElementById('lon').value;
				console.log("userInput: " + zip + " " + country);
				var req = new XMLHttpRequest();
				req.open("GET", "api.openweathermap.org/data/2.5/weather?lat=" + lat + "&lon=" + lon + "&appid=" + apiKey, false);
				req.send(null);	
				var data = JSON.parse(req.responseText);
				name = data.name;
				lat = data.coord.lat;
				lon = data.coord.lon;
				sendPhp(name, lat, lon);
				event.preventDefault();
			});
			document.getElementById("zip-location-submit").addEventListener("click", function(event){
				var apiKey = "f10f1cf3299eadede2bbce70765881e2";
				var zip = document.getElementById('zip').value;
				var country = "us";
				var req = new XMLHttpRequest();
				req.open("GET", "https://api.openweathermap.org/data/2.5/weather?zip=" + zip + "," + country + "&appid=" + apiKey, false);
				req.send(null);	
				var data = JSON.parse(req.responseText);
				name = data.name;
				lat = data.coord.lat;
				lon = data.coord.lon;
				sendPhp(name, lat, lon);
				event.preventDefault();
			});
			document.getElementById("city-location-submit").addEventListener("click", function(event){
				var apiKey = "f10f1cf3299eadede2bbce70765881e2";
				var city = document.getElementById('city').value;
				var req = new XMLHttpRequest();
				req.open("GET", "api.openweathermap.org/data/2.5/weather?q=" + city + "&appid=" + apiKey, false);
				req.send(null);	
				var data = JSON.parse(req.responseText);
				name = data.name;
				lat = data.coord.lat;
				lon = data.coord.lon;
				sendPhp(name, lat, lon);
				event.preventDefault();
			});
		}
		//This function send the values in name, lat, & lon to the variables in the php file. form (html)->javascript->php->database
		function sendPhp(name, lat, lon){
			var req = new XMLHttpRequest();
			var send = document.getElementById('input').value;
			req.open("POST", "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>", false);
			req.setRequestHeader('Content-Type', "application/x-www-form-urlencoded");
			req.send("name=" + name + "&lat=" + lat + "&lon=" + lon);
			var sendData = req.responseText;
			console.log(sendData);
			event.preventDefault();
		}
	</script> 
</head>
<body>
	<?php
	//include header
	include_once "./php/header.php";
	?>
	<div class="container-md mx-auto my-3 d-flex flex-column card bg-light">
		<div class="py-2">
			<h2 class="text-center">Add Location by Longitude & Latitude</h2>
		</div>
		<div class="create-account flex-grow-1">
			<form method="post" class="col-md-7 mx-auto pb-3">
				<div class="create-account-input form-group">
					<label for="lat">Latitude:</label><br>
					<input id="lat" type="text" class="form-control" name="lat" required>
				</div>
				<div class="create-account-input form-group" id="create-account-email">
					<label for="lon">Longitude:</label><br>
					<input id="lon" type="email" class="form-control" name="lon" required>
				</div>
				<div class="d-flex flex-row justify-content-around">
					<input type="submit" value="addLatLon" id="latlon-location-submit"
						class="btn btn-primary">
					<a href="./index.php" class="btn btn-secondary">Cancel</a>
				</div>
			</form>
		</div>
	</div>
		<div class="container-md mx-auto my-3 d-flex flex-column card bg-light">
		<div class="py-2">
			<h2 class="text-center">Add Location by zipcode</h2>
		</div>
		<div class="create-account flex-grow-1">
			<form method="post" class="col-md-7 mx-auto pb-3">
				<div class="create-account-input form-group">
					<label for="zip">Zipcode:</label>
					<input id = "zip" type="text"  class="form-control" name="zip" required>
				</div>
				<div class="d-flex flex-row justify-content-around">
					<input type="submit" value="addZip" id="zip-location-submit"
						class="btn btn-primary">
					<a href="./index.php" class="btn btn-secondary">Cancel</a>
				</div>
			</form>
		</div>
	</div>
	<div class="container-md mx-auto my-3 d-flex flex-column card bg-light">
		<div class="py-2">
			<h2 class="text-center">Add Location by City Name</h2>
		</div>
		<div class="create-account flex-grow-1">
			<form method="post" class="col-md-7 mx-auto pb-3">
				<div class="create-account-input form-group">
					<label for="city">City Name:</label>
					<input id="city" type="text"  class="form-control" name="city" required>
				</div>
				<div class="d-flex flex-row justify-content-around">
					<input type="submit" value="addCity" id="city-location-submit"
						class="btn btn-primary">
					<a href="./index.php" class="btn btn-secondary">Cancel</a>
				</div>
			</form>
		</div>
	</div>
</body>
</html>