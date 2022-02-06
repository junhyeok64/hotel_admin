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