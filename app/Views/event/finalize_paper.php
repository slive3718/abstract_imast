
<link href="<?=base_url()?>/assets/css/jquery_ui_style.css" rel="stylesheet">

<?php echo view('event/common/menu'); ?>
<?php //print_r($learning_objectives); exit;?>
<style>
    .table td {
        vertical-align: middle;
    }
    .table .text-end {
        width: 250px;
    }
</style>
<main>
    <div class="container pb-5">
        <?php echo view('event/common/shortcut_link'); ?>
        <div class="card shadow">
            <div class="card-header fw-bold"> General Information  <a href="<?=base_url()?>/user/edit_papers_submission/<?=$paper_id?>" class="btn btn-sm btn-primary float-end"><i class="fas fa-edit"></i> Edit</a></div>
            <div class="card-body">
                <table class="table">
                    <tbody>
                        <tr>
                            <td class="text-end">Paper ID : </td>
                            <td ><?=$papers->id?></td>
                        </tr>
                        <tr>
                            <td style="width:250px" class="text-end">Paper Title : </td>
                            <td><?=$papers->title?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card shadow">
            <div class="card-header fw-bold"> Uploaded File(s)<?=(!isset($authorInfo)? '<span class="text-danger text-sm badge " title="Please complete requirements before finalizing abstract."><i class="fas fa-exclamation-circle" > INCOMPLETE </i></span>' :'')?>  <a href="<?=base_url()?>/user/presentation_upload/<?=$paper_id?>" class="btn btn-sm btn-primary float-end"><i class="fas fa-edit"></i> Edit</a></div>
            <div class="card-body">
                <p> (The most recent uploaded file will appear at the top of the list) </p>
                <table class="table" style="margin-bottom:0px !important">
                    <?php if(!empty($paper_uploads)):
                    foreach ($paper_uploads as $index => $uploads): ?>
                            <a href="<?=base_url($uploads['file_path'].$uploads['file_name'])?>" > <?=$uploads['file_preview_name']?></a><br>
                    <?php endforeach; endif ?>
                </table>
            </div>
        </div>
        <div class="card shadow">
            <div class="card-header fw-bold"> Author Information <?=(!isset($authorInfo)? '<span class="text-danger text-sm badge " title="Please complete requirements before finalizing abstract."><i class="fas fa-exclamation-circle" > INCOMPLETE </i></span>' :'')?>  <a href="<?=base_url()?>/user/authors_and_copyright/<?=$paper_id?>" class="btn btn-sm btn-primary float-end"><i class="fas fa-edit"></i> Edit</a></div>
            <div class="card-body">
                <table class="table" style="margin-bottom:0px !important">
                    <tbody>
                        <tr>
                            <td class="text-end" style="width:250px">
                                Author List:
                            </td>
                            <td>
                                <?php if($authorInfo):
                                foreach ($authorInfo as $author):?>
                                <?=($author['is_presenting_author'] == "Yes")? '<strong> Presenting Author </strong>: ':'<strong> Co-Author </strong>: '?>
                                    (<?=$author['author_order']?>)

                                    <?=$author['name'].' '.$author['surname'] ?><br>
                                <?php endforeach; endif; ?>
                            </td>
                        </tr>
                        <?php if($authorInfo):
                        foreach ($authorInfo as $index=>$author):
                            ?>
                        <tr >
                            <td class="text-end">(<?=($index+1)?>) <?=($author['is_presenting_author'] == "Yes")? 'Presenting Author :':'Co-Author :'?></td>
                            <td><strong><?=UcFirst($author['name']).' '.UcFirst($author['surname']) ?></strong></td>
                        </tr>
                            <tr>
                                <td class="text-end">Author Info: </td>
                                <td >
                                Address: <?=$author['address']?>
                                <?=$author['city'], $author['province'], $author['zipcode'], $author['country'] ?><br>
                                Professional Degree(s): <?=$author['deg']?><br>
                                Email: <?=$author['email']?><br>
                                Institution: <?=$author['institution']?><br>
                                Work Phone: <?=$author['phone']?><br>
