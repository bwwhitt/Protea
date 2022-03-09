jQuery(document).ready(function($){    
    
    $(".forminator-field-email-academy input").focusout(function(){
        var current_val = $(this).val();
        var email_arr = $(this).val().split("@");
        var site_arr = email_arr[1];
        var domain_arr = site_arr.split(".");
        var lenght = email_arr.length;        
        var idx = email_arr[lenght - 1];        
        valMessage = 'Only alphabetic characters are allowed.';
        valHTML = '<span class="forminator-error-message" aria-hidden="true">Not a valid Email Address(".edu is required")</span>';
        setTimeout(() => {                   
            if(jQuery.inArray("edu", domain_arr) <= 0) {              
                $(this).parent().addClass( 'forminator-has_error' );
                $(this).parent().parent().addClass( 'forminator-has_error' );
                $( this ).parent().parent().find(".forminator-error-message").remove();
                //forminator-field
                $( this ).after('<label class="forminator-label--validation">' + valHTML + '</label>')
                //$( this ).parent().append('<label class="forminator-label--validation">' + valHTML + '</label>')
            }
        }, 100);
        
    })
    $(".wr_change_pwd_btn").click(function(){ 
        $(".wr_change_pwd_wrapper").toggleClass('show');
    });
    $( "#forminator-module-894").submit(function( event ) {        
        var email_obj = $(".forminator-field-email-academy input");
        var email = $("#forminator-field-email-1").val();
        var email_arr = email.split("@");
        var site_arr = email_arr[1];
        var domain_arr = site_arr.split(".");
        /*var email_arr = email.split(".");
        var lenght = email_arr.length;        
        var idx = email_arr[lenght - 1]; */
        valHTML = '<span class="forminator-error-message" aria-hidden="true">Not a valid Email Address(".edu is required")</span>';
        if(jQuery.inArray("edu", domain_arr) <= 0) {    
            email_obj.parent().addClass( 'forminator-has_error' );
            email_obj.parent().parent().addClass( 'forminator-has_error' );
            email_obj.parent().parent().find(".forminator-error-message").remove();
            //forminator-field
            email_obj.after('<label class="forminator-label--validation">' + valHTML + '</label>')    
            return false;
        }
        return true;
    });
    //$(".wrpt_change_pwd_btn").click(function(){
    $( "#wr_change_pwd_frm" ).submit(function( event ) {
        event.preventDefault();        
        var oldpwd = $("#wrpt_current_password").val();
        var newpwd = $("#wrpt_new_password").val();
        var confirmpwd = $("#wrpt_confirm_password").val();
        $(".wr_messagebox").html();
        if(newpwd=="" || oldpwd==""){
            $(".wr_messagebox").html('<div class="elementor-message elementor-message-error" role="alert">Please enter Current password and new password</div>');
            return false;
        }
        console.log((newpwd != confirmpwd));
        if(newpwd != confirmpwd){
            $(".wr_messagebox").html('<div class="elementor-message elementor-message-error" role="alert">Confirm password doesn\'t match with new password</div>');
            return false;
        }
        $.ajax({
            url:frontendajax.ajaxurl,
            data:{action:'wrpt_change_password', new_password:newpwd,old_password:oldpwd,confirm_password:confirmpwd  },
            type:"post",
            success:function(res) {
                var data = JSON.parse(res);
                $(".wr_messagebox").html('<div class="elementor-message elementor-message-'+ data.status +'" role="alert">'+ data.message+'</div>');
                $( "#wr_change_pwd_frm" ).find("input[type=password]").val("");
            }
        })    
    })
    /*valMessage = 'Only alphabetic characters are allowed.',
    valHTML = '<label class="forminator-label--validation">' + valMessage + '</label>';
    $(document).bind('ready ajaxComplete', function() { 
        $("#forminator-field-email-1").focusout(function(){
            $(this).parent().parent().append(valHTML);
        })
    });*/
})
