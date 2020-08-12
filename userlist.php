<!DOCTYPE html>
<html lang="en">
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
	$conn = dbconnect();
	require_once "./php/header.php";
	?>
	<div class="container-md card-shadow bg-light my-2 py-2 px-0 overflow-auto">
		<h1 class="ml-2">List of users</h1>
		<div class= "account-info-box">
		<div class="element-list">
			<?php 
			$sql = "SELECT * FROM `Account`";
			$result = mysqli_query($conn, $sql);

			if(mysqli_num_rows($result) > 0) {
				echo '<table class="table table-sm table-bordered mr-2">';
				echo '<tr>';
				echo '<th></th><th></th>';	//empty cells for edit and delete buttons
				echo '<th>Username</th>';
				//echo '<th>Password</th>';
				echo '<th>Admin ID</th>';
				echo '<th>Email</th>';
				echo '<th>Birth Date</th>';
				echo '<th>Name</th>';
				echo '<th>Location ID</th>';
				echo '<th>Max Saveable Locations</th>';
				echo '</tr>';
				while($row = mysqli_fetch_array($result)) {
					echo '<tr>';
					echo '<td><a href="./delete_user.php?lid='.$row["lid"].'">Delete</a></td>';
					echo '<td><a href="./edit_user.php?id='.$row["lid"].'">Edit</a></td>';
					echo '<td>'.$row["username"].'</td>';
					//echo '<td>'.$row["psswrd"].'</td>';
					if($row["aid"] == null) echo '<td>NULL</td>';
					else echo '<td>'.$row["aid"].'</td>';
					echo '<td>'.$row["email"].'</td>';
					echo '<td>'.$row["b_date"].'</td>';
					echo '<td>'.$row["name"].'</td>';
					echo '<td>'.$row["lid"].'</td>';
					echo '<td>'.$row["locMax"].'</td>';
					echo '</tr>';
				}
				echo '</table>';
			}
			dbclose($conn);
			?>
		</div>
	</div>
</body>
</html>
