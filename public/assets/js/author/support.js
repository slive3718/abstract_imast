// $('#opensupport').on('click', function(){
//
// })

function openSupport(){
    $('#supportemail').modal('show')
}

function sendSupport(){
    let fname = $('#fnameInput').val()
    let lname = $('#lnameInput').val()
    let email = $('#semailInput').val()
    let abstract_id = $('#abstractIDInput').val()
    let message = $('#support_messageInput').val()

    if(fname == ''){
        toastr.warning('First Name cannot be empty')
        return false;
    }

    if(lname == ''){
        toastr.warning('Last Name cannot be empty')
        return false;
    }

    if(email == ''){
        toastr.warning('Email cannot be empty')
        return false;
    }

    if(message == ''){
        toastr.warning('Message Name cannot be empty')
        return false;
    }
    $.ajax({
        url:base_url+'/user/send_support_mail',
        method: "POST",
        dataType: "json",
        data:{
            'abstract_id' : 0,
            'fname': fname,
            'lname': lname,
            'email': email,
            'message':message
        },
        success: function(response){
            console.log(response);
            if(response.status == 200){
                swal.fire(
                    'success',
                    'Support message successfully sent',
                    'success'
                )
                $('#supportemail').modal('hide')
            }else{
                swal.fire(
                    'error',
                    'Something went wrong',
                    'error'
                )
            }
        }
    })
}