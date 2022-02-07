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
		case "room_count":
			/*
			sdate edate type 
			*/
			switch($type) {
				case "prev":	//전월
					$sdate = date("Y-m-d", strtotime($sdate." -1 months"));
					$edate = date("Y-m-t", strtotime($sdate));
				break;
				case "next":	//익월
					$sdate = date("Y-m-d", strtotime($sdate." +1 months"));
					$edate = date("Y-m-t", strtotime($sdate));
				break;
				case "month":	//금월
				default:
					$sdate = $sdate;
					$edate = $edate;
				break;
			}

			$_sdate = $sdate;
			$_edate = $edate;
			$room = array();
			$room_qry = "select * from room where state = 'Y' order by num asc";
			$room_res = mysqli_query($dbconn, $room_qry);
			while($room_row = mysqli_fetch_array($room_res)) {
				$room[$room_row["num"]]["name"] = $room_row["name"];
				$room[$room_row["num"]]["img"] = $room_row["img"];
			}
			$data = array();
    		for($sdate; $sdate<=$edate; $sdate = date("Y-m-d", strtotime($sdate." +1 days"))) {
    			$data[$sdate] = array();
    		}
    		$cnt_qry = "select * from reserve_check where 1=1 and date >= '".$_sdate."' and date <= '".$_edate."'";
    		$cnt_qry .= " order by date asc"; //객실타입대로 밀어넣다보니 order by 없으면 prev_date 안먹힘
    		$cnt_res = mysqli_query($dbconn, $cnt_qry);
    		$i=0;
    		while($cnt_row = @mysqli_fetch_array($cnt_res)) {
    			if($prev_date != $cnt_row["date"]) {
    				$i = 0; //배열 뽑기 편하게 일자 바뀌면 0으로 리셋
    			}
    			$data[$cnt_row["date"]][$i]["name"] = $room[$cnt_row["room_type"]]["name"];
    			$data[$cnt_row["date"]][$i]["cnt"] = $cnt_row["cnt"];
    			$data[$cnt_row["date"]][$i]["room_type"] = $cnt_row["room_type"];
    			$i++; $prev_date = $cnt_row["date"];
    		}

			if($show_type == "calender") {
				$out = "<tr>";
        		$sdate = $_sdate; //리필

        		for($sdate; $sdate<=$edate; $sdate = date("Y-m-d", strtotime($sdate." +1 days"))) {

        			$show_room = "";
        			$today_room = count($data[$sdate]);
        			for($t=0; $t<$today_room; $t++) {
        				$show_room .= "<br/>".$data[$sdate][$t]["name"]." - ".$data[$sdate][$t]["cnt"];
        			}

        			$yoile = date("w", strtotime($sdate));
        			switch($yoile) {
        				case "0":
        					$class = "red";
        				break;
        				case "6":
        					$class = "blue";
        				break;
        				default:
        					$class = "";
        				break;
        			}
        			if($_sdate == $sdate) { //첫날 요일잡아주기
        				for($i=0;$i<=$yoile; $i++) {
        					switch($i) {
                				case "0":
                					$class = "red";
                				break;
                				case "6":
                					$class = "blue";
                				break;
                				default:
                					$class = "";
                				break;
                			}
                			if($i!=$yoile) {
        						$out .= "<td class='".$class."'></td>";
        					}
        				}
        				$out .= "<td class='".$class."'><b>".$sdate."</b>".$show_room."</td>";
        			} else {
        				$out .= "<td class='".$class."'><b>".$sdate."</b>".$show_room."</td>";
        			}
        			if($yoile == 6) {
        				$out .= "</tr><tr>";
        			}
        		}
        		
        		$edate_yolie = date("w", strtotime($edate));
        		for($edate_yolie; $edate_yolie<6; $edate_yolie++) {
        			$out .= "<td></td>";
        		}
        		$out .= "</tr>";
        		echo "<script type=\"text/javascript\">";
        		echo "$('#room_calender').show();";
        		echo "$('#room_table').hide();";
        		echo "$('#room_calender_data').html(\"".$out."\");";
        		echo "$(\"input[name='sdate']\").val('".$_sdate."');";
        		echo "$(\"input[name='edate']\").val('".$_edate."');";
        		echo "$(\"input[name='type']\").val('month');";
        		echo "$('.top_title_text').html(\"".date("Y-m", strtotime($_sdate))."\");";
        		echo "</script>";

			} else if ($show_type == "table") {
				$out = "";
				$sdate = $_sdate; //리필

        		for($sdate; $sdate<=$edate; $sdate = date("Y-m-d", strtotime($sdate." +1 days"))) {
        			$yoile = date("w", strtotime($sdate));
        			switch($yoile) {
        				case "0":
        					$class = "red";
        				break;
        				case "6":
        					$class = "blue";
        				break;
        				default:
        					$class = "";
        				break;
        			}

        			$out .= "<tr class='".$class."'>";
        			$show_room = "";
        			$today_room = count($data[$sdate]);
        			$out .= "<td class='center' rowspan='".($today_room+1)."'>".$sdate."</td>";
        			if($today_room == 0) {
        				$out .= "<td colspan='2'></td>";
        			}
        			for($t=0; $t<$today_room; $t++) {
        				//$show_room .= "<br/>".$data[$sdate][$t]["name"]." - ".$data[$sdate][$t]["cnt"];
        				$out .= "<td>";
        				$out .= $data[$sdate][$t]["name"];
        				$out .= "</td>";
        				$out .= "<td>";
        				$out .= $data[$sdate][$t]["cnt"];
        				$out .= "</td>";
        				$out .= "</tr><tr class='".$class."'>";
        			}
        			$out .= "</tr>";
        		}
        		echo "<script type=\"text/javascript\">";
        		echo "$('#room_calender').hide();";
        		echo "$('#room_table').show();";
        		echo "$('#room_table_data').html(\"".$out."\");";
        		echo "$(\"input[name='sdate']\").val('".$_sdate."');";
        		echo "$(\"input[name='edate']\").val('".$_edate."');";
        		echo "$(\"input[name='type']\").val('month');";
        		echo "$('.top_title_text').html(\"".date("Y-m", strtotime($_sdate))."\");";
        		echo "</script>";

			}
		break;
	}
?>