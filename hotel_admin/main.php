    <script type="text/javascript">
      var page = "main";
    </script>
    <style type="text/css">
      /* Track */
::-webkit-scrollbar-track {
  background: black;
}

/* Handle */
::-webkit-scrollbar-thumb {
  background: #888;
}

    </style>
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
          //'Y','S','C','T'
          $month_arr  = array();
          $prev_arr   = array();

          //당월, 전월 결제상태별 금액 조회
          $state_qry = "select state from reserve group by state";
          $state_res = mysqli_query($dbconn, $state_qry);
          while($state_row = mysqli_fetch_array($state_res)) {
            $month_arr[$state_row["state"]] = 0;
            $prev_arr[$state_row["state"]] = 0;
          }

          $month_qry = "select sum(price) as price, state from reserve where 1=1 ";
          $month_qry .= " and reserve_time >= '".date("Y-m-01 00:00:00")."' and reserve_time <= '".date("Y-m-d 23:59:59")."'";
          $month_qry .= " group by state";
          $month_res = mysqli_query($dbconn, $month_qry);
          while($month_row = @mysqli_fetch_array($month_res)) {
            $month_arr[$month_row["state"]] += $month_row["price"];
          }
          
          $month_price    = $month_arr["Y"]+$month_arr["S"]+$month_arr["E"];
          $month_success  = $month_arr["E"]+$month_arr["S"];
          $month_cancel   = $month_arr["C"];

          //전월 결제금액은 _prev 테이블에 미리 저장하여 조회
          $prev_qry = "select * from reserve_prev where 1=1 ";
          $prev_qry .= " and date = '".date("Y-m", strtotime(date("Y-m")." -1 month"))."'";
          $prev_res = mysqli_query($dbconn, $prev_qry);
          $prev_row = @mysqli_fetch_array($prev_res);

          $prev_price = ($prev_row["price"] == "") ? 0 : $prev_row["price"];
          $prev_success = ($prev_row["success"] == "") ? 0 : $prev_row["success"];
          $prev_cancel = ($prev_row["cancel"] == "") ? 0 : $prev_row["cancel"];

          $per_price = ($month_price>0) ? @(($month_price - $prev_price) / $month_price)*100 : "당월 금액이 없어 비교 할 수 없습니다.";
          if($per_price  != "당월 금액이 없어 비교 할 수 없습니다.") {
            $per_price = ($per_price > 0) ? "+".@number_format($per_price,2)."%" : "".@number_format($per_price,2)."%";
            $css_price = ($per_price > 0) ? " text-success" : " text-danger";
          }
          $per_success = ($month_success>0) ? @(($month_success - $prev_success) / $month_success)*100 : "당월 금액이 없어 비교 할 수 없습니다.";
          if($per_success  != "당월 금액이 없어 비교 할 수 없습니다.") {
            $per_success = ($per_success > 0) ? "+".@number_format($per_success,2)."%" : "".@number_format($per_success,2)."%";
            $css_success = ($per_success > 0) ? " text-success" : " text-danger";
          }
          $per_cancel = ($month_cancel>0) ? @(($month_cancel - $prev_cancel) / $month_cancel)*100 : "당월 금액이 없어 비교 할 수 없습니다.";
          if($per_cancel  != "당월 금액이 없어 비교 할 수 없습니다.") {
            $per_cancel = ($per_cancel > 0) ? "+".@number_format($per_cancel,2)."%" : "".@number_format($per_cancel,2)."%";
            $css_cancel = ($per_cancel > 0) ? " text-success" : " text-danger";
          }
        ?>
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">
            <div class="row">
              <div class="col-sm-4 grid-margin">
                <div class="card">
                  <div class="card-body">
                    <h5>당월 예약금액</h5>
                    <div class="row">
                      <div class="col-8 col-sm-12 col-xl-8 my-auto">
                        <div class="d-flex d-sm-block d-md-flex align-items-center">
                          <h2 class="mb-0"><?=won?> <?=@number_format($month_price)?></h2>
                          <p class="ml-2 mb-0 font-weight-medium<?=$css_price?>"><?=$per_price?></p>
                        </div>
                        <h6 class="text-muted font-weight-normal"> Since last month</h6>
                      </div>
                      <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                        <i class="icon-lg mdi mdi-codepen text-primary ml-auto"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-4 grid-margin">
                <div class="card">
                  <div class="card-body">
                    <h5>당월 확정금액</h5>
                    <div class="row">
                      <div class="col-8 col-sm-12 col-xl-8 my-auto">
                        <div class="d-flex d-sm-block d-md-flex align-items-center">
                          <h2 class="mb-0"><?=won?> <?=@number_format($month_success)?></h2>
                          <p class="ml-2 mb-0 font-weight-medium<?=$css_success?>"><?=$per_success?></p>
                        </div>
                        <h6 class="text-muted font-weight-normal"> Since last month</h6>
                      </div>
                      <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                        <i class="icon-lg mdi mdi-wallet-travel text-success ml-auto"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-4 grid-margin">
                <div class="card">
                  <div class="card-body">
                    <h5>당월 취소금액</h5>
                    <div class="row">
                      <div class="col-8 col-sm-12 col-xl-8 my-auto">
                        <div class="d-flex d-sm-block d-md-flex align-items-center">
                          <h2 class="mb-0"><?=won?> <?=@number_format($month_cancel)?></h2>
                          <p class="ml-2 mb-0 font-weight-medium<?=$css_cancel?>"><?=$per_cancel?> </p>
                        </div>
                        <h6 class="text-muted font-weight-normal"> Since last month</h6>
                      </div>
                      <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                        <i class="icon-lg mdi mdi-monitor text-danger ml-auto"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-4 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Transaction History</h4>
                    <canvas id="transaction-history" class="transaction-chart"></canvas>
                    <?php
                      //'Y','S','C','T'
                      //당일, 하루전, 이틀전 금액 조회
                      $transaction_arr = array();
                      //매출 없을땔 위해, 역순출력을 위해 초기화
                       $transaction_arr[date("Y-m-d", strtotime(date("Y-m-d")." -2 days"))] = $transaction_arr[date("Y-m-d", strtotime(date("Y-m-d")." -1 days"))] = $transaction_arr[date("Y-m-d")] = 0;

                      $transaction_qry = "select sum(price) as price, date_format(reserve_time, '%Y-%m-%d') as date from reserve where 1=1 ";
                      $transaction_qry .= " and reserve_time >= '".date("Y-m-d 00:00:00", strtotime(date("Y-m-d")." -2 days"))."' and reserve_time <= '".date("Y-m-d H:i:s")."'";
                      $transaction_qry .= " and state not in ('C', 'T')";
                      $transaction_qry .= " group by DATE_FORMAT(reserve_time, '%Y-%m-%d')";

                      $transaction_res = mysqli_query($dbconn, $transaction_qry);
                      while($transaction_row = @mysqli_fetch_array($transaction_res)) {
                        $transaction_arr[$transaction_row["date"]] = $transaction_row["price"];
                      }

                      foreach($transaction_arr as $key=>$value) {
                        $trans_diff = $util->date_diff(date("Y-m-d"), $key);
                        $show_diff = ($trans_diff == 0) ? "당일" : $trans_diff."일전";
                    ?>
                    <div class="bg-gray-dark d-flex d-md-block d-xl-flex flex-row py-3 px-4 px-md-3 px-xl-4 rounded mt-3">
                      <div class="text-md-center text-xl-left">
                        <h6 class="mb-1"><?=$key?></h6>
                        <p class="text-muted mb-0"><?=$show_diff?></p>
                      </div>
                      <div class="align-self-center flex-grow text-right text-md-center text-xl-right py-md-2 py-xl-0">
                        <h6 class="font-weight-bold mb-0"><?=won?> <?=@number_format($value)?></h6>
                      </div>
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
              <div class="col-md-8 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex flex-row justify-content-between">
                      <h4 class="card-title mb-1">Open Projects</h4>
                      <p class="text-muted mb-1">Your data status</p>
                    </div>
                    <div class="row">
                      <div class="col-12">
                        <div class="preview-list">
                          <div class="preview-item border-bottom">
                            <div class="preview-thumbnail">
                              <div class="preview-icon bg-primary">
                                <i class="mdi mdi-file-document"></i>
                              </div>
                            </div>
                            <div class="preview-item-content d-sm-flex flex-grow">
                              <div class="flex-grow">
                                <h6 class="preview-subject">Admin dashboard design</h6>
                                <p class="text-muted mb-0">Broadcast web app mockup</p>
                              </div>
                              <div class="mr-auto text-sm-right pt-2 pt-sm-0">
                                <p class="text-muted">15 minutes ago</p>
                                <p class="text-muted mb-0">30 tasks, 5 issues </p>
                              </div>
                            </div>
                          </div>
                          <div class="preview-item border-bottom">
                            <div class="preview-thumbnail">
                              <div class="preview-icon bg-success">
                                <i class="mdi mdi-cloud-download"></i>
                              </div>
                            </div>
                            <div class="preview-item-content d-sm-flex flex-grow">
                              <div class="flex-grow">
                                <h6 class="preview-subject">Wordpress Development</h6>
                                <p class="text-muted mb-0">Upload new design</p>
                              </div>
                              <div class="mr-auto text-sm-right pt-2 pt-sm-0">
                                <p class="text-muted">1 hour ago</p>
                                <p class="text-muted mb-0">23 tasks, 5 issues </p>
                              </div>
                            </div>
                          </div>
                          <div class="preview-item border-bottom">
                            <div class="preview-thumbnail">
                              <div class="preview-icon bg-info">
                                <i class="mdi mdi-clock"></i>
                              </div>
                            </div>
                            <div class="preview-item-content d-sm-flex flex-grow">
                              <div class="flex-grow">
                                <h6 class="preview-subject">Project meeting</h6>
                                <p class="text-muted mb-0">New project discussion</p>
                              </div>
                              <div class="mr-auto text-sm-right pt-2 pt-sm-0">
                                <p class="text-muted">35 minutes ago</p>
                                <p class="text-muted mb-0">15 tasks, 2 issues</p>
                              </div>
                            </div>
                          </div>
                          <div class="preview-item border-bottom">
                            <div class="preview-thumbnail">
                              <div class="preview-icon bg-danger">
                                <i class="mdi mdi-email-open"></i>
                              </div>
                            </div>
                            <div class="preview-item-content d-sm-flex flex-grow">
                              <div class="flex-grow">
                                <h6 class="preview-subject">Broadcast Mail</h6>
                                <p class="text-muted mb-0">Sent release details to team</p>
                              </div>
                              <div class="mr-auto text-sm-right pt-2 pt-sm-0">
                                <p class="text-muted">55 minutes ago</p>
                                <p class="text-muted mb-0">35 tasks, 7 issues </p>
                              </div>
                            </div>
                          </div>
                          <div class="preview-item">
                            <div class="preview-thumbnail">
                              <div class="preview-icon bg-warning">
                                <i class="mdi mdi-chart-pie"></i>
                              </div>
                            </div>
                            <div class="preview-item-content d-sm-flex flex-grow">
                              <div class="flex-grow">
                                <h6 class="preview-subject">UI Design</h6>
                                <p class="text-muted mb-0">New application planning</p>
                              </div>
                              <div class="mr-auto text-sm-right pt-2 pt-sm-0">
                                <p class="text-muted">50 minutes ago</p>
                                <p class="text-muted mb-0">27 tasks, 4 issues </p>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row ">
              <div class="col-12 grid-margin">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Reserve Status</h4>
                    <div class="table-responsive">
                      <table class="table">
                        <thead>
                          <tr>
                            <th>
                              <div class="form-check form-check-muted m-0">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input">
                                </label>
                              </div>
                            </th>
                            <th> Booker (Phone) </th>
                            <th> Room Type - (Room_cnt) </th>
                            <th> Room Cost </th>
                            <th> Stay </th>
                            <th> Start Date </th>
                            <th> End Date </th>
                            <th> Payment Status </th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                            //객실 정보 미리 불러다놓고 결제내역 내에서 반복조회 x
                            $room_qry = "select * from room";
                            $room_res = mysqli_query($dbconn, $room_qry);
                            $room = array();
                            while($room_row = mysqli_fetch_array($room_res)) {
                              $room[$room_row["num"]]["name"] = $room_row["name"];
                              $room[$room_row["num"]]["img"] = $room_row["img"];
                            }
                            //상위 다섯개만 불러오고 추가적인 결제건들은 리스팅 페이지로 가서 조회하기
                            $reserve_qry = "select * from reserve where 1=1 ";
                            $reserve_qry .= " order by num desc limit 0, 5";
                            $reserve_res = mysqli_query($dbconn, $reserve_qry);
                            while($reserve_row = mysqli_fetch_array($reserve_res)) {
                              $night = $util->date_diff($reserve_row["sdate"], $reserve_row["edate"]);
                              $show_state = $css_state =  "";
                              switch($reserve_row["state"]) {//'Y','S','C','T'
                                case "Y"://예약시도
                                  $show_state = "예약확인";
                                  $css_state = " badge-outline-success";
                                break;
                                case "S":
                                  $show_state = "예약확정";
                                  $css_state = " badge-outline-info";
                                break;
                                case "E":
                                  $show_state = "투숙완료";
                                  $css_state = " badge-outline-info";
                                break;
                                case "C":
                                  $show_state = "예약취소";
                                  $css_state = " badge-outline-danger";
                                break;
                                case "T"://입금전
                                  $show_state = "결제대기";
                                  $css_state = " badge-outline-warning";
                                break;
                                default:
                                  $show_state = "확인요망";
                                  $css_state = " badge-outline-secondary";
                                break;
                              }
                          ?>
                          <tr>
                            <td>
                              <div class="form-check form-check-muted m-0">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input">
                                </label>
                              </div>
                            </td>
                            <td>
                              <!--<img src="assets/images/faces/face1.jpg" alt="image" />-->
                              <span class="pl-2"><?=$reserve_row["reserve_name"]?> (<?=$reserve_row["phone"]?>)</span>
                            </td>
                            <td> <?=$room[$reserve_row["room_type"]]["name"]?> - ( <?=$reserve_row["room_cnt"]?> 개 )</td>
                            <td> <?=won?> <?=@number_format($reserve_row["price"])?> </td>
                            <td> <?=$night?> </td>
                            <td> <?=date("Y-m-d", strtotime($reserve_row["sdate"]))?> </td>
                            <td> <?=date("Y-m-d", strtotime($reserve_row["edate"]))?> </td>
                            <td>
                              <div class="badge<?=$css_state?>"><?=$show_state?></div>
                            </td>
                          </tr>
                          <?php
                            }
                          ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 col-xl-4 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex flex-row justify-content-between">
                      <h4 class="card-title">Review</h4>
                      <p class="text-muted mb-1 small">View all</p>
                    </div>
                    <div class="preview-list">
                      <?php
                        //리뷰 리스팅, 별도의 페이지 x
                        $review_qry = "select * from review order by wdate desc limit 0,8";
                        $review_res = mysqli_query($dbconn, $review_qry);

                        while($review_row = mysqli_fetch_array($review_res)) {
                          $reserve_qry = "select * from reserve where num = '".$review_row["reserve_num"]."'";
                          $reserve_res = mysqli_query($dbconn, $reserve_qry);
                          $reserve_row = @mysqli_fetch_array($reserve_res);
                          $star = "";
                          for($s=1; $s<=5; $s++) {
                            if($s <= $review_row["star"]) {
                              $star .= "<i class='fa fa-star'></i>";
                            } else {
                              $star .= "<i class='fa fa-star-o'></i>";
                            }
                          }
                      ?>
                      <div class="preview-item border-bottom">
                        <div class="preview-thumbnail">
                          <img src="<?=base_url?>/<?=$room[$reserve_row['room_type']]["img"]?>" alt="image" class="rounded-circle" />
                        </div>
                        <div class="preview-item-content d-flex flex-grow">
                          <div class="flex-grow">
                            <div class="d-flex d-md-block d-xl-flex justify-content-between">
                              <h6 class="preview-subject">
                                <a href="javascript:;"><?=$reserve_row["reserve_name"].$review_row["star"]?></a> - <?=$star?>
                              </h6>
                              <p class="text-muted text-small"><?=$review_row["wdate"]?></p>
                            </div>
                            <div class="d-flex d-md-block d-xl-flex justify-content-between">
                              <h6 class="preview-subject">
                                <a href="javascript:;"><?=$room[$reserve_row["room_type"]]["name"]?></a>
                              </h6>
                            </div>
                            <p class="text-muted"><?=$review_row["contents"]?></p>
                          </div>
                        </div>
                      </div>
                      <?php
                        }
                      ?>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-xl-4 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Portfolio Slide</h4>
                    <div class="owl-carousel owl-theme full-width owl-carousel-dash portfolio-carousel" id="owl-carousel-basic">
                      <div class="item">
                        <img src="assets/images/dashboard/Rectangle.jpg" alt="">
                      </div>
                      <div class="item">
                        <img src="assets/images/dashboard/Img_5.jpg" alt="">
                      </div>
                      <div class="item">
                        <img src="assets/images/dashboard/img_6.jpg" alt="">
                      </div>
                    </div>
                    <div class="d-flex py-4">
                      <div class="preview-list w-100">
                        <div class="preview-item p-0">
                          <div class="preview-thumbnail">
                            <img src="assets/images/faces/face12.jpg" class="rounded-circle" alt="">
                          </div>
                          <div class="preview-item-content d-flex flex-grow">
                            <div class="flex-grow">
                              <div class="d-flex d-md-block d-xl-flex justify-content-between">
                                <h6 class="preview-subject">CeeCee Bass</h6>
                                <p class="text-muted text-small">4 Hours Ago</p>
                              </div>
                              <p class="text-muted">Well, it seems to be working now.</p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <p class="text-muted">Well, it seems to be working now. </p>
                    <div class="progress progress-md portfolio-progress">
                      <div class="progress-bar bg-success" role="progressbar" style="width: 50%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-12 col-xl-4 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">To do list</h4>
                    <div class="todo_button" style="">
                      <i class="mdi mdi-arrow-left-drop-circle" style="cursor:pointer;" onclick="admin.todo_paging('prev')"></i>
                      <i class="mdi mdi-arrow-right-drop-circle" style="cursor:pointer;" onclick="admin.todo_paging('next')"></i>
                    </div>
                    <div class="add-items d-flex">
                      <form name="todo_list" style="display:contents;" onSubmit="return false;">
                      <input type="hidden" name="todo_page" value="1">
                      <input type="hidden" name="todo_end" value="99">
                      <input type="text" name="password" class="form-control todo-list-input" placeholder="enter task..">
                      <button type="button" class="add btn btn-primary todo-list-add-btn">Add</button>
                      </form>
                    </div>
                    <div class="list-wrapper">
                      <ul class="d-flex flex-column text-white todo-list todo-list-custom" id="todo_list">
                        <?php
                          $todo_qry = "select * from todo where state='Y' order by `check`='N' desc ,num desc limit 0,10";
                          $todo_res = mysqli_query($dbconn, $todo_qry);
                          while($todo_row = @mysqli_fetch_array($todo_res)) {
                            $checked = ($todo_row["check"] == "N") ? "" : " checked";
                            $complete = ($todo_row["check"] == "N") ? "" : "completed";
                        ?>
                        <li class="<?=$complete?>">
                          <div class="form-check form-check-primary">
                            <label class="form-check-label">
                              <input name="num[]" value="<?=$todo_row['num']?>" class="checkbox" onchange="admin.todo_change('<?=$todo_row["num"]?>','check', this.checked)" type="checkbox"<?=$checked?>> <?=$todo_row["text"]?> 
                            </label>
                          </div>
                          <i class="remove mdi mdi-close-box" onclick="admin.todo_change('<?=$todo_row["num"]?>','state')"></i>
                        </li>
                        <?php
                          }
                        ?>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row" style="display:none;">
              <div class="col-12">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Visitors by Countries</h4>
                    <div class="row">
                      <div class="col-md-5">
                        <div class="table-responsive">
                          <table class="table">
                            <tbody>
                              <tr>
                                <td>
                                  <i class="flag-icon flag-icon-us"></i>
                                </td>
                                <td>USA</td>
                                <td class="text-right"> 1500 </td>
                                <td class="text-right font-weight-medium"> 56.35% </td>
                              </tr>
                              <tr>
                                <td>
                                  <i class="flag-icon flag-icon-de"></i>
                                </td>
                                <td>Germany</td>
                                <td class="text-right"> 800 </td>
                                <td class="text-right font-weight-medium"> 33.25% </td>
                              </tr>
                              <tr>
                                <td>
                                  <i class="flag-icon flag-icon-au"></i>
                                </td>
                                <td>Australia</td>
                                <td class="text-right"> 760 </td>
                                <td class="text-right font-weight-medium"> 15.45% </td>
                              </tr>
                              <tr>
                                <td>
                                  <i class="flag-icon flag-icon-gb"></i>
                                </td>
                                <td>United Kingdom</td>
                                <td class="text-right"> 450 </td>
                                <td class="text-right font-weight-medium"> 25.00% </td>
                              </tr>
                              <tr>
                                <td>
                                  <i class="flag-icon flag-icon-ro"></i>
                                </td>
                                <td>Romania</td>
                                <td class="text-right"> 620 </td>
                                <td class="text-right font-weight-medium"> 10.25% </td>
                              </tr>
                              <tr>
                                <td>
                                  <i class="flag-icon flag-icon-br"></i>
                                </td>
                                <td>Brasil</td>
                                <td class="text-right"> 230 </td>
                                <td class="text-right font-weight-medium"> 75.00% </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <div class="col-md-7">
                        <div id="audience-map" class="vector-map"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- content-wrapper ends -->
          <!-- partial:partials/_footer.html -->
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