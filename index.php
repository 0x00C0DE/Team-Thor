<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
	<?php
	if(!session_id()) @session_start();
	require_once "./.dblogin.php";
	?>
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
	  <div class="container-md mx-auto d-flex flex-row justify-content-end">
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
				$result = mysqli_fetch_array($result);

				if(mysqli_num_rows($result) == 1){
					$row = mysqli_fetch_array($result);
					echo $row['name'];
				}
				echo '</a><';
				echo '<a class="btn btn-secondary" href="./logout.php">Log Out';
			}
			echo '</a>'
			?>
		</div>
	</div>

	<div class="content bg-light container-md">
		<div class="user-listing-preview">
			<h3>Job Listings</h3>
			<?php
				$conn = dbconnect();
				$sql = "SELECT * FROM `Account`";
				$result = mysqli_query($conn, $sql);

				if(mysqli_num_rows($result) > 0) {
					while($row = mysqli_fetch_array($result)) {
						echo '<div class="user-listing-preview-item">';
						echo '<h4>' . $row['username'] . '</h4>';
						echo '<p>' . substr($row['psswrd'], 0, min(strlen($row['psswrd']), 30)) . '... </p>';
						echo '<p>Salary: $' .$row['email'] . '. </p>';
						echo '<p>Posted by ' . $row['name'] . ' on ' . date("l F j, Y", strtotime($row['date'])) . '.</p>';
						echo "</div>";
					}
			 	}
			 ?>
		</div>
	</div>
</body>

</html>
