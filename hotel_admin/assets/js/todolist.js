(function($) {
  'use strict';
  $(function() {
    var todoListItem = $('.todo-list');
    var todoListInput = $('.todo-list-input');
    $('.todo-list-add-btn').on("click", function(event) {
      event.preventDefault();
      todo_insert();
    });

    todoListItem.on('change', '.checkbox', function() {
      if ($(this).attr('checked')) {
        //$(this).removeAttr('checked');
      } else {
        //$(this).attr('checked', 'checked');
      }

      $(this).closest("li").toggleClass('completed');

    });

    todoListItem.on('click', '.remove', function() {
      $(this).parent().remove();
    });

  });
})(jQuery);

function todo_insert() {
  var todoListItem = $('.todo-list');
  var todoListInput = $('.todo-list-input');
    event.preventDefault();

    var item = $('.todo-list-input').val();

    if (item) {
      $.ajax({
        type : "POST",
        data : {"mode":"todo_add", "text":item},
        url  : "state.php",
        success : function(e) {
          var res = e.split("||");
          if(res[0] == "SUCC") {
            var new_num = res[1];
            todoListItem.prepend("<li><div class='form-check'><label class='form-check-label'><input class='checkbox' name='num[]' value='"+new_num+"' type='checkbox'/>" + item + "<i class='input-helper'></i></label></div><i class='remove mdi mdi-close-box' onclick='admin.todo(\""+new_num+"\")'></i></li>");
            todoListInput.val("");
          }
        }
      })
    }
}