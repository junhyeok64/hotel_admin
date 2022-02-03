<?php
	include "./common/top.php";
	if($alogin != true) {
		include "login.php";
	} else {
		include "main.php";
	}
	include "./common/bottom.php";
?>
