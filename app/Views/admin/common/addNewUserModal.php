
<!-- Modal -->
<div class="modal fade" id="addNewUserModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addUserForm" class="bg-light p-2 border rounded shadow-sm">
                    <div class="row g-1">
                        <input type="hidden" name="user_id" id="user_id" value="">

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" id="name" class="form-control" name="name" value="" placeholder="Name">
                                <label for="name">Name</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" id="middle_name" class="form-control" name="middle_name" value="" placeholder="Middle Name">
                                <label for="middle_name">Middle Name</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" id="surname" class="form-control" name="surname" value="" placeholder="Surname">
                                <label for="surname">Surname</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" id="email" class="form-control" name="email" value="" placeholder="Email">
                                <label for="email">Email</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" id="institution" class="form-control" name="institution" value="" placeholder="Institution">
                                <label for="institution">Institution</label>
                            </div>
                        </div>

                        <div class="col-12 mt-3 p-0">
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="password" id="password" class="form-control" name="password" value="" placeholder="**********">
                                    <label for="password">Password</label>
                                </div>
                            </div>

                            <div class="col-12 mt-2">
                                <div class="form-floating">
                                    <input type="password" id="confirm_password" class="form-control" name="confirm_password" value="" placeholder="**********">
                                    <label for="confirm_password">Confirm Password</label>
                                </div>
                            </div>

                            <small>Note: User passwords can be reset here <br> Passwords must be at least 6 characters long.</small>
                        </div>


                        <div class="col-md-12" id="is_regular_reviewer_div"> <!-- Make the checkbox span the entire row -->
                            <div class="alert alert-warning mt-3" role="alert">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_regular_reviewer" id="is_regular_reviewer" value="1">
                                    <label class="form-check-label" for="is_regular_reviewer">Regular Reviewer</label>
                                </div>
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <small>If Regular Reviewer is unchecked above, this new user will only have submission log in permissions</small>
                            </div>
                        </div>
                        <div class="col-md-12" id="is_deputy_reviewer_div"> <!-- Make the checkbox span the entire row -->
                            <div class="alert alert-warning mt-3" role="alert">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_deputy_reviewer" id="is_deputy_reviewer" value="1">
                                    <label class="form-check-label" for="is_deputy_reviewer">Deputy Reviewer</label>
                                </div>
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <small>If Deputy Reviewer is unchecked above, this new user will only have submission log in permissions</small>
                            </div>
                        </div>

                        <div class="col-md-12" id="is_session_moderator_div"> <!-- Make the checkbox span the entire row -->
                            <div class="alert alert-warning mt-3" role="alert">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_session_moderator" id="is_session_moderator" value="1">
                                    <label class="form-check-label" for="is_session_moderator">Session Moderator</label>
                                </div>
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <small>If Session Moderator is checked above, this new user will be available on scheduler session chair</small>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h5 class="fw-bold mb-3">Divisions</h5>
                        <div class="row row-cols-2" id="divisionContainer">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="saveUserBtn" action="insert">Save changes</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
    $(function(){
        $.ajax({
            url: base_url+'admin/getDivisions', // Replace with your actual URL
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Clear the existing checkboxes if any
                $('#divisionContainer').html('');

                // Iterate over the divisions received from the server
                $.each(data, function(i, division) {
                    $('#divisionContainer').append(
                        '<div class="col">' +
                        '<div class="form-check">' +
                        '<input class="form-check-input" type="checkbox" name="divisions[]" id="division_' + division.id + '" value="' + division.id + '">' +
                        '<label class="form-check-label" for="division_' + division.id + '">' +
                        division.name +
                        '</label>' +
                        '</div>' +
                        '</div>'
                    );
                });
            },
            error: function(xhr, status, error) {
                console.error('Error fetching divisions:', error);
                // Optionally, display an error message to the user
            }
        });

        // Insert and Update User
        let userForm = $('#addUserForm');
        userForm.on('click', '#saveUserBtn', function() {
            let action = $(this).attr('action')
            handleUser(action)
        });

        // $('#is_regular_reviewer').on('change', function(){
        //     console.log('here')
        //     if($(this).prop('checked') === true){
        //         $('#is_deputy_reviewer').prop('checked', false)
        //     }
        // })
        // $('#is_deputy_reviewer').on('change', function(){
        //     if($(this).prop('checked') === true){
        //         $('#is_regular_reviewer').prop('checked', false)
        //     }
        // })
    })

    $('#reviewerTableBody').on('click', '.manageUserBtn', function(){
        let reviewerID = $(this).attr('reviewerid')
        $('#addNewUserModal').modal('show')
        $('#addUserForm')[0].reset();
        $('#saveUserBtn').attr('action','update')
        $('#addUserForm #user_id').val(reviewerID)

        $.post(base_url+'admin/getUserById',{
            'user_id':reviewerID
        }, function(data){
            console.log(data)
            $('#username').val(data.username)
            $('#name').val(data.name)
            $('#middle_name').val(data.middle_name)
            $('#surname').val(data.surname)
            $('#email').val(data.email)
            $('#institution').val(data.profile.institution)
            $('#password').val('******')
            $('#confirm_password').val('******')
            if(data.is_regular_reviewer === '1'){
                $('#is_regular_reviewer').prop('checked', true)
                $('#is_regular_reviewer_div').css('display', 'block')
                $('#is_deputy_reviewer_div').css('display', 'none')
            }
            if(data.is_deputy_reviewer === '1'){
                $('#is_deputy_reviewer').prop('checked', true)
                $('#is_deputy_reviewer_div').css('display', 'block')
                $('#is_regular_reviewer_div').css('display', 'none')
            }
            let divisions = JSON.parse(data.profile.division_id);
            $.each(divisions, function(i, val){
                console.log(val)
                $('#division_'+val).prop('checked', true)
            })
        }, 'json')
    })

    function addUserModal(){
        $('#addUserForm')[0].reset();
        $('#saveUserBtn').attr('action','insert')
        $('#addUserForm #user_id').val('')
        $('#addNewUserModal').modal('show')
        $('#addNewUserModal .modal-title').html('Details')
    }

    function handleUser(action) {
        let formData = new FormData(document.getElementById('addUserForm'));
        let url = action === 'insert' ? baseUrlAdmin + 'user/create_user' : baseUrlAdmin + 'user/update_user';
        let confirmText = action === 'insert' ? "Yes, create it!" : "Yes, update it!";
        let successMessage = action === 'insert' ? "User created successfully" : "User updated successfully";

        if($('#password').val() !== $('#confirm_password').val()){
            toastr.error('Confirm Password Not Matched!')
            return false;
        }

        Swal.fire({
            title: "Are you sure?",
            text: "Please double check the information before continuing.",
            icon: "info",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: confirmText
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    data: formData,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    success: function(data) {
                        if (data.status === 'success') {
                            Swal.fire({
                                'icon': 'success',
                                'title': 'Success',
                                'text': successMessage
                            });
                            $('#addNewUserModal').modal('hide')
                            $('.doSearchBtn').click();
                            getReviewerList();
                        } else {
                            if (data.errors) {
                                let errorMessages = Object.values(data.errors).join('\n');
                                Swal.fire({
                                    'icon': 'error',
                                    'title': 'Error',
                                    'text': errorMessages
                                });
                            }
                        }
                    },
                    error: function() {
                        Swal.fire({
                            'icon': 'error',
                            'title': 'Error',
                            'text': 'An unexpected error occurred. Please try again.'
                        });
                    }
                });
            }
        });
    }
</script>
