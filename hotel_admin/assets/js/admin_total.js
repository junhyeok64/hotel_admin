var admin = {
	login : function(e) {
		if($("input[name='userid']").val() == "") {
			alert("아이디를 확인 해주세요");
			$("input[name='userid']").focus();
			return false;
		} else if($("input[name='password']").val() == "") {
			alert("비밀번호를 확인 해주세요");
			$("input[name='password']").focus();
			return false;
		} else {
			admin.form_ajax('login_form','html');
		}
	},
	form_ajax : function(form, type) {
		$.ajax({
			type : "POST",
			data : $("form[name='"+form+"']").serialize(),
			url  : "state.php",
			success : function(e) {
				switch(type) {
					case "html":
						$("#script").html(e);
					break;
					case "alert":
						alert(e);
					break;
				}

			}
		})
	},
	todo_change : function(num, type, val) {
		$.ajax({
			type : "POST",
			data : {"mode":"todo_change", "num":num, "type":type, "val":val},
			url  : "state.php",
			success : function(e) {
				//alert(e);
				//admin.todo_paging('');
			}
		})
	},
	todo_paging : function(type) {
		var todo_page = $("input[name='todo_page']").val();
		var todo_end = $("input[name='todo_end']").val();
		if(type != "") {
			if((todo_page == todo_end || todo_page > todo_end) && type == "next") {
				alert("마지막페이지입니다");
				return false;
			}
			if((todo_page < 1 || todo_page == 1) && type == "prev") {
				alert("첫번째페이지입니다");
				return false;
			}
		}
		$.ajax({
			type : "POST",
			data : {"mode":"todo_paging", "type":type, "page":todo_page},
			url  : "state.php",
			success : function(e) {
				$("#todo_list").html(e);
			}
		})
	},
	room_count : function(type) {
		switch(type) {
			case "calender": case "table":
				$('input[name=show_type]').val(type);
				admin.form_ajax('room_count_form', 'html');
				$("#show_type").html(type.charAt(0).toUpperCase()+type.slice(1));
			break;
		}
	},
	room_count_page : function(type) {
		$("input[name='type']").val(type);
		admin.form_ajax("room_count_form", "html");
	},
	room_count_detail : function(date) {
		$("#room_count_detail").show();
		$.ajax({
			type : "POST",
			data : {"mode":"room_count_detail", "date":date},
			url  : "state.php",
			success : function(e) {
				$("#rcnt_detail").html(e);
			}
		})
	},
	room_count_detail_change : function(date,num,type) {
		var price = "rcnt_price_"+date+"_"+num;
		var cnt = "rcnt_cnt_"+date+"_"+num;

		$.ajax({
			type : "POST",
			data : {"mode":"room_count_detail_change", "date":date, "type":type, "num":num, "cnt":$("input[name='"+cnt+"']").val(), "price":$("input[name='"+price+"']").val()},
			url  : "state.php",
			success : function(e) {
				$("#script").html(e);
			}
		})
	}
}
$("input[name='password']").keyup(function(e){
	if(e.keyCode == 13) {
		switch(page) {
			case "login":
				admin.login();
			break;
			case "main":
				e.preventDefault();
				todo_insert();
			break;
		}
	}
})