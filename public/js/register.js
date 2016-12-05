$(function() {
    $(".form-signup").validate({
        rules:
        {
            name:"required",
            email:"required email",
            'rules-check':{ required:true }
        },
        messages:
        {
            name:"Pole wymagane",
            email:"Pole wymagane",
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
        var v = $(".form-signup").validate();
        v.cancelSubmit = true;
        v.element("#rules-check");
    });
});

