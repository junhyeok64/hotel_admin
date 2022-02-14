<?php
  $page = "reserve_chart";
  include "./common/top.php"
?>
    <script type="text/javascript">
      var page = "reserve_chart";

      var reserve_data_arr = new Array();
      var reserve_title_arr = new Array();
    </script>
    <div class="container-scroller">
      <?php
        $date_type = empty($_GET["date_type"]) ? "day" : $_GET["date_type"];
        $chart_type = empty($_GET["chart_type"]) ? "table" : $_GET["chart_type"];

        if($chart_type == "table") { //매출 차트, 테이블 폼 노출유무
          $chart_class = " hide";
          $table_class = " show";
        } else {
          $chart_class = " show";
          $table_class = " hide";
        }

        $date_diff = 99;

        switch($date_type) {
          case "day":
            $sdate = empty($_GET["sdate"]) ? date("Y-m-01") : $_GET["sdate"]; //시작일
            $edate = empty($_GET["edate"]) ? date("Y-m-t") : $_GET["edate"];  //종료일
            $qry_sdate = $sdate;  //쿼리용 시작일 (month/year 폼 맞춤용)
            $qry_edate = $edate;
            $_date_type = "left(reserve_time,10)"; //쿼리
            $add_date = " +1 days ";  //반복문 돌릴때 ++용
            $add_date_sub = "Y-m-d";  //반복문 datatype
            $script = " +7 days ";    //차트 뽑을때 일일 다 뽑으면 cols너무 많아짐
            $date_diff = $util->date_diff($sdate, $edate);  //일자 차이가 7일 미만일땐 일일 다뽑도록
          break;
          case "month":
            $sdate = empty($_GET["sdate"]) ? date("Y-01") : $_GET["sdate"];
            $edate = empty($_GET["edate"]) ? date("Y-m") : $_GET["edate"];
            $qry_sdate = $sdate."-01";
            $qry_edate = date("Y-m-t", strtotime($edate));
            $_date_type = "left(reserve_time,7)";
            $add_date = " +1 month ";
            $add_date_sub = "Y-m";
            $script = "";
          break;
          case "year":
            $sdate = empty($_GET["sdate"]) ? date("Y", strtotime((date("Y")." -1 year"))) : $_GET["sdate"];
            $edate = empty($_GET["edate"]) ? date("Y") : $_GET["edate"];
            $qry_sdate = $sdate."-01-01";
            $qry_edate = $edate."-12-31";//error
            $_date_type = "left(reserve_time,4)";
            $add_date = " +1 year ";
            $add_date_sub = "Y";
            $script = "";
          break;
        }

        $_sdate = $sdate;
        $_edate = $edate;

        include "./common/left_menu.php";
      ?>
      <div class="container-fluid page-body-wrapper">
        <!-- partial:../../partials/_navbar.html -->
        <?php
          include "./common/sub_top.php";
        ?>
        <!-- partial -->
        <div class="main-panel">
          <div class="main-panel reserve_page reserve_chart_div">
            <div class="content-wrapper">
              <div class="page-header">
                <form name="reserve_form">
                  <div class="form-group">
                    <input type="text" class="form-control rlist_sdate" name="sdate" value="<?=$sdate?>" placeholder="Stardate" >
                    <input type="hidden" name="date_type" value="<?=$date_type?>">
                    <input type="hidden" name="chart_type" value="<?=$chart_type?>">
                    <b class="rlist_bar">~</b>
                    <input type="text" class="form-control rlist_edate" name="edate" value="<?=$edate?>" placeholder="Enddate">
                    <button type="submit" class="btn btn-primary mb-2" style="height:37px;">Submit</button>
                  </div>
                  <div class="form-group rchart_button">
                    <?php
                      $day_type = $month_type = $year_type = "";
                      switch($date_type) {  //일별/월별/년별 버튼css
                        case "day": case "month": case "year":
                          ${$date_type."_type"} = " on";
                        break;
                      }
                      //폰환경에서 width100 maring-bottom3
                    ?>
                    <button type="button" onclick="admin.rchart_date('day');" class="btn btn-outline-light btn-fw<?=$day_type?>">일별</button>
                    <button type="button" onclick="admin.rchart_date('month');" class="btn btn-outline-light btn-fw<?=$month_type?>">월별</button>
                    <button type="button" onclick="admin.rchart_date('year');" class="btn btn-outline-light btn-fw<?=$year_type?>">년별</button>
                    <?php if($chart_type == "table") { ?>
                    <button type="button" onclick="admin.rchart_date('chart')" class="btn btn-outline-light btn-fw">
                      <i class="mdi mdi-chart-line"></i>
                    </button>
                    <?php } else { ?>
                    <button type="button" onclick="admin.rchart_date('table')" class="btn btn-outline-light btn-fw">
                      <i class="mdi mdi-table-large"></i>
                    </button>
                    <?php } ?>
                  </div>
                </form>
              </div>
            <div class="row<?=$chart_class?>" style="">
              <div class="col-lg-6 grid-margin stretch-card" style="display:contents;max-width:100%">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Reserve chart</h4>
                    <canvas id="lineChart" style="height:250px"></canvas>
                  </div>
                </div>
              </div>
            </div>
            <div class="row<?=$table_class?>" style="">
              <div class="col-lg-6 grid-margin stretch-card" style="display:contents;max-width:100%">
                <div class="card" style="width:100%">
                  <div class="card-body">

                  </div>
                  <div class="card-body">
                    <h4 class="card-title">Reserve Chart</h4>
                    
                    <div class="table-responsive reserve_chart" style="width:90%">
                      <table class="table table-hover">
                        <thead>
                          <tr>
                            <th>Date</th>
                            <th>예약금액</th>
                            <th>확정금액</th>
                            <th>취소금액</th>
                          </tr>
                        </thead>
                        <tbody id="reserve_chart">
                          
                          <?php
                            $data = array();
                            $script = "";

                            //'Y','S','C','T','E'
                            $script .= "<script type=\"text/javascript\">";
                            $s = 0;
                            for($_sdate; $_sdate<=$_edate; $_sdate) {
                              $data[$_sdate]["Y"] = 0;  //일자별 금액 초기화(일자 안찍힘 방지)
                              $data[$_sdate]["S"] = 0;
                              $data[$_sdate]["C"] = 0;
                              $data[$_sdate]["T"] = 0;
                              $data[$_sdate]["E"] = 0;
                              
                              switch($date_type) {  //차트용 cols
                                case "year":
                                  $script .= "reserve_title_arr[".($s)."] = '".$_sdate."';";
                                  $_sdate++;
                                break;
                                case "month":
                                  $script .= "reserve_title_arr[".($s)."] = '".$_sdate."';";
                                  $_sdate = date($add_date_sub, strtotime($_sdate.$add_date));
                                break;
                                case "day":
                                  if($date_diff < 7) {  //7일미만은 다찍기, 이상은 7일주기로 찍기
                                    $script .= "reserve_title_arr[".$s."] = '".$_sdate."';";
                                  } else {
                                    if($s%6 == 0 || $s==0){
                                      if($s == 1) {
                                        $script .= "reserve_title_arr[0] = '".$_sdate."';";
                                      } else {
                                        $script .= "reserve_title_arr[".($s/6)."] = '".$_sdate."';";
                                      }
                                    }
                                  }
                                  $_sdate = date($add_date_sub, strtotime($_sdate.$add_date));
                                break;
                              }
                              $s++;
                            }

                            $qry = "select sum(price) as price, ".$_date_type." as date, state from reserve where 1=1 ";
                            $qry .= " and reserve_time >= '".$qry_sdate."' and reserve_time <= '".$qry_edate."'";
                            $qry .= " group by ".$_date_type.", state";
                            //echo $qry;

                            $res = mysqli_query($dbconn, $qry);
                            
                            while($row = mysqli_fetch_array($res)) {
                               $data[$row["date"]][$row["state"]] = $row["price"]; //초기화한 배열에 금액 넣기
                            }                          
                            $r = 0;
                            $prev_price = 0;
                            foreach($data as $key => $value) {
                              //결제타입별 금액정리
                              $price = $data[$key]["Y"]+$data[$key]["S"]+$data[$key]["E"];  //예약금액
                              $success = $data[$key]["S"]+$data[$key]["E"]; //확정금액
                              $cancel = $data[$key]["C"]; //취소금액

                              $total_price += $price;
                              $total_success += $success;
                              $total_cancel += $cancel;

                              
                              switch($date_type) {
                                case "year":
                                  $script .= "reserve_data_arr[".$r."] = '".$price."';";
                                break;
                                case "month":
                                  $script .= "reserve_data_arr[".$r."] = '".$price."';";
                                break;
                                case "day":
                                if($date_diff < 7) {
                                  $script .= "reserve_data_arr[".$r."] = '".$price."';";
                                } else {
                                  if($r%6==0 || $r==0) {
                                    $script .= "reserve_data_arr[".($r/6)."] = '".$total_price."';";
                                   }
                                }
                                break;
                              }
                              $r++;
                              if($price > $prev_price) {
                                $diff_price = "<span style='color:red'>▲</span";
                              } else if($price < $prev_price) {
                                $diff_price = "<span style='color:blue'>▼</span";
                              } else if($price == $prev_price) {
                                $diff_price = "<span style='color:black'> -</span";
                              }

                              if($success > $prev_success) {
                                $diff_success = "<span style='color:red'>▲</span";
                              } else if($success < $prev_success) {
                                $diff_success = "<span style='color:blue'>▼</span";
                              } else if($success == $prev_success) {
                                $diff_success = "<span style='color:black'> -</span";
                              }

                              if($cancel > $prev_cancel) {
                                $diff_cancel = "<span style='color:red'>▲</span";
                              } else if($cancel < $prev_cancel) {
                                $diff_cancel = "<span style='color:blue'>▼</span";
                              } else if($cancel == $prev_cancel) {
                                $diff_cancel = "<span style='color:black'> -</span";
                              }

                              echo "<tr>";
                              echo "<td class='center'>".$key."</td>";
                              echo "<td class='price' text='Total : ".@number_format($total_price)."'>".@number_format($price)." ".$diff_price."</td>";
                              echo "<td class='price' text='Total : ".@number_format($total_success)."'>".@number_format($success)." ".$diff_success."</td>";
                              echo "<td class='price' text='Total : ".@number_format($total_cancel)."'>".@number_format($cancel)." ".$diff_cancel."</td>";
                              echo "</tr>";
                              $prev_price = $price;
                              $prev_success = $success;
                              $prev_cancel = $cancel;
                            }
                            $script .= "</script>";
                          ?>
                          <tr class="bold">
                            <td class="center">Total</td>
                            <td><?=@number_format($total_price)?></td>
                            <td><?=@number_format($total_success)?></td>
                            <td><?=@number_format($total_cancel)?></td>
                          </tr>
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
          <div id="rlist_div"></div>
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
    <!-- container-scroller -->
    <!-- plugins:js -->
    <?php
      include "./common/bottom.php";
      echo $script;
    ?>
    <style type="text/css">
      #rlist_div {width:150px;height:50px;border:1px solid black;background-color:#323232;}
    </style>
    


    <script type="text/javascript">
      $(".price").mouseenter(function(e){
        var divtop = e.pageY;
        var divleft = e.pageX+50;
        //var num = $(this).attr("num");
        var text = $(this).attr("text");
        $("#rlist_div").empty().append("<div style='position:absolute;top:5px;right:5px'>"+text+"</div>");
        $("#rlist_div").css({
          "top" : divtop,
          "left" : divleft,
          "position":"absolute"
        }).show();

      })
      $(".price").mouseleave(function(e){
        $("#rlist_div").hide();
      })
    </script>