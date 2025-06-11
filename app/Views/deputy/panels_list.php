

<?php echo view('deputy/common/menu'); ?>

<main>
    <div class="container-fluid">
        <?php echo view('admin/common/shortcut_link'); ?>
        <div class="card">
            <div class="card-body">
                <table id="papersTable" class="table table-striped">
                    <thead>
                        <th>ID</th>
                        <th>Submitter</th>
                        <th>Panel Title</th>
                        <th>Division</th>
                        <th>Review Progress</th>
                        <th>Total</th>
                        <th>Average</th>
                        <th>Status</th>
                        <th>Action</th>
                    </thead>
                    <tbody id="papersTableBody"  style="overflow:auto">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>


<!-- Modal -->
<div class="modal fade" id="ReviewPanelSubmittedModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignRegularModalLabel">  Score and Comments Panel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="height:80vh; overflow-y:auto">
                <form id="formChairAcceptance">
                    <div class="p-0 card m-0">
                        <div class="card-header"> Chair Options </div>
                        <div class="card-body">
                            <form id="formChairAcceptance">
                                <div>
                                    <div class="form-group">
                                        <label for="comments_program">Aluminum Program Chair Comments:</label>
                                        <textarea id="comments_program" name="comments_program" rows="7" class="form-control" placeholder="Type here..."></textarea>
                                    </div>

                                    <div id="acceptDiv" class="form-group mt-3">
                                        <label><span class="text-danger">*</span><b>Accept/Reject:</b></label>
                                        <div>
                                            <input type="radio" id="accept" name="acceptance" value="1" class="form-radio">
                                            <label for="accept">Accept for proceedings</label><br>

                                            <input type="radio" id="reject" name="acceptance" value="2" class="form-radio">
                                            <label for="reject">Reject for proceedings</label><br>

                                            <input type="radio" id="suggested" name="acceptance" value="3" class="form-radio">
                                            <label for="suggested">Suggested revisions</label><br>

                                            <input type="radio" id="required" name="acceptance" value="4" class="form-radio">
                                            <label for="required">Required revisions</label><br>
                                        </div>
                                    </div>


                                </div>



                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="acceptanceBtn" type="button" class="btn btn-success">Save and Close</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>
