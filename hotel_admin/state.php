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
			$util->admin_logout();
			$out = "<script type=\"text/javascript\">";
			$out .= "location.href='".base_admin."';";
			$out .= "</script>";
			echo $out;
		break;
		case "todo_add":
			$text = addslashes($text);
			$in_qry = "insert into todo (`text`, `check`, `state`, `wdate`) values ";
			$in_qry .= "('".$text."', 'N', 'Y', now())";
			$in_res = mysqli_query($dbconn, $in_qry);
			if($in_res) {
				$in_num = @mysqli_insert_id($dbconn);
				echo "SUCC||".$in_num;
			} else {
				echo "FAIL||";
			}
		break;
		case "todo_change":
			$qry = "";
			switch($type) {
				case "check":
					$checked = ($val == "true") ? "Y" : "N";
					$qry = "update todo set `check` = '".$checked."' where num = '".$num."'";
				break;
				case "state":
					$qry = "update todo set `state` = 'N' where num = '".$num."'";
				break;
			}
			
			if($qry != "") {
				echo $qry;
				$res = mysqli_query($dbconn, $qry);
			}
		break;
		case "todo_paging":
			if($type != "") {
				$page = ($type == "next") ? $page+1 : $page-1;
				$page = ($page < 0) ? 1 : $page;
			}
			$start = ($page-1)*5;
			$limit = 5;
			$todo_qry = "select * from todo where state='Y' order by `check`='N' desc ,num desc";
			$todo_cres = mysqli_query($dbconn, $todo_qry);
			$todo_cnt = @mysqli_num_rows($todo_cres);
			$todo_qry .= " limit ".$start.",".$limit."";
			$todo_res = mysqli_query($dbconn, $todo_qry);
			$out = "";
			while($todo_row = @mysqli_fetch_array($todo_res)) {
				$checked = ($todo_row["check"] == "N") ? "" : " checked";
				$complete = ($todo_row["check"] == "N") ? "" : "completed";
				$out .= "<li class=\"".$complete."\">";
				$out .= "<div class=\"form-check form-check-primary\">";
				$out .= "<label class=\"form-check-label\">";
				$out .= "<input name='num[]' type='checkbox' class='checkbox' value=\"".$todo_row["num"]."\" onchange=\"admin.todo_change('".$todo_row["num"]."', 'check', this.checked)\"".$checked."/>";
				$out .= $todo_row["text"];
				$out .= "<i class='input-helper'></i>";
				$out .= "</label>";
				$out .= "</div>";
				$out .= "<i class=\"remove mdi mdi-close-box\" onclick=\"admin.todo_change('".$todo_row["num"]."','state')\"></i>";
				$out .= "</li>";
			}
			$out .= "<script type=\"text/javascript\">";
			$out .= "$(\"input[name='todo_page']\").val('".($page)."');";
			$out .= "$(\"input[name='todo_end']\").val('".@ceil($todo_cnt/$limit)."');";
			$out .= "</script>";
			echo $out;
		break;
	}
?>