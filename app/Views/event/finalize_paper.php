
<link href="<?=base_url()?>/assets/css/jquery_ui_style.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.5/js/lightbox.min.js" integrity="sha512-KbRFbjA5bwNan6DvPl1ODUolvTTZ/vckssnFhka5cG80JVa5zSlRPCr055xSgU/q6oMIGhZWLhcbgIC0fyw3RQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.5/css/lightbox.min.css" integrity="sha512-xtV3HfYNbQXS/1R1jP53KbFcU9WXiSA1RFKzl5hRlJgdOJm4OxHCWYpskm6lN0xp0XtKGpAfVShpbvlFH3MDAA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
<main >
        <div class="container pb-5 ">
            <?php echo view('event/common/shortcut_link'); ?>
                <div class="card p-4">
                    <div class="col-md-12">

                        <!-- <p class="mb-4">
                                    Thank you for submitting an abstract for the 2023 Public Health in the Rockies Conference.  Abstracts will be reviewed by a diverse conference planning committee and recruited reviewers.  Based on the abstracts submitted, the planning committee will ensure that the conference program will be representative of the variety of topics and geographic areas represented in public health in Colorado and Wyoming.  Selected presenters will be notified in mid-May, 2023.<br>
                                    <br>
                                    If you have any questions regarding your submission, please contact:
                                   <a href = "mailto: info@coloradopublichealth.org">info@coloradopublichealth.org</a>

                              </p> -->
                        <button class="btn btn-success finalizePaperBtn" style="max-width:200px"> Finalize</button>
                        <button class="btn btn-primary" onclick="printDiv('printSection')">Print</button>
                    </div>
                </div>
            <div id="printSection">
                <div class="card shadow">
                    <div class="card-header fw-bold"> General Information  <a href="<?=base_url()?>/user/edit_papers_submission/<?=$paper_id?>" class="btn btn-sm btn-primary float-end btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</a></div>
                    <div class="card-body">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="text-end">Paper ID : </td>
                                    <td ><?=$papers->custom_id?></td>
                                </tr>
                                <tr>
                                    <td style="width:250px" class="text-end">Submission Title : </td>
                                    <td><?=$papers->title?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card shadow">
                    <div class="card-header fw-bold"> Author Information <?=(isset($incomplete['author'])? '<span class="text-danger text-sm badge " title="Please complete requirements before finalizing abstract."><i class="fas fa-exclamation-circle" > INCOMPLETE </i></span>' :'')?>  <a href="<?=base_url()?>/user/authors_and_copyright/<?=$paper_id?>" class="btn btn-sm btn-primary float-end btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</a></div>
                    <div class="card-body">
                        <table class="table" style="margin-bottom:0px !important">
                            <tbody>
                                <tr>
                                    <td class="text-end" style="width:250px;">
                                        Author List:
                                    </td>
                                    <td>
                                        <?php if(!empty($authors)):
                                            foreach ($authors as $author): ?>
                                            <?=($author['is_presenting_author'] == "Yes")? '<strong> Presenting Author </strong>: ':'<strong> Co-Author </strong>: '?>
                                            <?=$author['author_order'] ? '('.$author['author_order']. ')' : ''?>
                                            <?=$author['name'].' '.$author['surname']?>  <br>
                                        <?php endforeach; endif; ?>
                                    </td>
                                </tr>
                                <?php if(!empty($authors)):
                                foreach ($authors as $index => $author):
                                    $designations = '';
                                    if (is_array($author['designations'])) {
                                        foreach ($author['designations'] as $i => $designation){
                                            $designationCount = count($author['designations']);
                                            $designations = '';

                                            foreach ($author['designations'] as $i => $designation) {
                                                if($designation['name'] == 'Other')
                                                    $designation['name'] = $author['other_designation'];
                                                if($designation['name'] == 'None')
                                                    $designation['name'] = '';
                                                $designations .= $designation['name'];

                                                if ($designationCount > 1 && $i > 0 && $i < ($designationCount - 1)) {

                                                    $designations .= ', ';
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                <tr >
                                    <td class="text-end">(<?=($index+1)?>) <?=($author['is_presenting_author'] == "Yes")? 'Presenting Author :':'Co-Author :'?></td>
                                    <td><strong><?=UcFirst($author['name']).' '.UcFirst($author['middle_name']).' '.UcFirst($author['surname']) ?><?= ($designations ? ', '. $designations : '')?> </strong></td>
                                </tr>
                                    <tr>
                                        <td class="text-end" style="vertical-align: top">Author Info: </td>
                                        <td >
                                        Email: <?=$author['email']?><br>
                                        Institution: <?=$author['institution_name'].($author['institution_city'] ? ', '.$author['institution_city']: '')  . ($author['institution_country'] ? ', '.$author['institution_country'] : '')?><br>
                                        Work Phone: <?=$author['phone']?><br>
                                        Cell Phone: <?=$author['cellphone']?><br>

                                        <?php if(!empty($study_groups) && !empty($author['study_group_affiliation'])): ?>
                                        <?php foreach ($study_groups as $study_group): ?>
                                        <?php if($author['study_group_affiliation'] == $study_group['id']): ?>
                                        Study Group: <?=$study_group['surname']?>
                                        <?php endif ?>
                                        <?php endforeach; ?>
                                        <?php else: ?>
                                        Study Group: None
                                        <?php endif; ?>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-end">Correspondence :</td>
                                    <td><?=($author['is_correspondent'] == 'Yes' ? 'Yes':'No')?></td>
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
                    <div class="card-header fw-bold"> Paper Information <?=(!isset($papers)? '<span class="text-danger text-sm badge " title="Please complete requirements before finalizing abstract."><i class="fas fa-exclamation-circle" > INCOMPLETE </i></span>' :'')?> <a href="<?=base_url()?>/user/edit_papers_submission/<?=$paper_id?>" class="btn btn-sm btn-primary float-end btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</a></div>
                    <div class="card-body">
                        <table class="table" style="border-bottom-width:4px !important">
                            <tbody>
                            <?php if ($papers): ?>
                                <tr>
                                    <td class="text-end"  style="width:250px;">Session Types : </td>
                                    <td>Consider for Podium Presentation</td>
                                    <td style="width: 100px;"><a class="float-end btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit </a></td>
                                </tr>
                                <tr>
                                    <td class="text-end">Basic Science Proposal Format :</td>
                                    <td><?= htmlspecialchars($papers->basic_science_format) ?></td>
                                    <td style="width: 100px;"><a href="<?=base_url().'user/edit_papers_submission/'.$paper_id?>" class="float-end btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit </a></td>
                                </tr>
                                <tr>
                                    <td class="text-end">Previous Publication :</td>
                                    <td><?= htmlspecialchars($papers->previous_presentation) ?></td>
                                    <td style="width: 100px;"><a href="<?=base_url().'user/edit_papers_submission/'.$paper_id?>" class="float-end btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit </a></td>
                                </tr>

                                <tr>
                                    <td class="text-end">Abstract Category :</td>
                                    <td><?= htmlspecialchars(!empty($categories) ? isset(($categoriesById = array_column($categories, 'name', 'category_id'))[$papers->abstract_category]) ? $categoriesById[$papers->abstract_category] : 'N/A' : '') ?></td>
                                    <td style="width: 100px;"><a href="<?=base_url().'user/edit_papers_submission/'.$paper_id?>" class="float-end btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit </a></td>
                                </tr>
                                <tr>
                                    <td class="text-end">Abstract Title :</td>
                                    <td><?= htmlspecialchars($papers->title) ?></td>
                                    <td style="width: 100px;"><a href="<?=base_url().'user/edit_papers_submission/'.$paper_id?>" class="float-end btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit </a></td>
                                </tr>
                                <tr>
                                    <td class="text-end">Hypothesis :</td>
                                    <td><?= htmlspecialchars($papers->hypothesis) ?></td>
                                    <td style="width: 100px;"><a href="<?=base_url().'user/edit_papers_submission/'.$paper_id?>" class="float-end btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit </a></td>
                                </tr>
                                <tr>
                                    <td class="text-end">Study Design :</td>
                                    <td><?= htmlspecialchars($papers->study_design) ?></td>
                                    <td style="width: 100px;"><a href="<?=base_url().'user/edit_papers_submission/'.$paper_id?>" class="float-end btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit </a></td>
                                </tr>
                                <tr>
                                    <td class="text-end">Introduction :</td>
                                    <td><?= htmlspecialchars($papers->introduction) ?></td>
                                    <td style="width: 100px;"><a href="<?=base_url().'user/edit_papers_submission/'.$paper_id?>" class="float-end btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit </a></td>
                                </tr>
                                <tr>
                                    <td class="text-end">Methods :</td>
                                    <td><?= htmlspecialchars($papers->methods) ?></td>
                                    <td style="width: 100px;"><a href="<?=base_url().'user/edit_papers_submission/'.$paper_id?>" class="float-end btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit </a></td>
                                </tr>
                                <tr>
                                    <td class="text-end">Results :</td>
                                    <td><?= htmlspecialchars($papers->results) ?></td>
                                    <td style="width: 100px;"><a href="<?=base_url().'user/edit_papers_submission/'.$paper_id?>" class="float-end btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit </a></td>
                                </tr>
                                <tr>
                                    <td class="text-end">Conclusion :</td>
                                    <td><?= htmlspecialchars($papers->conclusions) ?></td>
                                    <td style="width: 100px;"><a href="<?=base_url().'user/edit_papers_submission/'.$paper_id?>" class="float-end btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit </a></td>
                                </tr>
                                <tr>
                                    <td class="text-end">Minimum Follow-up Period :</td>
                                    <td><?= htmlspecialchars($papers->min_follow_up_period) ?></td>
                                    <td style="width: 100px;"><a href="<?=base_url().'user/level_of_evidence/'.$paper_id?>" class="float-end btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit </a></td>
                                </tr>
                                <tr>
                                    <td class="text-end">SRS Grant Funded? :</td>
                                    <td><?= htmlspecialchars($papers->is_srs_funded) ?></td>
                                    <td style="width: 100px;"><a href="<?=base_url().'user/level_of_evidence/'.$paper_id?>" class="float-end btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit </a></td>
                                </tr>
                            <?php if($papers->is_srs_funded == "Yes") : ?>
                                <tr>
                                    <td class="text-end">Primary Investigator :</td>
                                    <td><?= htmlspecialchars($papers->primary_investigator) ?></td>
                                    <td style="width: 100px;"><a href="<?=base_url().'user/level_of_evidence/'.$paper_id?>" class="float-end btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit </a></td>
                                </tr>
                                <tr>
                                    <td class="text-end">Grant Year :</td>
                                    <td><?= htmlspecialchars($papers->grant_year) ?></td>
                                    <td style="width: 100px;"><a href="<?=base_url().'user/level_of_evidence/'.$paper_id?>" class="float-end btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit </a></td>
                                </tr>
                            <?php endif ?>
                                <tr>
                                    <td class="text-end" style="vertical-align:top">Image Caption :</td>
                                    <td>
                                        <div>
                                            <div>
                                                <?= htmlspecialchars($papers->image_caption) ?>
                                            </div>
                                            <?php if (!empty($paper_uploads)): ?>
                                                <?php foreach ($paper_uploads as $index => $uploads): ?>
                                                    <div class="mb-3 text-center" style="width: 100px;">
                                                        <a href="<?= base_url($uploads['file_path'] . $uploads['file_name']) ?>" data-lightbox="image-<?= $paper_id ?>">
                                                            <img src="<?= base_url($uploads['file_path'] . $uploads['file_name']) ?>" class="img-fluid d-block mx-auto" style="width: 100px;">
                                                        </a>
                                                    </div>
                                                    <a class="d-block small mt-1" href="<?= base_url($uploads['file_path'] . $uploads['file_name']) ?>" download="<?=$uploads['file_preview_name'] ?>">
                                                        <?= htmlspecialchars($uploads['file_preview_name']) ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    <td style="width: 100px;">
                                        <a href="<?=base_url()?>/user/presentation_upload/<?=$paper_id?>" class="float-end btn btn-primary btn-sm" ><i class="fas fa-edit"></i> Edit</a>
                                    </td>
                                </tr>
                            <?php endif; ?>
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
                                    <td class="text-end"  style="width:250px;"><strong>User/Submitter Name:</strong></td>
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
            </div>

<!--            For testing preview Image to send -->
<!--            <div id="preview-container" style="margin-top: 20px;"></div>-->

            <div class="card p-4">
                <div class="col-md-12">

                <!-- <p class="mb-4">
                            Thank you for submitting an abstract for the 2023 Public Health in the Rockies Conference.  Abstracts will be reviewed by a diverse conference planning committee and recruited reviewers.  Based on the abstracts submitted, the planning committee will ensure that the conference program will be representative of the variety of topics and geographic areas represented in public health in Colorado and Wyoming.  Selected presenters will be notified in mid-May, 2023.<br>
                            <br>
                            If you have any questions regarding your submission, please contact:
                           <a href = "mailto: info@coloradopublichealth.org">info@coloradopublichealth.org</a>

                      </p> -->
                      <button class="btn btn-success finalizePaperBtn" id="finalizePaperBtn" style="max-width:200px"> Finalize</button>
                    <button class="btn btn-primary" onclick="printDiv('printSection')">Print</button>
                </div>
            </div>
        </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

<script>
    $(function(){



        $('.finalizePaperBtn').on('click', function(){
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
            // Get the content of the 'printSection'
            const htmlContent = document.getElementById('printSection').innerHTML;

            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = htmlContent;

            const editButtons = tempDiv.querySelectorAll('a');
            editButtons.forEach(button => {
                button.remove()
            });

            const updatedHtmlContent = tempDiv.innerHTML;

            sendDivAsImage('printSection').then(function(response){
                $.ajax({
                    url: base_url + 'user/save_finalize_paper',
                    headers: {'X-Requested-With': 'XMLHttpRequest'},
                    data: {
                        'paper_id': paper_id,
                        'html': updatedHtmlContent,
                        'preview_image': response
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
                                text: "Your submission has been finalized and a confirmation email has been sent out",
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
    })


    function printDiv(divId) {
        window.onbeforeprint = function() {
            $('a:has(i.fas.fa-edit)').hide();
        };
        window.onafterprint = function() {
            $('a:has(i.fas.fa-edit)').show();
        };


        let printContents = document.getElementById(divId).innerHTML;
        let originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload(); // Reload to restore original content
    }

    function sendDivAsImage(divId) {
        const targetDiv = document.getElementById(divId);

        // Hide edit buttons before capture
        $(targetDiv).find('a:has(i.fas.fa-edit)').hide();

        return html2canvas(targetDiv).then(canvas => {
            // Show them back after capture
            $(targetDiv).find('a:has(i.fas.fa-edit)').show();

            let imageData = canvas.toDataURL('image/png');7
            // <div id="preview-container" style="margin-top: 20px;"></div> // for testing preview imageData

            return  imageData;
        });
    }



</script>