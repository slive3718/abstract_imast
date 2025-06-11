
<!--<script  type="text/javascript" src="--><?//=base_url('assets/js/submissionFunction.js')?><!--"></script>-->


<?php //print_r($abstract_id);exit;?>
<?php echo view('author/common/menu'); ?>

<main>
    <div class="container-fluid" style="padding-bottom:200px">

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

        <div class="container-fluid row mt-5 ">
            <h5> Abstract Disclosure System Main Menu</h5>
            <hr />
            <div class=" card p-2">
                <p>Thank you for submitting your disclosure. You may update your disclosure any time before February 1, 2023 by returning to the <a href="<?=base_url()?>/author/view_disclosure" >disclosure system</a>. </p>
                <p>If you have any questions regarding your disclosure or abstract submissions, please contact:</p>
                <p>SRS Meetings Team <a href="mailto:Meetings@srs.org">Meetings@srs.org</a></p>
                <br>
                <br>
                <p><a href="<?=base_url()?>/<?=$event->uri?>"> Click Here </a> to return to the Abstract Submission System </p>
            </div>
        </div>

    </div>
</main>