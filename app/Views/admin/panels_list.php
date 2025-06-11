

<?php echo view('admin/common/menu'); ?>

<main>
    <div class="container-fluid" style="padding-bottom:150px">
        <div class="customButtonsDiv mb-2">
<!--        <a href="--><?php //=base_url()?><!--admin/exportScores" class="btn btn-success text-white position-relative" title="Export all abstract scores to excel">Export All Abstract Scores </a>-->
        </div>
        <div class="" id="panelDiv">


        </div>

<!--        <table id="abstractTable" class="table table-striped">-->
<!--            <thead>-->
<!--                <th>ID</th>-->
<!--                <th>Author List</th>-->
<!--                <th>Paper Title</th>-->
<!--                <th>Submission Type</th>-->
<!--                <th>Type</th>-->
<!--                <th>Division</th>-->
<!--                <th>Submitter</th>-->
<!--                <th>Reviewer</th>-->
<!--                <th>Status</th>-->
<!--                <th>Action</th>-->
<!--            </thead>-->
<!--            <tbody id="abstractTableBody">-->

<!--            </tbody>-->
<!--        </table>-->

    </div>
</main>


<script>
    let baseUrlAdmin = "<?=base_url().'admin/'?>";
    $(function(){

        getAbstracts();
        

        $("#abstractTableBody").on('click', '.addReviewerBtn', function(){ // Submit Reviews
            let abstract_id = $(this).attr('abstract_id')
            window.location.href= baseUrlAdmin+"assign_reviewer_view/"+abstract_id;
        })

        $("#abstractTableBody").on('click', '.acceptanceBtn', function(){ // Submit Reviews
            let abstract_id = $(this).attr('abstract_id')
            window.location.href= baseUrlAdmin+"abstract_acceptance_view/"+abstract_id;
        })

        $('#abstractTableBody').on('click', '.deleteAbstractBtn', function(){
            console.log($(this).attr('abstract_id'))
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
                        console.log(data)

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

        $(document).on('click', '.panelRow td', function(event) {
            let columnIndex = $(this).index(); // Get the column index

            if (columnIndex === 9) {
                event.stopPropagation(); // Prevent the event from bubbling up
                return;
            }

            let individual_panel_id = $(this).closest('.panelRow').attr('individual_panel_id');
            if (individual_panel_id) {
                window.location.href = baseUrlAdmin + 'view_individual_panel/' + individual_panel_id;
            }
        });

        $(document).on('click', '.viewPanelBtn' , function(e){
            e.preventDefault();
            let abstract_id = $(this).attr('abstract_id');
            if(abstract_id){
                window.location.href = baseUrlAdmin+'view_abstract_panel/'+abstract_id;
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
            $.post(baseUrlAdmin + 'getAllPanels', {
                submission_type: 'panel'
            }, function (response) {

                $('#abstractTableBody').html('');
                $.each(response, function (i, val) {
                    // console.log(val)
                    let type_name = 'N/A';
                    if (val.type.name) {
                        type_name = val.type.name;
                    }

                    // Acceptance status
                    let acceptance_preference = (val.admin_acceptance_preference == '1') ? 'Podium Presentation' :
                        (val.admin_acceptance_preference == '2') ? 'Poster Presentation' :
                            (val.admin_acceptance_preference == '3') ? 'ePoster Presentation' :
                                (val.admin_acceptance_preference == '4') ? 'Invited Speaker' : '';
                    let acceptance_date = val.presentation_date;
                    let acceptance_start = val.presentation_start_time;
                    let acceptance_end = val.presentation_end_time;
                    let acceptanceStatus = (val.admin_acceptance_status == 'accepted') ? '<span class="text-success text-nowrap "> Accepted: ' + acceptance_preference + '</span>' :
                        (val.admin_acceptance_status == 'declined') ? '<span class="text-warning text-nowrap "> Declined: ' + acceptance_preference + '</span>' :
                            (val.admin_acceptance_status == 'reserved') ? '<span class="text-info text-nowrap "> Reserved: ' + acceptance_preference + '</span>' :
                                (val.admin_acceptance_status == 'declined_withdraw') ? '<span class="text-danger text-nowrap "> Withdraw: ' + acceptance_preference + '</span>' : 'No Acceptance';

                    // let viewAbstractBtn = '<button class="btn btn-primary btn-sm viewAbstractBtn text-nowrap" abstract_id=' + val.id + '><i class="fas fa-pager"></i> View Abstract </button>';
                    // Create elements
                    let cardDiv = $('<div>', { class: 'card panelDetails shadow-sm m-2' });
                    let cardHeader = $('<div>', { class: 'card-header' });
                    let panelNum = $('<div>', { class: 'fw-bolder', text: 'Panel # ' });
                    let coordinatorDiv = $('<div>', { class: 'fw-bolder', text: 'Coordinator(s) :' });
                    let title = $('<div>', { class: 'fw-bolder', text: 'Title : ' });
                    let division = $('<div>', { class: 'fw-bolder', text: 'Division : ' });
                    let cardBody = $('<div>', { class: 'card-body' });
                    let table = $('<table>', { class: 'table table-striped table-bordered table-hover' });
                    let thead = $('<thead>');
                    let tbody = $('<tbody>', { id: 'abstractTableBody_'+val.id });

                    panelNum.append('<a href="" class="btn btn-outline-primary btn-sm viewPanelBtn" abstract_id="'+val.id+'">'+val.custom_id+'</a>')

                    title.append(val.title.replace(/<.*?>/g, ''))
                    division.append(val.division.name)

                    if(val.coordinators){
                        $.each(val.coordinators, function(i, coordinator){
                            coordinatorDiv.append('<span class="ms-2">'+coordinator.user_name+' '+coordinator.user_surname+'</span>')
                        })
                    }
// Append header elements
                    cardHeader.append(panelNum, coordinatorDiv, title, division);

// Create table headers
                    let tr = $('<tr>');
                    let headers = ['Abstract #', 'Author', 'Copyright', 'Code', 'Status', 'Formal Upload', 'Category', 'Comments', 'Completion', 'Participate', 'Actions'];
                    headers.forEach(headerText => {
                        tr.append($('<th>', { text: headerText }));
                    });
                    thead.append(tr);

                    table.append(thead, tbody);

                    cardDiv.append(cardHeader, cardBody.append(table));

                    $('#panelDiv').append(cardDiv);


                    $.each(val.panelists, function(i, panel){
                        if((!panel.is_removed || panel.is_removed == '0')  && panel.details) {
                            $('#abstractTableBody_' + val.id).append(
                                createPanel(val, panel,)
                            );
                        }
                    })

                    function createPanel(val, panel) {
                        let $tr = $('<tr class="panelRow" individual_panel_id="'+panel.details.id+'">');

                        // Add the table data cells
                        let customId = 'N/A';
                        if (panel.details) {
                            customId = panel.details.custom_id;
                        }
                        let copyright = 'N/A';
                        if(panel.is_copyright_agreement_accepted == 1){
                            copyright =  '<i class="fas fa-check text-success" aria-hidden="true"></i>'
                        }else{
                            copyright = '<i class="fas fa-times text-danger" aria-hidden="true"></i>'
                        }

                        let adminIndividualAcceptance = panel.admin_individual_acceptance;
                        let individualAcceptanceStatus = ''

                        if(adminIndividualAcceptance !== null){
                            if(adminIndividualAcceptance.acceptance_confirmation == 5){
                                individualAcceptanceStatus = "Declined/Withdrawn for Participation"
                            }else if(adminIndividualAcceptance.acceptance_confirmation == 4){
                                individualAcceptanceStatus = "Required Revision"
                            }else if(adminIndividualAcceptance.acceptance_confirmation == 3){
                                individualAcceptanceStatus = "Suggested Revision"
                            }else if(adminIndividualAcceptance.acceptance_confirmation == 2){
                                individualAcceptanceStatus = "Rejected"
                            }else if(adminIndividualAcceptance.acceptance_confirmation == 1){
                                individualAcceptanceStatus = "Accepted"
                            }
                        }

                        let individualPresentationPref = '';
                        if(adminIndividualAcceptance !== null){
                            if(adminIndividualAcceptance.presentation_preference == 1){
                                individualPresentationPref = "Presentation Only"
                            }else if(adminIndividualAcceptance.presentation_preference == 2){
                                individualPresentationPref = "Publication Only"
                            }else if(adminIndividualAcceptance.presentation_preference == 3){
                                individualPresentationPref = "Presentation and Publication"
                            }
                        }

                        let panelistAcceptance = '';

                        if (panel.acceptance) {
                            let confirmation = panel.acceptance.acceptance_confirmation;
                            if (confirmation === "1") {
                                panelistAcceptance = "Yes";
                                if (panel.acceptance.presentation_saved_name.trim() !== "") {
                                    panelistAcceptance += "<span class='badge text-success badge-success'>(Uploaded)</span>";
                                }
                            } else if (confirmation === "2") {
                                panelistAcceptance = "No";
                            } else {
                                panelistAcceptance = "N/A";
                            }
                        } else {
                            panelistAcceptance = "N/A";
                        }



                        $tr.append($('<td>').text(customId));
                        $tr.append($('<td>').text(panel.user_name + ' ' + panel.user_surname));
                        $tr.append($('<td>').html(copyright)); // Empty cell
                        $tr.append($('<td>').text(panel.custom_code));
                        $tr.append($('<td>').text(individualAcceptanceStatus+ ( individualAcceptanceStatus == 'Accepted' ? ' ('+ individualPresentationPref +')' :'' ))); // Empty cell
                        $tr.append($('<td>')); // Empty cell
                        $tr.append($('<td>')); // Empty cell
                        $tr.append($('<td>')); // Empty cell
                        $tr.append($('<td>')); // Empty cell
                        $tr.append($('<td>').html(`<div class='text-nowrap'>${panelistAcceptance}</div>`)); // Empty cell
                        $tr.append($('<td>')); // Empty cell

                        // Return the constructed row element
                        return $tr;
                    }

                });
            }, 'json').then(function (r) {
                // Additional processing after fetching abstracts

                // Process reviewers
                $.each(r, function (i, item) {
                    let reviewer_institution = '';
                    $.each(item.reviewers, function (i, reviewer) {
                        if (reviewer && reviewer.institution) {
                            reviewer_institution = " <br><i class='badge bg-info badge-sm w-100'>(" + reviewer.institution.name + ")</i>";
                        }
                        $('#reviewer_' + reviewer.abstract_id).append('<div class="">' +
                            '<span reviewer_="' + reviewer.id + '">' +
                            ((reviewer.abstract_review == "0") ? '<i class="fas fa-check text-success h5"></i> ' :
                                (reviewer.abstract_review == "1") ? '<span class="text-danger h5"> COI </span> ' :
                                    '<i class="fas fa-check opacity-0 h5"></i> ') +
                            reviewer.name + ' ' + reviewer.surname + reviewer_institution + '</span></div><br>');
                    });
                });

                // Initialize DataTable
                $('#abstractTable').dataTable({
                    paging: false,
                    dom: 'Bfrtip',
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
                });

                // Close loading message
                swal.close();

                // Resolve the promise with the response data
                resolve(r);
            }).fail(function (error) {
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

</script>