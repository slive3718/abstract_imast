

<?php echo view('deputy/common/menu'); ?>
<style>

    .log-trigger:hover {
        background-color: rgba(37, 99, 235, 0.08);
        border-color: rgba(37, 99, 235, 0.2);
        color: #1d4ed8;
    }

    .log-trigger i {
        font-size: 0.9em;
    }

    /* ===== Professional Tooltip Styling ===== */
    .tippy-box {
        font-size: 0.875rem;
        max-width: 320px;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        background: white;
        color: #333;
        border: 1px solid #f0f0f0;
    }

    .tippy-box[data-theme~='light'] {
        background: white;
        color: #333;
    }


    .tippy-content {
        padding: 1rem;
    }

    .tippy-content .card {
        border: none;
        box-shadow: none;
        padding: 0;
    }

</style>
<main>
    <div class="container-fluid" style="padding-bottom:200px">
        <?php echo view('admin/common/shortcut_link'); ?>
        <div class="card">
            <div class="card-body">

                <div class="ms-2">
                    <p class="">Welcome, <span class="fw-bolder"><?= ucfirst(session('name')). ' ' . ucfirst(session('surname'))?></span></p>
                    <p>
                            You have been assigned to all papers in the <span class="fw-bolder" > <?= (!empty($divisions) ? $divisions: '')?> Division </span> submitted to date. All of these papers are listed below. Papers with a new formal proposal upload
                            will be indicated with <b><span class="text-danger">[ NEW ]</span></b> in the Formal Uploads column. If you want to assign this to a reviewer, click on 'assign now' and this displays the list of
                            reviewers available to review this paper. Once the paper has been assigned, the <b><span class="text-danger">[ NEW ]</span></b> indicator will be removed and an automatic email is sent to the
                            reviewer.
                            <br><br>
                            Please note that if a submitter uploads subsequent Formal Proposals, the Program Chair will not be alerted; these will be managed by the administrator.
                            Only the first submitted Formal Proposal will alert the Program Chair to assign a reviewer.
                            <br><br>
                            To view the reviewer scores and comments, click on the Paper ID#, Presenter name or Title.
                            <br><br>
                            To follow the progress of your reviewers, click on ‘Reviewers & Progress Report’ under the Program Chair Options icon.
                            <br><br>
                            A green check mark beside the reviewer name indicates that the paper has been reviewed, a red X indicates that it has been declined and no indication means that it has not yet been reviewed.

                        </p>
                    <div class="accordion" id="accordionExample" style="max-width:1200px">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button collapsed bg-primary text-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    More Paper Details? To view more details about each Paper click on  the Abstract Details button.
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                        <div><strong>View Review Details?</strong> Click on the title to view the review details.</div>
                                        <div><strong>More Paper Details?</strong> To view more details about each Paper click on <img src="https://s3.amazonaws.com/owpmlibs/img/stock_search_16_16.png" width="16" height="16"> .</div>
                                        <div><strong>New Formal Proposal</strong> Click on 'Assign Now' to assign the paper to a reviewer.</div>
                                        <div><strong>Sorting Columns?</strong> To sort columns, click on the headings at the top of each column.</div>
                                        <div><strong>Search for Data?</strong> Use the search field to find specific data on this page. Note that this search function will only search on the text that is on this page.</div>
                                        <div><strong>View Progress of reviewers?</strong> Go to the Program Chair options and choose the Reviewers & Progress link.</div>
                                        <div><strong>Switch to Panels?</strong> Under the Program Chair Options icon, click on Panel Review Management</div>
                                        <div><strong>Not suitable?</strong> If this paper is not suitable for your division please check this box for it to be reassigned</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 ">
                        <p>All submitted papers are listed below:</p>
                    </div>
                </div>
                <table id="papersTable" class="table table-striped table-bordered table-hover">

                    <thead>
                    <tr>
                        <th class="bg-primary text-white" colspan="16">Reviewers Table</th>
                    </tr>
                        <th>ID</th>
                        <th>Submitter</th>
                        <th>Paper Title</th>
                        <th>Type</th>
                        <th>Division</th>
                        <th>Review Progress</th>
                        <th>Reviewers</th>
                        <th>Formal Uploads</th>
                        <th>PC Status</th>
                        <th>PC Recommendation for Publishing</th>
                        <th>PC Comments</th>
                        <th>Review Status</th>
        <!--                <th>Reviewer Approval</th>-->
                        <th>PC Details</th>
                        <th>Not Suitable</th>
                        <th>Action</th>
                    </thead>
                    <tbody id="papersTableBody"  style="overflow:auto; cursor:pointer">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>


