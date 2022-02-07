<?php
	include "./common/top.php";
?>
	<div class="container-scroller">
      <!-- partial:partials/_sidebar.html -->
      <?php
        //좌측 사이드메뉴 분리
        include "./common/left_menu.php";
      ?>
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_navbar.html -->
        <?php
          //상단 탑메뉴 분리
          include "./common/sub_top.php";
          $sdate = $_sdate = date("Y-m-01");
          $edate = $_edate = date("Y-m-t");

          $room = array();
          $room_qry = "select * from room where state = 'Y' order by num asc";
          $room_res = mysqli_query($dbconn, $room_qry);
          while($room_row = mysqli_fetch_array($room_res)) {
          	$room[$room_row["num"]]["name"] = $room_row["name"];
          	$room[$room_row["num"]]["img"] = $room_row["img"];
          }

        ?>
        <style type="text/css">
        	.table-bordered th {text-align:center;}
        	.table-bordered .center {text-align:center;}
        	.table-bordered .red {background-color:#FFCBCB;}
        	.table-bordered .blue {background-color:#C4DEFF;}
        	.calendar td {line-height:35px;height:150px;}
        	.table-bordered b {text-align:center;}
        	h3 {text-align:center;}
        </style>
		<!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">
            <div class="page-header">
              <h3 class="page-title"> Room Count </h3>
            </div>
            <div class="row">
              <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                  	<h3><?=date("Y-m")?></h3>
                    <div style="float:right;margin-bottom: 1.125rem;">
                    	<button class="btn btn-outline-secondary dropdown-toggle" type="button" id="show_type" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Calender </button>
                    	<div class="dropdown-menu" aria-labelledby="show_type">
                          <a class="dropdown-item" href="javascript:;">Calender</a>
                          <a class="dropdown-item" href="javascript:;">Table</a>
                        </div>
                    </div>
                    <div class="table-responsive" id="room_calender">
                      <table class="table table-bordered calendar">
                        <thead>
                          <tr>
                            <th class="red"> Sun </th>
                            <th> Mon </th>
                            <th> Tue </th>
                            <th> Wed </th>
                            <th> Thu </th>
                            <th> Fri </th>
                            <th class="blue"> Sta </th>
                          </tr>
                        </thead>
                        <tbody id="room_calender_data">
                        	<?php
                        		$out = "<tr>";
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
                        				for($i=0;$i<$yoile; $i++) {
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
                        					$out .= "<td class='".$class."'></td>";
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
                        		echo $out;
                        	?>
                        </tbody>
                      </table>
                    </div>
                    <div class="table-responsive" id="room_table">
                      <table class="table table-bordered">
                        <thead>
                          <tr>
                            <th> Date </th>
                            <th> Room Type </th>
                            <th> Count </th>
                          </tr>
                        </thead>
                        <tbody id="room_table_data">
                        	<?php
                        		
                        		$sdate = $_sdate; //리필, 모바일용 달력x 일반테이블
                        		$out = "";

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
                        		echo $out;
                        	?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          </div>
          <!-- content-wrapper ends -->
          <!-- partial:../../partials/_footer.html -->
          <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
              <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © bootstrapdash.com 2020</span>
              <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center"> Free <a href="https://www.bootstrapdash.com/bootstrap-admin-template/" target="_blank">Bootstrap admin templates</a> from Bootstrapdash.com</span>
            </div>
          </footer>
          <!-- partial -->
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
<?php
	include "./common/bottom.php";
?>