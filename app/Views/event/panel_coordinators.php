
<link href="<?=base_url()?>/assets/css/jquery_ui_style.css" rel="stylesheet">

<?php echo view('event/common/menu'); ?>


<main>
    <div class="container">
        <div class="card p-lg-5 p-md-2 p-sm-1 p-xs-1">
            <div class="disclosureContent">
                <div class="container">
                    <?php echo view('event/common/shortcut_link_panel'); ?>
                    <div>
                        <strong>Please read these instructions first:</strong></div>

                    <table border="0" style="margin-left: 20px; margin-top: 0">

                        <tbody><tr>
                            <td valign="top"><li></li></td>
                            <td>Please add the name of the Panel Coordinator by clicking on the "Search for a Coordinator " button.  If you have searched the database  and the name is not there, you will then be prompted to add the new name.</td>
                        </tr>
                        <tr>
                            <td valign="top"><li></li></td>
                            <td>All Coordinators will automatically receive communication about this submission.</td>
                        </tr>
                        <tr>
                            <td valign="top"><li></li></td>
                            <td>This page is for the coordinator only; panelists names will be added in a subsequent page.</td>
                        </tr>
                        </tbody></table>

                    <br>

                    <div><strong>Panelist Copyright:</strong></div>

                    <table border="0" style="margin-left: 20px; margin-top: 0">

                        <tbody><tr>
                            <td valign="top"><li></li></td>
                            <td>Copyright Agreements are not required from the panel coordinator.</td>
                        </tr>
                        <tr>
                            <td valign="top"><li></li></td>
                            <td>Copyright Agreements are required from each panelist,  before the panel can be published.</td>
                        </tr>
                        <tr>
                            <td valign="top"><li></li></td>
                            <td>Panelists will automatically receive an email regarding copyright requirements once they have been added to the panel on a subsequent page.</td>
                        </tr>
                        <tr>
                            <td valign="top"><li></li></td>
                            <td>The Coordinator can view the copyright status on the "My Submissions" page.</td>
                        </tr>
                        </tbody></table>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-lg-5 col-md-12 col-sm-12">
                    <div class="mb-5 justify-content-end">
                        <input type="hidden" value="<?=$paper_id?>" id="panelPaperID">
                        <button class="btn btn-primary btn-sm showSearchPanelModal"  id="showSearchPanelModal" style="min-width:100px; width:200px; max-width:200px; height:38px">Search for Name</button>
                    </div>
                </div>
            </div>
            <div class="">
                <div class="card">
                    <div class="card-header"style="background-color:#FFCC99">
                        Panel Coordinators List
                    </div>
                    <div class="card-body" style="min-height:120px;overflow-x:scroll">
                        <table class="table ">
                            <thead>
                            <tr>
                                <th></th>
                                <th>Author Name</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody class="panelCoordinatorList">

                            </tbody>
                        </table>
                    </div>
                </div>
                <br><br><br><br>
            </div>
            <div class="row">

                <div class="col-8"><button class="btn btn-success" id="savePanelCoordinators" style="max-width:200px"> Save and Continue</button></div>

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


<script  type="text/javascript" src="<?=base_url('assets/js/panelSubmissionFunctions.js?v=6')?>"></script>
<script  type="text/javascript" src="<?=base_url('assets/js/locationAutocomplete.js')?>"></script>
<script  type="text/javascript" src="<?=base_url('assets/js/addressAutoComplete.js')?>"></script>
