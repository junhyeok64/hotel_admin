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
				$data = @json_decode($row, true);
				if(!$data) {
					$data = array();
				}
				if(count($data) > 0) {
					$data_cnt = count($data["이름"]);
					if($data_cnt > 0) {
						$type = "member";
					}
				}
			}
			if($type == "member") {
				echo "<script>member();</script>";
			} else {
				echo "<script>new_member();</script>";
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
			//bg https://cdn-lostark.game.onstove.com/2018/obt/assets/images/pc/bg/bg_profile_equipment.png
			$url = "https://lostark.game.onstove.com/Profile/Character/".urlencode($name);

			$data = array();
			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_URL,$url);
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_BINARYTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_REFERER, "");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);       //POST data
			curl_setopt($ch, CURLOPT_POST, false);

			$result = curl_exec($ch);
			curl_close ($ch);

			//levle
			$patten = "/<div class=\"level-info2__expedition\"><span>장착 아이템 레벨<\/span><span><small>Lv.<\/small>(.*?)<\/span><\/div>/is";
			preg_match_all($patten,$result,$level); 
			$_level = str_replace("<small>", "", $level[1][0]);
			$user_level = str_replace("</small>", "", $_level);

			//main_img
			$patten = "/<div class=\"profile-equipment__character\">(.*?)<\/div>/is";
			preg_match_all($patten,$result,$main_img); 
			$_main_img = explode("alt=", $main_img[1][0]);
			$_main_img = str_replace("<img src=\"", "", $_main_img[0]);
			$main_img = str_replace("\"", "", $_main_img);

			//class
			//profile-character-info__img
			$patten = "/<img class=\"profile-character-info__img\"(.*?)>/is";
			preg_match_all($patten,$result,$class); 
			$_class = explode("alt=", $class[1][0]);
			$class_name = str_replace("\"", "", $_class[1]);
			$class_img = str_replace("src=", "", $_class[0]);
			$class_img = str_replace("\"", "", $class_img);
			$class_img = str_replace(" ", "", $class_img);
			$class_img = "<img src=\"".$class_img."\">";
			$user_name = $name;

			//조회한 캐릭터가 이미 저장되어있는지 조회
			$type = "null";
			$qry = "select * from bynnark where 1=1 ";
			$qry .= " and (userip = '".$_SERVER["REMOTE_ADDR"]."' or name = '".$name."')";
			$res = mysqli_query($dbconn, $qry);
			$cnt = mysqli_num_rows($res);
			if($cnt > 0) {
				$type = "not_null";
			}

			$script_arr = array();
			$script_arr[] = "class_img";
			$script_arr[] = "class_name";
			$script_arr[] = "user_name";
			$script_arr[] = "user_level";
			$script_arr[] = "main_img";

			$out = "";
			//지정할 캐릭터가 맞는지, 해당 원정대의 다른 캐릭터들도 데려올건지 확인
			if($type == "null") {
				//캐릭 없을시 
				$out .= "<script type=\"text/javascript\">";
				for($a=0; $a<count($script_arr); $a++) {
					if($script_arr[$a] == "main_img") {
						$out .= "$(\".".$script_arr[$a]."\").attr('src', '".${$script_arr[$a]}."');";
					} else {
						$out .= "$(\".".$script_arr[$a]."\").html('".${$script_arr[$a]}."');";
					}
				}
				$out .= "</script>";
				echo $out;
			} else {
				//기존 이용 내역 있음
				$out .= "<table>";
				while($row = mysqli_fetch_array($res)) {
					$data = json_decode($row["data"], true);
					$out .= "<tr>";
					$out .= "<td></td>";
					$out .= "<td></td>";
					$out .= "<td></td>";
					$out .= "</tr>";

				}
				$out .= "</table>";

			}


			/*echo "<xmp>";
			print_r($_class);
			echo "</xmp>";*/
		break;
	}
?>

