<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
	<?php
	session_save_path("./sessionDir");
	session_start();
	require_once "./.dblogin.php";

	require_once "./php/meta.php";
	?>
	<link rel="stylesheet" href="./css/indexStyle.css">
</head>
<body>
	<?php
	//create database connection
	$conn = dbconnect();

	//include header
	require_once './php/header.php';
	?>

	<div class="container-md mb-3">
		<?php
		//read api key from file
		$apikeyfile = fopen("api_secret_key","r") or die("Could not open api key file");
		$apikey = trim(fread($apikeyfile,filesize("api_secret_key")));
		fclose($apikeyfile);

		if(userIsLoggedIn()){	//check if the user is logged in
			$query = "SELECT locations.name AS name, lat, lon, user_lid, is_subscribed FROM locations ";
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

					//get mail icon
					$mailIcon = "./Icons/Mail.png";
					if($location["is_subscribed"]=="YES") $mailIcon = "./Icons/MailFilled.png";

					//create location history link
					$histLink = './weather_history.php?location='.$location["name"];
			
					$temp_units = "C";

					//render weather forecast
					echo '<div class="card shadow bg-light container-fluid my-2 py-2"';
					echo 'data-location="'.$location["name"].'" data-user="';
					echo $location["user_lid"].'">';
					echo '<div class="w-100 row mx-auto">';	//forecast header
					echo '<div class="col-8 text-center"><h3>'.$location["name"].'</h3></div>';	//location name
					echo '<div class="col-4 d-flex flex-row justify-content-end">';
					echo '<a class="btn btn-light" href="'.$histLink.'">';	//view location weather history
					echo '<img class="small" src="./Icons/Clock.png">';
					echo '</a>';
					echo '<form action="./php/toggleSub.php" method="post">';	//toggle email subscription
					echo '<input name="location_name" class="d-none" value="';
					echo $location["name"].'">';
					echo '<button class="btn btn-light" type="submit">';
					echo '<img class="small" src="'.$mailIcon.'"></button></form>';
					echo '<button type="button" class="btn btn-light" data-toggle="modal" data-target="#delete';
					echo $location["name"].'Modal">';	//delete location button
					echo '<img class="small" src="./Icons/Trash.png">';
					echo '</button></div>';
					echo '<div id="delete'.$location["name"].'Modal" class="modal fade"';	//delete location modal
					echo 'tabindex="-1" role="dialog" aria-hidden="true">';
					echo '<div class="modal-dialog"><div class="modal-content">';
					echo '<div class="modal-header"><h4>Confirm Delete</h4>';
					echo '<button type="button" class="close" data-dismiss="modal" ';
					echo 'aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
					echo '<div class="modal-body">Are you sure you want to delete the ';
					echo $location["name"].' forecast?</div>';
					echo '<div class="modal-footer">';
					echo '<form action="./php/deleteLocation.php" method="post">';
					echo '<input name="location_name" class="d-none" value="'.$location["name"].'">';
					echo '<button class="btn btn-danger" type="submit">Delete</button></form>';
					echo '<button class="btn btn-primary" type="button" data-dismiss="modal">Cancel</button>';
					echo '</div></div>';
					echo '</div></div>';
					echo '</div><div class="w-100 d-flex flex-column flex-md-row ';	//forecast body
					echo 'flex-nowrap mx-auto my-2">';
					for($j=0; $j<sizeof($daily_forecast); $j++){
						//parse api results
						$day_forecast = $daily_forecast[$j];
						$weather_id = $day_forecast->{"weather"}[0]->{"id"};
						$weather_icon = getIconFromWeatherCode($weather_id);
						echo '<div class="card flex-fill m-1 overflow-hidden day-forecast-card">';
						//day name
						$day = date("l",$day_forecast->{"dt"}+0);
						echo '<h5 class="text-center">'.$day.'</h5>';
						//weather icon
						$weather_descrip = ucfirst($day_forecast->{"weather"}[0]->{"description"});
						echo '<div class="text-center flex-fill weather-icon-div" title="'.$weather_descrip.'">';
						echo '<img class="rounded img-fluid" src="'.$weather_icon.'"></div>';
						echo '<div class="d-flex flex-row">';
						//maximum temperature
						$temp_max = $day_forecast->{"temp"}->{"max"};	//max temp in kelvin
						$temp_max = round($temp_max);
						echo '<div class="text-center flex-fill temp-min-max" title="Maximum temperature">';
						echo '<img class="img-fluid" src="./Icons/TempHigh.png">';
						echo $temp_max.$temp_units.'</div>';
						//minimum temperature
						$temp_min = $day_forecast->{"temp"}->{"min"};	//min temp in kelvin
						$temp_min = round($temp_min);
						echo '<div class="text-center flex-fill temp-min-max" title="Minimum temperature">';
						echo '<img class="img-fluid" src="./Icons/TempLow.png">';
						echo $temp_min.$temp_units.'</div>';
						echo '</div>';
						//extra forecast details
						echo '<div class="w-100 collapse overflow-auto detailedForecast detailedForecast'.$i.'">';
						//sunrise
						$sunrise = $day_forecast->{"sunrise"};
						$sunrise = new DateTime("@$sunrise");
						echo '<div class="d-flex flex-row overflow-hidden align-items-center" title="Sunrise">';
						echo '<img class="img-fluid" src="./Icons/Sunrise.png">';
						echo '<div class="mx-1 flex-fill text-center">'.$sunrise->format("H:i:s").'</div>';
						echo '</div>';	//todo: time zones
						//sunset
						$sunset = $day_forecast->{"sunset"};
						$sunset = new DateTime("@$sunset");
						echo '<div class="d-flex flex-row overflow-hidden align-items-center" title="Sunset">';
						echo '<img class="img-fluid" src="./Icons/Sunset.png">';
						echo '<div class="mx-1 flex-fill text-center">'.$sunset->format("H:i:s").'</div>';
						echo '</div>';	//todo: time zones
						//morning temperature
						$temp_morn = $day_forecast->{"temp"}->{"morn"};
						$temp_morn = round($temp_morn);
						echo '<div class="d-flex flex-row overflow-hidden align-items-center" ';
						echo 'title="Temperature in the morning">';
						echo '<div class="d-flex flex-row">';
						echo '<img class="img-fluid" src="./Icons/Sunrise.png">';
						echo '<img class="img-fluid" src="./Icons/Temp.png">';
						echo '</div>';
						echo '<div class="mx-1 flex-fill text-center">'.$temp_morn.$temp_units.'</div>';
						echo '</div>';
						//day temperature
						$temp_day = $day_forecast->{"temp"}->{"day"};
						$temp_day = round($temp_day);
						echo '<div class="d-flex flex-row overflow-hidden align-items-center" ';
						echo 'title="Temperature in the day">';
						echo '<div class="d-flex flex-row">';
						echo '<img class="img-fluid" src="./Icons/Sun.png">';
						echo '<img class="img-fluid" src="./Icons/Temp.png">';
						echo '</div>';
						echo '<div class="mx-1 flex-fill text-center">'.$temp_day.$temp_units.'</div>';
						echo '</div>';
						//evening temperature
						$temp_eve = $day_forecast->{"temp"}->{"eve"};
						$temp_eve = round($temp_eve);
						echo '<div class="d-flex flex-row overflow-hidden align-items-center" ';
						echo 'title="Temperature in the evening">';
						echo '<div class="d-flex flex-row">';
						echo '<img class="img-fluid" src="./Icons/Sunset.png">';
						echo '<img class="img-fluid" src="./Icons/Temp.png">';
						echo '</div>';
						echo '<div class="mx-1 flex-fill text-center">'.$temp_eve.$temp_units.'</div>';
						echo '</div>';
						//night temperature
						$temp_night = $day_forecast->{"temp"}->{"night"};
						$temp_night = round($temp_night);
						echo '<div class="d-flex flex-row overflow-hidden align-items-center" ';
						echo 'title="Temperature at night">';
						echo '<div class="d-flex flex-row">';
						echo '<img class="img-fluid" src="./Icons/Night.png">';
						echo '<img class="img-fluid" src="./Icons/Temp.png">';
						echo '</div>';
						echo '<div class="mx-1 flex-fill text-center">'.$temp_night.$temp_units.'</div>';
						echo '</div>';
						//pressure
						$press = $day_forecast->{"pressure"};
						echo '<div class="d-flex flex-row overflow-hidden align-items-center" title="Pressure">';
						echo '<img class="img-fluid half" src="./Icons/Pressure.png">';
						echo '<div class="mx-1 flex-fill text-center">'.$press.'hPa</div>';
						echo '</div>';
						//humidity
						$humid = $day_forecast->{"humidity"};
						echo '<div class="d-flex flex-row overflow-hidden align-items-center" title="Humidity">';
						echo '<img class="img-fluid half" src="./Icons/Humidity.png">';
						echo '<div class="mx-1 flex-fill text-center">'.$humid.'%</div>';
						echo '</div>';
						//dew point
						$dew = round($day_forecast->{"dew_point"});
						echo '<div class="d-flex flex-row overflow-hidden align-items-center" title="Dew point">';
						echo '<img class="img-fluid half" src="./Icons/DewPoint.png">';
						echo '<div class="mx-1 flex-fill text-center">'.$dew.$temp_units.'</div>';
						echo '</div>';
						//wind (speed & direction)
						$wind_speed = round($day_forecast->{"wind_speed"});
						$wind_dir = $day_forecast->{"wind_deg"};
						echo '<div class="d-flex flex-row overflow-hidden align-items-center"';
						echo 'title="Wind speed and direction">';
						echo '<img class="img-fluid third" src="./Icons/Wind.png">';
						echo '<div class="mx-1 flex-fill d-flex flex-column">';
						echo '<div class="text-center">'.$wind_speed.'m/s</div>';
						echo '<div class="text-center">'.$wind_dir.'&deg;</div>';
						echo '</div></div>';
						//cloud cover
						$clouds = $day_forecast->{"clouds"};
						echo '<div class="d-flex flex-row overflow-hidden align-items-center" title="Cloud cover">';
						echo '<img class="img-fluid half" src="./Icons/Cloud.png">';
						echo '<div class="mx-1 flex-fill text-center">'.$clouds.'%</div>';
						echo '</div>';
						//chance of precipitation
						$precip = round($day_forecast->{"pop"} * 100);
						echo '<div class="d-flex flex-row overflow-hidden align-items-center" ';
						echo 'title="Chance of precipitation">';
						echo '<img class="img-fluid half" src="./Icons/Rain.png">';
						echo '<div class="mx-1 flex-fill text-center">'.$precip.'%</div>';
						echo '</div>';
						//uv index
						$uvi = round($day_forecast->{"uvi"});
						echo '<div class="d-flex flex-row overflow-hidden align-items-center" title="UV index">';
						echo '<img class="img-fluid half" src="./Icons/Sun.png">';
						echo '<div class="mx-1 flex-fill text-center">'.$uvi.'</div>';
						echo '</div>';
						if($j < 3){
							//button to view hourly forecast for the day
							echo '<div class="p-1 text-center">';
							echo '<a href="./hourly.php?location='.$location["name"];
							echo '&date='.date("Y-m-d",$day_forecast->{"dt"});
							echo '" class="btn btn-secondary">View Hourly Forecast</a>';
							echo '</div>';
						}
						//close divs
						echo '</div></div>';
					}
					echo '</div>';
					//toggle forecast body extension button
					echo '<button class="btn btn-block w-100 collapsed showDetailsButton" type="button"';
					echo 'data-toggle="collapse" data-target=".detailedForecast'.$i.'" aria-expanded="false"';
					echo 'aria-controls="collapseForecast"></button>';
					echo '</div>';

					curl_close($curl);
					$i += 1;
				}
			}
			else{	//user has no saved locations
				echo '<div class="card shadow my-3 p-3">';
				echo '<h2 class="text-center">You have no saved locations';
				echo '</h2>';
				echo '</div>';
			}
		}
		else{	//user is not logged in
			echo '<div class="card shadow my-3 p-3">';
			echo '<h2 class="text-center">';
			echo 'You are not logged in</h2>';
			echo '<h5 class="text-center">';
			echo 'You must be logged in to view weather forecasts';
			echo '</h5>';
			echo '<div class="d-flex flex-row justify-content-center">';
			echo '<a href="./login.php" class="btn btn-primary">Log In</a>';
			echo '<a href="./create_account.php" class="btn btn-primary">Create Account</a>';
			echo '</div>';
			echo '</div>';
		}
		?>
		<?php
		$sql = "SELECT * FROM `Account`";
		//$result = mysqli_query($conn, $sql);

		/*if(mysqli_num_rows($result) > 0) {
			echo '<h3>Users</h3>';
			while($row = mysqli_fetch_array($result)) {
				echo '<div class="user-listing-preview-item">';
				echo '<h4>'.$row['username'].'</h4>';
				echo '<p>Pwd: '.$row['psswrd'].'</p>';
				echo '<p>Email: '.$row['email'].'</p>';
				echo "</div>";
			}
		}*/
		?>
	</div>
</body>
</html>
