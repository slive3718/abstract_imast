
<link href="<?=base_url()?>/assets/css/jquery_ui_style.css" rel="stylesheet">




<?php echo view('event/common/menu'); ?>
<!-- --><?php //print_R($author);exit;?>
<style>
    .table > :not(caption) > * > * {
        padding: 0;
    }
</style>
<main>
    <div class="container pb-5">
        <?php echo view('event/common/shortcut_link'); ?>
        <div class="card p-lg-5 p-md-2 p-sm-1 p-xs-1 shadow">
            <h5>Please read these instructions first:</h5>
            <h5>Adding/Editing Authors:</h5>
            <ol>
                <li>
                    Please add all authors by first searching the database by clicking on the
                    <button type="button" class="showSearchAuthorModal btn btn-primary btn-sm">Add/Search for an Author</button>
                    button as their data may already be entered in the system. If this person is not already in the database, you may add them as a new author.
                </li>
                <li>
                    You must select one presenting author. The presenting author will receive all notifications regarding this submission.
                </li>
                <li>
                    Please check "Correspondent" for each author that should receive communication about this submission. The presenting author will automatically be a correspondent.
                </li>
                <li>
                    If your author data is INCOMPLETE or needs editing, please click on the <i class="fas fa-user-edit"></i> icon to fill out required fields and edit your information.
                </li>
                <li>
                    You can add up to <strong>40 authors</strong>.
                </li>
                <li>
                    To adjust the author order, use the <i class="fas fa-arrow-up"></i> and <i class="fas fa-arrow-down"></i> arrows.
                </li>
                <li>
                    A Relevant Financial Relationships Disclosure is required for each author.
                </li>
            </ol>

            <h5>Author Disclosures:</h5>
            <ol>
                <li>
                    Disclosures are required for each author before the submission deadline. Your abstract cannot be finalized until all authors have completed their disclosures.
                </li>
                <li>
                    If you are the submitter and an author, your disclosures will automatically be checked in the system.
                </li>
                <li>
                    For other authors, the system will automatically check their disclosures once the author name is added. If the co-author’s disclosure is not current, you can email them by clicking <strong>'Email Now''</strong> displayed beside their name. This author will receive an email to complete their disclosures, and their status will be updated here.
                </li>
                <li>
                    As a submitter, you will receive an email confirmation each time an author completes their disclosure. At any time before the submission deadline, you may return to the abstract submission system to check the status of your authors’ disclosures.
                </li>
                <li>
                    You will receive an email when all authors on the submission have submitted disclosures.
                </li>
            </ol>

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
                                <th>Disclosure Status</th>
                                <th>Email Info</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody class="authorList">

                            </tbody>
                        </table>
                    </div>
                </div>
                <br><br>
            </div>
<!--            <div class="row container">-->
<!--                <h5>  <span class="text-danger"> *</span>Is the presenting author or senior author of this paper from Asia or Oceania?</h5>-->
<!--                <div class="form-check">-->
<!--                    <input class="form-check-input" type="radio" name="author_q_1" id="author_q_1_yes" value="1" --><?php //=!empty($paper) && !empty($paper['author_q_1']) && $paper['author_q_1'] == 1 ? 'checked' : ''?><!-- required>-->
<!--                    <label class="form-check-label" for="author_q_1_yes">-->
<!--                        Yes, the presenting author and/or senior author of this paper is from Asia or Oceania-->
<!--                    </label>-->
<!--                </div>-->
<!--                <div class="form-check">-->
<!--                    <input class="form-check-input" type="radio" name="author_q_1" id="author_q_1_no" value="0" --><?php //=!empty($paper) && !empty($paper['author_q_1']) && $paper['author_q_1'] == 0 ? 'checked' : ''?><!-- >-->
<!--                    <label class="form-check-label" for="author_q_1_no">-->
<!--                        No, the presenting author and/or senior author is <span class="fw-bolder">not</span> from Asia or Oceania-->
<!--                    </label>-->
<!--                </div>-->
<!---->
<!--                <h5 class="mt-4">  <span class="text-danger"> *</span>Are the majority of co-authors on this paper from Asia or Oceania?</h5>-->
<!--                <div class="form-check">-->
<!--                    <input class="form-check-input" type="radio" name="author_q_2" id="author_q_2_yes" value="1" --><?php //=!empty($paper) && !empty($paper['author_q_2']) && $paper['author_q_2'] == 1 ? 'checked' : ''?><!-- required>-->
<!--                    <label class="form-check-label" for="author_q_2_yes">-->
<!--                        Yes, the majority of co-authors on this paper are from Asia or Oceania-->
<!--                    </label>-->
<!--                </div>-->
<!--                <div class="form-check">-->
<!--                    <input class="form-check-input" type="radio" name="author_q_2" id="author_q_2_no" value="0" --><?php //=!empty($paper) && !empty($paper['author_q_2']) && $paper['author_q_2'] == 0 ? 'checked' : ''?><!-- >-->
<!--                    <label class="form-check-label" for="author_q_2_no">-->
<!--                        No, the majority of co-authors on this paper are from other continents (Europe, North America, South America)-->
<!--                    </label>-->
<!--                </div>-->
<!--            </div>-->
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
<?= view('event/common/addInstitutionModal'); ?>
<script>
    let current_user_id = "<?=session('user_id')?>"
    let basic_science_format_status = "<?= !empty($paper) && $paper['basic_science_format'] == 'Yes' ? 1 : 0?>"
    let disclosure_current_date = `<?= $disclosure_current_date ?? ''?>`
    let previousUrl = "<?= $previous_url ?? ''?>"
    let previousPage = "<?= $previous_page ?? '' ?>"

</script>
<script>
    $(function(){
        $(document).on('change', '.presentingAuthor', function() {
            let selectedPresenter = $('.presentingAuthor:checked').length; // Count checked checkboxes

            if (selectedPresenter > 1) {
                toastr.info('Only one presenter is allowed.');
                $(this).prop('checked', false); // Uncheck the last clicked checkbox
            }
        });
    })
</script>
<script  type="text/javascript" src="<?=base_url('assets/js/author_copyright.js')?>"></script>
<script  type="text/javascript" src="<?=base_url('assets/js/addressAutoComplete.js')?>"></script>
<script  type="text/javascript" src="<?=base_url('assets/js/institution.js')?>"></script>
<script  type="text/javascript" src="<?=base_url('assets/js/institution_autocomplete.js')?>"></script>

