<?php
if(isset($_SESSION["loggedin"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
	require_once "./.dblogin.php";	//Make new session using POST
	$conn = dbconnect();
	$username = $_SESSION["user"];

	$fields = Array("name","email","b_date");	//fields which may be updated by the user
	foreach($fields as $field){
		$value = clean_input($_POST[$field]);	//attempt to fetch field value
		if(empty($value) == false){	//if a value was given, update the field in the db
			$stmt = $conn->prepare("UPDATE Account SET ".$field."=? WHERE username=?");
			$stmt->bind_param("ss", $value, $username);
			$stmt->execute();
			$stmt->close();
		}
	}
	dbclose($conn);	//closes connection to database

	header('location: profile.php');
	exit;
}
?>

<h2> Edit your Profile</h2>
<form method="POST" action="edit_profile.php">
	<div class="profile-page-update-input">
		<label>Change Name: </label>                                  
		<input type="text" name="name" value="<?php echo $name; ?>">
	</div>
	<div class="profile-page-update-input">
		<label> Change Email: </label>
		<input type="email" name="email" value="<?php echo $email; ?>">
	</div>
	<div class="profile-page-update-input">
		<label>Change Birth Date: </label>
		<input type="date" name="b_date" value="<?php echo $b_date; ?>">
	</div>
	<input type="submit" name="submit" value="Update Info" id="update-info-submit">
</form>
