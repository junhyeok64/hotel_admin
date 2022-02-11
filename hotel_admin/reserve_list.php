<?php
	include "./common/top.php";
?>
	<script type="text/javascript">
		var page = "reserve_list";
	</script>
	<div class="container-scroller">
      <!-- partial:partials/_sidebar.html -->
      <?php
        //좌측 사이드메뉴 분리
        include "./common/left_menu.php";
        $_page = empty($_GET["page"]) ? 1 : $_GET["page"];
        $sdate = $_GET["sdate"];
        $edate = $_GET["edate"];
        $keyword = $_GET["keyword"];
        $keyword_type = $_GET["keyword_type"];
        $limit = $block = 10;
      ?>
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_navbar.html -->
		<?php
		//상단 탑메뉴 분리
		include "./common/sub_top.php";
		?>
		 <!-- partial -->
        <div class="main-panel reserve_page">
          <div class="content-wrapper">
            <div class="page-header">
              <h3 class="page-title"> Basic Tables </h3>
              <nav aria-label="breadcrumb">
              </nav>
            </div>
            <div class="row" style="display:contents;">
            	<div class="col-lg-6 grid-margin stretch-card" style="max-width:100%;">
                <div class="card" style="width:100%">
                  <div class="card-body">
                    <h4 class="card-title">Reserve List</h4>
                    <form name="reserve_form">
	                    <div class="form-group">                   	
          							<select name="keyword_type" class="js-example-basic-single" style="width:15%;float:left;">
          								<option value="num" <?php if($select == "num") { echo "selected"; } ?>>Order Num</option>
          								<option value="reserve_name" <?php if($select == "reserve_name") { echo "selected"; } ?>>Booker</option>
          								<option value="phone" <?php if($select == "phone") { echo "selected"; } ?>>Phone</option>
          								<option value="price" <?php if($select == "price") { echo "selected"; } ?>>Price</option>
          								<option value="reserve_time" <?php if($select == "date") { echo "selected"; } ?>>Pay Date</option>
          							</select>
          							<input type="text" class="form-control rlist_keyword" name="keyword" value="<?=$keyword?>" placeholder="Keyword" >
          							<input type="text" class="form-control rlist_sdate" name="sdate" value="<?=$sdate?>" placeholder="Stardate" >
          							<input type="hidden" name="page" value="<?=$_page?>">
          							<b class="rlist_bar">~</b>
          							<input type="text" class="form-control rlist_edate" name="edate" value="<?=$edate?>" placeholder="Enddate">
          							<button type="submit" class="btn btn-primary mb-2" style="height:37px;">Submit</button>
	                    </div>
                    </form>
                    <div class="table-responsive">
                      <table class="table table-hover">
                        <thead>
                          <tr>
                            <th>Order Num</th>
                            <th>Booker(Phone)</th>
                            <th>Room Type - (Room Cnt)</th>
                            <th>Room Cost</th>
                            <th>Stay</th>
                            <th>StartDate</th>
                            <th>EndtDate</th>
                            <th>PayDate</th>
                            <th>Status</th>
                          </tr>
                        </thead>
                        <tbody id="reserve_list">
                        	<?php
                        		$data = array();
                        		$_room = $util->room_arr("");
                        		$room = $_room["room"];
                        		$room_arr = $_room["sub"];

                        		$qry = "select * from reserve where 1=1 ";
                        		if($sdate) {
                        			$qry .= " and sdate >= '".$sdate."'";
                        		}
                        		if($edate) {
                        			$qry .= " and edate <= '".$edate."'";
                        		}
                        		if($keyword) {
                        			switch($keyword_type) {                        				
                        				case "price":	case "num":	case "reserve_name":
                        					$qry .= " and ".$keyword_type." = '".$keyword."'";
                        				break;
                        				/*case "phone":
                        					$qry .= " and ".$keyword_type." like '".$keyword."%'";
                        				break;*/
                        				case "reserve_time":	case "phone":
                        					$qry .= " and ".$keyword_type." like '%".$keyword."%'";
                        				break;

                        			}
                        		}
                        		$_qry = str_replace(" * ", " num ", $qry);
                        		$_res = mysqli_query($dbconn, $_qry);
                        		$cnt = @mysqli_num_rows($_res);
                        		$qry .= " order by num desc limit ".(($_page-1)*10).",".$limit."";

                        		//echo $qry;

                        		$res = mysqli_query($dbconn, $qry);
                        		$sub_cnt = @mysqli_num_rows($res);
                        		$i=1;
                        		if($cnt > 0) {
                        			$num = $cnt - (($_page-1)*$limit);
                        			$total_page = ceil($cnt / $block);
                        			while($row = mysqli_fetch_array($res)) {
                        				$data[$i]["state"] = $row["state"];
                        				//'Y','S','C','T','E'
                        	?>
                        	<input type="hidden" name="eq" value="<?=$i?>">
            							<tr>
            								<td><?=$row["num"]?></td>
            								<td><?=$row["reserve_name"]?> ( <?=$row["phone"]?> )</td>
            								<td><?=$room[$row["room_type"]]["name"]?> - ( <?=$row["room_cnt"]?> )</td>
            								<td><?=won?> <?=@number_format($row["price"])?></td>
            								<td><?=$util->date_diff($row["sdate"], $row["edate"])?></td>
            								<td><?=date("Y-m-d", strtotime($row["sdate"]))?></td>
            								<td><?=date("Y-m-d", strtotime($row["edate"]))?></td>
            								<td><?=date("Y-m-d H:i:s", strtotime($row["reserve_time"]))?></td>
            								<td>
            									<select name="keyword_type" class="js-example-basic-single" onchange="admin.reserve_state('<?=$row['num']?>',this.value, '<?=$i?>')" style="width:80%">
            										<option  class="badge-outline-success" value="Y" <?php if($row["state"] == "Y") { echo "selected"; } ?>>예약확인</option>
            										<option value="E" <?php if($row["state"] == "E") { echo "selected"; } ?>>투숙완료</option>
            										<option value="S" <?php if($row["state"] == "S") { echo "selected"; } ?>>예약완료</option>
            										<option value="C" <?php if($row["state"] == "C") { echo "selected"; } ?>>예약취소</option>
            										<option value="T" <?php if($row["state"] == "T") { echo "selected"; } ?>>결제시도</option>
            									</select>
            								</td>
            							</tr>
            							<?php
            									$i++;
            									}
            								} else {
            							?>
            							<tr>
            								<td colspan="7">일치하는 결제건이 없습니다.</td>
            							</tr>
          							<?php
          								}
          							?>
                        </tbody>
                      </table>
                      <dir>
                      	<center class="paging">
                      		<p>
                      			<a href="javascript:admin.reserve_page('1');"><<</a>
                      		<?php
                      			for($p=1; $p<=$total_page; $p++) {
                      				if($p == $_page) {
                      					echo "<b>".$p."</b>";
                      				} else {
                      					echo "<a href=\"javascript:admin.reserve_page('".$p."');\">".$p."</a>";
                      				}
                      			}
                      		?>
                      			<a href="javascript:admin.reserve_page('<?=$total_page?>');">>></a>
                      		</p>
                      	</center>
                      </dir>
                    </div>
                  </div>
                </div>
              </div>
             </div>
            </div>
            <!-- partial:../../partials/_footer.html -->
          <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
              <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © bootstrapdash.com 2020</span>
              <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center"> Free <a href="https://www.bootstrapdash.com/bootstrap-admin-template/" target="_blank">Bootstrap admin templates</a> from Bootstrapdash.com</span>
            </div>
          </footer>
          <!-- partial -->
        </div>
<?php
	include "./common/bottom.php";
?>
<script type="text/javascript">
	$(document).ready(function(){
		<?php
		//페이지 로드 됐을때 color잡아주기
			for($s=1; $s<=$sub_cnt; $s++) {
				switch($data[$s]["state"]) {
					case "Y":
						$color = "00d25b";
					break;
					case "S": case "E":
						$color = "8f5fe8";
					break;
					case "C":
						$color = "fc424a";
					break;
					case "T":
						$color = "ffab00";
					break;
					default:
						$color = "e4eaec";
					break;
				}
		?>
		$(".select2-selection__rendered").eq(<?=$s?>).css("color","#<?=$color?>");
		<?php
			}
		?>
	})
</script>