

<?php //print_r($author_details);exit;?>
<?php echo view('author/common/menu'); ?>

<main>
    <div class="container-fluid">

<!--        <div class="row">-->
<!--            <div class="col-md-12 text-center text-sm-start">-->
<!--                <h4><strong>--><?php //=$event->name?><!--</strong></h4>-->
<!--                <h6 class="mb-0"><strong>--><?php //=gmdate('F j', $event->start_timestamp)?><!-----><?php //=gmdate('j, Y', $event->end_timestamp)?><!--</strong></h6>-->
<!--                <h6 class="mt-0"><strong>--><?php //=$event->city?><!--, --><?php //=$event->state?><!--</strong></h6>-->
<!--            </div>-->
<!--        </div>-->

        <div class="row mt-5">


        <h5> Abstract Disclosure System Main Menu</h5>
        <hr />
        </div>

        <div class="row mt-5">
            <div class="col-md-12">
                <div id="landing-page-contents" class="container-fluid p-4">
                    <div class="submission-menu">

                        <?php
                            if(!empty($author_details)):
                            foreach ($author_details as $details):
                            ?>
                            <a href="<?= base_url().'author/copyright_of_publication_agreement/'.$details['paper_id']?>" class="btn btn-white btn-sm round-0 text-start mt-2 ps-0 fw-bold" style="width:80%; border-bottom:1px solid red">
                                <num class="btn-sm me-2 text-white " style="background-color:#FF6600; padding:5px 10px 5px 10px"></num>Submission <?=strip_tags(trim($details['id']))?>, <label class="fw-normal"><?=strip_tags(trim($details['title']))?></label>
                                <?= $details['is_copyright_agreement_accepted'] == 1 ? '<span class="float-end text-success"><i class="fw-bold fas fa-exclamation-circle"></i> Complete </span>':'<span class="float-end text-danger"><i class="fw-bold fas fa-exclamation-circle"></i> Incomplete </span>' ?>
                            </a>
                        <?php
                            endforeach;
                            endif
                        ?>


                        <!--<a href="<?php /*=base_url()*/?>/author/conflict_of_interest_disclosure" class="btn btn-white btn-sm round-0 text-start ps-0 fw-bold" style="width:80%; border-bottom:1px solid red"><num class="btn-sm me-2 text-white " style="background-color:#FF6600; padding:5px 10px 5px 10px">1 </num> Conflict of Interest Disclosure
                            <?php /*=isset($author_details) && ($author_details['is_declaration_accepted'] == '1')? '<span class="float-end text-success"><i class="fw-bold  fas fa-check-circle"> Completed</i>  </span>' :'<span class="float-end text-danger"><i class="fw-bold fas fa-exclamation-circle"> Incomplete </i></span>' */?>
                        </a>
                        <a href="<?php /*=base_url()*/?>/author/review" class="btn btn-white btn-sm round-0 text-start mt-2 ps-0 fw-bold printPrevBtn" style="width:80%; border-bottom:1px solid red"><num class="btn-sm me-2 text-white " style="background-color:#FF6600; padding:5px 10px 5px 10px">2 </num>Print Preview/Finalize
                            <?php /*=isset($author_details) && ($author_details['is_finalized'] == '1')? '<span class="float-end text-success"><i class="fw-bold  fas fa-check-circle"> Completed</i>  </span>' :'<span class="float-end text-danger"><i class="fw-bold fas fa-exclamation-circle"> Incomplete </i></span>' */?>
                        </a>-->

                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<script>
    //$(function(){
    //    let status = "<?php //=isset($author_details) && ($author_details['is_declaration_accepted'] == '1')? 1:0 ?>//";
    //   if(status == 0){
    //        $('.printPrevBtn').on('click', function(e){
    //            e.preventDefault();
    //            toastr.warning('Please complete Conflict of Interest Disclosure before proceeding to finalize');
    //        })
    //   }
    //})
</script>
