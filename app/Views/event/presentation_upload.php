
<script  type="text/javascript" src="<?=base_url('assets/js/submissionFunction.js?v=1')?>"></script>



<?php echo view('event/common/menu'); ?>
<?php //print_r($abstract_id);exit ; ?>
<main>
    <div class="container" style="padding-bottom: 200px">
        <?php echo view('event/common/shortcut_link'); ?>
        <div class="card p-lg-5 p-md-2 p-sm-1 p-xs-1 p-3 shadow">
            <p>You may upload one associated file or graphic in jpeg format here. If you have no image to include then 'Continue' to next page.</p>
           <div class="card p-3">
                <p class="fw-bold">Current uploaded files</p>
                <table class="table table-striped table-bordered table-hover uploadsTable">
                    <thead>
                        <tr>
                            <td>File Count</td>
                            <td>File Name</td>
                            <td>File Size</td>
                            <td>Date Uploaded</td>
<!--                            <td>Action</td>-->
                        </tr>
                    </thead>
                    <tbody class="uploadsTableBody">
<!--                  Fill with ajax-->
                    </tbody>
                </table>

            </div>
            <div class="my-4">
                <p><strong>Step 1.</strong> Click on "Choose File" and navigate the file you wish to upload.</p>
                <input type="file" name="uploadFile" accept=".jpeg, .JPEG, .jpg, .JPG" class="form-control uploadFile" id=""  >
                <p><strong>Step 2.</strong> Click on "Upload File" to upload the new file to the system server.</p>
                <button class="btn btn-primary btn-sm uploadFileBtn">Upload File</button> <br><br>


                <div class="text-center m-auto p-4" style="width: 600px; border:4px dotted black">
                    Total Abstract Body Count: <span id="abstract_body_count">0 characters</span> <br>
                    Image Caption Body Count: <span id="image_caption_body_count" >0 characters</span><br>
                    Limit: 2500
                </div>

                <label for="image_caption" class="fw-bolder"> Image Caption</label>
                <textarea name="image_caption" id="image_caption" class="form-control countWords" rows="4" placeholder="Please type here for the caption..."><?= !empty($paper) && $paper['image_caption'] ? $paper['image_caption']: ''?></textarea>
                <label class="counted_words fw-bolder"></label>

                <p class="mt-4">Step 3. Finished Uploading, continue.</p>
                <button class="btn btn-success btn-sm presentationContinueBtn">Continue</button>
            </div>


<!--            <div class="mt-5">-->
<!--                <h6 class="fw-bolder">Option Two: NO, I do not have files to upload:</h6>-->
<!---->
<!--                <p class="fw-bold">Click on the button, "No uploads, proceed to preview page"</p>-->
<!--                <button class="btn btn-success btn-sm noUploadBtn">No Uploads, proceed to next page</button>-->
<!--            </div>-->
        </div>

    </div>
</main>

