
<!-- Modal -->
<div class="modal fade" id="abstractDetailsModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Paper Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card p-lg-5 p-md-2 p-sm-1 p-xs-1 shadow">
                    <div class="card-header fw-bold"> General Information  <a href="YOUR_BASE_URL/user/edit_papers_submission/YOUR_PAPER_ID" class="btn btn-sm btn-primary float-end"><i class="fas fa-edit"></i> Edit</a></div>
                    <table class="table">
                        <tbody>
                        <tr>
                            <td class="text-end">Paper ID : </td>
                            <td>YOUR_PAPER_ID</td>
                        </tr>
                        <tr>
                            <td style="width:250px" class="text-end">Paper Title : </td>
                            <td>YOUR_PAPER_TITLE</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="card p-lg-5 p-md-2 p-sm-1 p-xs-1 shadow">
                    <div class="card-header fw-bold"> Uploaded File(s)<span class="text-danger text-sm badge " title="Please complete requirements before finalizing abstract."><i class="fas fa-exclamation-circle" > INCOMPLETE </i></span>  <a href="YOUR_BASE_URL/user/presentation_upload/YOUR_PAPER_ID" class="btn btn-sm btn-primary float-end"><i class="fas fa-edit"></i> Edit</a></div>
                    <div class="card-body">
                        <p> (The most recent uploaded file will appear at the top of the list) </p>
                        <table class="table" style="margin-bottom:0px !important">
                            <a href="YOUR_FILE_PATH/YOUR_FILE_NAME">YOUR_FILE_PREVIEW_NAME</a>
                        </table>
                    </div>
                </div>

                <div class="card p-lg-5 p-md-2 p-sm-1 p-xs-1 shadow">
                    <div class="card-header fw-bold"> Author Information <span class="text-danger text-sm badge " title="Please complete requirements before finalizing abstract."><i class="fas fa-exclamation-circle" > INCOMPLETE </i></span>  <a href="YOUR_BASE_URL/user/authors_and_copyright/YOUR_PAPER_ID" class="btn btn-sm btn-primary float-end"><i class="fas fa-edit"></i> Edit</a></div>
                    <div class="card-body">
                        <table class="table" style="margin-bottom:0px !important">
                            <tbody>
                            <tr>
                                <td class="text-end" style="width:250px">
                                    Author List:
                                </td>
                                <td>
                                    Presenting Author: YOUR_AUTHOR_NAME YOUR_AUTHOR_SURNAME<br>
                                    Co-Author: YOUR_AUTHOR_NAME YOUR_AUTHOR_SURNAME<br>
                                </td>
                            </tr>
                            <tr >
                                <td class="text-end">(1) Presenting Author :</td>
                                <td><strong>Your Author Name</strong></td>
                            </tr>
                            <tr>
                                <td class="text-end">Author Info: </td>
                                <td >
                                    Address: YOUR_AUTHOR_ADDRESS YOUR_AUTHOR_CITY, YOUR_AUTHOR_PROVINCE, YOUR_AUTHOR_ZIPCODE, YOUR_AUTHOR_COUNTRY<br>
                                    Professional Degree(s): YOUR_AUTHOR_DEG<br>
                                    Email: YOUR_AUTHOR_EMAIL<br>
                                    Institution: YOUR_AUTHOR_INSTITUTION<br>
                                    Work Phone: YOUR_AUTHOR_PHONE<br>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-end">Correspondence :</td>
                                <td>No</td>
                            </tr>
                            <tr>
                                <td colspan="2"><br></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card p-lg-5 p-md-2 p-sm-1 p-xs-1 shadow">
                    <div class="card-header fw-bold"> Paper Information <span class="text-danger text-sm badge " title="Please complete requirements before finalizing abstract."><i class="fas fa-exclamation-circle" > INCOMPLETE </i></span> <a href="YOUR_BASE_URL/user/edit_papers_submission/YOUR_PAPER_ID" class="btn btn-sm btn-primary float-end"><i class="fas fa-edit"></i> Edit</a></div>
                    <div class="card-body">
                        <table class="table" style="border-bottom-width:4px !important">
                            <tbody>
                            <tr>
                                <td class="text-end">Division : </td>
                                <td>YOUR_DIVISION_NAME</td>
                            </tr>
                            <tr>
                                <td class="text-end">Paper Type : </td>
                                <td>YOUR_PAPER_TYPE_NAME</td>
                            </tr>
                            <tr>
                                <td class="text-end">Paper Title : </td>
                                <td>YOUR_PAPER_TITLE</td>
                            </tr>
                            <tr>
                                <td class="text-end">Paper Summary : </td>
                                <td>YOUR_PAPER_SUMMARY</td>
                            </tr>
                            <tr>
                                <td class="text-end">Are you interested in submitting this paper to IJMC as well ? </td>
                                <td>I am interested in submitting this paper to IJMC</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card p-lg-5 p-md-2 p-sm-1 p-xs-1 shadow">
                    <div class="card-header fw-bold"> User Information </div>
                    <div class="card-body">
                        <table class="table">
                            <tbody>
                            <tr>
                                <td class="text-end"><strong>User/Submitter Name:</strong></td>
                                <td class="text-start">YOUR_USER_NAME YOUR_USER_SURNAME</td>
                            </tr>
                            <tr>
                                <td class="text-end"><strong>User/Submitter Email: 	</strong></td>
                                <td class="text-start">YOUR_USER_EMAIL</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>