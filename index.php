<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
	<?php
	session_save_path("./sessionDir");
	if(!session_id()) @session_start();
	require_once "./.dblogin.php";
	?>
	<meta name="viewport" content="width=device-width">
	<link rel="stylesheet" href="./indexStyle.css">
	<!-- Boostrap css -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
	<!-- JS for Bootstrap-->
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigiin="anonymous"></script>
</head>
<body>
  <div class="container-fluid w-100 bg-info">
	  <h1 class="display-3 pt-2 text-center">Weather App</h1>
	  <div class="container-md mx-auto d-flex flex-row justify-content-center justify-content-md-end pb-2">
		  <?php
		  echo '<a class="btn mr-2 ';
			if(!isset($_SESSION["loggedin"])) {
				echo 'btn-secondary" href="./create_account.php">Create Account</a>';
				echo '<a class="btn btn-secondary" href="./login.php">Log In';
			}
			else if($_SESSION["loggedin"] === true){
				echo 'btn-success" href="./profile.php">';
				$con = dbconnect();
				$sql = "SELECT name FROM Account WHERE username='" . $_SESSION['user'] . "'";
				$result = mysqli_query($con,$sql);

				if(mysqli_num_rows($result) == 1){
					$row = mysqli_fetch_array($result);
					echo $row['name'];
				}
				echo '</a>';
				echo '<a class="btn btn-secondary" href="./logout.php">Log Out';
			}
			echo '</a>'
			?>
		</div>
	</div>

	<div class="container-md mb-3">
		<?php
		//show errors
		ini_set('display_errors',1);
		ini_set('display_startup_errors',1);
		error_reporting(E_ALL);

		//read api key from file
		$apikeyfile = fopen("api_secret_key","r") or die("Could not open api key file");
		$apikey = trim(fread($apikeyfile,filesize("api_secret_key")));
		fclose($apikeyfile);

		//temporary data for testing
		$locations = array(
			array("locName" => "Corvallis","lat" => "44.578056","lon" => "-123.275278"),
			array("locName" => "New York","lat" => "40.714270","lon" => "-74.005970")
		);

		for($i=0; $i<sizeof($locations); $i++){
			//generate url for request
			$apiurl = "https://api.openweathermap.org/data/2.5/onecall";
			$apiurl .= "?lat=" . urlencode($locations[$i]["lat"]);
			$apiurl .= "&lon=" . urlencode($locations[$i]["lon"]);
			$apiurl .= "&apikey=" . urlencode($apikey);
			$apiurl .= "&exclude=current,minutely,hourly";

			//get weather
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
			echo '<div class="col-8 text-center"><h3 class="">'.$locations[$i]["locName"].'</h3></div>';	//location name
			echo '<div class="col-4 text-right"><button class="btn" class="button"></button>';
			echo '<button class="btn" type="button"></button></div>';
			echo '</div>';
			echo '<div class="w-100 d-flex flex-column flex-md-row flex-nowrap mx-auto my-2">';	//forecast body
			for($j=0; $j<sizeof($daily_forecast); $j++){
				//parse api results
				$day_forecast = $daily_forecast[$j];
				$day = date("l",$day_forecast->{"dt"}+0);
				$weather_id = $day_forecast->{"weather"}[0]->{"id"};
				if($weather_id < 300) $weather_icon = "";	//thunderstorms
				else if($weather_id < 600) $weather_icon = "./Rain.png";	//drizzle & rain
				else if($weather_id < 700) $weather_icon = "./Snow.png";	//snow
				else if($weather_id < 800) $weather_icon = "";	//atmospheric conditions
				else if($weather_id < 801) $weather_icon = "./Sun.png";	//clear
				else $weather_icon = "./Cloud.png";	//cloudy
				$temp_min = $day_forecast->{"temp"}->{"min"};	//min temp in kelvin
				$temp_min = round($temp_min - 272.15);
				$temp_max = $day_forecast->{"temp"}->{"max"};	//max temp in kelvin
				$temp_max = round($temp_max - 272.15);
				echo '<div class="card flex-fill mx-1 overflow-hidden day-forecast-card">';
				echo '<h5 class="text-center">'.$day.'</h5>';	//day name
				echo '<div class="text-center flex-fill weather-icon-div"><img class="rounded img-fluid" src="'.$weather_icon.'"></div>';	//weather icon
				echo '<div class="d-flex flex-row">';
				echo '<div class="text-center flex-fill temp-max">'.$temp_max.'C</div>'; //maximum temperature
				echo '<div class="text-center flex-fill temp-min">'.$temp_min.'C</div>'; //minimum temperature
				echo '</div></div>';
			}
			echo '</div>';
			echo '<div class="collapse" id="detailedForecast'.$i.'">';	//forecast body extension
			echo 'Extended forecast';
			echo '</div>';
			echo '<button class="btn btn-block w-100" type="button" data-toggle="collapse"';	//toggle forecast body extension button
			echo 'data-target="#detailedForecast'.$i.'" aria-expanded="false"';
			echo 'aria-controls="collapseForecast">&#x25bc;</button>';
			echo '</div>';

			curl_close($curl);
		}
		?>
	</div>
</body>
</html>
