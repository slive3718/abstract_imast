
<!-- Modal -->
<div class="modal fade" id="searchUserModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Search User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="userSearchBox" placeholder="JohnDoe">
                    <label for="userSearchBox">Name</label>
                    <button type="button" class="btn btn-primary btn-sm doSearchBtn my-2 float-end">Search <i class="fas fa-magnifying-glass"></i></button>
                </div>
                <div class="searchedResults">
                    <table class="table table-striped table-bordered" id="searchResultsTable">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Last Name</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody id="searchResultsTable">

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function(){
        $('#userSearchBox').on('keypress', function(e){
            if(e.keyCode === 13){
                $('.doSearchBtn').click();
            }
        })
        $('.doSearchBtn').on('click', function(e){
            e.preventDefault();
            let searchValue = $('#userSearchBox').val();
            $.ajax({
                url: base_url+'admin/searchUser',
                data: {
                    searchValue: searchValue
                },
                type: "POST",
                beforeSend: function(){
                    // Show loading indicator before the request is made
                    swal.fire({
                        title: 'Searching...',
                        text: 'Please wait while we search.',
                        allowOutsideClick: false,
                        onOpen: function() {
                            swal.showLoading(); // Show loading animation
                        }
                    });
                },
                success: function(data) {
                    var table = $('#searchResultsTable').DataTable();
                    table.clear().draw();
                    if (data.length > 0) {
                        $.each(data, function(i, val) {
                            let manageBtn = '<button class="btn btn-success btn-sm manageSearchedUserBtn text-nowrap mb-2" type="paper" user_id="'+val.id+'" > Manage </button>'
                            let deleteBtn = '<button class="btn btn-danger btn-sm deleteUserBtn text-nowrap mb-2 ms-2" data-user_id="'+val.id+'" > Delete </button>'
                            table.row.add([
                                val.name,
                                val.email,
                                val.surname,
                                manageBtn + deleteBtn,
                            ]).draw(false);
                        });
                    } else {

                    }
                },
                complete: function() {
                    swal.close();
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while processing your search. Please try again.'
                    });
                }
            })
        })

        $('#searchResultsTable').on('click', '.manageSearchedUserBtn', function(){
            let user_id = $(this).attr('user_id')
            $('#addNewUserModal').modal('show')
            $('#addUserForm')[0].reset();
            $('#saveUserBtn').attr('action','update')
            $('#addUserForm #user_id').val(user_id)
            $('#addNewUserModal .modal-title').html('Details')

            $.post(base_url+'admin/getUserById',{
                'user_id':user_id
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
                }
                if(data.is_deputy_reviewer === '1'){
                    $('#is_deputy_reviewer').prop('checked', true)
                }
                if(data.is_session_moderator === '1'){
                    $('#is_session_moderator').prop('checked', true)
                }
                let divisions = JSON.parse(data.profile.division_id);
                $.each(divisions, function(i, val){
                    $('#division_'+val).prop('checked', true)
                })
            }, 'json')
        })
    })

    $('#searchResultsTable').on('click', '.deleteUserBtn', function(){
        let user_id = $(this).data('user_id');
        alert(user_id);

        swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: base_url+'admin/user/delete',
                    type: 'POST',
                    data: {user_id: user_id},
                    success: function(response) {
                        if (response.status === 'success') {
                            swal.fire(   {
                                title: 'Deleted!',
                                text: response.message,
                                icon: 'success'
                            }).then((result)=>{
                                if (result.isConfirmed) {
                                    $('.doSearchBtn').click();
                                }
                            });
                            // Optionally, refresh the search results
                        } else {
                            swal.fire(
                                'Error!',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        swal.fire(
                            'Error!',
                            'An error occurred while deleting the user.',
                            'error'
                        );
                    }
                });
            }
        })
    })

</script>