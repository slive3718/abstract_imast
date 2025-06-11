
<script type="text/javascript" src="<?=base_url('assets/js/submissionFunction.js?v=1')?>"></script>

<?php //echo'<pre>'; print_r($abstract_details);exit;?>
<?php echo view('acceptance/common/menu'); ?>
<?php
// echo '<pre>';
// print_r($acceptanceDetails);
// exit ;
?>
<main>
    <div class="container-fluid">
                <div>
            <h6 class="fw-bolder"> Abstract ID:  <?=$abstract_id?></h6>
            <?php if(isset($disclosure_details) && !empty($disclosure_details)) :
               foreach(($disclosure_details) as $author) :
                    if($author['is_presenting_author']=='Yes'):
               ?>
            <h6 class="fw-bolder"> Presenting Author:  <?=$author['author']['name'].' '.$author['author']['surname']?></h6>
            <?php 
            endif;
            endforeach;
            endif
            ?>
            <h6 class="fw-bolder"> Speaking Date and Time: <?=date("F d, Y", strtotime($abstract_details->presentation_date))?> at <?=date("H:i A", strtotime($abstract_details->presentation_start_time))?> </h6>
            <h6 class="fw-bolder"> Accepted Session Type: <?=isset($abstract_preference)?$abstract_preference:''?></h6>
        </div>
        
        <div class="card p-5">
            <form id="uploadCVForm" name="form1" onsubmit="return false;" class="ED_jUniform" style="line-height:150%;"
                  novalidate="novalidate">
                <input type="hidden" name="abstract_id" id="" value="<?=$abstract_id?>">
                <p class="h6 ">To comply with accredi.tion, all presenters must log into the Speaker Centre to upload their CVs (no more than 2 pages). 
                    CV must be in PDF format. The system will automatically add the presenter last name to the beginning of the PDF file name.
                    For example: 'MyCV.pdf will become 'Jon_MyCV.pdf.
                    <p>

                <div class="mb-3">
                <label for="formFile" class="form-label">Upload CV here:</label>
                    <input name="CVFile" class="form-control" type="file" accept=".pdf" id="CVFile">
                </div>
                <div id="uploadedCVDiv" class="<?=(!empty($acceptanceDetails) && $acceptanceDetails->cv_saved_name !== '')?"d-block": "d-none"?>">
                   <?=(!empty($acceptanceDetails) && $acceptanceDetails->cv_saved_name !== '')?"<b>Uploaded File: </b><a  href='".base_url($acceptanceDetails->cv_file_path).$acceptanceDetails->cv_saved_name."' target='_blank'>".$acceptanceDetails->cv_saved_name.'</a>': "d-none"?>
                </div>
                <button class="mt-5 btn btn-primary uploadCVBtn">Upload <i class="fas fa-upload"></i></button>
               
                <div class="mt-5">
                    <button class="btn btn-success continueBtn">Save and Continue</button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
    let baseUrlAcceptance = "<?=base_url().'/'.$event->uri.'/acceptance/'?>";
    $(function(){
  

        $('.uploadCVBtn').on('click', function(e){
            e.preventDefault();
            let formData = new FormData(document.getElementById('uploadCVForm'));
            // console.log(formData);return false;
            $.ajax({        // Ajax request
            url: baseUrlAcceptance+'curriculumVitaeDoUpload',
            type: 'POST',
            dataType: 'json',
            processData: false, // Prevent jQuery from converting data to a query string
            contentType: false, // Let the server handle the data as FormData
            data: formData,
            success: function(response) {
                console.log(response);
                if(response.status == "success"){
                    $('#uploadedCVDiv').removeClass('d-none').html('<b>Uploaded File: </b>'+ response.data.cv_saved_name)
                       
                    swal.fire({
                        'title': 'success',
                        'html': response.msg,
                        'icon': 'success'
                    })
                }else if(response.status == "204"){
                    swal.fire({
                        'title': 'info',
                        'html': response.msg,
                        'icon': 'info'
                    })
                }
            },
            error: function(xhr, status, error) {
                // Handle errors
                console.error('Error:', status, error);
            }
            });
           
        })

        $('.continueBtn').on('click', function(){
            if(!$('#uploadedCVDiv').hasClass('d-none')){
                window.location.href= baseUrlAcceptance+"acceptanceMenu/"+<?=$abstract_id?>
            }else{
                 swal.fire({
                        'title': 'warning',
                        'html': "Missing Presentation File, Please upload to continue",
                        'icon': 'info'
                    })
                return false;
            }
        })
    })
</script>
