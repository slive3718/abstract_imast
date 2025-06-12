
<!-- Add Author Modal -->
<div class="modal fade" id="addAuthorModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"  data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add an Author</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSaveAuthor" action="" method="post" enctype="multipart/form-data" role="form">
                    <!-- Nav tabs -->
                    <input type="hidden" name="author_id" id="author_id" value="">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab" aria-controls="personal" aria-selected="true">Personal Information</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="author-info-tab" data-bs-toggle="tab" data-bs-target="#authorInfo" type="button" role="tab" aria-controls="authorInfo" aria-selected="false">Author Information</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="other-details-tab" data-bs-toggle="tab" data-bs-target="#other-details" type="button" role="tab" aria-controls="other-details" aria-selected="false">Address Information</button>
                        </li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                            <!-- Personal Information Fields -->
                            <div class="row mb-3">
                                <div class="row my-3">
                                    <div class="col-md-6">
                                        <label class="form-label" for="authorFName">First Name <span class="text-danger">*</span></label>
                                        <input type="text" name="authorFName" title="First Name" class="form-control required" id="authorFName" placeholder="">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="authorMName">Middle Name</label>
                                        <input type="text" name="authorMName" title="Middle Name" class="form-control" id="authorMName" placeholder="">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label" for="authorLName">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="authorLName" title="Last Name" class="form-control required" id="authorLName" placeholder="">
                                    </div>
                                    <!--                                    <div class="col-md-6">-->
                                    <!--                                        <label class="form-label" for="authorDeg">Credentials/Degree</label>-->
                                    <!--                                        <input type="text" name="authorDeg" title="Degree" class="form-control" id="authorDeg" placeholder="">-->
                                    <!--                                    </div>-->
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label" for="authorEmail">Primary Email <span class="text-danger">*</span></label>
                                        <input type="email" name="authorEmail" title="Email" class="form-control required" id="authorEmail" placeholder="">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="authorConfirmEmail">Retype Email <span class="text-danger">*</span></label>
                                        <input type="email" name="authorConfirmEmail" title="Confirm Email" class="form-control required" id="authorConfirmEmail" placeholder="">
                                    </div>
                                </div>


                            </div>
                        </div>
                        <div class="tab-pane fade" id="authorInfo" role="tabpanel" aria-labelledby="author-info-tab">
                            <!-- Affiliation Fields -->
                            <div class="row mb-3">
                                <!-- Your affiliation fields here -->
                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label class="form-label" for="authorInstitution">Institution <span class="text-danger">*</span></label>
                                            <input name="authorInstitution" title="Institution" id="authorInstitution" class="form-control required">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="authorPhone">Degree </label>
                                            <input type="text" name="authorDegree" title="Phone" class="form-control shadow-none" id="authorDegree" style="max-width:400px" placeholder="">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="authorPhone">Work Phone <span class="text-danger">*</span></label>
                                            <input type="text" name="authorPhone" title="Phone" class="form-control shadow-none" id="authorPhone" style="max-width:400px" placeholder="">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="authorPhone">Fax </label>
                                            <input type="text" name="authorFax" title="Phone" class="form-control shadow-none" id="authorFax" style="max-width:400px" placeholder="">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="other-details" role="tabpanel" aria-labelledby="other-details-tab">
                            <!-- Other Details Fields -->
                            <div class="row my-3">
                                <!-- Your other details fields here -->
                                <div class="col">

                                    <div class="mb-3">
                                        <label class="form-label" for="authorAddress">Address</label>
                                        <input type="text" name="authorAddress" title="Address" class="form-control shadow-none" id="authorAddress" style="max-width:400px" placeholder="">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="authorCountry">Country <span class="text-danger">*</span></label>
                                        <input type="text" name="authorCountry" title="Country" class="form-control shadow-none"  id="authorCountry" style="max-width:400px" placeholder="" required>
                                        <input type="text" name="authorCountryId" title="Country" class="form-control shadow-none d-none" id="authorCountryId" style="max-width:400px" placeholder="">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="authorProvince">Province/State <span class="text-danger">*</span></label>
                                        <input type="text" name="authorProvince" title="Province" class="form-control shadow-none"  id="authorProvince" style="max-width:400px" placeholder="" required>
                                        <input type="text" name="authorProvinceId" title="Province" class="form-control shadow-none d-none" id="authorProvinceId" style="max-width:400px" placeholder="">
                                    </div>

                                </div>
                                <div class="col">

                                    <div class="mb-3">
                                        <label class="form-label" for="authorCity">City <span class="text-danger">*</span></label>
                                        <input type="text" name="authorCity" title="City" class="form-control shadow-none" id="authorCity" style="max-width:400px" placeholder="" required>
                                        <input type="text" name="authorCityId" title="City" class="form-control shadow-none d-none" id="authorCityId" style="max-width:400px" placeholder="">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="authorZipcode">Postal Code/Zip Code</label>
                                        <input type="text" name="authorZipcode" title="Postal Code" class="form-control shadow-none"  id="authorZipcode" style="max-width:400px" placeholder="">
                                        <input type="text" name="authorZipcodeId" title="Postal Code" class="form-control shadow-none d-none" id="authorZipcodeId" style="max-width:400px" placeholder="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary closeBtn" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
            </form>
        </div>
    </div>
</div>