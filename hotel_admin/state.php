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
		case "todo_change":	//todolist 상태값 변경
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
		case "todo_paging":	//todolist paging
			if($type != "") {
				$page = ($type == "next") ? $page+1 : $page-1;
				$page = ($page < 0) ? 1 : $page;
			}
			$start = ($page-1)*5;
			$limit = 5;
			$todo_qry = "select * from todo where state='Y' order by `check`='N' desc ,num desc";	//complete한건 뒤로 깔리게, 삭제한건 노출 안되게
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

			$_sdate = $sdate;	//초기화용 함수 따로 빼주기
			$_edate = $edate;
			$room = array();		//객실 정보 미리뽑기
			$room_num = array();	//배열 매칭용 객실번호
			$room_qry = "select * from room where state = 'Y' order by num asc";	//객실정보 미리 불러다 쿼리 최소한 사용하게
			$room_res = mysqli_query($dbconn, $room_qry);
			while($room_row = mysqli_fetch_array($room_res)) {
				$room[$room_row["num"]]["name"] = $room_row["name"];
				$room[$room_row["num"]]["img"] = $room_row["img"];
				$room_num[] = $room_row["num"];
			}
			$data = array();
    		for($sdate; $sdate<=$edate; $sdate = date("Y-m-d", strtotime($sdate." +1 days"))) { //reserve_check에 비어있는날 있을수있으니 초기화
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
    			$data[$cnt_row["date"]][$i]["price"] = $cnt_row["price"];
    			$data[$cnt_row["date"]][$i]["room_type"] = $cnt_row["room_type"];
    			$i++; $prev_date = $cnt_row["date"];
    		}

			if($show_type == "calender") {	//달력형태로 출력
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
                			if($i!=$yoile) { //첫날이 토욜이면 class안들어가 예외처리
        						$out .= "<td class='".$class."'></td>";
        					}
        				}
        				$out .= "<td class='".$class."' onclick='admin.room_count_detail(\\\"".$sdate."\\\");'><b>".$sdate."</b>".$show_room."</td>";
        			} else {
        				$out .= "<td class='".$class."' onclick='admin.room_count_detail(\\\"".$sdate."\\\");'><b>".$sdate."</b>".$show_room."</td>";
        			}
        			if($yoile == 6) {
        				$out .= "</tr><tr>";
        			}
        		}
        		
        		$edate_yolie = date("w", strtotime($edate));
        		for($edate_yolie; $edate_yolie<6; $edate_yolie++) { //끝날 이후도 틀 잡아주기
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

			} else if ($show_type == "table") {	//테이블 형태로 출력
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
        			//$today_room = count($data[$sdate]);	//객실수량 조절을 위해 전체객실로 변경
        			$today_room = count($room);
        			$out .= "<td class='center' rowspan='".($today_room+1)."'>".$sdate."</td>";
        			if($today_room == 0) {
        				$out .= "<td colspan='2'></td>";
        			}
        			for($t=0; $t<$today_room; $t++) {
        				$out .= "<td>";
        				$out .= $room[$room_num[$t]]["name"];
        				$out .= "</td>";
        				$out .= "<td>";
        				$out .= "<input name='rcnt_price_".$sdate."_".$room_num[$t]."' class='rcnt_price form-control' value='".$data[$sdate][$t]["price"]."' numberOnly >";
        				$out .= "</td>";
        				$out .= "<td>";
        				$out .= "<input name='rcnt_cnt_".$sdate."_".$room_num[$t]."' class='rcnt_cnt form-control max-width50' value='".$data[$sdate][$t]["cnt"]."' numberOnly >";
        				$out .= "</td>";
        				$out .= "<td class='center'>";
						$out .= "<i class='mdi mdi-arrow-up-drop-circle' onclick=\\\"admin.room_count_detail_change(\'".$sdate."\', \'".$room[$room_num[$t]]["room_type"]."\', \'up\')\\\"></i>";
						$out .= "<i class='mdi mdi-arrow-down-drop-circle' onclick=\\\"admin.room_count_detail_change(\'".$date."\', \'".$room[$room_num[$t]]["room_type"]."\', \'down\')\\\"></i>";
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
        		echo "$('#rcnt_detail').html('')";//켈린더폼 팝업 초기화
        		echo "
				$(\"input:text[numberOnly]\").on(\"keyup\", function() {
				  $(this).val($(this).val().replace(/[^0-9]/g,\"\"));
				});
				$(\".rcnt_price\").keyup(function(e){
					if(e.keyCode == 13) {
						var name = $(this).attr(\"name\");
						var name_arr = name.split(\"_\");
						admin.room_count_detail_change(name_arr[2], name_arr[3], 'price');
					}
				})";
				echo "
				$(\".rcnt_cnt\").keyup(function(e){
					if(e.keyCode == 13) {
						var name = $(this).attr(\"name\");
						var name_arr = name.split(\"_\");
						admin.room_count_detail_change(name_arr[2], name_arr[3], 'cnt');
					}
				})";
        		echo "</script>";

			}
		break;
		case "room_count_detail":	//객실수량 div
			$room = array();		//객실 정보 미리뽑기
			$room_num = array();	//배열 매칭용 객실번호
			$room_qry = "select * from room where 1=1 and state = 'Y'";
			$room_res = mysqli_query($dbconn, $room_qry);
			while($room_row = mysqli_fetch_array($room_res)) {
				$room[$room_row["num"]]["name"] = $room_row["name"];
				$room[$room_row["num"]]["room_type"] = $room_row["num"];
				$room[$room_row["num"]]["cnt"] = 0;
				$room[$room_row["num"]]["price"] = 0;
				$room_num[] = $room_row["num"];
			}
			$reserve_qry = "select * from reserve_check where 1=1 and ";
			$reserve_qry .= " date = '".$date."'";	//해당일자의 객실 수량만 뽑아오기
			$reserve_res = mysqli_query($dbconn, $reserve_qry);
			while($reserve_row = @mysqli_fetch_array($reserve_res)) {
				$room[$reserve_row["room_type"]]["cnt"] = $reserve_row["cnt"];
				$room[$reserve_row["room_type"]]["price"] = $reserve_row["price"];
			}

			$out = "<h4 class=\"card-title\">".$date."</h4>";
			for($r=0; $r<count($room); $r++) {
				$_r = $r+1;	//높이계산용
				//$top = (83*$_r) - ($_r*$_r);
				$top = 95+(70*($_r-1))+$_r;
				$out .= "<div class=\"form-group\">";
				$out .= "<label class=\"rcnt_lable\">".$room[$room_num[$r]]["name"]."</label>";
				$out .= "<div style=\"clear:both;\">";
				$out .= "<input type=\"text\" name=\"rcnt_price_".$date."_".$room[$room_num[$r]]["room_type"]."\" class=\"rcnt_price form-control max-width50\" value=\"".$room[$room_num[$r]]["price"]."\" numberOnly>";
				$out .= "<input type=\"text\" name=\"rcnt_cnt_".$date."_".$room[$room_num[$r]]["room_type"]."\" class=\"rcnt_cnt form-control max-width30 margin-left10\" value=\"".$room[$room_num[$r]]["cnt"]."\" numberOnly>";
				$out .= "</div>";
				$out .= "<div class=\"rcnt_detail_button\" style=\"top:".$top."px;\">";
				$out .= "<i class=\"mdi mdi-arrow-down-drop-circle\" onclick=\"admin.room_count_detail_change(\'".$date."\', \'".$room[$room_num[$r]]["room_type"]."\', \'down\')\"></i>";
				$out .= "<i class=\"mdi mdi-arrow-up-drop-circle\" onclick=\"admin.room_count_detail_change(\'".$date."\', \'".$room[$room_num[$r]]["room_type"]."\', \'up\')\"></i>";
				$out .= "</div>";
				$out .= "</div>";
			}

			$out .= "</div>";

			echo "<script type=\"text/javascript\">";
			echo "$('#rcnt_detail').html('".$out."');";
			echo "$('#room_table_data').html('')";//테이블폼 초기화
			echo "
			$(\"input:text[numberOnly]\").on(\"keyup\", function() {
			  $(this).val($(this).val().replace(/[^0-9]/g,\"\"));
			});
			$(\".rcnt_price\").keyup(function(e){
				if(e.keyCode == 13) {
					var name = $(this).attr(\"name\");
					var name_arr = name.split(\"_\");
					admin.room_count_detail_change(name_arr[2], name_arr[3], 'price');
				}
			})";
			echo "
			$(\".rcnt_cnt\").keyup(function(e){
				if(e.keyCode == 13) {
					var name = $(this).attr(\"name\");
					var name_arr = name.split(\"_\");
					admin.room_count_detail_change(name_arr[2], name_arr[3], 'cnt');
				}
			})";
			echo "</script>";
		break;
		case "room_count_detail_change":
			/* date ,type(up/down), num*/
			switch($type) {
				case "up":	case "down":
					$qry = "select num, cnt from reserve_check where room_type = '".$num."' and date = '".$date."'";
					$res = mysqli_query($dbconn, $qry);
					$row = @mysqli_fetch_array($res);
					if($row["num"] > 0) {
						$state_qry = "update reserve_check set ";
						$state_qry .= ($type == "down") ? "cnt = cnt-1 " : "cnt = cnt+1 ";
						$state_qry .= " where num = '".$row["num"]."'";
					} else {
						$state_qry = "insert into reserve_check (room_type, date, cnt, price) values ";
						$state_qry .= "('".$num."', '".$date."', '1', '0')";
					}
					$state_res = mysqli_query($dbconn, $state_qry);
					$show_cnt = ($type == "down") ? $row["cnt"]-1 : $row["cnt"]+1;
					
					$out = "$(\"input[name='rcnt_cnt_".$date."_".$num."']\").val('".$show_cnt."');";
					//$out .= "admin.room_count_detail('".$date."');";
				break;
				case "price":
				case "cnt":
					$qry = "select num, cnt from reserve_check where room_type = '".$num."' and date = '".$date."'";
					$res = mysqli_query($dbconn, $qry);
					$row = @mysqli_fetch_array($res);
					if($row["num"] > 0) {
						$state_qry = "update reserve_check set ";
						$state_qry .= " ".$type." = '".${$type}."'";
						$state_qry .= " where num = '".$row["num"]."'";
					} else {
						$cnt = ($cnt == "") ? "0" : $cnt; 
						$price = ($cnt == "") ? "0" : $cnt;
						$state_qry = "insert into reserve_check (room_type, date, cnt, price) values ";
						$state_qry .= "('".$num."', '".$date."', '".$cnt."', '".$price."')";
					}
					$state_res = mysqli_query($dbconn, $state_qry);
					$out = "$(\"input[name='rcnt_".$type."_".$date."_".$num."']\").val('".${$type}."');";
				break;
			}
			echo "<script type=\"text/javascript\">";
			echo $out;
			echo "
			$(\"input:text[numberOnly]\").on(\"keyup\", function() {
			  $(this).val($(this).val().replace(/[^0-9]/g,\"\"));
			});
			$(\".rcnt_price\").keyup(function(e){
				if(e.keyCode == 13) {
					var name = $(this).attr(\"name\");
					var name_arr = name.split(\"_\");
					admin.room_count_detail_change(name_arr[2], name_arr[3], 'price');
				}
			})";
			echo "
			$(\".rcnt_cnt\").keyup(function(e){
				if(e.keyCode == 13) {
					var name = $(this).attr(\"name\");
					var name_arr = name.split(\"_\");
					admin.room_count_detail_change(name_arr[2], name_arr[3], 'cnt');
				}
			})";
			echo "</script>";
		break;
	}
?>