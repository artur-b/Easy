$(function() {
    $(".form-signup").validate({
        rules:
        {
            'rules-check':{ required:true }
        },
        messages:
        {
            'rules-check':{ required:"(wymagana akceptacja)" }
        },
        errorPlacement: function(error, element)
        {
            if (element.is(":checkbox"))
            {
                error.appendTo( element.parents('.checkbox') );
            }
            else
            {
                error.insertAfter( element );
            }
        }
    });
    $("#register-with-fb").click(function() {
        $(".form-signup").validate().cancelSubmit = true;
    });
});

