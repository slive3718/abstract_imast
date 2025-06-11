<!-- Modal -->
<div class="modal fade" id="abstractDetailsModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Paper Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <!-- General Information -->
                <div class="general-info card shadow">
                    <div class="card-header fw-bold" data-bs-toggle="collapse" data-bs-target="#collapseGeneral" aria-expanded="true" aria-controls="collapseGeneral">
                        General Information <i class="fas fa-chevron-down float-end"></i>
                    </div>
                    <div class="collapse show" id="collapseGeneral">
                        <div class="card-body">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <td style="width:200px" class="text-end">Paper ID : </td>
                                    <td id="paper-id"></td>
                                </tr>
                                <tr>
                                    <td class="text-end">Paper Title : </td>
                                    <td id="paper-title"></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Uploaded Files -->
                <div class="uploaded-files card shadow">
                    <div class="card-header fw-bold" data-bs-toggle="collapse" data-bs-target="#collapseUploadedFiles" aria-expanded="true" aria-controls="collapseUploadedFiles">
                        Uploaded File(s) <i class="fas fa-chevron-down float-end"></i>
                    </div>
                    <div class="collapse show" id="collapseUploadedFiles">
                        <div class="card-body p-4">
                            <p>(The most recent uploaded file will appear at the top of the list)</p>
                            <div id="uploaded-files"></div>
                        </div>
                    </div>
                </div>

                <!-- Author Information -->
                <div class="author-info card shadow">
                    <div class="card-header fw-bold" data-bs-toggle="collapse" data-bs-target="#collapseAuthorInfo" aria-expanded="true" aria-controls="collapseAuthorInfo">
                        Author Information <i class="fas fa-chevron-down float-end"></i>
                    </div>
                    <div class="collapse show" id="collapseAuthorInfo">
                        <div class="card-body">
                            <table class="table">
                                <tbody id="author-info-body"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Paper Information -->
                <div class="paper-info card shadow">
                    <div class="card-header fw-bold" data-bs-toggle="collapse" data-bs-target="#collapsePaperInfo" aria-expanded="true" aria-controls="collapsePaperInfo">
                        Paper Information <i class="fas fa-chevron-down float-end"></i>
                    </div>
                    <div class="collapse show" id="collapsePaperInfo">
                        <div class="card-body">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <td class="text-end" style="width:200px">Division : </td>
                                    <td id="division"></td>
                                </tr>
                                <tr>
                                    <td class="text-end">Paper Type : </td>
                                    <td id="paper-type"></td>
                                </tr>
                                <tr>
                                    <td class="text-end">Paper Summary : </td>
                                    <td id="paper-summary"></td>
                                </tr>
                                <tr>
                                    <td class="text-end">Interest in IJMC : </td>
                                    <td id="ijmc-interest"></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- User Information -->
                <div class="user-info card shadow">
                    <div class="card-header fw-bold" data-bs-toggle="collapse" data-bs-target="#collapseUserInfo" aria-expanded="true" aria-controls="collapseUserInfo">
                        User Information <i class="fas fa-chevron-down float-end"></i>
                    </div>
                    <div class="collapse show" id="collapseUserInfo">
                        <div class="card-body">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <td class="text-end" style="width:200px"><strong>User/Submitter Name:</strong></td>
                                    <td id="user-name"></td>
                                </tr>
                                <tr>
                                    <td class="text-end"><strong>User/Submitter Email:</strong></td>
                                    <td id="user-email"></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
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