<!-- Modal -->
<div class="modal fade" id="assignRegularModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignRegularModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="height:80vh; overflow-y:auto">
                <table class="table table-striped" id="regularReviewerTable">
                    <thead>
                    <th></th>
                    <th>Reviewer Name</th>
                    <th>Reviewer Institution</th>
                    <th>Emailed</th>
                    <th>Status</th>
                    </thead>
                    <tbody id="regularReviewerTableBody" >
<!--    Filled with Ajax -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?=view('deputy/common/abstractDetailsModal')?>
<script>
    let baseUrlDeputy = "<?=base_url().'deputy/'?>";
    $(function(){
        
        getPapers();

        $("#papersTableBody").on('click', '#assignReviewerBtn', async function(){
            let paper_id = $(this).attr('paper_id');
            let divisionName = $(this).attr('divisionName');
            let reviewers_reviewed = $(this).attr('reviewers_reviewed')

            if ($.fn.DataTable.isDataTable('#regularReviewerTable')) {
                $('#regularReviewerTable').DataTable().destroy();
            }

            if(reviewers_reviewed >= 3){
                Swal.fire({
                    title: "Info",
                    text: "This paper has now been reviewed by three reviewers.  No further assignments are necessary.",
                    icon: "info",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, continue"
                }).then(async (result) => {
                    if (result.isConfirmed) {
                       await getRegularReviewersByDivision(paper_id, divisionName)

                    }
                });
            }else{
              await  getRegularReviewersByDivision(paper_id, divisionName)
            }
        });


        $('#regularReviewerTableBody').on('click', '.selectReviewerBox', function(e) {
            e.preventDefault(); // Prevent the default action of the click event

            let checkbox = $(this); // Save a reference to the checkbox

            let reviewerID = checkbox.attr('reviewerID');
            let paperID = checkbox.attr('paperID');
            let isChecked = checkbox.prop('checked');


            if(isChecked == true){
                Swal.fire({
                    title: "Are you sure?",
                    text: "This will send an automatic email notifying the reviewer.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, send and assign it!"
                }).then((result) => {
                    if (result.isConfirmed) {

                        Swal.fire({
                            title: "Please Wait!",
                            html: "Sending email to reviewer...",
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            allowEnterKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.post(baseUrlDeputy + 'assignPaperToRegularReviewer', {
                            'reviewerID': reviewerID,
                            'paperID': paperID,
                            'isChecked': isChecked
                        }, function(response) {
                            Swal.close(); // Close the loading spinner

                            if (response.status == '200') {
                                toastr.success(response.message);
                            } else {
                                toastr.error(response.message);
                            }
                            // Toggle the checkbox state based on the response status
                            checkbox.prop('checked', response.status == '200' ? isChecked : !isChecked);
                            getPapers();
                        }, 'json');
                    }
                });
            }else{
                $.post(baseUrlDeputy + 'assignPaperToRegularReviewer', {
                    'reviewerID': reviewerID,
                    'paperID': paperID,
                    'isChecked': isChecked
                }, function(response) {
                    if (response.status == '200') {
                        toastr.success(response.message);
                    } else {
                        toastr.info(response.message);
                    }
                    // Toggle the checkbox state based on the response status
                    checkbox.prop('checked', response.status == '200' ? isChecked : !isChecked);
                    getPapers();
                }, 'json');
            }

        });

        // $('#papersTableBody').on('click', '.formalUploadsFile', function(e){
        //     e.preventDefault();
        //     let uploadId = $(this).attr('uploadId');
        //     let href = $(this).attr('href')
        //
        //     $.post(baseUrlDeputy + 'addPaperUploadViews',{
        //         'upload_id':uploadId
        //     }, function (response) {
        //         if(response.status == "200"){
        //             window.location.href= href
        //         }
        //     }, 'json')
        // })

        $('#papersTableBody').on('click', '.abstractDetails', function(){
            let  paper_id = $(this).attr('paper_id');

            $.ajax({
                url: baseUrlDeputy+'allPaperDetails/'+paper_id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if(response.status == 200 && response.data) {
                        var paper = response.data;
                        var uploads = paper.uploads;
                        var authors = paper.authors;

                        // Populate General Information
                        $('.general-info #paper-id').text(paper.id);
                        $('.general-info #paper-title').html(stripTags(paper.title));

                        // Populate Uploaded Files
                        var filesHtml = '';
                        if(uploads.length > 0) {
                            uploads.forEach(function(upload) {
                                filesHtml += '<a href="' + upload.file_path + upload.file_name + '">' + upload.file_preview_name + '</a><br>';
                            });
                        } else {
                            filesHtml = 'No files uploaded';
                        }
                        $('#uploaded-files').html(filesHtml);

                        // Populate Author Information
                        var authorsHtml = '';
                        if (authors.length > 0) {
                            authorsHtml += '<tr><td>Author List: </td>';
                            authorsHtml += '<td>';
                            authors.forEach(function(author) {
                                var capitalizedFirstName = author.name.charAt(0).toUpperCase() + author.name.slice(1);
                                var capitalizedLastName = author.surname.charAt(0).toUpperCase() + author.surname.slice(1);
                                if (author.is_presenting_author == "Yes") {
                                    authorsHtml += 'Presenting Author: ' + '<span style="font-weight: bold;">' + capitalizedFirstName + ' ' + capitalizedLastName + '</span><br>';
                                } else {
                                    authorsHtml += 'Co-Author: ' + '<span style="font-weight: bold;">' + capitalizedFirstName + ' ' + capitalizedLastName + '</span><br>';
                                }
                            });
                            authorsHtml += '</td></tr>';

                            authors.forEach(function(author) {
                                var capitalizedFirstName = author.name.charAt(0).toUpperCase() + author.name.slice(1);
                                var capitalizedLastName = author.surname.charAt(0).toUpperCase() + author.surname.slice(1);
                                if (author.is_presenting_author == "Yes") {
                                    authorsHtml += '<tr><td style="width:200px">Presenting Author : </td>';
                                    authorsHtml += '<td>';
                                    authorsHtml += '<strong>' + capitalizedFirstName + ' ' + capitalizedLastName + '</strong><br>';
                                    authorsHtml += 'Address: ' + author.address + '<br>' + author.country + '<br>Email: ' + author.email + '<br> Institution: ' + author.institution + '<br> Work Phone: ' + author.phone + '<br> Fax: ' + author.fax;
                                    authorsHtml += '</td></tr>';
                                }
                            });

                            authors.forEach(function(author) {
                                var capitalizedFirstName = author.name.charAt(0).toUpperCase() + author.name.slice(1);
                                var capitalizedLastName = author.surname.charAt(0).toUpperCase() + author.surname.slice(1);
                                if (author.is_presenting_author !== "Yes") {
                                    authorsHtml += '<tr><td style="width:200px">Co-Author : </td>';
                                    authorsHtml += '<td>';
                                    authorsHtml += '<strong>' + capitalizedFirstName + ' ' + capitalizedLastName + '</strong><br>';
                                    authorsHtml += 'Address: ' + author.address + '<br>' + author.country + '<br>Email: ' + author.email + '<br> Institution: ' + author.institution + '<br> Work Phone: ' + author.phone + '<br> Fax: ' + author.fax;
                                    authorsHtml += '</td></tr>';
                                }
                            });
                        } else {
                            authorsHtml = '<tr><td colspan="2">No authors available</td></tr>';
                        }




                        $('.author-info tbody').html(authorsHtml);

                        // Populate Paper Information
                        $('.paper-info #division').text(paper.division_name);
                        $('.paper-info #paper-type').text(paper.paper_type_name);
                        $('.paper-info #paper-summary').text(stripTags(paper.summary));
                        $('.paper-info #ijmc-interest').text((paper.is_ijmc_interested == "1")?"Yes": "No");

                        // Populate User Information
                        $('.user-info #user-name').text(paper.submitter.name + ' ' + paper.submitter.surname);
                        $('.user-info #user-email').text(paper.submitter.email);
                    } else {
                        console.error('Error fetching data: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data: ' + error);
                }
            });

            $('#abstractDetailsModal').modal('show')
        })

        $("#papersTable").on('click', 'tr td:not(:nth-child(15)):not(:nth-child(13)):not(:nth-child(14))', function(){
            let paper_id = ($(this).closest('tr').attr('paper_id'));
            window.location.href= baseUrlDeputy+'review_details/'+paper_id
        });

        $('#papersTable').on('click', '.notSuitableChkbox', function(){
            let paper_id = ($(this).closest('tr').attr('paper_id'));
            let checked = $(this).is(':checked');
            let val = checked ? 1 : 0;
            $.post(baseUrlDeputy + 'updatePaperSuitableStatus',{
                "paper_id":paper_id,
                "is_suitable":val
            }, function (response) {
                if(response.status === 200){
                    toastr.success(response.message)
                }else{
                    toastr.error(response.message)
                }
            }, 'json')
        })

        $('#papersTableBody').on('click', '.reReviewBtn', function(){
            let paper_id = $(this).attr('paper_id');

            // if ($.fn.DataTable.isDataTable('#regularReviewerTable')) {
            //     $('#regularReviewerTable').DataTable().destroy();
            // }

            Swal.fire({
                title: "This will send email to the reviewers.",
                text: "Do you want to continue?",
                icon: "info",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, send it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Please Wait!",
                        html: "Sending email to reviewer...",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.post(baseUrlDeputy + 'sendReReviewEmailToReviewers', {
                        'paper_id': paper_id
                    }, function(response){
                        Swal.close(); // Close the loading spinner
                        if (response.status == "200") {
                            Swal.fire({
                                title: "Success!",
                                text: response.message,
                                icon: "success"
                            });
                        } else {
                            Swal.fire({
                                title: "Error!",
                                text: response.message,
                                icon: "error"
                            });
                        }
                    }, 'json')
                }
            });
        })

        $('#papersTableBody').on('click', '.clearSeenUpdatesBtn', function() {
            // Store reference to the clicked element
            const $button = $(this);

            Swal.fire({
                title: "Info",
                text: "This will remove all recent activity tags on this paper.",
                icon: "info",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, continue"
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const swalInstance = Swal.fire({
                        title: 'Processing',
                        html: '',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {
                        let paper_id = $button.data('paper-id');

                        // Make the AJAX request
                        const response = await $.post(base_url + 'logs/add_seen_logs', {
                            'paper_id': paper_id,
                            'ref_1': 'deputy'
                        });

                        // If successful, update the UI
                        if (response.status === 200) {
                            // Get the parent table row
                            const $row = $button.closest('tr');

                            $row.find('.recentUpdateNotif').fadeOut(300, function() {
                                $(this).remove();
                            });

                            // Clear tooltip content
                            $row.attr('data-tippy-content', '');

                            // If you're using the Tippy.js library, you might need to update the instance
                            if ($button._tippy) {
                                $button._tippy.setContent('');
                            }
                        } else {
                            Swal.fire('Error', response.message || 'Failed to clear notifications', 'error');
                        }
                    } catch (error) {
                        Swal.fire('Error', 'An error occurred while processing your request', 'error');
                        console.error(error);
                    } finally {
                        swalInstance.close();
                    }
                }
            });
        });

    })

    function getRegularReviewersByDivision(paper_id, divisionName){

        $('#regularReviewerTableBody').html('');

        Swal.fire({
            title: 'Processing',
            html: 'Please wait while we load reviewers...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.post(baseUrlDeputy + 'getRegularReviewersByDivision', {
            'paper_id': paper_id
        }, function(result){
            swal.close()
            if(result.status == '200') {
                $.each(result.data, function (i, val) {
                    // console.log(val)
                    let isAssigned = (val.is_assigned && val.is_assigned.is_deleted !== 1 && val.is_assigned.is_declined !== "1") ? 'checked' : '';
                    let selectReviewerBox = '<input type="checkbox" class="selectReviewerBox" name="selectReviewerBox" '+isAssigned+' id="" paperID = "'+paper_id+'" reviewerID = "'+val.user_id+'">';
                    // console.log(val.emailLog)
                    let emailLog = '';
                    // console.log(val.emailLog)
                    if (val.emailLog[0] && val.emailLog[0].length > 0) {
                        emailLog = val.emailLog[0][val.emailLog.length - 1].created_at;
                    }

                    let reviewStatus= '';

                    reviewStatus = (() => {

                        if(isAssigned == 'checked' && val.reviews.length === 0 ){
                            return '<span class="badge bg-secondary">Not Reviewed</span>';
                        }

                        if (val.reviews.length === 0) {
                            return '<span class="badge bg-secondary"></span>';
                        }


                        switch(val.reviews.is_approved) {
                            case '1':
                                return '<span class="badge bg-success">Approved</span>';
                            case '2':
                                return '<span class="badge bg-warning text-dark">I still have concern</span>';
                            case '3':
                                return '<span class="badge bg-danger">Declined</span>';
                            default:
                                return '<span class="badge bg-info"></span>';
                        }
                    })();

                    $('#regularReviewerTableBody').append(
                        '<tr>' +
                        '<td>'+selectReviewerBox+'</td>' +
                        '<td>' + val.user_name + ' ' + val.surname + '</td>' +
                        '<td>'+((val.institution)? val.institution:'')+'</td>' +
                        '<td>'+emailLog+'</td>' +
                        '<td>'+reviewStatus+'</td>' +
                        '</tr>'
                    );
                });
            }
            // Initialize DataTable with custom sorting for checkbox column
            $('#regularReviewerTable').DataTable({
                "columnDefs": [{
                    "targets": 0, // Index of the checkbox column
                    "orderable": true, // Allow sorting
                    "type": "checkbox" // Define custom sorting type
                }]
            });

        }, 'json');

        $("#assignRegularModal").modal('show');
        $('#assignRegularModalLabel').html('Regular Reviewers List <br> Division: <strong>' + divisionName + '</strong>');
    }

    function getPapers() {
        if ($.fn.DataTable.isDataTable('#papersTable')) {
            $('#papersTable').dataTable().fnClearTable();
            $('#papersTable').dataTable().fnDestroy();
        }
        $.post(baseUrlDeputy + 'getAllDeputyReviewerPapersByDivision', async function (response) {
            // console.log(response[0]);
            $('#papersTableBody').html('');

            $.each(response.data, function (i, val) {
                console.log(val)
                let isApproved = '';
                let reviewed = 0;
                let division_name = (val.division ? val.division.name : '')
                let submitterRecentLogs = ''
                let submitterLogsCount = 0
                if(val.submitter_logs && val.submitter_logs.length > 0){
                    $.each(val.submitter_logs, function(i , log){
                        submitterRecentLogs +=  renderUpdateList(log)
                        submitterLogsCount++;
                    })
                }
                let reviewers = []
                try {
                    reviewers = $.map(val.reviewers, function (reviewer) {
                        let reviewerRecentUpdate = ''

                        let reviewerLogsCount = 0;
                        if (reviewer.logs && reviewer.logs.length > 0) {
                            $.each(reviewer.logs, function (i, log) {
                                reviewerRecentUpdate += renderUpdateList(log)
                                reviewerLogsCount++;
                            })
                        }

                        let isApproved = '';

                        if (reviewer.review?.is_approved !== undefined) {
                            switch (reviewer.review.is_approved) {
                                case '1':
                                    isApproved = '<span class="badge bg-success ms-2" style="font-size:11px !important">I approve</span>';
                                    break;
                                case '2':
                                    isApproved = '<span class="badge bg-warning text-dark ms-2" style="font-size:11px !important">Still have concerns</span>';
                                    break;
                                default:
                                    isApproved = '';
                            }
                        }
                        if (reviewer.is_declined > 0) {
                            isApproved = '<span class="badge bg-danger ms-2" style="font-size:11px !important">Declined</span>';
                            return '<div class="card p-1 bg-transparent shadow-sm mb-1">' + reviewer.name + ' ' + reviewer.surname + '<br>' + isApproved + '</span></div>';
                        } else if (reviewer.review) {
                            reviewed++;
                            return '<div class="card p-1 bg-transparent shadow-sm mb-3 log-trigger position-relative" data-tippy-content="' + escapeHtml(reviewerRecentUpdate) + '">' +
                                '<span> <i  class="fas fa-check text-success"></i> ' + reviewer.name + ' ' + reviewer.surname + (reviewerRecentUpdate.trim() !== "" ? `<span class="recentUpdateNotif position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">${reviewerLogsCount}<span class="visually-hidden">unread messages</span></span>` : "") + '<br>' + isApproved + '</span></div>';
                        } else {
                            return '<div class="card p-1 bg-transparent shadow-sm mb-3 log-trigger position-relative"><span class="">' + reviewer.name + ' ' + reviewer.surname + '</span></div>';
                        }
                    }).join('');
                }catch (error){
                    throw new Error(error);
                }

                let isNewUpload = false; // Flag to track if there are new uploads

                let upload = $.map(val.uploads, function(upload) {
                    if ($.inArray(upload.id, val.uploadsWithViews) === -1) {
                        isNewUpload = true; // Set flag to true if there are new uploads
                        return ''; // Return empty string for new uploads
                    } else {
                        return '';
                    }
                }).join('<br>');

// Add "Yes [new]" if there are new uploads
                let uploadStatus = '';
                if(val.uploads && val.uploads.length == 1) {
                    uploadStatus = '<span class="badge bg-success text-white">[NEW]</span>'
                }else if(val.uploads && val.uploads.length > 1){
                    uploadStatus = '<span class="badge bg-success text-white">[Re-Upload]</span>'
                }

                if (isNewUpload) {
                    if(val.reviewers.length < 1) {
                        upload = 'Yes'+uploadStatus;
                    }else{
                        upload = 'Yes'+uploadStatus;
                    }

                }
                let pcStatus = '';
                if(val.deputyAcceptance.acceptance_status) {
                    const statusArray = JSON.parse(val.deputyAcceptance.acceptance_status);
                    if($.inArray('1', statusArray) !== -1) {
                        pcStatus += `<span class="badge bg-success text-white my-1">Accept for proceedings<span>`;
                    }
                    if($.inArray('2', statusArray) !== -1) {
                        pcStatus += `<span class="badge bg-info text-white my-1">Approved Transactions</span>`;
                    }
                    if($.inArray('3', statusArray) !== -1) {
                        pcStatus += `<span class="badge bg-primary text-white my-1">Approved for inclusion <br>in the Division’s Program </span>`
                    }
                    if($.inArray('4', statusArray) !== -1) {
                        pcStatus += `<span class="badge bg-danger text-white my-1">Rejected </span>`;
                    }
                }

                let assignBtn = '<button class="btn btn-success btn-sm assignReviewerBtn" id="assignReviewerBtn" paper_id=' + val.id + ' divisionName="' + division_name + '"  reviewers_reviewed = "'+reviewed+'"> Assign Now </button>'
                let btnAbstractDetails = '<a class="btn btn-info btn-sm text-white text-sm small abstractDetails" paper_id="'+val.id+'"> <small>Abstract Details</small> <i class="fas fa-magnifying-glass"></i></a>'
                let reReviewBtn = '<a class="btn btn-warning btn-sm text-dark text-sm small reReviewBtn mt-2" paper_id="'+val.id+'"> <small>Email reviewers to re-review </small> <i class="fas fa-envelope" aria-hidden="true"></i></a>'
                let isSuitableStatus = ((val.deputyAcceptance.is_suitable == "1") ? "checked" : '');
                let notSuitableCheckbox = '<input type="checkbox" class="notSuitableChkbox" value="1" '+isSuitableStatus+' >'

                let clearSeenUpdatesBtn = `<button class="btn btn-sm btn-info mt-2 clearSeenUpdatesBtn" data-paper-id="${val.id}" title="This will clear all notifications of this paper"> <i class="fas fa-exclamation-circle"></i> Clear Activity Tag</button>`
                // console.log(response.data)
                $('#papersTableBody').append('<tr paper_id="'+val.id+'")>' +
                    '<td>' + val.custom_id + '</td>' +
                    '<td><div class="card log-trigger text-nowrap p-1" data-tippy-content="'+escapeHtml(submitterRecentLogs)+'">' + val.paper_submitter.name + ' ' + val.paper_submitter.surname +(submitterLogsCount > 0 ? '<span class="recentUpdateNotif position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">'+submitterLogsCount+'<span class="visually-hidden">unread messages</span></span>' : '')+ '</div></td>' +
                    '<td>' + (val.title).replace( /<.*?>/g, '' )+ '</td>' +
                    '<td>' + val.type.acronym + '</td>' +
                    '<td>' + division_name + '</td>' +
                    '<td>'+reviewed+'/'+val.reviewers.length+'</td>' +
                    '<td class="text-nowrap">'+reviewers+'</td>' +
                    '<td class="text-nowrap">'+upload+'</td>' +
                    '<td>'+pcStatus+'</td>' +
                    '<td>'+(val.deputyAcceptance.is_recommended_for_publications || '')+'</td>' +
                    '<td>'+(val.deputyAcceptance.comments || '')+'</td>' +
                    '<td>'+(reviewed >= 3 ? 'Closed to review':'')+'</td>' +
                    // '<td>'+isApproved+'</td>' +
                    '<td>'+btnAbstractDetails+'</td>' +
                    '<td>'+notSuitableCheckbox+'</td>' +
                    '<td>' + assignBtn +'<br>'+reReviewBtn+ '<br>'+clearSeenUpdatesBtn+'</td>' +
                    '</tr>')
            })
        }, 'json').then(function (r) {

            $('#papersTable').DataTable({})
            $('#papersTableBody').on('mouseenter', '.log-trigger', function() {
                // Get content and handle empty/undefined cases
                let content = $(this).data('tippy-content') || 'No activity.';
                content = content.trim() === '' ? 'No activity.' : content;

                // Initialize or update tooltip
                const instance = tippy(this, {
                    content: content,
                    allowHTML: true,
                    interactive: true,
                    theme: 'light-border', // Using our enhanced theme
                    placement: 'top',
                    arrow: true,
                    animation: 'fade',
                    delay: [100, 0],
                    maxWidth: 320,
                    onShow(instance) {
                        if (instance.props.content === 'No activity.') {
                            instance.setProps({
                                theme: 'light-border no-activity' // Special styling for empty state
                            });
                        }
                    },
                    onHidden(instance) {
                        instance.destroy(); // Clean up to prevent memory leaks
                    }
                }).show();
            });

            $('#papersTableBody').on('mouseenter', function() {
                console.log('test')
            });
        })
    }

    function renderUpdateList(log) {
        return $('<div>')
            .addClass('card my-1 border-bottom border-lg')
            .append(
                $('<div>').addClass('log-item text-dark').html(`<strong>Activity:</strong> ${log.action}`),
                $('<div>').addClass('log-item  text-dark').html(`<strong>Context:</strong> ${log.context}`),
                $('<div>').addClass('log-item text-dark').html(`<strong>Date:</strong> ${log.created_at}`)
            )
            .prop('outerHTML'); // Convert to HTML string
    }


    // Function to escape HTML for tooltip content
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }


    function stripTags(html) {
        // Create a new div element
        var div = document.createElement("div");
        // Set the HTML content of the div to the provided HTML string
        div.innerHTML = html;
        // Return the text content of the div, which will not include any HTML tags
        return div.textContent || div.innerText || "";
    }



</script>

<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>
