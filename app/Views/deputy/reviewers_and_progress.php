

<?php echo view('deputy/common/menu'); ?>

<main>
    <div class="container-fluid" style="padding-bottom:100px">
        <?php echo view('admin/common/shortcut_link'); ?>
        <div>

            <div class="card">
                <div class="card-header">
                    <p>
                        The list of reviewers that have been assigned to your division are listed below with their progress on the right hand side.
                        You may filter this list to only those that actually have been assigned papers by checking the “Display only those reviewers with assignments.
                    </p>
<!--                    <fieldset class="fieldset border-primary bordered border-1">-->
<!--                        <legend> Add a Reviewer </legend>-->
<!---->
<!--                        <br><a href="javascript:void(0)" onclick="create();" title="">Click here</a> to add a new reviewer to system.-->
<!--                    </fieldset>-->

                </div>
                <div class="card-body">
                    <div>
                        <input type="checkbox" onclick="showOnlyReviewersWithAssignments()" name="reviewFilter1" id="reviewFilter1">
                        <label for="reviewFilter1"> Display only those reviewers with assignments</label>
                    </div>
                    <div class="container mt-3">
                        <div class="table-responsive">
                            <table class="table table-bordered" style="background-color: #F0F0DD;">
                                <tbody>
                                <tr>
                                    <td class="text-center" style="background-color: #D9DFD9; width: 50%;">Reviewer Name</td>
                                    <td class="text-center" style="width: 50%;">
                                        Number of papers reviewed / total assigned <br><br>
                                        Percentage Completed
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="container bg-light p-3">

                        <div id="reviewers-container">
                            <?php if ($divisionReviewerReviews): ?>
                                <?php foreach ($divisionReviewerReviews as $divisionReviewerReview): ?>
                                    <?php
                                    $reviewedCount = $divisionReviewerReview['reviewedCount'];
                                    $assignedCount = $divisionReviewerReview['assignedCount'];
                                    $progressPercentage = ($assignedCount > 0) ? ($reviewedCount / $assignedCount) * 100 : 0;
                                    ?>
                                    <div class="row mb-3 border p-3 bg-white reviewer" data-assigned-count="<?= $assignedCount ?>">
                                        <div class="col-md-6 text-center bg-success bg-opacity-10 p-3">
                                            <strong>
                                                <?= $divisionReviewerReview['name'] ?> <?= $divisionReviewerReview['surname'] ?>
                                            </strong>
                                        </div>
                                        <div class="col-md-6 text-center p-3">
                                            <?= $reviewedCount ?> / <?= $assignedCount ?><br>
                                            <div class="progress mt-2">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $progressPercentage ?>%;" aria-valuenow="<?= $progressPercentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <?= number_format($progressPercentage, 2) ?>%
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</main>


<?=view('deputy/common/panelDetailsModal')?>
<script>
    let baseUrlDeputy = "<?=base_url().'deputy/'?>";
    $(function() {
        // $.post(baseUrlDeputy+"getRegularReviewersByDivision", function(response){
        //     console.log(response)
        // })
    });


    function editReviewer(reviewerId) {
        // Implement your edit functionality here, e.g., redirect to an edit page or open a modal
        console.log('Edit reviewer with ID:', reviewerId);
    }


    function showOnlyReviewersWithAssignments() {
        const isChecked = document.getElementById('reviewFilter1').checked;
        const reviewers = document.querySelectorAll('.reviewer');
        reviewers.forEach(reviewer => {
            const assignedCount = reviewer.getAttribute('data-assigned-count');
            if (isChecked) {
                if (assignedCount > 0) {
                    reviewer.style.display = 'flex';
                } else {
                    reviewer.style.display = 'none';
                }
            } else {
                reviewer.style.display = 'flex';
            }
        });
    }
</script>