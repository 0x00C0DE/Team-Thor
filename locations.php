
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
		<div class="card shadow bg-light container-fluid my-2 py-2">
			WIP
		</div>
	</div>
</body>