<!-- Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script src="<?=base_url()?>assets/js/helpers.js"></script>
<script>
    let attrId_array = {};
    let current_user_id = "<?=session('user_id')?>"
    let previousUrl = "<?= $previous_url ?? ''?>"
    let previousPage = "<?= $previous_page ?? ''?>"
    $(function(){

        getPaperUploads();

        $('.uploadFileBtn').on('click', function(){
            if(paper_id){
                let file = $('.uploadFile')[0].files[0];
                let fd = new FormData();

                if (!file) {
                    // Display a SweetAlert message if no file is selected
                    Swal.fire({
                        title: "Info",
                        icon: 'info',
                        html: 'No file selected!',
                    });
                    // Prevent further actions
                    return false;
                }
                fd.append('file', file);
                fd.append('paper_id', paper_id);

                $.ajax({
                    url : base_url+ 'user/presentation_do_upload',
                    type: 'POST',
                    data: fd,
                    contentType: false,
                    processData: false,
                    mimeType: "multipart/form-data",
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Uploading...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        console.log(response);
                        response = JSON.parse(response);
                        Swal.close();
                        $('.uploadFile').val('');
                        if(response.status == 200){
                            Swal.fire({ icon: 'success', title: 'success', text: 'Presentation file uploaded successfully' });
                            getPaperUploads();
                        } else if(response.status == 401){
                            Swal.fire({ icon: 'error', title: 'Warning', text: response.message });
                        }
                    }, error: function(e) {
                        Swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong: ' + e.message, });
                    }})
            }else{
                toast.r('no abstract id.')
            }
        })
        //
        // $('.uploadFileBtn').on('click', function(){
        //
        //     Swal.fire({
        //         title: "Info",
        //         icon: 'info',
        //         html: 'We highly recommend that you use the AFS Congress Powerpoint Template that can be downloaded under the Submitter Instructions icon found above.',
        //         showCancelButton: true,
        //         confirmButtonText: "Save",
        //         denyButtonText: `Don't save`
        //     }).then((result) => {
        //         /* Read more about isConfirmed, isDenied below */
        //         if (result.isConfirmed) {
        //             if(paper_id){
        //                 let file = $('.uploadFile')[0].files[0];
        //                 let fd = new FormData();
        //
        //                 if (!file) {
        //                     // Display a SweetAlert message if no file is selected
        //                     Swal.fire({
        //                         title: "Info",
        //                         icon: 'info',
        //                         html: 'No file selected!',
        //                     });
        //                     // Prevent further actions
        //                     return false;
        //                 }
        //                 fd.append('file', file);
        //                 fd.append('paper_id', paper_id);
        //
        //                 $.ajax({
        //                     url : base_url+ 'user/presentation_do_upload',
        //                     type: 'POST',
        //                     data: fd,
        //                     contentType: false,
        //                     processData: false,
        //                     mimeType: "multipart/form-data",
        //                     beforeSend: function() {
        //                         Swal.fire({
        //                             title: 'Uploading...',
        //                             allowOutsideClick: false,
        //                             didOpen: () => {
        //                                 Swal.showLoading();
        //                             }
        //                         });
        //                     },
        //                     success: function(response) {
        //                         console.log(response);
        //                         response = JSON.parse(response);
        //                         Swal.close();
        //                         $('.uploadFile').val('');
        //                         if(response.status == 200){
        //                             Swal.fire({ icon: 'success', title: 'success', text: 'Presentation file uploaded successfully' });
        //                             getPaperUploads();
        //                         } else if(response.status == 401){
        //                             Swal.fire({ icon: 'error', title: 'Warning', text: response.message });
        //                         }
        //                     }, error: function(e) {
        //                         Swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong: ' + e.message, });
        //                     }})
        //             }else{
        //                 toast.r('no abstract id.')
        //             }
        //         }
        //     });
        //
        //
        // })

        $('.presentationContinueBtn').on('click', function(e){
            e.preventDefault();


            let abstract_body_count = parseInt(`<?= intval($paper['abstract_body_count']) ?>`) || 0;
            let image_caption_body_count = parseInt($('#image_caption_body_count').text()) || 0;

            console.log(image_caption_body_count + abstract_body_count);
            if ((image_caption_body_count + abstract_body_count) > 2500) {
                toastr.error('Total Words Count Exceed!');
                return false;
            }


            $.ajax({
                url: "<?= base_url().'user/update_paper_ajax' ?>",
                type: "POST", // Use "GET" if your backend expects it
                data: {
                    'image_caption': $('#image_caption').val(), // Get the value properly
                    'paper_id': "<?= $paper_id ?>",
                    'image_upload_finished': 1,
                },
                dataType: "json", // Expecting JSON response
                success: function(response) {
                    if (response.status === 200) {

                        if((previousPage === 'finalize_paper' || previousPage === 'submission_menu') && previousUrl) {
                            return window.location.href = previousUrl
                        }

                        window.location.href = "<?= base_url() ?>/user/finalize_paper/<?= $paper_id ?>";
                    } else {
                        alert("Error updating paper: " + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                }
            });

        })

        $('.uploadsTableBody').on('click', '.deleteUploadBtn',function(e) {
            e.preventDefault();
            Swal.fire({
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
                        url: base_url + 'user/delete_presentation_upload',
                        type: 'POST',
                        data: {
                            'upload_id': $(this).attr('upload_id')
                        },
                        dataType: 'json',
                        success: function (response) {
                            console.log(response);
                            if (response.status == 200) {
                                getPaperUploads();
                                Swal.fire(
                                    'Deleted!',
                                    'Your file has been deleted.',
                                    'success'
                                )
                            }

                        }, error: function (e) {
                            Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong: ' + e.message,});
                        }
                    })

                }
            })

        })

        image_caption_counter();
    })

    function image_caption_counter(){
        WordCounterHelper.init(
            'textarea.countWords',  // Textarea selector
            '.counted_words',       // Word count display
            '#image_caption_body_count' // Total word count display
        );

        let abstract_body_character_count = `<?=$paper['abstract_body_count']?>`;
        $('#abstract_body_count').html(abstract_body_character_count)

        $('textarea.countWords').on('input', function(){
            let abstract_body_count = $('#abstract_body_count').text();
            let image_caption_body_count = $('#image_caption_body_count').text();

            if(parseInt(abstract_body_count) + parseInt(image_caption_body_count) > 2500){
                toastr.error('Total of description already exceed 2500 words!')
                $('#abstract_body_count').closest('div').addClass('text-danger')
                $('#abstract_body_count').closest('div').removeClass('text-success')
            }else{
                $('#abstract_body_count').closest('div').addClass('text-success')
                $('#abstract_body_count').closest('div').removeClass('text-danger')
            }
        })

        $('textarea.countWords').trigger('input');
    }

    function getPaperUploads(){

        $.ajax({
           url : base_url+'user/getPaperUploads',
           type: 'POST',
           data: {
               'paper_id': paper_id
           },
           dataType:'json',
           success: function(response) {

               $('.uploadsTableBody').html('');
               $.each(response.data, function(i, item){
                   console.log(item);

                  $('.uploadsTableBody').append('<tr>' +
                      '<td>'+(i+1)+'</td>' +
                      '<td><a href="'+base_url+'/public/uploads/presentation/'+item.paper_id+'/'+item.file_name+'" download="'+item.file_preview_name+'" target="_blank">'+item.file_preview_name+'</a></td>' +
                      '<td>'+item.file_size+'</td>' +
                      '<td>'+item.created_at+'</td>' +
                      '<td><a href="" class="btn btn-danger btn-sm deleteUploadBtn" upload_id="'+item.id+'">Delete</a></td>' +
                      '</tr>')
               })

           }, error: function(e) {
               Swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong: ' + e.message, });
           }})
    }
</script>

