function importUsers(){
    let custom_modal = $('#custom_modal');


    let modal_body = custom_modal.find('.modal-body');
    let modal_body_content = `
        <div class="mb-3">
            <label for="user_upload" class="form-label fw-bold">File Upload</label>
            <input type="file" name="user_import_file" id="user_import_file" class="form-control">
            
            <button class="btn btn-primary mt-2" onClick="doImportUsers();"> Upload </button>
        </div>
    `;
    modal_body.html(modal_body_content)
    custom_modal.modal('show')
}

function doImportUsers() {
    let formData = new FormData();
    let file = $('#user_import_file').prop('files')[0]; // Get the file object

    if (file) {

        Swal.fire({
            title: "Confirm Import.",
            text: "This will update users with existing email addresses and create new users for email addresses that do not already exist.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, continue import!"
        }).then((result) => {
            if (result.isConfirmed) {

                formData.append('user_import_file', file);

                Swal.fire({
                    title: 'Importing, Please wait....',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                $.ajax({
                    url: base_url+ '/admin/importUsers', // Adjust the URL to your endpoint
                    method: 'POST',
                    data: formData,
                    processData: false, // Prevent jQuery from processing the data
                    contentType: false, // Let the browser set the content type automatically
                    success: function(response) {
                        if (response.status === 200) {
                            Swal.fire({
                                title: "Import success!",
                                text: "Users imported successfully!",
                                icon: "success"
                            });
                            window.location.reload(); // Refresh the page if needed
                        } else {
                            Swal.fire({
                                title: "Failed importing",
                                text: response.message || 'Failed to import users.',
                                icon: "error"
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        alert('An error occurred while importing users.');
                    }
                });

            }
        });


    } else {
        alert('Please select a file to upload.');
    }
}
