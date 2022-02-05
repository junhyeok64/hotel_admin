<?php
	include "../config/hotel_config.php";
	foreach($_POST as $key=>$value) {
		$$key = $value;
	}
	switch($mode) {
		case "login":
			$qry = "select * from admin_member where id = '".$userid."'";
			$qry .= " and password = md5('".$password."') ";
			$res = mysqli_query($dbconn, $qry);
			$cnt = @mysqli_num_rows($res);
			$out = "<script type=\"text/javascript\">";
			if($cnt > 0) {
				$row = mysqli_fetch_array($res);
				$util->admin_login($row["id"]);

				$out .= "location.reload();";
			} else {
				$out .= "alert('입력된 정보를 확인해주세요');";
			}
			$out .= "</script>";
			echo $out;
		break;
		case "logout":
			$util->admin_logut();
			$out = "<script type=\"text/javascript\">";
			$out .= "location.hef='".base_admin."';";
			$out .= "</script>";
		break;
	}
?>