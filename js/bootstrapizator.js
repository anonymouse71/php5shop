jQuery(document).ready(function ($) {
    $("input[type=button],input[type=submit],input[type=file],button").addClass("btn btn-default");
    $("input[type=text],input[type=password],textarea").addClass("form-control").css('width', 'auto').css('display', 'inline-block');
    $("select").addClass("btn btn-sm btn-default");
    $("table").addClass('table-bordered table-condensed table table-responsive table-striped table-hover table-condensed');
    $(".pagination a").addClass("btn btn-primary btn-sm");
});