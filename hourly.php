<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
	<?php
	session_save_path("./sessionDir");
	session_start();
	require_once "./.dblogin.php";
	require_once "./php/meta.php";
	?>
</head>
<body>
	<?php
	//create database connection
	$conn = dbconnect();

	//include header
	require_once './php/header.php';

	//function to show errors
	function showError($title,$message){
		echo '<h2 class="text-body text-center">'.$title.'</h2>';
		echo '<h4 class="text-danger text-center">'.$message.'<h4>';
		echo '<div class="w-100 text-center">';
		echo '<a class="btn btn-primary" href="./index.php">Go Back</a>';
		echo '</div>';
	}
	?>

	<div class="container-md mb-3">
		<div class="card shadow bg-light container-fluid my-2 py-2">
			<?php
			if(isset($_GET["location"]) && isset($_GET["date"])){
				if(!userIsLoggedIn()){
					showError('Not Logged In','You must be logged in to view the hourly forecast');
					die();
				}

				//send query for location data
				$query = "SELECT locations.name as name,lat,lon FROM locations ";
				$query .= "JOIN Account ON Account.lid=locations.user_lid ";
				$query .= "WHERE locations.name='".$_GET["location"]."' ";
				$query .= "AND Account.username='".$_SESSION["user"]."'";
				$result = mysqli_query($conn,$query);
				if(!$result || mysqli_num_rows($result)!=1){
					showError('Could Not Find Location','No location with a matching name could be found');
					echo $_GET["location"];
					die();
				}
				$locationData = mysqli_fetch_array($result);

				//read api key
				$apiKeyFile = fopen("api_secret_key","r");
				if(!$apiKeyFile){
					showError("Failed To Read API Key","Could not open the api key file");
					die();
				}
				$apiKey = trim(fread($apiKeyFile,filesize("api_secret_key")));
				fclose($apiKeyFile);

				//fetch forecast data
				$apiUrl = "https://api.openweathermap.org/data/2.5/onecall";
				$apiUrl .= "?lat=".urlencode($locationData["lat"]);
				$apiUrl .= "&lon=".urlencode($locationData["lon"]);
				$apiUrl .= "&apikey=".urlencode($apiKey);
				$apiUrl .= "&exclude=current,minutely,daily";
				$apiUrl .= "&units=metric";

				//get weather forecast from api
				$curl = curl_init();
				curl_setopt_array($curl, [
					CURLOPT_URL => $apiUrl,
					CURLOPT_RETURNTRANSFER => 1
				]);
				$forecast = json_decode(curl_exec($curl));
				
				//filter only hourly data for the requested date
				function inCorrectDate($var){
					$varDate = date('Y-m-d',$var->{"dt"});
					return strcmp($varDate,$_GET["date"])==0;
				}
				$hourlyData = $forecast->{"hourly"};
				$hourlyData = array_filter($hourlyData,"inCorrectDate");

				//check that data was returned for the specified date
				if(sizeof($hourlyData) == 0){
					showError('Date Out Of Range','There are no data for the selected day');
					die();
				}

				//render hourly forecast
				$lowestIndex = array_keys($hourlyData)[0];
				echo '<h1 class="text-center">'.date("l",$hourlyData[$lowestIndex]->{"dt"}).'</h1>';	//title
				echo '<div class="overflow-auto">';
				echo '<table class="table table-sm">';
				echo '<tr>';
				echo '<th>Time</th>';
				echo '<th>Weather</th>';
				echo '<th>Temperature</th>';
				echo '<th>Feels Like</th>';
				echo '<th>Humidity</th>';
				echo '<th>Pressure</th>';
				echo '<th>Dew Point</th>';
				echo '<th>Cloud Cover</th>';
				echo '<th>Wind Speed</th>';
				echo '<th>Chance of Precipitation</th>';
				echo '<th>Visibility</th>';
				echo '</tr>';
				foreach($hourlyData as $hour){
					//echo 'Time:'.$hour->{"dt"}.'<br>';
					echo '<tr class="text-center">';
					echo '<td>'.date("H",$hour->{"dt"}).':00</td>';	//time
					$weatherIcon = getIconFromWeatherCode($hour->{"weather"}[0]->{"id"});
					echo '<td title="'.$hour->{"weather"}[0]->{"description"}.'"><img class="" src="';	//weather
					echo $weatherIcon.'" style="width:25px;height:25px"></td>';
					echo '<td title="'.$hour->{"temp"}.'C">'.round($hour->{"temp"}).'C</td>';	//temperature
					echo '<td title="'.$hour->{"feels_like"}.'C">'.round($hour->{"feels_like"}).'C</td>';	//feels
					echo '<td>'.$hour->{"humidity"}.'%</td>';	//humidity
					echo '<td>'.$hour->{"pressure"}.'hPa</td>';	//pressure
					echo '<td title="'.$hour->{"dew_point"}.'C">'.round($hour->{"dew_point"}).'C</td>';	//dew point
					echo '<td>'.$hour->{"clouds"}.'%</td>';	//cloud cover
					echo '<td title="'.$hour->{"wind_speed"}.'m/s">';	//wind speed
					echo round($hour->{"wind_speed"}).'m/s</td>';
					echo '<td>'.$hour->{"pop"}.'%</td>';	//chance of precip
					echo '<td title="'.$hour->{"visibility"}.'m">';	//visibility
					echo round($hour->{"visibility"}/1000).'km</td>';
					echo '</tr>';
				}
				echo '</table></div>';
			}
			else{
				showError('Missing Data','Some or all data was not properly passed to the page');
			}
			?>
		</div>
	</div>
</body>
</html>
