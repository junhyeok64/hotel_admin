<?php
	include "./common/top.php";
?>
	<script type="text/javascript">
		var page = "room_count";
	</script>
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
        <style type="text/css">
        	.table-bordered th {text-align:center;}
        	.table-bordered .center {text-align:center;}
        	.table-bordered .red {background-color:#FFCBCB;}
        	.table-bordered .blue {background-color:#C4DEFF;}
        	.calender td {line-height:35px;height:150px;}
        	.table-bordered b {text-align:center;}
        	.top_title {text-align:center;margin-left:35%;width:30%;}
        	.top_title i {color:black;/*font-size:35px;*/}
        	.max-width80 {max-width:80%}
        	.max-width50 {max-width:50%}
        	.max-width30 {max-width:30%}
        	.max-width20 {max-width:20%}
        	.margin-left10 {margin-left:10px;}
        	#room_count_detail i {font-size:35px;color:black;/*float:right;clear:both;*/}
        	#room_count_detail .mdi {cursor:pointer}
        	#rcnt_detail input {float:left;}
        	.rcnt_lable {float:left;clear:both;margin:10px 0;}
        </style>
		<!-- partial -->
		<form name="room_count_form">
			<input type="hidden" name="mode" value="room_count">
			<input type="hidden" name="sdate" value="<?=$sdate?>">
			<input type="hidden" name="edate" value="<?=$edate?>">
			<input type="hidden" name="show_type" value="calender">
			<input type="hidden" name="type" value="month">
		</form>
        <div class="main-panel">
          <div class="content-wrapper">
            <div class="page-header">
				<h3 class="page-title"> Room Count </h3>			
            </div>
            <div class="row">
              <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                  	<h3 class="top_title">
                  		<i class="mdi mdi-arrow-left-drop-circle" style="cursor:pointer;float:left;" onclick="admin.room_count_page('prev')"></i>
                  		<b class="top_title_text"><?=date("Y-m")?></b>
                  		<i class="mdi mdi-arrow-right-drop-circle" style="cursor:pointer;float:right;" onclick="admin.room_count_page('next')"></i>
                  	</h3>
                  	
                    <div style="float:right;margin:1.125rem 0;">
                    	<button class="btn btn-outline-secondary dropdown-toggle" type="button" id="show_type" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Calender </button>
                    	<div class="dropdown-menu" aria-labelledby="show_type">
                          <a class="dropdown-item" href="javascript:admin.room_count('calender');">Calender</a>
                          <a class="dropdown-item" href="javascript:admin.room_count('table');">Table</a>
                        </div>
                    </div>
                    <div class="table-responsive" id="room_calender">
                      <table class="table table-bordered calender">
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
    <div id="Mask"></div>
        <script type="text/javascript">
            function star(num) {
                $(".review_star").removeClass("fa-star-o");
                $(".review_star").removeClass("fa-star");
                $("input[name='star']").val(num);
                for(i=0; i<5; i++){
                    if(i<num) {
                        $(".review_star").eq(i).addClass("fa-star");
                    } else {
                        $(".review_star").eq(i).addClass("fa-star-o");
                    }
                }
            }
        </script>
        <!--  pop   -->
        <style type="text/css">
        	.rcnt_detail_button {
        		float:right;/*width:90%*/;position:absolute;margin-left:75%;
        	}
        	.rcnt_detail_button i {
        		float:right;
        	}
        </style>
        <div id="room_count_detail" class="pop-wrap">
            <div class="pop-layer">
            <div class="close">x</div>
                <div>
                	<div class="col-md-6 grid-margin stretch-card" style="max-width:100%;text-align:left">
						<div class="card">
							<div class="card-body" id="rcnt_detail">
								<h4 class="card-title"><?=date("Y-m-d")?></h4>
								<div class="form-group">
									<input type="text" class="form-control max-width80" value="서울 더블 디럭스 룸 - 10" aria-label="Username">
									<div class="rcnt_detail_button" style="top:60px;">
										<i class="mdi mdi-arrow-down-drop-circle"></i>
										<i class="mdi mdi-arrow-up-drop-circle" onclick="admin.room_count_detail_change()"></i> 
									</div>
								</div>
								<div class="form-group">
									<input type="text" class="form-control max-width80" value="서울 더블 디럭스 룸 - 10" aria-label="Username">
									<div class="rcnt_detail_button" style="top:115px;">
										<i class="mdi mdi-arrow-up-drop-circle" onclick="admin.room_count_detail_change()"></i> 
										<i class="mdi mdi-arrow-down-drop-circle"></i>
									</div>
								</div>
								<div class="form-group">
									<input type="text" class="form-control max-width80" value="서울 더블 디럭스 룸 - 10" aria-label="Username">
									<div class="rcnt_detail_button" style="top:170px;">
										<i class="mdi mdi-arrow-up-drop-circle" onclick="admin.room_count_detail_change()"></i> 
										<i class="mdi mdi-arrow-down-drop-circle"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
                </div>
            </div>
        </div>
        <!--  //pop -->
        <style type="text/css">
        	.rcnt_detail_table {
        		color:black;
        	}
        	.rcnt_detail_table i {
        		font-size:30px;
        	}
        	#Mask{
			  display: none;
			  position:fixed;
			  top: 0;
			  left: 0;
			  width: 100%;
			  height: 100%;
			  background:#000;
			  opacity:.3; 
			  filter:alpha(opacity:30);
			  z-index: 150;
			}
			.pop-wrap {
			    display: none;
			    position: fixed;
			    left: 0;
			    right: 0;
			    top: 0;
			    bottom: 0;
			    text-align: center;
			    background-color: rgba(0, 0, 0, 0.5);
			  z-index:9999;
			}
			.pop-wrap:before {
			    content: "";
			    display: inline-block;
			    height: 100%;
			    vertical-align: middle;
			    margin-right: -.25em;
			}
			.pop-layer {
			    display: inline-block;
			    position:relative;
			    vertical-align: middle;
			    width: 500px;
			    padding:20px;
			    background-color: #fff;
			    z-index: 10;
			}
			.pop-wrap .close{
			  position:absolute;
			  top:10px;
			  right:10px;
			  cursor:pointer;
			}
			@media (max-width: 767px) {
			  .pop-wrap:before {
			    height: 0;
			  } 
			  .pop-layer {
			    width:auto;
			  }
			}
        </style>
<?php
	include "./common/bottom.php";
?>
<script type="text/javascript">
	$(document).ready(function(){
		admin.form_ajax("room_count_form", "html");
	})
	$("#room_count_detail .close").click(function(){
		$("#room_count_detail").hide();
	});
</script>