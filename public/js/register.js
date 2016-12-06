$(function() {
    $(".form-signup").validate({
        rules:
        {
            accept:"required"
        },
        messages:
        {
            accept:"(wymagana akceptacja)"
        },
        ignore: ".ignore",
        errorPlacement: function(error, element)
        {
            if (element.is(":checkbox")) {
                error.appendTo( element.parents('.checkbox') );
            } else {
                error.insertAfter( element );
            }
        }
    });
    $("#register-with-fb").click(function() {
        var v = $(".form-signup").validate();
        var el = v.element("#accept");
        return el;        
    });
});

