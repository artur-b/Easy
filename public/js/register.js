$(function() {
    $(".form-signup").validate({
        rules:
        {
            name:"required",
            email:"required email",
            password:"required",
            pesel:"required",
            phone:"required",
            accept:"required"
        },
        messages:
        {
            name:"Pole wymagane",
            email:"Pole wymagane",
            password:"Pole wymagane",
            pesel:"Pole wymagane",
            phone:"Pole wymagane",
            accept:"Wymagana akceptacja regulaminu"
        },
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
        console.log(v);
        return false;        
    });
});

