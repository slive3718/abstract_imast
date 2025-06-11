
<link href="<?=base_url()?>/assets/css/jquery_ui_style.css" rel="stylesheet">



<?php echo view('admin/common/menu'); ?>
<!-- <?php// print_r($abstract_details->id); exit;?> -->
<style>
    .table > :not(caption) > * > * {
        padding: 0;
    }
</style>
<main>
    <div class="container pb-5">
        <?php echo view('admin/common/shortcut_link_detail_back'); ?>
        <div class="card p-lg-5 p-md-2 p-sm-1 p-xs-1 shadow">
                    <h5>Please read these instructions first:</h5>
                    <h5>Editing Author Data</h5>
                    <ul>
                        <li>
                            Please add all authors by first searching the database by clicking on the
                            <button type="button" class="showSearchAuthorModal btn btn-primary btn-sm"> Add/Search for an Author </button>
                            button as their data may already be entered in the system. If this person is not already in the database, you may add them as a new author.
                        </li>
                        <li>
                            Please check "Correspondent" for each author that should receive communication about this submission. The presenting author will automatically be a correspondent.
                        </li>
                        <li>
                            If your author data is INCOMPLETE or needs editing, please click on the icon to fill out required fields and edit your information.
                        </li>
                    </ul>
                    <h5>Author Copyright</h5>
                    <ul>
                        <li>
                            Copyright agreements are required from each author before the paper can be published.
                        </li>
                        <li>
                            If you are the submitter and an author, please click on the Disclose Now link to continue to the copyright agreement. Once you have filled out the copyright agreement, you will be redirected back to the submission pages.
                        </li>
                        <li>
                            For the co-author(s), the system will automatically email them regarding copyright requirements once this page is completed.
                        </li>
                        <li>
                            Submitters may return to the abstract submission system to view the copyright statuses.
                        </li>
                    </ul>
            <div class="row mt-5">
                <div class="col-lg-5 col-md-12 col-sm-12">
                    <div class="mb-5 justify-content-end">
                        <button class="btn btn-primary btn-sm showSearchAuthorModal" style="min-width:100px; width:200px; max-width:200px; height:38px"> Add/Search for an Author</button>
                    </div>
                </div>
            </div>
            <div class="">
                <div class="card">
                    <div class="card-header"style="background-color:#FFCC99">
                        Author List
                    </div>
                    <div class="card-body" style="min-height:120px;overflow-x:scroll">
                        <table class="table p-0">
                            <thead>
                            <tr>
                                <th></th>
                                <th>Author Name</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>Author Info</th>
                                <th>Copyright Status</th>
                                <th>Email Info</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody class="authorList">

                            </tbody>
                        </table>
                    </div>
                </div>
                <br><br><br><br>
            </div>
            <div class="row">

                <div class="col-8"><button class="btn btn-success" id="savePaperAuthors" style="max-width:200px"> Save and Continue</button></div>

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

    <script  type="text/javascript" src="<?=base_url('assets/js/addressAutoComplete.js')?>"></script>
<script>
    let current_user_id = "<?=session('user_id')?>"
</script>
