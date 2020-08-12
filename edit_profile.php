<?php
if(userIsLoggedIn() && $_SERVER["REQUEST_METHOD"] == "POST") {
	require_once "./.dblogin.php";	//Make new session using POST
	$username = $_SESSION["user"];

	$fields = Array("name","email","b_date");	//fields which may be updated by the user
	foreach($fields as $field){
		$value = clean_input($_POST[$field]);	//attempt to fetch field value

		if(empty($value) == false){	//if a value was given, update the field in the db
			$query = "UPDATE Account SET ".$field;
			$query .= "='".$value."' WHERE username='".$username."'";
			//echo $query."<br>";
			mysqli_query($conn,$query);
		}
	}
	
	echo '<script>window.open("./profile.php","_self");</script>';//reload the page to show the changes
}
?>

<h3> Edit Your Profile</h3>
<form method="POST" action="profile.php">
	<div class="input-group mb-2">
		<div class="input-group-prepend">
			<span class="input-group-text">Name</span>
		</div>                                  
		<input type="text" class="form-control" name="name" value="<?php echo $name; ?>">
	</div>
	<div class="input-group mb-2">
		<div class="input-group-prepend">
			<span class="input-group-text">Email</span>
		</div>                                  
		<input type="email" class="form-control" name="email" value="<?php echo $email; ?>">
	</div>
	<div class="input-group mb-2">
		<div class="input-group-prepend">
			<span class="input-group-text">Birth Date</span>
		</div>                                  
		<input type="date" class="form-control" name="b_date" value="<?php echo $b_date; ?>">
	</div>
	<div class="text-center">
		<button class="btn btn-primary" type="submit">Update Info</button>
	</div>
</form>
