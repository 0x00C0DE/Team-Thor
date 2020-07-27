<?php
session_save_path("./sessionDir");
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
	<?php
	require_once "./php/meta.php";
	?>
</head>
<body>
	<?php
	include "./php/header.php";
	?>
	<div class="container-md mx-auto bg-light card shadow my-3">
		<div class="py-2">
			<h2 class="text-center">Log In</h2>
		</div>
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
			<div class="d-flex flex-row justify-content-around">
				<input type="submit" id="login-submit" value="Log In" class="btn btn-primary">
				<a href="./index.php" class="btn btn-secondary">Cancel</a>
			</div>
		</form>
	</div>
</body>

</html>
