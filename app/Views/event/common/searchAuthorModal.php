
<!--  Search Author Modal -->
<div class="modal fade" id="searchAuthorModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Search an Author</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12 col-md-12  col-sm-12">
                                <p class="text-danger">Please enter the author's last name</p>
                                <div class="input-group mb-3 ">
                                    <label class="input-group-text text-dark" style="background-color:lightgray" for="authorName">Last Name</label>
                                    <input type="text" name="authorName" id="authorName" class="form-control shadow-none">
                                </div>

                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="mb-5">
                                        <button class="btn btn-success btn-sm searchAuthorBtn" style="min-width:100px; width:200px; max-width:200px; height:38px"><i class="fas fa-search"></i> Search Author </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="authorResults" style="display:none">
                    <div class="authorResultsBody">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="">
                                    <table class="authorResultTable table table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th></th>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Email</th>
                                            <th>State</th>
                                            <th>Country</th>
                                            <th>Search Results</th>
                                        </tr>
                                        </thead>
                                        <tbody class="authorResultTableBody" id="authorResultTableBody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-sm addAuthorBtn float-left" style="min-width:100px; width:331px; max-width:400px; height:38px"><i class="fas fa-plus"></i> Add an author who is not found in database</button>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-success addMarkedAuthor">Add marked author</button>
                            <button type="button" class="btn btn-success addMarkedPanel" style="display:none">Add marked panel</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
