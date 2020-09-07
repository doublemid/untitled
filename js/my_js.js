
$(".container").delegate("#logout", "click", function(event) {
    event.preventDefault();
    $.ajax({
        type: "get",
        url: '/auth/logout',
        success:function(data){
            $('#headerAuth').html(data);
            alert( "Вы разлогинились!" );
            ajaxsort();
        },
    });
})

$(".container").delegate("#auth", "click", function(e) {
    e.preventDefault();
    var form = $(authForm);
    $.ajax({
        type: "POST",
        url: '/auth',
        data: form.serialize(),
        success:function(data){
            var json = $.parseJSON(data);
            if(json.success){
                alert(json.msg);
                ajaxsort();
            }
            else {
                alert(json.msg);
            }
            $('#headerAuth').html(json.data);
            ajaxsort();
        },
    });
});

$("#editTask").click(function(e) {
    e.preventDefault();
    var status=$('#status').val();
    var id=$('#taskId').val();
    var text=$('#message-text').val();
    $.ajax({
        type: "POST",
        url: '/tasks/edittask',
        data:{
            status:status,
            id:id,
            text:text,
        },
        success:function(data){
            var json = $.parseJSON(data);
            if(json.success){
                $('#editModal').modal('hide')
                alert(json.msg);
                ajaxsort();
            }
            else {
                alert(json.msg);
            }
        },
    });
});

$("#status").click( function(){
    if( $(this).is(':checked') )
        $(this).val("1");
    else $(this).val("0");
});

$("#createNewTask").click(function(e) {
    e.preventDefault();
    var form = $("#newTask");
    $.ajax({
        type: "POST",
        url: '/tasks/newtask',
        data: form.serialize(),
        success:function(data){
            var json = $.parseJSON(data);
            if(json.success){
                alert(json.msg);
                ajaxsort();
            }
            else {
                alert(json.msg);
            }
            ajaxsort()
            $.get("tasks/taskpages", function(data){
                $('.pagination ').html(data);
            });
        },
    });
});

$(".pagination ").delegate("a", "click", function(e) {
    e.preventDefault();
    $(' li.active').removeClass('active');
    $(this).parent().addClass( "active" );
    ajaxsort()
});

$('#listTask').on('click', 'a', function (){
    var id= $(this).attr("id");
    $('#taskId').val(id);
    $('#message-text').text($("#"+'textTask'+id).text());
    if( $('#'+'statusTask'+id).attr('data') == 1 ){
        $('#status').attr("checked", true);
    }
    if( $('#'+'statusTask'+id).attr('data') == 0 ){
        $('#status').attr("checked", false);
    }
    $('#editModal').modal('show');
});
1
$(document).ready(function() {
    $('li.page-item').first().addClass( "active" );
});

$('.sort').click(function(){
    $('#parametrSort').text( $(this).text());
    $('.sort.active').removeClass('active');
    $(this).addClass( "active" );
    ajaxsort()
});

$('.order').click(function(){
    $('#orderSort').text( $(this).text());
    $('.order.active').removeClass('active');
    $(this).addClass( "active" );
    ajaxsort()
});

$('a.page-link').click(function() {
    $(' li.active').removeClass('active');
    $(this).parent().addClass( "active" );
    var page = $(this).text();
    ajaxsort()
});

function ajaxsort() {
    var order=$('.order.active').val();
    var sort=$('.sort.active').val();
    var page=$('li.active a.page-link').text();
    $.ajax({
        url: "/tasks/list",
        data: {
            page:page,
            order:order,
            sort:sort,
        },
        type: "GET",
        dataType: "html",
        success: function (data) {
            $('#listTask').html(data);
        },
    });
};
