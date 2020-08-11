<div class="container-fluid w-100 bg-white px-0">
	<h1 class="display-3 pt-2 px-2 text-center d-none d-md-block">Thor Weather</h1>
	<div class="navbar navbar-expand-md navbar-light bg-grey">
		<a class="navbar-brand d-inline d-md-none" href="./index.php">Thor Weather</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbar">
			<ul class="navbar-nav mr-auto">
				<?php
				$navbarItems = array(array("Home","./index.php","nav-link"));
				if(!isset($_SESSION["loggedin"])) {	//user is not logged in
					array_push(
						$navbarItems,
						array("Create Account","./create_account.php","nav-link"),
						array("Log In","./login.php","nav-link")
					);
				}
				else if($_SESSION["loggedin"] === true){	//user is logged in
					array_push($navbarItems,array(
						"Add New Location",
						"#",
						"dropdown-toggle",
						array(
							array("Add Location By City Name","#","dropdown-item"),
							array("Add Location By Postal Code","#","dropdown-item"),
							array("Add Location By Coordinates","#","dropdown-item")
						)
					));
					$sql = "SELECT name, aid FROM Account WHERE username='" . $_SESSION['user'] . "'";
					$result = mysqli_query($conn,$sql);
					if(mysqli_num_rows($result) == 1){
						$row = mysqli_fetch_array($result);
						array_push($navbarItems,array("Logged in as ".$row['name'],"./profile.php","nav-link"));
						if($row['aid'] != null){
							array_push($navbarItems,array("User List","./userlist.php","nav-link"));
						}
					}
					array_push($navbarItems,array("Log Out","./logout.php","nav-link"));
					$sqladmin = "SELECT aid FROM Account WHERE username = " . "'" .$_SESSION["user"] . "'";

					$resultadmin = mysqli_query($conn, $sqladmin);
					$rowadmin = mysqli_fetch_array($resultadmin);
					if(($rowadmin['aid']) == 1){
					array_push($navbarItems,array("User list","./userlist.php","nav-link"));
					}
				}
				//add navbar items
				function showNavItem($item){	//adds a navbar item
					//get current page name
					$script = $_SERVER["SCRIPT_NAME"];
					$pagename = "./".substr($script,strrpos($script,"/")+1);
					//show navbar item
					echo '<li class="nav-item';
					if($pagename == $item[1]) echo ' active';
					echo '">';
					echo '<a class="bg-grey rounded '.$item[2].'" href="'.$item[1].'">';
					echo $item[0].'</a></li>';
				}
				foreach($navbarItems as $item){
					if(sizeof($item) == 4){	//create dropdown
						echo '<li class="nav-item dropdown bg-grey rounded">';
						echo '<a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">';
						echo $item[0];
						echo '</a><ul class="dropdown-menu bg-grey">';
						foreach($item[3] as $option) showNavItem($option);
						echo '</ul></li>';
					}
					else if(sizeof($item) == 3) showNavItem($item);
				}
				?>
			<ul>
		</div>
	</div>
</div>
