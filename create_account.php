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
	<?php
	require_once "./php/meta.php";
	?>
</head>
<body>
	<?php
	//include header
	include_once "./php/header.php";
	?>
	<div class="container-md mx-auto my-3 d-flex flex-column card bg-light">
		<div class="py-2">
			<h2 class="text-center">Create Account</h2>
		</div>
		<div class="create-account flex-grow-1">
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
				<div class="d-flex flex-row justify-content-around">
					<input type="submit" value="Create Account" id="create-account-submit"
						class="btn btn-primary">
					<a href="./index.php" class="btn btn-secondary">Cancel</a>
				</div>
			</form>
		</div>
	</div>
</body>

</html>
