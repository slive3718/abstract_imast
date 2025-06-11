
<link href="<?=base_url()?>/assets/css/jquery_ui_style.css" rel="stylesheet">

<?php echo view('event/common/menu'); ?>


<main>
    <div class="container">
        <div class="card p-lg-5 p-md-2 p-sm-1 p-xs-1">
            <div class="disclosureContent">
                <div class="container">
                    <?php echo view('event/common/shortcut_link_panel'); ?>
                    <div class="noteDiv">
                        <div><strong>Please read these instructions first:</strong></div>

                        <table border="0" style="margin-left: 20px; margin-top: 0">
                            <tbody><tr>
                                <td valign="top"><li></li></td>
                                <td>
                                    Please add all panelists that you would like to appear on your submission by
                                    clicking on the "Search for a Panelist" button.  Their information may already
                                    be entered in the system. If this person is not in the database, then you will
                                    have the prompt to add them after searching.
                                </td>
                            </tr>
                            <tr>
                                <td valign="top"><li></li></td>
                                <td>
                                    Once this page is completed, all panelists will receive an email that will include
                                    a unique invitation code that will link them to this Panel. You may add extra panelists
                                    after finalizing.
                                </td>
                            </tr>
                            <tr>
                                <td valign="top"><li></li></td>
                                <td>
                                    If the Coordinator is also a panelist, add their name below and they will receive
                                    the same invitation code to submit their panel.
                                </td>
                            </tr>
                            </tbody></table>
                    </div>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-lg-5 col-md-12 col-sm-12">
                    <div class="mb-5 justify-content-end">
                        <input type="hidden" value="<?=$paper_id?>" id="panelPaperID">
                        <button class="btn btn-primary btn-sm showSearchPanelModal"  id="showSearchPanelModal" style="min-width:100px; width:200px; max-width:200px; height:38px">Search for a Panelist</button>
                    </div>
                </div>
            </div>
            <div class="">
                <div class="card">
                    <div class="card-header"style="background-color:#FFCC99">
                        Panelist List
                    </div>
                    <div class="card-body" style="min-height:120px;overflow-x:scroll">
                        <table class="table ">
                            <thead>
                            <tr>
                                <th></th>
                                <th>Author Name</th>
                                <th></th>
                                <th>Action</th>
                                <th>Emailed</th>
                            </tr>
                            </thead>
                            <tbody class="panelistList">

                            </tbody>
                        </table>
                    </div>
                </div>
                <br><br><br><br>
            </div>
            <div class="row">

                <div class="col-8"><button class="btn btn-success" id="savePanelist" style="max-width:200px"> Save and Continue</button></div>

                <div class="col-4">
                    <fieldset class="card p-3">
                        <legend>My Recent Author</legend>
                        <p> Quick add author to Submissions </p>
                        <table class="table table-striped table-responsive table-bordered table-border-success table-responsive quickAddAuthorTable " >
                            <tbody class="">
                            <?php if(isset($recentAuthors) && !empty($recentAuthors)) :
                                foreach($recentAuthors as $recentAuthor):
                                    ?>
                                    <tr class="">
                                        <td class="shadow-sm"><?=$recentAuthor['name'].' '.$recentAuthor['surname']?></td>
                                        <td class="shadow-sm" style="width: 30px;"><a class="btn btn-success btn-sm quickAddAuthorBtn" href="" author_id="<?=$recentAuthor['id']?>"><i class="fa fa-plus"></i></a></td>
                                    </tr>
                                <?php endforeach; endif;?>
                            </tbody>
                        </table>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</main>

<?= view('event/common/searchAuthorModal'); ?>
<?= view('event/common/addAuthorModal'); ?>


<script  type="text/javascript" src="<?=base_url('assets/js/panelistSubmissionFunctions.js?v=7')?>"></script>
<script  type="text/javascript" src="<?=base_url('assets/js/locationAutocomplete.js')?>"></script>
<script  type="text/javascript" src="<?=base_url('assets/js/addressAutoComplete.js')?>"></script>
