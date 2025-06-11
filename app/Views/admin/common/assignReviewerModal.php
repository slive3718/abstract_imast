
<!-- Modal -->
<div class="modal fade" id="assignReviewerModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Regular Reviewers List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <input type="hidden" name="reviewerID" id="reviewerID" value="">
            </div>
            <div class="modal-body" id="">
                <table class="table table-striped table-bordered table-hover" id="assignReviewersTable">
                    <thead>
                    <th></th>
                    <th>ID</th>
                    <th>Submission Title</th>
                    <th>Submitter</th>
                    <th>Submission Type</th>
                    </thead>
<!--                    this will be filled with Ajax -->
                    <tbody id="assignReviewersTableBody">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="doAssignToProgramChair">Save changes</button>
            </div>
        </div>
    </div>
</div>