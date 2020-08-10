<!DOCTYPE html>
<html lang="en">
<head>
	<?php
	session_save_path("./sessionDir");
	session_start();
	require_once "./.dblogin.php";
	require_once "php/meta.php";

	if(userIsLoggedIn()) {
	   $conn = dbconnect();
	   $username = $_SESSION["user"];
	   $email = $b_date = $name = "";
	
	   $sql = "SELECT email, name, b_date FROM Account WHERE username=?";

	   if($stmt = mysqli_prepare($conn, $sql)) {
	      mysqli_stmt_bind_param($stmt, "s", $param_username);
	      $param_username = $username;

	      if(mysqli_stmt_execute($stmt)) {
	         mysqli_stmt_store_result($stmt);

	         if(mysqli_stmt_num_rows($stmt) == 1 ) {
	            mysqli_stmt_bind_result($stmt, $email, $name, $b_date);
	            mysqli_stmt_fetch($stmt);
	         }
	      }
	      mysqli_stmt_close($stmt);
	   }
	}
	else{
	   header("location: login.php");
	   exit;
	}
	?>
</head>
<body>
<?php
	require_once "php/header.php";
?>
	<div class="container-md mb-3">
		<div class="card shadow bg-light container-fluid my-2 py-2">
			<div class="w-100 mb-3">
				<h1 class="text-center">Account Details</h1>
				<script>
					function changeTabs(){
						window.setTimeout(
							()=>{
								let nonActiveTabs = $('.profileTab.collapsed');
								for(i=0; i<nonActiveTabs.length; i++){
									nonActiveTabs[i].classList.remove('active');
								}
								let activeTab = $('.profileTab:not(.collapsed)');
								activeTab[0].classList.add('active');
							},0
						);
					}
				</script>
				<ul class="nav nav-tabs" onclick="changeTabs()">
					<li class="nav-item">
						<a href="#viewProfile" class="nav-link active profileTab"
						data-toggle="collapse" role="button">View Profile</a>
					</li>
					<li class="nav-item">
						<a href="#editProfile" class="nav-link collapsed profileTab"
						data-toggle="collapse" role="button">Edit Profile</a>
					</li>
				</ul>
			</div>
			<div class="accordion" id="accordionDiv">
				<div class="collapse show" id="viewProfile" data-parent="#accordionDiv">
					<h3>Profile</h3>
					<table class="table table-sm border-bottom">
						<tr>
							<td>Name</td><td><?php echo $name?></td>
						</tr>
						<tr>
							<td>Email</td><td><?php echo $email?></td>
						</tr>
						<tr>
							<td>Birthday</td><td><?php echo $b_date?></td>
						</tr>
					</table>
				</div>
				<div class="collapse" id="editProfile" data-parent="#accordionDiv">
				<?php
					require_once "edit_profile.php";
				?>
				</div>
			</div>
		</div>
	</div>
<?php
	dbclose($conn);
?>
</body>
</html>
