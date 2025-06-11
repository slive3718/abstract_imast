
<?php echo view('admin/common/menu'); ?>
<?php //print_r($abstract_id);exit ; ?>
<main>
    <div class="container" style="padding-bottom: 200px">
        <?php echo view('admin/common/shortcut_link_detail_back'); ?>
        <div class="card p-lg-5 p-md-2 p-sm-1 p-xs-1 p-3 shadow">

            <p>You may upload your proposal here as a PowerPoint (doc, docx, ppt, pptx and pdf).. <two class=""></two></p>
            <p class="">The system will automatically add the date and your proposal ID proceeding the name of your file. For example, the naming protocol will be: 09252017_17-001_myfile.pdf. Please be sure not to include the name of any authors in your file name. </p>
            <p class="">These files cannot be removed. Once a new file is uploaded to the system the Program Chair will be automatically notified. </p>
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
                <input type="file" name="uploadFile" class="form-control uploadFile" id=""  >
                <p><strong>Step 2.</strong> Click on "Upload File" to upload the new file to the system server.</p>
                <button class="btn btn-primary btn-sm uploadFileBtn" individual_panel_id="<?=$individual_panel_id?>">Upload File</button>
                <h6 class="mt-4" hidden>Image Caption</h6>

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

<script>
    let attrId_array = {};

    $(function(){

        getIndividualUploads();
    //
       $('.uploadFileBtn').on('click', function(){
            let individual_panel_id = $(this).attr('individual_panel_id')
           Swal.fire({
               title: "Info",
               icon: 'info',
               html: 'We highly recommend that you use the AFS Congress Powerpoint Template that can be downloaded under the Submitter Instructions icon found above.',
               showCancelButton: true,
               confirmButtonText: "Save",
               denyButtonText: `Don't save`
           }).then((result) => {
               /* Read more about isConfirmed, isDenied below */
               if (result.isConfirmed) {
                   if(individual_panel_id){
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
                       fd.append('individual_panel_id', individual_panel_id);

                       $.ajax({
                           url : base_url+ 'admin/presentation_do_upload',
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

                       getIndividualUploads();
                   }else{
                       toastr.error('no abstract id.')
                   }
               }
           });

       })

        $('.presentationContinueBtn').on('click', function(){
            let individual_panel_id = $('.uploadFileBtn').attr('individual_panel_id')
            window.location.href = base_url+'admin/view_individual_panel/'+individual_panel_id;
        })

    //
       })

    function getIndividualUploads(){
        let individual_panel_id = $('.uploadFileBtn').attr('individual_panel_id')
       $.ajax({
           url : base_url+'admin/getIndividualUploads',
           type: 'POST',
           data: {
               'individual_panel_id': individual_panel_id
           },
           dataType:'json',
           success: function(response) {

               $('.uploadsTableBody').html('');
               $.each(response.data, function(i, item){
                   console.log(item);

                  $('.uploadsTableBody').append('<tr>' +
                      '<td>'+(i+1)+'</td>' +
                      '<td><a href="'+base_url+'/public/uploads/individual_panel/presentation/'+item.individual_panel_id+'/'+item.file_name+'" download="'+item.file_preview_name+'" target="_blank">'+item.file_preview_name+'</a></td>' +
                      '<td>'+item.file_size+'</td>' +
                      '<td>'+item.created_at+'</td>' +
                      // '<td><a href="" class="btn btn-danger btn-sm deleteUploadBtn" upload_id="'+item.id+'">Delete</a></td>' +
                      '</tr>')
               })

           }, error: function(e) {
               Swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong: ' + e.message, });
           }})
    }
    //
    //function updateCount(attrId, count) {
    //    attrId_array[attrId] = count
    //    let sum = 0;
    //    $.each(attrId_array,function(){sum+=parseFloat(this) || 0;});
    //    $('#char_counter').html(sum)
    //    $('#totalCount').val(sum)
    //    if(sum > 2500){
    //        swal.fire(
    //            'warning',
    //            'You are exceeding the limit of 2500 characters',
    //            'warning'
    //        )
    //        return false;
    //    }
    //}
</script>

