<?php
session_start();

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
	header("location: index.php");
	exit;
}

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
	require_once "./.dblogin.php";

	$con = dbconnect();

	// Check if username is empty
	if(empty(trim($_POST["username"]))){
		$username_err = "PLease enter your username";
	} else{
		$username = trim($_POST["username"]);
	}

	// Check if password is empty
	if(empty(trim($_POST["pwrd"]))){
		$password_err = "Please enter your password";
	} else{
		$password = trim($_POST["pwrd"]);
	}

	// Validate credentials
	if(empty($username_err) && empty($password_err)){
		// Prepare a select statement
		$sql = "SELECT username, psswrd FROM Account WHERE username=?";
		if($stmt = mysqli_prepare($con, $sql)) {
			// Bind variables to the prepared statement as parameters
			mysqli_stmt_bind_param($stmt, "s", $param_username);

			// Set parameters
			$param_username = $username;

			// Attempt to execute the prepared statement
			if(mysqli_stmt_execute($stmt)){
				// Store result
				mysqli_stmt_store_result($stmt);

				// Check if username exists, if yes then verify password
				if(mysqli_stmt_num_rows($stmt) == 1){
					// Bind result variables
					mysqli_stmt_bind_result($stmt, $username, $hashed_password);
					if(mysqli_stmt_fetch($stmt)){
						if(password_verify($password, $hashed_password)){
							// Password is correct, so start a new session
							// Store data in session variables
							$_SESSION["loggedin"] = true;
							$_SESSION["user"] = $username;
							// Redirect user to welcome page
							header("location: index.php");
						} else{
							// Display an error message if password is not valid
							$password_err = "Your password is invalid";
						}
					}
				} else{
					// Display an error message if username doesn't exist
					$username_err = "Your username is invalid";
				}
			} else{
				echo "Please try again.";
			}

			// Close statement
			mysqli_stmt_close($stmt);
		}
	}
	dbclose($con);
}
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<!-- Boostrap css -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
	<!-- JS for Bootstrap-->
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigiin="anonymous"></script>
</head>
<body>
	<div class="d-flex flex-column min-vh-100">
		<h1 class="container-fluid bg-info text-center py-2">Log In</h1>

		<div class="container-md mx-auto bg-light flex-grow-1">
			<form class="col-md-7 mx-auto pb-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
				<div class="login-input form-group">
					<label for="username">Username:</label>
					<input type="text" name="username" class="form-control <?php
						if(!empty($username_err)) echo "input-error";
					?>" value="<?php echo $username; ?>" required>
					<?php
					if(!empty($username_err)) {
						echo "<p><span class='input-error'>" . $username_err . "</span></p>";
					}
					?>
				</div>

				<div class="login-input form-group">
					<label for="pwrd">Password:</label>
					<input type="password" name="pwrd" class="form-control <?php
						if(!empty($password_err)) echo "input-error";
					?>" required>
					<?php
					if(!empty($password_err)) {
						echo "<p><span class='input-error'>". $password_err . "</span></p>";
					}
					?>
				</div>
				<input type="submit" id="login-submit" value="Log In" class="btn btn-primary">
			</form>
		</div>
	</div>
</body>

</html>