<?=view('deputy/common/panelDetailsModal')?>
<script>
    let baseUrlDeputy = "<?=base_url().'deputy/'?>";
    $(function(){

        getPanels();
        $('#regularReviewerTable').DataTable({})

        //todo: add accept and reject function instead of assigning a regular reviewer

        $('#papersTableBody').on('click', '#reviewNowBtn', function(){
            let paper_id = $(this).attr('paper_id');
            $('#ReviewPanelSubmittedModal #acceptanceBtn').attr('paper_id', paper_id);

            $.get(baseUrlDeputy + 'getAcceptanceDetails/' + paper_id, function(response){
                // console.log(response);
                $('#formChairAcceptance').trigger('reset');
                if(response.status === 200) {
                    // Populate form with response data
                    $('#comments_program').val(response.data.comments); // Set comments

                    // Set acceptance status based on response data
                    switch (response.data.acceptance_status) {
                        case '1':
                            $('#accept').prop('checked', true); // Accept
                            break;
                        case '2':
                            $('#reject').prop('checked', true); // Reject
                            break;
                        case '3':
                            $('#suggested').prop('checked', true); // Suggested revisions
                            break;
                        case '4':
                            $('#required').prop('checked', true); // Required revisions
                            break;
                        default:
                            // Default action
                            break;
                    }
                }
            },'json');

            $('#ReviewPanelSubmittedModal').modal('show');
        });


        // $("#papersTableBody").on('click', '#assignReviewerBtn', function(){
        //
        //     if ($.fn.DataTable.isDataTable('#regularReviewerTable')) {
        //         // Destroy the existing DataTable instance
        //         $('#regularReviewerTable').DataTable().destroy();
        //     }
        //
        //     let paper_id = $(this).attr('paper_id')
        //     let divisionName = $(this).attr('divisionName')
        //     $.post(baseUrlDeputy+'getRegularReviewersByDivision',{
        //         'paper_id':paper_id
        //     }, function(result){
        //         if(result.status == '200') {
        //             console.log(result)
        //
        //             $('#regularReviewerTableBody').html('');
        //             $.each(result.data, function (i, val) {
        //                 console.log(val)
        //                 let isAssigned;
        //                 if(val.is_assigned && val.is_assigned.is_deleted !== 1){
        //                     isAssigned = 'checked';
        //                 }
        //                 let selectReviewerBox = '<input type="checkbox" class="selectReviewerBox" name="selectReviewerBox" '+isAssigned+' id="" paperID = "'+paper_id+'" reviewerID = "'+val.id+'">'
        //                 $('#regularReviewerTableBody').append(
        //                     '<tr>' +
        //                     '<td>'+selectReviewerBox+'</td>' +
        //                     '<td>' + val.user_name + ' ' + val.surname + '</td>' +
        //                     '<td>'+((val.institution_name)? val.institution_name:'')+'</td>' +
        //                     '<td></td>' +
        //                     '<td></td>' +
        //                     '<td></td>' +
        //                     '</tr>'
        //                 );
        //             })
        //         }
        //         $('#regularReviewerTable').DataTable({})
        //     },'json')
        //     $("#assignRegularModal").modal('show')
        //     $('#assignRegularModalLabel').html('Regular Reviewers List <br> Division: <strong>' +divisionName +'</strong>')
        //
        // })
        //
        // $('#regularReviewerTableBody').on('click', '.selectReviewerBox', function(){
        //     let reviewerID = $(this).attr('reviewerID')
        //     let paperID = $(this).attr('paperID')
        //     let isChecked =  $(this).prop('checked');
        //     $.post(baseUrlDeputy+'assignPaperToRegularReviewer',{
        //         'reviewerID':reviewerID,
        //         'paperID': paperID,
        //         'isChecked': isChecked
        //         // 'reviewerID':reviewerID
        //     }, function(){
        //
        //     })
        // })

        $('#acceptanceBtn').on('click', function(){
            let formData = new FormData(document.getElementById('formChairAcceptance'));
            let paperID = $(this).attr('paper_id')
            formData.append('paper_id', paperID)
            $.ajax({
                url: baseUrlDeputy + 'acceptance',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    response = JSON.parse(response)
                    console.log(response)
                    if(response.status == "200") {
                        Swal.fire({
                            icon: "success",
                            title: "Your work has been saved",
                            showConfirmButton: true,
                            timer: 1500
                        }).then((result) => {
                            /* Read more about isConfirmed, isDenied below */
                            if (result.isConfirmed) {
                                $('#ReviewPanelSubmittedModal').modal('hide');
                            }
                        });
                    }
                    else {
                        Swal.fire({
                            icon: "error",
                            title: "Please contact administrator!",
                            showConfirmButton: true,
                            timer: 1500
                        });
                    }
                }
            });
        });

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
                        var individual_panel = paper.individual_panel;
                        // Populate General Information
                        $('.general-info #paper-id').text(paper.custom_id);
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
                        let authorsHtml = '';
                        if (authors.length > 0) {

                            authors.forEach(function(author) {

                                var capitalizedFirstName = author.name.charAt(0).toUpperCase() + author.name.slice(1);
                                var capitalizedLastName = author.surname.charAt(0).toUpperCase() + author.surname.slice(1);
                                if (author.author_type == "coordinator") {
                                    authorsHtml += '<tr><td class="text-end" style="width:200px">Coordinator: </td>';
                                    authorsHtml += '<td>';
                                    authorsHtml += '<span style="font-weight: bold;">' + capitalizedFirstName + ' ' + capitalizedLastName + '</span><br>';
                                    authorsHtml += '<tr><td  class="text-end">Institution: </td>';
                                    authorsHtml += '<td>'+author.institution+'</td></tr>';
                                    authorsHtml += '<tr><td  class="text-end">Email: </td>';
                                    authorsHtml += '<td>'+author.email+'</td></tr>';
                                }
                                authorsHtml += '</td></tr>';
                            });

                        } else {
                            authorsHtml = '<tr><td colspan="2">No authors available</td></tr>';
                        }

                        let panelHtml = '';
                        if (individual_panel.length > 0) {
                            panelHtml += '<div class="card">';
                            panelHtml += '<div class="card-body">';

                            let index = 0;
                            individual_panel.forEach(function(individual_panel) {
                                index++;
                                var capitalizedFirstName = individual_panel.name.charAt(0).toUpperCase() + individual_panel.name.slice(1);
                                var capitalizedLastName = individual_panel.surname.charAt(0).toUpperCase() + individual_panel.surname.slice(1);
                                var individualPanelTitle = individual_panel.individual_panel_title;

                                panelHtml += '<div class="card mb-3">';
                                panelHtml += '<div class="card-header">';
                                panelHtml += '<span class="fw-bold">' + index + '. Panel: ' + individual_panel.custom_id + '</span>';
                                panelHtml += '</div>';
                                panelHtml += '<div class="card-body">';
                                panelHtml += '<div class="row mb-2">';
                                panelHtml += '<div class="col-4 text-end fw-bold">Presenting Author:</div>';
                                panelHtml += '<div class="col-8">' + capitalizedFirstName + ' ' + capitalizedLastName + '</div>';
                                panelHtml += '</div>';
                                panelHtml += '<div class="row mb-2">';
                                panelHtml += '<div class="col-4 text-end fw-bold">Institution:</div>';
                                panelHtml += '<div class="col-8">' + (individual_panel.institution ?? '') + '</div>';
                                panelHtml += '</div>';
                                panelHtml += '<div class="row mb-2">';
                                panelHtml += '<div class="col-4 text-end fw-bold">Email:</div>';
                                panelHtml += '<div class="col-8">' + individual_panel.email + '</div>';
                                panelHtml += '</div>';
                                panelHtml += '<div class="row mb-2">';
                                panelHtml += '<div class="col-4 text-end fw-bold">Individual Paper ID:</div>';
                                panelHtml += '<div class="col-8">' + individual_panel.custom_id + '</div>';
                                panelHtml += '</div>';
                                panelHtml += '<div class="row mb-2">';
                                panelHtml += '<div class="col-4 text-end fw-bold">Individual Title Submitted:</div>';
                                panelHtml += '<div class="col-8">' + individualPanelTitle + '</div>';
                                panelHtml += '</div>';
                                panelHtml += '</div>'; // Close card-body
                                panelHtml += '</div>'; // Close card
                            });

                            panelHtml += '</div>'; // Close card-body
                            panelHtml += '</div>'; // Close card
                        } else {
                            panelHtml = '<div class="card">';
                            panelHtml += '<div class="card-body">';
                            panelHtml += '<p>No authors available</p>';
                            panelHtml += '</div>'; // Close card-body
                            panelHtml += '</div>'; // Close card
                        }





                        $('.author-info tbody').html(authorsHtml);
                        $('.panelist-info-body').html(panelHtml);

                        // Populate Paper Information
                        $('.general-info #division').text(paper.division_name);
                        // $('.general-info #paper-type').text(paper.paper_type_name);
                        $('.general-info #paper-summary').text(stripTags(paper.summary));
                        $('.general-info #ijmc-interest').text((paper.is_ijmc_interested == "1")?"Yes": "No");

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
    })

    function getPanels() {
        $.post(baseUrlDeputy + 'getAllDeputyReviewerPanelsByDivision', function (response) {
            // console.log(response[0]);
            $('#papersTableBody').html('');

            if ($.fn.DataTable.isDataTable('#papersTable')) {
                // Destroy the existing DataTable instance
                $('#papersTable').DataTable().destroy();
            }

            $.each(response.data, function (i, val) {
                console.log(val)
                let reviewNowBtn = '<button class="btn btn-success btn-sm reviewNowBtn" id="reviewNowBtn" paper_id=' + val.id + ' divisionName="'+val.division.name+'"> Review Now </button>'
                let viewPaperDetailsBtn = '<button class="btn btn-info text-white btn-sm abstractDetails" id="abstractDetails" paper_id=' + val.id + '> Details <i class="text-small fas fa-magnifying-glass"></i></button>'
                $('#papersTableBody').append('<tr>' +
                    '<td>' + val.id + '</td>' +
                    '<td>' + val.paper_submitter.name + ' ' + val.paper_submitter.surname + '</td>' +
                    '<td>' + val.title.replace( /<.*?>/g, '' ) + '</td>' +
                    // '<td>' + val.type.acronym + '</td>' +
                    '<td>' + val.division.name + '</td>' +
                    '<td></td>' +
                    '<td></td>' +
                    '<td></td>' +
                    '<td>'+viewPaperDetailsBtn+'</td>' +
                    '<td>' + reviewNowBtn + '</td>' +
                    '</tr>')
            })
        }, 'json').then(function (r) {
            //
            // $.each(r, function (i, rating) {
            //     if (rating.rating !== null) {
            //         $('#rating_' + rating.abstract_id).append(rating.rating.total_score)
            //     }
            //
            // })
            $('#papersTable').DataTable({})
        })
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