

<?php echo view('admin/common/menu'); ?>
<Style>
    #abstractTable_filter{
        margin-bottom:10px
    }

    .sticky-top{
        top: 50px;
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
                    <table id="abstractTable" class="table-responsive table-bordered border-5" >
                        <thead class="table-active sticky-top" style="">
                        <tr>
                            <th>ID</th>
                            <th>Author List</th>
                            <th>Paper Title</th>
                            <th>Category</th>
                            <th>Acceptance <br> Status</th>
                            <th>Accepted Type</th>
                            <th>Reviewer</th>
                            <th>Participation</th>
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
                        // getRegularReviewersByDivision(paper_id, divisionName)
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

        fetchAllPapers();

        async function fetchAllPapers() {
            try {
                const response = await $.post(`${baseUrlAdmin}getAllPapers`, { submission_type: 'paper' });

                $('#abstractTableBody').empty();

                // console.log(response)
                if (!response.data.length){
                    swal.close();
                    toastr.info('No submitted papers found!')
                    return
                };

                response.data.forEach(paper => {
                    const divisionName = paper.division?.name || '';
                    const typeName = paper.type?.name || 'N/A';
                    const uploadStatus = getUploadStatus(paper);
                    const acceptanceStatus = getAdminAcceptance(paper);
                    const presentationPref = getPresentationPreference(paper);
                    const color = getAdminAcceptanceColor(paper);
                    const dpcFinal = getDPCStatus(paper);
                    const submitterComments = getSubmitterComments(paper);
                    const isFlagged = paper.adminComment?.is_flag === "1" ? 'Yes' : 'No';
                    const adminComment = paper.adminComment?.comment || '';

                    const buttons = generateButtons(paper.id);

                    const category = paper.category.name

                    $('#abstractTableBody').append(`
                <tr class="tableRow" style="cursor:pointer; background-color: ${color}" abstract_id="${paper.id}">
                    <td>${paper.custom_id}</td>
                    <td id="authorList_${paper.id}" class="author_td"></td>
                    <td>${stripTags(paper.title)}</td>
                    <td id="">${category}</td>
                    <td class="text-nowrap">${acceptanceStatus}</td>
                    <td class="text-nowrap">${presentationPref}</td>
                    <td id="reviewer_${paper.id}"></td>
                    <td><strong class="text-primary">Author Acceptance</strong><br><span id="author-acceptance-${paper.id}"></span></td>
                    <td>${isFlagged}<br>${adminComment}</td>
                    <td style="min-width:96px">${buttons}</td>
                </tr>
            `);
                });

                populateAdditionalData(response.data);

                initializeDataTable();
                swal.close();
            } catch (error) {
                console.error('Error fetching abstracts:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to fetch abstracts',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            }
        }

        function getUploadStatus(paper) {
            if (!paper.uploads || !paper.uploads.length) return '';

            return paper.uploads.length === 1
                ? '<span class="badge bg-success text-white">[NEW]</span>'
                : '<span class="badge bg-success text-white">[Re-Upload]</span>';
        }

        function getAdminAcceptance(paper) {
            if (!paper.adminOption) return 'N/A';

            const acceptanceMap = {
                1: 'Accepted',
                2: 'Rejected',
                3: 'Suggested Revision',
                4: 'Required Revision',
                5: 'Declined/Withdrawn for Participation'
            };

            const acceptanceStatus = acceptanceMap[paper.adminOption.acceptance_confirmation] || 'N/A';
            return `${acceptanceStatus}`;
        }

        function getPresentationPreference(paper){
            if (!paper.adminOption) return '';
            let presentationPref = ''
            $.each(paper.types, function(i, type){
                // console.log(type)
                if(type && type.type === paper.adminOption.presentation_preference)
                    presentationPref = type.name || 'N/A';
            })
            return presentationPref;
        }

        function getAdminAcceptanceColor(paper) {
            if (!paper.adminOption) return '';
            let color = ''
            $.each(paper.types, function(i, type){
                if(type && type.type === paper.adminOption.presentation_preference)
                    color = type.color;
            })

            return `${color}`;
        }

        function getDPCStatus(paper) {
            if (!paper.dpc) return '';

            return paper.dpc.map(dpc => `
        <div class="card bg-transparent p-1 shadow-sm mb-1">
            <span class="fw-bolder">Status: </span> ${getAcceptanceStatus(dpc.acceptance_status)}
        </div>
        <div class="card bg-transparent p-1 shadow-sm mb-1">
            <span class="fw-bolder">Comment: </span> ${dpc.comments || ''}
        </div>
        <div class="card bg-transparent p-1 shadow-sm">
            <span class="fw-bolder">Recommendation:</span> ${dpc.is_recommended_for_publications || ''}
        </div>
    `).join('');
        }

        function getAcceptanceStatus(status) {
            const statusMap = {
                1: '<span class="text-primary">Approved for Proceedings</span>',
                2: '<span class="text-primary">Approved for Transactions</span>',
                3: '<span class="text-primary">Approved for Inclusion in Division’s Program</span>',
                4: '<span class="text-danger">Rejected</span>'
            };
            return statusMap[status] || '';
        }

        function getSubmitterComments(paper) {
            if (!paper.reviewers) return '';

            return paper.reviewers
                .filter(reviewer => reviewer.review)
                .map(reviewer => reviewer.review.submitter_comment_on_upload)
                .join('<br>');
        }

        function generateButtons(id) {
            return `
        <button class="btn btn-primary btn-sm viewAbstractBtn" abstract_id="${id}">
            <i class="fas fa-pager"></i> View Abstract
        </button>
        <button class="btn btn-success btn-sm assignReviewerBtn mt-2" abstract_id="${id}">
            Assign Now
        </button>
        <button class="btn btn-info btn-sm mt-2 acceptanceBtn" abstract_id="${id}">
            <i class="fas fa-list"></i> Acceptance
        </button>
        <button class="btn btn-danger btn-sm deleteAbstractBtn mt-2" abstract_id="${id}">
            <i class="fas fa-times"></i> Delete Abstract
        </button>
    `;
        }

        function populateAdditionalData(data) {
            data.forEach(paper => {
                populateAuthors(paper);
                populateReviewers(paper);
            });
        }

        function populateAuthors(paper) {
            if (!paper.authors) return;

            paper.authors.forEach(author => {
                if(author.is_removed === '0') {
                    const institution = author.institution ? ` <i class='badge bg-info'>(${author.institution.name})</i>` : '';
                    const copyrightStatus = (author.details && author.details.signature_signed_date !== null)
                        ? "<i class='ms-2 fas fa-check text-success'></i>"
                        : "<i class='ms-2 fas fa-times text-danger'></i>";

                    $('#authorList_' + author.paper_id).append(`
            <div class="text-nowrap">
                ${author.is_presenting_author === 'Yes' ? '<span class="fw-bolder">Lead Presenter: </span>' : '<span class="fw-bolder">Co Presenter: </span>'}
                ${author.user_name} ${author.user_surname} ${copyrightStatus}
            </div>
        `);

                    $('#author-acceptance-' + author.paper_id).append(`<div class="text-nowrap">${getAuthorAcceptance(paper, author)}</div>`);
                }
            });

        }

        function getAuthorAcceptance(paper, author) {
            if (!author.is_presenting_author || !paper.adminOption || paper.adminOption.acceptance_confirmation === 2) {
                return ``;
            }

            switch (parseInt(author.acceptance?.acceptance_confirmation, 10)) {
                case 1:
                    return `<strong>Yes, will participate</strong> (${author.user_name} ${author.user_surname})` +
                        (author.acceptance.presentation_saved_name.trim() ? `<span class='badge bg-success'>uploaded</span>` : '');
                case 2:
                    return `<strong>No, cannot participate</strong> (${author.user_name} ${author.user_surname})`;
                default:
                    return `<strong>Incomplete</strong> (${author.user_name} ${author.user_surname})`;
            }
        }

        function populateReviewers(paper) {
            if (!paper.reviewers) return;

            paper.reviewers.forEach(reviewer => {
                const statusBadge = reviewer.is_declined
                    ? '<span class="text-danger ms-1">Declined</span>'
                    : reviewer.review
                        ? '<span class="text-success ms-1">Reviewed</span>'
                        : '';

                $('#reviewer_' + reviewer.paper_id).append(`
            <div class="text-nowrap card bg-transparent shadow-sm p-1 mb-1">
                ${reviewer.details.name} ${reviewer.details.surname} ${statusBadge}
            </div>
        `);
            });
        }

        function initializeDataTable() {
            $('#abstractTable').DataTable({
                paging: false,
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                buttons: { dom: { button: { className: 'btn btn-outline-primary' } } }
            });
        }


    }

    function stripTags(input) {
        return $("<div>").html(input).text();
    }


</script>