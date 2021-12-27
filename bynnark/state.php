<?php
	include "../config/loa_config.php";
	/*
이름
레벨
카던
가디언
오레하
아르고스
발탄
비아
발탄하드
비아하드
쿠크
아브렐
	*/
	foreach($_POST as $key => $value) {
		$$key = $value;
	}
	switch($mode) {
		case "index":
			$qry = "select * from lostark.bynnark where 1=1 and userip = '".$_SERVER["REMOTE_ADDR"]."'";
			$res = @mysqli_query($dbconn, $qry);
			$cnt = @mysqli_num_rows($res);
			$type = "new";
			if($cnt > 0) {
				$row = mysqli_fetch_array($res);
				if($data) {
					$data = json_decode($row, true);
					$data_cnt = count($data["이름"]);
					if($data_cnt > 0) {
						$type = "member";
					}
				}
			}
			if($type == "member") {
				echo "<script>new_member();</script>";
			} else {
				echo "<script>member();</script>";
			}
		break;
		case "member":
			$out = "";
			$qry = "select * from lostark.bynnark where 1=1 and userip = '".$_SERVER["REMOTE_ADDR"]."'";
			$res = mysqli_query($dbconn, $qry);
			$row = mysqli_fetch_array($res);

			$data = json_decode($row["data"]);
			$out .= "<table>";
			$out .= "<tr>";
			$prev_level = 0;
			$main_char = "";
			for($i=0; $i<count($data["name"]); $i++) {
				$out .= "<td>".$data["name"][$i]."<td>";
				if($prev_level < $data["level"][$i]) {
					$prev_level = $data["level"][$i];
					$main_char = $data["name"][$i];
				}
			}
			$out .= "</tr>";
			$out .= "</table>";
			$out .= "<script>";
			$out .= "$(\"input[name='char_name']\").val('".$main_char."')";
			$out .= "</script>";
		break;
		case "new":
			$out = "";
			$out .= "";
		break;
	}
?>