

<?php echo view('admin/common/menu'); ?>
<Style>
    #abstractTable_filter{
        margin-bottom:10px
    }

</Style>
<main>
    <div class="container-fluid p-0">
        <div class="card p-0 m-0 shadow-lg">
            <div class="card-body">
                <div class="customButtonsDiv mx-3 mb-5 float-end">
                    <a href="<?=base_url()?>admin/exportScores" class="btn btn-success text-white position-relative" title="Export all abstract scores to excel">Export All Abstract Scores</a>
                </div>
                <div class="">
                    <table id="abstractTable" class="table table-responsive table-striped table-bordered" >
                        <thead class=" table-active" style="">
                        <tr>
                            <th>ID</th>
                            <th>Author List</th>
                            <th>Paper Title</th>
<!--                            <th>Type</th>-->
                            <th>Type</th>
                            <th>Division</th>
                            <th>Submitter</th>
                            <th>Formal <br> Uploads</th>
                            <th>Submission <br> Status & <br> Preference</th>
                            <th>Reviewer</th>
                            <th>PC Final</th>
                            <th>Status</th>
                            <th>Flagged</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody id="abstractTableBody">
                        <!-- This will be filled by jQuery and Datatables -->
                        </tbody>
                    </table>
                </div>
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


<script>
    let baseUrlAdmin = "<?=base_url().'admin/'?>";
    $(function(){

        getAbstracts();
        

        $("#abstractTableBody").on('click', '#assignReviewerBtn', function(){
            let paper_id = $(this).attr('abstract_id');
            let divisionName = $(this).attr('divisionName');
            let reviewers_reviewed = $(this).attr('reviewers_reviewed')

            if ($.fn.DataTable.isDataTable('#abstractTable')) {
                $('#abstractTable').DataTable().destroy();
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
                }).then((result) => {
                    if (result.isConfirmed) {
                        getRegularReviewersByDivision(paper_id, divisionName)
                    }
                });
            }else{
                getRegularReviewersByDivision(paper_id, divisionName)
            }
        });

        function getRegularReviewersByDivision(paper_id, divisionName){
            if ($.fn.DataTable.isDataTable('#regularReviewerTable')) {
                $('#regularReviewerTable').DataTable().destroy();
            }

            $.post(baseUrlAdmin + 'getRegularReviewersByDivision', {
                'paper_id': paper_id
            }, function(result){
                if(result.status == '200') {
                    $('#regularReviewerTableBody').html('');
                    if(result.data.length > 0) {
                        $.each(result.data, function (i, val) {
                            console.log(val)
                            let isAssigned = (val.is_assigned && val.is_assigned.is_deleted !== 1 && val.is_assigned.is_declined !== "1") ? 'checked' : '';
                            let selectReviewerBox = '<input type="checkbox" class="selectReviewerBox" name="selectReviewerBox" ' + isAssigned + ' id="" paperID = "' + paper_id + '" reviewerID = "' + val.user_id + '" divisionName="'+val.division[0].name+'">';
                            // console.log(val.emailLog)
                            let emailLog = '';
                            // console.log(val.emailLog)
                            if (val.emailLog[0] && val.emailLog[0].length > 0) {
                                emailLog = val.emailLog[0][val.emailLog.length - 1].created_at;
                            }

                            $('#regularReviewerTableBody').append(
                                '<tr>' +
                                '<td>' + selectReviewerBox + '</td>' +
                                '<td>' + val.user_name + ' ' + val.surname + '</td>' +
                                '<td>' + ((val.institution) ? val.institution : '') + '</td>' +
                                '<td>' + emailLog + '</td>' +
                                '</tr>'
                            );
                        });
                    }
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
            $('#assignRegularModalLabel').html('Regular Reviewers List <br> Division: <strong>' + divisionName + '</strong> <br> <small>Only regular reviewers assigned to this division will appear below</small>');
        }

        // ############### Start Assigning reviewer ##################
        $('#regularReviewerTableBody').on('click', '.selectReviewerBox', function(e) {
            e.preventDefault(); // Prevent the default action of the click event

            let checkbox = $(this); // Save a reference to the checkbox

            let reviewerID = checkbox.attr('reviewerID');
            let paperID = checkbox.attr('paperID');
            let isChecked = checkbox.prop('checked');
            let divisionName = checkbox.prop('divisionName');


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

                        $.post(baseUrlAdmin + 'assignPaperToRegularReviewer', {
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
                            getRegularReviewersByDivision(paperID, divisionName)
                            getAbstracts();
                        }, 'json');
                    }
                });
            }else{
                $.post(baseUrlAdmin + 'assignPaperToRegularReviewer', {
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
                    getRegularReviewersByDivision(paperID, divisionName)
                    getAbstracts();
                }, 'json');
            }

        });

        // ################## End Assigning reviewer #########################

        $("#abstractTableBody").on('click', '.acceptanceBtn', function(){ // Submit Reviews
            let abstract_id = $(this).attr('abstract_id')
            window.location.href= baseUrlAdmin+"abstract_acceptance_view/"+abstract_id;
        })

        $('#abstractTableBody').on('click', '.deleteAbstractBtn', function(){
            // console.log($(this).attr('abstract_id'))
            let abstract_id = $(this).attr('abstract_id')
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                if (result.isConfirmed) {
                    $.post(baseUrlAdmin+'delete_abstract', {'abstract_id': abstract_id}, function(data){
                        // console.log(data)

                        if(data.status == 'success'){
                            Swal.fire(
                            'Deleted!',
                            data.msg,
                            'success'
                            )
                        }else{
                            Swal.fire(
                            'Error!',
                            data.msg,
                            'error'
                            )
                        }
                    }, 'json')
                  
                    getAbstracts();
                }
            })
        })

        $('#abstractTableBody ').on('click', '.viewAbstractBtn', function(){
            let abstract_id = $(this).attr('abstract_id');
            if(abstract_id){
                window.location.href = baseUrlAdmin+'view_abstract/'+abstract_id;
            }
        })
    })

    async function getAbstracts() {
        if ($.fn.DataTable.isDataTable('#abstractTable')) {
            $('#abstractTable').DataTable().destroy();
        }

        // Display loading message using SweetAlert2

        Swal.fire({
            title: "Please Wait!",
            html: "Fetching All Abstracts...",
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
            },
        })

        return new Promise((resolve, reject) => {
            $.post(baseUrlAdmin + 'getAllPapers', {
                submission_type:'paper'
            }, function(response) {

                // console.log(response.data[0].division['name'])
                $('#abstractTableBody').html('');

                if(response.data.length < 1){
                    return false;
                }

                $.each(response.data, function(i, val) {
                    let division_name = (val.division ? val.division.name : '')
                    let reviewed = 0;
                    // console.log(val)
                    let type_name = 'N/A'
                    if(val.type.name !== '' || val.type.name !== undefined){
                        type_name = val.type.name
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

                    // Populate the table with the response data
                    substance_area = [];
                    let assignBtn = '<button class="btn btn-success btn-sm assignReviewerBtn mt-2" id="assignReviewerBtn" abstract_id =' + val.id + ' divisionName="' + division_name + '"  reviewers_reviewed = "'+reviewed+'"> Assign Now </button>'

                    // let assignBtn = '<button class="btn btn-success btn-sm addReviewerBtn mt-2" abstract_id=' + val.id + '><i class="fas fa-plus"></i> Add Reviewer </button>';
                    let deleteAbstractBtn = '<button class="btn btn-danger btn-sm deleteAbstractBtn mt-2 text-nowrap" abstract_id=' + val.id + '><i class="fas fa-times"></i> Delete Abstract </button>';
                    let viewAbstractBtn = '<button class="btn btn-primary btn-sm viewAbstractBtn text-nowrap" abstract_id=' + val.id + '><i class="fas fa-pager"></i> View Abstract </button>';
                    let acceptanceBtn = '<button class="btn btn-info btn-sm mt-2 acceptanceBtn text-nowrap"  abstract_id=' + val.id + '><i class="fas fa-list" ></i> Acceptance</button>';

                    let acceptance_preference = ((val.admin_acceptance_preference) == '1') ? 'Podium Presentation' :
                        ((val.admin_acceptance_preference) == '2') ? 'Poster Presentation' :
                            ((val.admin_acceptance_preference) == '3') ? 'ePoster Presentation' :
                                ((val.admin_acceptance_preference) == '4') ? 'Invited Speaker' :
                                    '';
                    let acceptance_date = val.presentation_date;
                    let acceptance_start = val.presentation_start_time;
                    let acceptance_end = val.presentation_end_time;
                    let acceptanceStatus = ((val.admin_acceptance_status == 'accepted') ?
                        '<span class="text-success text-nowrap "> Accepted: ' + acceptance_preference + '</span>' :
                        (val.admin_acceptance_status == 'declined') ?
                            '<span class="text-warning text-nowrap "> Declined: ' + acceptance_preference + '</span>' :
                            (val.admin_acceptance_status == 'reserved') ?
                                '<span class="text-info text-nowrap "> Reserved: ' + acceptance_preference + '</span>' :
                                (val.admin_acceptance_status == 'declined_withdraw') ?
                                    '<span class="text-danger text-nowrap "> Withdraw: ' + acceptance_preference + '</span>' :
                                    'No Acceptance')

                    let isFlag = '';
                    let adminComment  = '';
                    if(val.adminComment){
                        if(val.adminComment.is_flag == "1"){
                            isFlag = 'Yes'
                            adminComment  = val.adminComment.comment;
                        }else{
                            isFlag = 'No'

                        }
                    }

                    let dpc_final = '';
                    if (val.dpc) {
                        $.each(val.dpc, function(i, dpc) {
                            let acceptance_status = ((dpc.aceptance_status == '1') ?
                                '<span class="text-primary text-nowrap "> Approved for Proceedings </span>' :
                                (dpc.acceptance_status == '2') ?
                                    '<span class="text-primary text-nowrap "> Approved for Transactions </span>' :
                                    (dpc.acceptance_status == '3') ?
                                        '<span class="text-primary text-nowrap "> Approved for inclusion in the Division’s Program  </span>' :
                                        (dpc.acceptance_status == '4') ?
                                            '<span class="text-danger text-nowrap "> Rejected </span>' :
                                            '')


                            dpc_final += '<div class="card bg-transparent p-1 shadow-sm mb-1"><span class="fw-bolder"> Status: </span> ' + acceptance_status + '</div>';
                            dpc_final += '<div class="card bg-transparent p-1 shadow-sm mb-1"><span class="fw-bolder"> Comment : </span>' +(dpc.comments ? dpc.comments : '') + '</div>';
                            dpc_final += '<div class="card bg-transparent p-1 shadow-sm"><span class="fw-bolder"> Recommendation:</span> ' +(dpc.is_recommended_for_publications ? dpc.is_recommended_for_publications : '') +'<br/>'+ (dpc.is_suitable == "1" ? '<span class="badge bg-danger text-white">Not Suitable </span>' : '') + '</div> ';
                        });
                    }

                    let adminAcceptace = 'N/A'
                    let adminPresentationPref = 'N/A';
                    if(val.adminOption){
                        if(val.adminOption.acceptance_confirmation == 1){
                            adminAcceptace = "Accepted"
                            if(val.adminOption.presentation_preference == 1){
                                adminPresentationPref = 'Presentation Only';
                            }else if(val.adminOption.presentation_preference == 2){
                                adminPresentationPref = 'Publication Only';
                            }else if(val.adminOption.presentation_preference == 3){
                                adminPresentationPref = 'Presentation and Publication';
                            }
                        }else if(val.adminOption.acceptance_confirmation == 2){
                            adminAcceptace = "Rejected"
                        }else if(val.adminOption.acceptance_confirmation == 3){
                            adminAcceptace = "Suggested Revision"
                        }else if(val.adminOption.acceptance_confirmation == 4){
                            adminAcceptace = "Required Revision"
                        }else if(val.adminOption.acceptance_confirmation == 5){
                            adminAcceptace = "Declined/Withdrawn for Participation"
                        }
                    }

                    let submitterComments = ''
                    if(val.reviewers){

                        $.each(val.reviewers, function(i, reviewer){
                            // console.log(reviewer)
                            if(reviewer.review){
                                submitterComments += reviewer.review.submitter_comment_on_upload
                                reviewed ++;
                            }
                        })
                    }

                    // console.log(val)

                    $('#abstractTableBody').append('<tr class="tableRow" style="cursor:pointer" abstract_id="' + val.id + '">' +
                        '<td>' + val.custom_id + '</td>' +
                        '<td id="authorList_' + val.id + '" class="author_td"></td>' +
                        '<th>' + stripTags(val.title.replace( /<.*?>/g, '' ) ) + '</th>' +
                        // '<td id="topics_' + val.submission_type + '">'+val.submission_type+'</td>' +
                        '<td id="topics_' + val.type.id + '">'+type_name+'</td>' +
                        '<td id="population_' + val.division.id + '">'+val.division.name+'</td>' +
                        '<td class=""> <span class="text-nowrap fw-bolder">' + val.user_name + ' ' + val.user_surname +'</span><br> Comment:'+ submitterComments+ '</td>' +
                        '<td class="text-nowrap">'+upload+'</td>' +
                        '<td class="text-nowrap">'+adminAcceptace+' <br> ('+adminPresentationPref+')</td>' +
                        // '<td class="text-nowrap">'+adminPresentationPref+'</td>' +
                        '<td id="reviewer_' + val.id + '"></td>' +
                        '<td>'+dpc_final+'</td>' +
                        '<td>'+`<strong class="text-primary">Author Acceptance</strong> <br> <span class="text-nowrap" id="author-acceptance-${val.id}"></span>` +
                        '<br>' + ((acceptance_date) ? "Date: " + acceptance_date : '') + ((acceptance_start) ? ", Start: " + acceptance_start : '') + ((acceptance_end) ? " - End: " + acceptance_end : '') +
                        '</td>' +
                        '<td>'+isFlag+'<br>'+adminComment+'</td>' +
                        '<td style="min-width:96px">' + viewAbstractBtn + '<br>' + assignBtn + '<br>' + acceptanceBtn + '<br>' + deleteAbstractBtn + '</td>' +
                        '</tr>');
                });
            }, 'json').then(function(r) {
                // Additional processing after fetching abstracts
                $.each(r.data, function(i, item) {
                    // console.log(item)
                    // Process authors, reviewers, topics, etc.
                    let author_institution = '';
                    let authorCopyrightStatus = '';
                        if(item.authors) {
                            $.each(item.authors, function (i, author) {
                                if (author) {
                                    if (author.institution)
                                        author_institution = " <br><i class='badge bg-info'>(" + author.institution.name + ")</i>";
                                    if(author.copyright_agreement_date !== '' && author.is_copyright_agreement_accepted == 1)
                                        authorCopyrightStatus = "<i class='ms-2 fas fa-check text-success'></i>";
                                    else authorCopyrightStatus = "<i class='ms-2 fas fa-times text-danger'></i>";
                                }
                                $('#authorList_' + author.paper_id).append('<div class="text-nowrap">' + ((author.is_presenting_author == 'Yes') ? '<span class="fw-bolder">Lead Presenter: </span>' : '<span class="fw-bolder">Co Presenter: </span>') + author.user_name + ' ' + author.user_surname + authorCopyrightStatus+ '</div>');

                                let authorAcceptance = '';

                                if (author.is_presenting_author === 'Yes') {
                                    if (item.adminOption && parseInt(item.adminOption.acceptance_confirmation, 10) !== 2) {
                                        let name = `${author.user_name} ${author.user_surname}`;

                                        if (author.acceptance && author.acceptance.acceptance_confirmation !== undefined) {
                                            switch (parseInt(author.acceptance.acceptance_confirmation, 10)) {
                                                case 1:
                                                    authorAcceptance += `<strong>Yes, will participate</strong> (${name})`;
                                                    if (author.acceptance.presentation_saved_name.trim() !== "") {
                                                        authorAcceptance += "<span class='badge bg-success'>uploaded</span>";
                                                    }
                                                    authorAcceptance += `<br>`
                                                    break;
                                                case 2:
                                                    authorAcceptance += `<strong>No, cannot participate</strong> (${name})<br>`;
                                                    break;
                                                default:
                                                    authorAcceptance += `<strong>Incomplete</strong> (${name})<br>`;
                                            }
                                        } else {
                                            authorAcceptance += `<strong>Incomplete</strong> (${name})<br>`;
                                        }
                                    } else {
                                        console.log('Admin acceptance is 2 or undefined'); // Debugging message
                                        authorAcceptance += `<strong>N/A</strong><br>`;
                                    }

                                }



                                $('#author-acceptance-'+author.paper_id).append(`<div class='text-nowrap'>${authorAcceptance}</div>`)
                            });
                        }
                });
                $.each(r.data, function(i, item) {
                    let reviewer_institution = '';
                    $.each(item.reviewers, function(i, reviewer) {
                        let reviewer_declined = ''
                        let reviewer_approved = ''
                        if (reviewer) {
                            if(reviewer.is_declined == 1){
                                reviewer_declined = '<span class="fas fa-exclamation-circle text-danger ms-1" title="Declined By Reviewer" style="font-size:10px !important">Declined</span>';
                            }else if(reviewer.review !== null){
                                reviewer_declined = '<span class="fas fa-check-circle text-success ms-1" title="Reviewed By Reviwer" style="font-size:10px !important"> Reviewed </span>';
                            }

                            if(reviewer.review){
                                // console.log(reviewer)
                                if(reviewer.review.is_approved == 1) {
                                    reviewer_approved = '<span class="badge bg-success ms-1" title="Approved By Reviewer" style="font-size:10px !important">100% Complete</span>';
                                }else if(reviewer.review.is_approved == 2){
                                    reviewer_approved = '<span class="badge bg-warning ms-1" title="I still have concern" style="font-size:10px !important">I still have concern</span>';
                                }
                            }
                        }
                        $('#reviewer_' + reviewer.paper_id).append('<div class="text-nowrap card bg-transparent shadow-sm p-1 mb-1">'+ reviewer.details.name + ' ' + reviewer.details.surname + reviewer_institution + reviewer_declined+ reviewer_approved+'</div>');
                    });
                });
                // Initialize DataTable
                $('#abstractTable').DataTable({
                    paging: false,
                    dom: 'Bfrtip',
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                    // Apply Bootstrap 5 styling
                    buttons: {
                        dom: {
                            button: {
                                className: 'btn btn-outline-primary'
                            }
                        }
                    }
                });

                // Close loading message
                swal.close();
                // Resolve the promise with the response data
                resolve(r);
            }).fail(function(error) {
                console.error('Error fetching abstracts:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to fetch abstracts',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                reject(error); // Reject the promise if there's an error
            });
        });

    }

    function stripTags(input) {
        return $("<div>").html(input).text();
    }


</script>