
<script  type="text/javascript" src="<?=base_url('assets/js/submissionFunction.js?v=1')?>"></script>


<?php echo view('event/common/menu'); ?>

<main>
    <div class="container-fluid">

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

        <hr />


        <div class="card p-lg-5 p-md-2 p-sm-1 p-xs-1">
            <h5 class="fw-bold"> Author and Disclosure Panel</h5>
            <hr style="height: 5px; color:red" class="m-0">
            <div class="disclosureContent">
                <!--        This will be filled from database  -->
                <?=$event->disclosure_statement?>
            </div>

            <h5>Financial Relationship</h5>
            <p> Please select the statement that applies </p>
            <div>
                <input type="radio" name="statementType" id="statementType1">
                <label for="statementType1">I have NO financial relationship(s) with an ineligible company producing healthcare goods or services.</label>
            </div>
            <div>
            <input type="radio" name="statementType" id="statementType2">
            <label for="statementType2">I have a personal financial relationship with an ineligible company producing goods or services.</label>
            </div>



            <button class="btn btn-success mt-5" id="saveDisclosureBtn" style="max-width:200px"> Save and Continue</button>

            <!--            #########################           -->
        </div>
    </div>
</main>
    <script>
        let base_url = "<?=base_url()?>";
        let user_url = "<?=base_urL()?>/user/";
        let event_uri = "<?=$event->uri?>";


    </script>