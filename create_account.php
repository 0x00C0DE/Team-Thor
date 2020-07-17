<?php
 require_once "./.dblogin.php";

 $success = false;
 $error = false;
 $err = array();
 if($_SERVER["REQUEST_METHOD"] == "POST") {

	  $name = clean_input($_POST["name"]);
	  if(empty($name)) {
		 array_push($err, "nameempty");
	  }

	  $username = clean_input($_POST["username"]);
	  if(empty($username)) {
		  array_push($err, "usernameempty");

	  }

	  $email =clean_input($_POST["email"]);
	  if(empty($email)) {
		  array_push($err, "emailempty");
	  }

	  $bdate = clean_input($_POST["bdate"]);
	  if(empty($bdate)) {
		 array_push($err, "bdateempty");
	  }

	  if(empty(clean_input($_POST["pwrd"]))) {
		 array_push($err, "pwrdempty");

	  }
	  $pwrd = password_hash(clean_input($_POST["pwrd"]), PASSWORD_DEFAULT);

	  if(!(password_verify(clean_input($_POST["conf-pwrd"]), $pwrd))) {
		 array_push($err, "nomatch");
	  }

	  if(!sizeof($err)) {
		  $conn = dbconnect();

		  $stmt = $conn->prepare("INSERT INTO Account (username, email, psswrd, b_date, name) VALUES (?, ?, ?, ?, ?)");
		  $stmt->bind_param("sssss", $username, $email, $pwrd, $bdate, $name);

		  if($stmt->execute()) {
			  $success = true;
		  }


		  $stmt->close();
		  dbclose($conn);


	  }


 }

 if($success) {
	 header('location: login.php');
	 exit;
 }




?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta name="viewport" content="width=device-width">
	<!-- Boostrap css -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
	<!-- JS for Bootstrap-->
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigiin="anonymous"></script>
</head>
<body>
	<div class="content d-flex flex-column min-vh-100">
		<h1 class="container-fluid bg-info text-center py-2 m-0">Create an Account</h1>

		<div class="container-md mx-auto bg-light py-2" id="back-button-div">
			<script>
				function goBack(){
					history.back();
					// If history.back() does nothing, go to homepage
					window.open("./index.php","_self");
				}
			</script>
			<button class="btn btn-secondary" onclick="goBack()">Go Back</button>
		</div>

		<div class="create-account container-md mx-auto bg-light flex-grow-1">
			<form method="post" class="col-md-7 mx-auto pb-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
				<div class="create-account-input form-group">
					<label for="name">Name:</label>
					<input type="text"  class="form-control" name="name" required>
				</div>
				<div class="create-account-input form-group">
					<label for="username">Username:</label><br>
					<input type="text" class="form-control" name="username" required>
				</div>
				<div class="create-account-input form-group" id="create-account-email">
					<label for="email">Email:</label><br>
					<input type="email" class="form-control" name="email" required>
				</div>
				<div class="create-account-input form-group" id="create-account-bday">
					<label for="bdate">Birth Date:</label><br>
					<input type="date" class="form-control" name="bdate" required>
				</div>
				<div class="create-account-input form-group">
					<label for="pwrd">Password:</label><br>
					<input type="password" class="form-control" name="pwrd" required>
				</div>
				<div class="create-account-input form-group">
					<label for="conf-pwrd">Confirm Password:</label><br>
					<input type="password" class="form-control" name="conf-pwrd" required>
				</div>
				<input type="submit" value="Create Account" id="create-account-submit"
					class="btn btn-primary">
			</form>
		</div>
	</div>
</body>

</html>
