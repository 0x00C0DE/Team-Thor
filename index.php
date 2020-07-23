<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
	<?php
	session_save_path("./sessionDir");
	session_start();
	require_once "./.dblogin.php";
	?>
	<meta name="viewport" content="width=device-width">
	<link rel="stylesheet" href="./indexStyle.css">
	<!-- jQuery & Popper.js -->
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<!-- Boostrap css -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
	<!-- JS for Bootstrap-->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>
<body>
  <div class="container-fluid w-100 bg-info">
	  <h1 class="display-3 pt-2 text-center">Weather App</h1>
	  <div class="container-md mx-auto d-flex flex-row justify-content-center justify-content-md-end pb-2">
			<?php
			//show errors
			ini_set('display_errors',1);
			ini_set('display_startup_errors',1);
			error_reporting(E_ALL);

			//create database connection
			$conn = dbconnect();

			echo '<a class="btn mr-2 ';
			if(!isset($_SESSION["loggedin"])) {	//user is not logged in
				echo 'btn-secondary" href="./create_account.php">Create Account</a>';
				echo '<a class="btn btn-secondary" href="./login.php">Log In';
				echo '</a>';
			}
			else if($_SESSION["loggedin"] === true){	//user is logged in
				echo 'btn-success" href="./profile.php">';
				$sql = "SELECT name FROM Account WHERE username='" . $_SESSION['user'] . "'";
				$result = mysqli_query($conn,$sql);

				if(mysqli_num_rows($result) == 1){
					$row = mysqli_fetch_array($result);
					echo $row['name'];
				}
				echo '</a>';
				echo '<a class="btn btn-secondary" href="./logout.php">Log Out';
				echo '</a>';
			}
			?>
		</div>
	</div>

	<div class="container-md mb-3">
		<?php
		//read api key from file
		$apikeyfile = fopen("api_secret_key","r") or die("Could not open api key file");
		$apikey = trim(fread($apikeyfile,filesize("api_secret_key")));
		fclose($apikeyfile);

		if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] && isset($_SESSION["user"])){	//check if the user is logged in
			$query = "SELECT locations.name AS name, lat, lon FROM locations ";
			$query .= "LEFT JOIN Account ON locations.user_lid=Account.lid";
			$query .= " WHERE Account.username='".$_SESSION["user"]."'";
			$result = mysqli_query($conn, $query);

			if(mysqli_num_rows($result) > 0){
				$i = 0;	//index variable
				while($location = mysqli_fetch_array($result)){
					//generate url for request
					$apiurl = "https://api.openweathermap.org/data/2.5/onecall";
					$apiurl .= "?lat=" . urlencode($location["lat"]);
					$apiurl .= "&lon=" . urlencode($location["lon"]);
					$apiurl .= "&apikey=" . urlencode($apikey);
					$apiurl .= "&exclude=current,minutely,hourly";
					$apiurl .= "&units=metric";

					//get weather forecast from api
					$curl = curl_init();
					curl_setopt_array($curl, [
						CURLOPT_URL => $apiurl,
						CURLOPT_RETURNTRANSFER => 1
					]);
					$forecast = json_decode(curl_exec($curl));
					$daily_forecast = $forecast->{"daily"};
			
					//render weather forecast
					echo '<div class="card shadow bg-light container-fluid my-2 py-2">';
					echo '<div class="w-100 row mx-auto">';	//forecast header
					echo '<div class="col-8 text-center"><h3 class="">'.$location["name"].'</h3></div>';	//location name
					echo '<div class="col-4 text-right"><button class="btn" class="button"></button>';
					echo '<button class="btn" type="button"></button></div>';
					echo '</div>';
					echo '<div class="w-100 d-flex flex-column flex-md-row flex-nowrap mx-auto my-2">';	//forecast body
					for($j=0; $j<sizeof($daily_forecast); $j++){
						//parse api results
						$day_forecast = $daily_forecast[$j];
						$day = date("l",$day_forecast->{"dt"}+0);
						$weather_id = $day_forecast->{"weather"}[0]->{"id"};
						if($weather_id < 300) $weather_icon = "./Icons/Thunderstorm.png";	//thunderstorms
						else if($weather_id < 600) $weather_icon = "./Icons/Rain.png";	//drizzle & rain
						else if($weather_id < 700) $weather_icon = "./Icons/Snow.png";	//snow
						else if($weather_id < 800) $weather_icon = "";	//atmospheric conditions
						else if($weather_id < 801) $weather_icon = "./Icons/Sun.png";	//clear
						else $weather_icon = "./Icons/Cloud.png";	//cloudy
						echo '<div class="card flex-fill m-1 overflow-hidden day-forecast-card">';
						//day name
						echo '<h5 class="text-center">'.$day.'</h5>';
						//weather icon
						echo '<div class="text-center flex-fill weather-icon-div"><img class="rounded img-fluid" src="'.$weather_icon.'"></div>';
						echo '<div class="d-flex flex-row">';
						//maximum temperature
						$temp_max = $day_forecast->{"temp"}->{"max"};	//max temp in kelvin
						$temp_max = round($temp_max);
						echo '<div class="text-center flex-fill temp-max">'.$temp_max.'C</div>';
						//minimum temperature
						$temp_min = $day_forecast->{"temp"}->{"min"};	//min temp in kelvin
						$temp_min = round($temp_min);
						echo '<div class="text-center flex-fill temp-min">'.$temp_min.'C</div>';
						echo '</div>';
						//extra forecast details
						echo '<div class="w-100 collapse overflow-auto detailedForecast'.$i.'">';
						//sunrise
						$sunrise = $day_forecast->{"sunrise"};
						$sunrise = new DateTime("@$sunrise");
						echo '<div class="text-nowrap">Sunrise:'.$sunrise->format("H:i:s");
						echo '</div>';	//todo: time zones
						//sunset
						$sunset = $day_forecast->{"sunset"};
						$sunset = new DateTime("@$sunset");
						echo '<div class="text-nowrap">Sunset:'.$sunset->format("H:i:s");
						echo '</div>';
						//morning temperature
						$temp_morn = $day_forecast->{"temp"}->{"morn"};
						$temp_morn = round($temp_morn);
						echo '<div class="text-nowrap">Morning:'.$temp_morn;
						echo 'C</div>';
						//day temperature
						$temp_day = $day_forecast->{"temp"}->{"day"};
						$temp_day = round($temp_day);
						echo '<div class="text-nowrap">Day:'.$temp_day;
						echo 'C</div>';
						//evening temperature
						$temp_eve = $day_forecast->{"temp"}->{"eve"};
						$temp_eve = round($temp_eve);
						echo '<div class="text-nowrap">Evening:'.$temp_eve;
						echo 'C</div>';
						//night temperature
						$temp_night = $day_forecast->{"temp"}->{"night"};
						$temp_night = round($temp_night);
						echo '<div class="text-nowrap">Night:'.$temp_night;
						echo 'C</div>';
						//pressure
						$press = $day_forecast->{"pressure"};
						echo '<div class="text-nowrap">Pressure:'.$press;
						echo 'hPa</div>';
						//humidity
						$humid = $day_forecast->{"humidity"};
						echo '<div class="text-nowrap">Humidity:'.$humid;
						echo '%</div>';
						//dew point
						$dew = round($day_forecast->{"dew_point"});
						echo '<div class="text-nowrap">Dew Point:'.$dew;
						echo 'C</div>';
						//wind (speed & direction)
						$wind_speed = round($day_forecast->{"wind_speed"});
						$wind_dir = $day_forecast->{"wind_deg"};
						echo '<div class="text-nowrap">Wind:'.$wind_speed;
						echo 'm/s @ '.$wind_dir.'&deg;</div>';
						//cloud cover
						$clouds = $day_forecast->{"clouds"};
						echo '<div class="text-nowrap">Cloud Cover:'.$clouds;
						echo '%</div>';
						//chance of precipitation
						$precip = $day_forecast->{"pop"};
						echo '<div class="text-nowrap">Chance of Precipitation:'.$precip;
						echo '%</div>';
						//uv index
						$uvi = round($day_forecast->{"uvi"});
						echo '<div class="text-nowrap">Midday UV Index:'.$uvi;
						echo '</div>';
						echo '</div></div>';
					}
					echo '</div>';
					echo '<button class="btn btn-block w-100 collapsed" type="button" data-toggle="collapse"';	//toggle forecast body extension button
					echo 'data-target=".detailedForecast'.$i.'" aria-expanded="false"';
					echo 'aria-controls="collapseForecast"></button>';
					echo '</div>';

					curl_close($curl);
					$i += 1;
				}
			}
		}
		else{	//user is not logged in
			echo '<div class="card shadow my-3 p-3">';
			echo '<h2 class="text-center">';
			echo 'You are not logged in</h2>';
			echo '<h5 class="text-center">';
			echo 'You must be logged in to log in to view weather forecasts';
			echo '</h5>';
			echo '</div>';
		}
		?>
		<h3>Users</h3>
		<?php
		$sql = "SELECT * FROM `Account`";
		$result = mysqli_query($conn, $sql);

		if(mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_array($result)) {
				echo '<div class="user-listing-preview-item">';
				echo '<h4>' . $row['username'] . '</h4>';
				echo '<p>' . substr($row['psswrd'], 0, min(strlen($row['psswrd']), 30)) . '... </p>';
				echo '<p>Salary: $' .$row['email'] . '. </p>';
				echo '<p>Posted by ' . $row['name'] . ' on ' . date("l F j, Y", strtotime($row['b_date'])) . '.</p>';
				echo "</div>";
			}
		}
		?>
	</div>
</body>
</html>
