<meta name="viewport" content="width=device-width">
<link rel="stylesheet" href="./css/theme.css">
<!-- jQuery & Popper.js -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<!-- Boostrap css -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
<!-- JS for Bootstrap-->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<?php
//show errors
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

function userIsLoggedIn(){
	return (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] && isset($_SESSION["user"]));
}

function getIconFromWeatherCode($code){
	if($code < 300) return "./Icons/Thunderstorm.png";	//thunderstorms
	else if($code < 600) return "./Icons/Rain.png";	//drizzle & rain
	else if($code < 700) return "./Icons/Snow.png";	//snow
	else if($code < 800) return "";	//atmospheric conditions
	else if($code < 801) return "./Icons/Sun.png";	//clear
	else return "./Icons/Cloud.png";	//cloudy
}

function showGraph(
	$data,
	$units,
	$showMinMax=False,
	$graphHeight=200,
	$showMessage=True
){
	//find minimum and maximum value
	$min = 100000;
	$max = -100000;
	foreach($data as $dataPoint){
		$min = min($min,$dataPoint["value"]);
		$max = max($max,$dataPoint["value"]);
	}
	$scalingFactor = $graphHeight/($max - $min);

	//render minimum and maximum table
	if($showMinMax){
		echo '<table class="table table-sm border-bottom w-auto mx-auto" style="min-width:50%">';
		echo '<tr><td>Maximum</td><td>'.$max.$units.'</td></tr>';
		echo '<tr><td>Minimum</td><td>'.$min.$units.'</td></tr>';
		echo '</table>';
	}

	//render graph
	echo '<div class="w-auto mx-auto d-flex flex-row overflow-hidden">';
	echo '<div class="d-flex flex-column justify-content-between mr-1">';
	echo '<span class="text-dark">'.$max.$units.'</span>';
	echo '<span class="text-body">'.$min.$units.'</span>';
	echo '</div>';
	echo '<div class="flex-grow-1 d-flex flex-row py-2 overflow-auto align-items-end graphDiv" ';
	echo 'style="-ms-overflow-style:none;scrollbar-width:none;">';
	$i = 0;
	foreach($data as $dataPoint){
		$background = 'bg-info';
		if($i % 24 == 0) $background = 'bg-dark';
		echo '<div class="'.$background.' p-1 rounded" ';
		echo 'data-toggle="tooltip" data-html="true" data-placement="bottom" title="';
		echo $dataPoint["title"].'" ';
		echo 'style="margin-right:2px;height:';
		echo strval(($dataPoint["value"]-$min)*$scalingFactor).'">';
		echo '</div>';
		$i++;
	}
	echo '</div>';
	echo '</div>';

	//show message
	if($showMessage){
		echo '<div class="text-center text-muted">Some parts of the graph may be cut off, scroll ';
		echo 'right to see them</div>';
	}
}
?>
