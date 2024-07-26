$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-Token": $("meta[name=csrf-token]").attr("content"),
        },
    });

    $('#id_producto').select2();

});


$( "#id_producto" ).on( "change", function() {
    var id = $("#id_producto").val()
    if(id > 0){
        $("#codigo").val(id)
        $("#formCodigo").submit()
    }
  } );
