
<!--<script  type="text/javascript" src="--><?//=base_url('assets/js/submissionFunction.js')?><!--"></script>-->


<?php // echo "<pre>"; print_r($author_details);exit;?>
<?php echo view('author/common/menu'); ?>

<main>
    <div class="container-fluid pb-5">

        <div class="row">
            <div class="col-md-12 p-0">
                <img id="main-banner" src="<?=$event->main_banner?>" class="img-fluid figure-img" alt="Main Banner"/>
            </div>
            <hr />
        </div>

        <div class="row">
            <div class="col-md-12 text-center text-sm-start">
                <h4><strong><?=$event->name?></strong></h4>
                <h6 class="mb-0"><strong><?=gmdate('F j', $event->start_timestamp)?>-<?=gmdate('j, Y', $event->end_timestamp)?></strong></h6>
                <h6 class="mt-0"><strong><?=$event->city?>, <?=$event->state?></strong></h6>
            </div>
        </div>

        <div class=" card p-5">
            <div class="row">
                <div class="col">
                <h4 class="fw-bold"> Preview </h4>
                <hr />
                <p> You must click on the 'Finalize Disclosure' button to complete your disclosure and receive your confirmation email.</p>

                <a  class="btn btn-primary btn-sm finalizeAuthorDisclosureBtn"> Finalize Disclosure </a>

                </div>
            </div>
            <div class="row">
                <div class="col disclosure_data">
                    <h5 class="fw-bold"> Disclosure Information for  <?=(!empty(session('name') )? Ucfirst(session('name')) : '')?> <?=(!empty(session('surname') )? Ucfirst(session('surname')) : '')?></h5>
                    <table class="table table-striped">
                        <tbody>
                        <tr >
                            <td style="width:200px">Financial Disclosure : </td>
                            <td><?=(isset($author_details) ? (($author_details[0]->financial_relationship == 1) ? 'I have a personal financial relationship with an ineligible company producing goods or services.' :
                                'I have NO financial relationship(s) with an ineligible company producing healthcare goods or services.'):'');?></td>

                        </tr>
                        <tr class="">
                            <td></td>
                            <td class=" my-2" style="max-width:1200px" >
                                <?php if(isset($author_organizations) && !empty($author_organizations)) : ?>
                        <?php foreach($author_organizations as $index=>$organization) :?>
                            <strong> # <?=($index+1)?> Name of Corporate Organization :</strong> <?=($organization->organization_id == 0)? $organization->other_organization:$organization->name?><br>
                        <br>
                            <strong> Type of Affiliation/Financial Interest </strong>
                                <?php foreach ($organization->author_affiliations as $author_affiliations) :?>
                                <table class="table table-bordered table-striped">
                                    <tbody>
                                    <tr>
                                        <td style="width:30px"><input type="checkbox" checked onclick="return false;"> </td>
                                        <td> <label><?=$author_affiliations->name?></label></td>
                                    </tr>
                                    </tbody>
                                </table>

                                <?php endforeach?>
                        <?php endforeach ?>
                                <?php endif ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Declaration</td>
                            <td>
<!--                                ######-->
                                <table class="table table-bordered">
                                <?php $accepted='';
                                        if(isset($declarations)):
                                        if(isset($author_details)){
                                            if($author_details[0]->is_declaration_accepted){
                                                $accepted = "checked";
                                            }else{
                                                $accepted ='';
                                            }
                                        }
                                        foreach($declarations as $declaration): ?>
                                <tr>
                                    <td style="width:30px"><input type="checkbox" class="declarationCheckbox" onclick="return false;" id="declarationCheckbox_<?=$declaration->id?>" declaration_id = "<?=$declaration->id?>" <?=$accepted?>></td>
                                    <td><?=$declaration->content?></td>
                                </tr>

                        <?php endforeach; endif; ?>
                                </table>
<!--#########-->
                            </td>
                        </tr>
                        <tr>
                            <td> <label for="electronicSignature"> Electronic Signature:</label></td>
                            <td><u><i><?=(isset($author_details)?$author_details[0]->electronic_signature:'')?></i></u>
                                <?php if(isset($author_details)): ?>
                                    <?php if ($author_details[0]->update_date_time !== null) {
                                        echo date('m/d/y', strtotime($author_details[0]->update_date_time));
                                    }else{
                                        echo date('m/d/y', strtotime($author_details[0]->submit_date_time));
                                    } ?>
                                <?php endif;?>
                         </td>
                        </tr>

                        </tbody>
                    </table>
                    <a  class="btn btn-primary btn-sm finalizeAuthorDisclosureBtn"> Finalize Disclosure </a>
                </div>
            </div>
        </div>

    </div>
</main>
<script>

    $(function(){
        let on_error = false;
        $('.finalizeAuthorDisclosureBtn').on('click', function(e){

            e.preventDefault();

            $('.declarationCheckbox').each(function(i, obj){
                // console.log(obj.checked);
                if(this.checked == false){
                    console.log(on_error);
                    toastr.error ('All declaration should be accepted.');
                    on_error = 'true';
                }
            })
            if(on_error == 'true'){
                return false;
            }else{
                window.location.href="<?=base_url()?>/author/finalize";
            }
        })
    })
</script>