<!--                                Fax: --><?php //=$author['fax']?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-end">Correspondence :</td>
                            <td><?=($author['is_correspondent'])?'Yes':'No'?></td>
                        </tr>
                        <tr>
                            <td colspan="2"><br></td>
                        </tr>

                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header fw-bold"> Paper Information <?=(!isset($papers)? '<span class="text-danger text-sm badge " title="Please complete requirements before finalizing abstract."><i class="fas fa-exclamation-circle" > INCOMPLETE </i></span>' :'')?> <a href="<?=base_url()?>/user/edit_papers_submission/<?=$paper_id?>" class="btn btn-sm btn-primary float-end"><i class="fas fa-edit"></i> Edit</a></div>
            <div class="card-body">
                <table class="table" style="border-bottom-width:4px !important">
                    <tbody>
                        <?php if($papers):
                             ?>
                            <tr>
                                <td class="text-end">Division : </td>
                                <td><?=$papers->division_name?></td>
                            </tr>
                            <tr>
                                <td class="text-end">Paper Type : </td>
                                <td><?=$papers->paper_type_name?></td>
                            </tr>
                            <tr>
                                <td class="text-end">Paper Title : </td>
                                <td><?=$papers->title?></td>
                            </tr>
                            <tr>
                                <td class="text-end">Paper Summary : </td>
                            <td><?=$papers->summary?></td>
                            </tr>
                            <tr>
                                <td class="text-end">Are you interested in submitting this paper to IJMC as well ? </td>
                                <td>
                                    <?php
                                    echo $papers->is_ijmc_interested == 0
                                        ? 'I am NOT interested in submitting this paper to IJMC'
                                        : ($papers->is_ijmc_interested == 1
                                            ? 'I am interested in submitting this paper to IJMC'
                                            : 'I have already submitted this paper to IJMC');
                                    ?>
                                </td>


                            </tr>
                    <?php endif;?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header fw-bold"> User Information </div>
            <div class="card-body">
                <table class="table">
                    <tbody>
                        <tr>
                            <td class="text-end"><strong>User/Submitter Name:</strong></td>
                            <td class="text-start"><?=$userInfo['name']. ' ' . $userInfo['surname']?></td>
                        </tr>
                        <tr>
                            <td class="text-end"><strong>User/Submitter Email: 	</strong></td>
                            <td class="text-start"><?=$userInfo['email'] ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>


        <div class="card p-4">
            <div class="col-md-12">
            
            <!-- <p class="mb-4">
                        Thank you for submitting an abstract for the 2023 Public Health in the Rockies Conference.  Abstracts will be reviewed by a diverse conference planning committee and recruited reviewers.  Based on the abstracts submitted, the planning committee will ensure that the conference program will be representative of the variety of topics and geographic areas represented in public health in Colorado and Wyoming.  Selected presenters will be notified in mid-May, 2023.<br>
                        <br>
                        If you have any questions regarding your submission, please contact:
                       <a href = "mailto: info@coloradopublichealth.org">info@coloradopublichealth.org</a> 

                  </p> -->
                  <button class="btn btn-success finalizePaperBtn" id="finalizePaperBtn" style="max-width:200px"> Finalize Paper</button>
              </div>
        </div>
    </div>
</main>

<script>
    $(function(){



        $('#finalizePaperBtn').on('click', function(){
            let authors =  `<?=isset($authors)? (count($authors)):''?>`;
            let incomplete = `<?=isset($incompleteStatus)? (json_encode($incompleteStatus)):''?>`;
            incomplete = JSON.parse(incomplete);



            authors = JSON.parse(authors)

            if(authors < 0){
                toastr.warning("Missing or Incomplete Authors")
                return false;
            }

            if(Object.keys(incomplete).length !== 0){
                $.each(incomplete, function(i ,val){
                    toastr.warning(val[0].required)
                })

                return false;
            }

            validatePaper();
         $.ajax({
             url: base_url + 'user/save_finalize_paper',
             headers: {'X-Requested-With': 'XMLHttpRequest'},
             data: {
                 'paper_id': paper_id
             },
             method: "POST",
             dataType: "json",
             beforeSend: function() {
                 Swal.fire({
                     title: 'Please Wait !',
                     html: 'Finalizing...',// add html attribute if you want or remove
                     allowOutsideClick: false,
                     onOpen: () => {
                         Swal.showLoading()
                     }
                 });
             },
             success: function (response, status) {
                 if (response.status == "200") {
                     swal.fire({
                         title:"Submitted",
                         text: "Paper Submission Finalized",
                         type: "success",
                         icon: "success",
                         confirmButtonText: 'Ok',
                     }).then((result)=> {
                         if(result.isConfirmed){
                             window.location.href = base_url+'home';
                         }
                     });
                 }else{
                     Swal.fire(
                         'Sorry',
                         'Something went wrong, please contact administrator',
                         'warning'
                     )
                 }
             }
         });
        })
    })

    function validatePaper(){

    }
</script>