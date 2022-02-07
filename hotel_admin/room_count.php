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
        ?>
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
                    <h4 class="card-title">Bordered table</h4>
                    <div class="table-responsive">
                      <table class="table table-bordered">
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
                        <tbody>
                        	<?php
                        		$out = "<tr>";
                        		for($sdate; $sdate<=$edate; $sdate = date("Y-m-d", strtotime($sdate." +1 days"))) {
                        			$yoile = date("w", strtotime($sdate));
                        			if($_sdate == $sdate) { //첫날 요일잡아주기
                        				for($i=0;$i<$yoile; $i++) {
                        					$out .= "<td></td>";
                        				}
                        				$out .= "<td>".$sdate."</td>";
                        			} else {
                        				$out .= "<td>".$sdate."</td>";
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