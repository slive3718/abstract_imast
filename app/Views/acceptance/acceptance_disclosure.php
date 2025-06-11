
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
            <form id="acceptanceForm" name="form1" onsubmit="return false;" class="ED_jUniform" style="line-height:150%;"
                  novalidate="novalidate">
              
                  <h6> Disclosures can be done here <a href="https://disclosure.amedcoedu.com/events/4696" target="_blank"> Disclosure Page.</a></h6>
              
                  <div class="mt-5">
                    <button class="btn btn-success saveConfirmationBtn">Save and Continue</button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
    let baseUrlAcceptance = "<?=base_url().'/'.$event->uri.'/acceptance/'?>";
    $(function(){
        $('.saveConfirmationBtn').on('click', function(){
            Swal.fire({
                title: 'Are you sure?',
                text: "This will mark disclosure as done!",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, I am done!'
                }).then((result) => {
                if (result.isConfirmed) {
                     $.post(baseUrlAcceptance+'saveAcceptanceDisclosure/'+<?=$abstract_id?>, function(data){
                        data = JSON.parse(data)
                        if(typeof data === 'number'){
                            Swal.fire({
                                'title':'Success',
                                'html': "Disclosure Saved",
                                'icon': 'success'
                            }).then((result)=>{
                                if(result.isConfirmed) {
                                    window.location.href= baseUrlAcceptance+"acceptanceMenu/"+<?=$abstract_id?>
                                }
                            })
                        }
                    })
                    
                    }
                })
           
        })
    })
</script>