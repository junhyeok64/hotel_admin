<?php
	$page = "index";
	include "./common/top.php";
	if(admin_login != "true") {
		include "login.php";
	} else {
		include "main.php";
	}
	include "./common/bottom.php";
?>
