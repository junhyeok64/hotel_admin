<?php
	include "../config/loa_config.php";
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
			echo $type;
		break;
		case "member":
			$out = "";
			$qry = "select * from lostark.bynnark where 1=1 and userip = '".$_SERVER["REMOTE_ADDR"]."'";
			$res = mysqli_query($dbconn, $qry);
			$row = mysqli_fetch_array($res);

			$data = json_decode($row["data"]);
			$out .= "<table>";
			$out .= "<tr>";
			for($i=0; $i<count($data["name"]); $i++) {
				$out .= "<td>".$data["name"][$i]."<td>";
			}
			$out .= "</tr>";
			$out .= "</table>";
		break;
	}
?>