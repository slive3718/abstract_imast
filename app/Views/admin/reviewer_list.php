

<?php echo view('admin/common/menu'); ?>

<main style="padding-bottom:200px">
    <div class="container">
        <button class="btn btn-primary btn-sm mb-5 float-end" id="showImportModal"> <i class="fas fa-file-import"></i> Import Reviewers</button>
<!--        <button class="btn btn-primary btn-sm mb-5 float-end me-2" id="showAddUserModal"> <i class="fas fa-plus"></i> Add New </button>-->
        <table id="reviewerTable" class="table table-striped table-bordered table-hover" style="padding-top: 40px">
            <thead class="border border-info">
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Reviewer Type</th>
                <th>Divisions</th>
                <th>Symposium Assigned</th>
                <th>Total Assigned</th>
                <th>Comments</th>
                <th>Action</th>
            </thead>
            <tbody id="reviewerTableBody">
                <!-- This will be filled by jQuery and Datatables -->
            </tbody>
        </table>
    </div>
</main>



<!-- Modal -->
<div class="modal fade" id="importReviewerModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input name="reviewerImportFile" type="file" id="reviewerImportFile" value="" accept=".xls,.xlsx">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="importReviewersBtn">Save changes</button>
            </div>
        </div>
    </div>
</div>

<?= view('admin/common/assignReviewerModal') ?>
<?= view('admin/common/addNewUserModal') ?>
<script>
    let baseUrlAdmin = "<?=base_url().'admin/'?>";
    $(function(){
        getReviewerList();

        $('#showImportModal').on('click', function(){
            $('#importReviewerModal').modal('show')
        })

        $('#importReviewersBtn').on('click', async function(){
            let reviewerImportFile = $('#reviewerImportFile')[0].files[0];
            await doImport(reviewerImportFile);
        });

        $('#doAssignToProgramChair').on('click', function(){ // save assigned regular Reviewers
            let selectedPapers = [];
            let reviewerID = $("#reviewerID").val();
            $('input[name="paperSelectBox"]').each(function() {
                if ($(this).is(':checked')) {
                    selectedPapers.push($(this).attr('paperID'));
                }
            });

            $.post(baseUrlAdmin+'assignPapersToProgramChair', {
                "selectedPapers": selectedPapers,
                "reviewerID":reviewerID
            },function(response) { //todo: assign paper to program chair;
                console.log(response)
                if(response.status === 200){
                    swal.fire({
                        'title': 'Success',
                        'html': "Successfully Assigned!",
                        'icon': "success"
                    })
                }else{
                    swal.fire({
                        'title': 'error',
                        'html': response.message,
                        'icon': "error"
                    })
                }

            },'json');
        })

        $('#assignReviewersTableBody').on('click', 'tr', function () {
            // Toggle the checkbox within the row
            let checkbox = $(this).find('input[type="checkbox"]');
            checkbox.prop('checked', !checkbox.prop('checked'));
        });

        $('#showAddReviewerModal').on('click',(e)=>{
            addUserModal();
        })

    })



    function getReviewerList(){
        if ($.fn.DataTable.isDataTable('#reviewerTable')) {
            $('#reviewerTable').DataTable().destroy();
        }

        $.get(baseUrlAdmin+'getReviewerList', function(response){
            $('#reviewerTableBody').html('');
            $.each(response.data, function(i, val){
                // console.log(val)
                let assignPaperBtn = '<button class="btn btn-primary btn-sm showAssignReviewerModal text-nowrap" type="paper" reviewerID="'+val.id+'" > Assign Paper </button>'
                let assignPanelBtn = '<button class="btn btn-info btn-sm showAssignReviewerModal text-nowrap mt-2" type="panel" reviewerID="'+val.id+'" > Assign Panel </button>'
                let manageBtn = '<button class="btn btn-success btn-sm manageUserBtn text-nowrap mb-2" type="panel" reviewerID="'+val.user_id+'" > Manage </button>'

                let reviewerType = (val.is_deputy_reviewer === "1")?"Deputy":"Regular"

                let colors = ['bg-primary', 'bg-secondary', 'bg-success', 'bg-danger', 'bg-warning', 'bg-info',];
                let divisions = val.divisions.map((e) => {
                    if(e !== null) {
                        let color = colors[e.division_id % colors.length];
                        return '<div class="text-nowrap badge ' + color + '">' + e.name + '</div>';
                    }else{
                        return  ''
                    }
                }).join('');

               $('#reviewerTableBody').append('<tr>'+
                '<td>'+val.user_id+'</td>'+
                '<td>'+val.name+'</td>'+
                '<td>'+val.surname+'</td>'+
                '<td class="text-nowrap">'+val.email+'</td>'+
                   '<td>'+reviewerType+'</td>'+
                   '<td>'+divisions+'</td>'+
                   '<td></td>'+
                   '<td></td>'+
                   '<td></td>'+
                   '<td>'+manageBtn+'</td>'+
                // '<td>'+val.total_assigned.length+'</td>'+
                // '<td id="reviewer_'+val.primary_details.id+'"></td>'+
                '</tr>')
            })

        },'json').then(function(val){
            $.each(val, function(i, item){
                $.each(item.total_assigned, function(i, e){
                    $('#reviewer_'+e.reviewer).append((e.is_submitted == 1)?'<span class="text-success" title="Review submitted"> '+e.abstract_id+'</span>':'<span class="text-danger" title="No review"> '+e.abstract_id+'</span>')

                })
            })
            $('#reviewerTable').DataTable({
                // paging: false
            });
        })


    }

    $('#reviewerTableBody').on('click', '.showAssignReviewerModal', function() {
        $('#assignReviewerModal').modal('show');
        let submissionType = $(this).attr('type');
        let reviewerID = $(this).attr('reviewerid');

        // Check if DataTable exists, then destroy it
        if ($.fn.DataTable.isDataTable('#assignReviewersTable')) {
            $('#assignReviewersTable').DataTable().destroy();
        }

        // Clear the table body
        $('#assignReviewersTableBody').html('');
        $('#reviewerID').val(reviewerID);

        // Fetch data via AJAX
        $.post(baseUrlAdmin + 'getAllPapers', {
            'submission_type': submissionType
        }, function(data) {
            if (data) {
                // Iterate through the papers data
                $.each(data, function(i, val) {
                    let isSelected = '';

                    // Iterate through the reviewers array
                    $.each(val.reviewers, function(j, reviewer) {
                        // Check if the reviewer's ID matches the selectedReviewerID
                        if (reviewer.reviewer_id == reviewerID) {
                            // If there's a match, set the 'checked' attribute to true
                            isSelected = 'checked';
                            // Exit the loop since we found the selected reviewer
                            return false; // Break the loop
                        }
                    });

                    // Create the checkbox element
                    let checkBox = '<input type="checkbox" name="paperSelectBox" paperID="' + val.id + '" ' + isSelected + '>';

                    // Safely access val.type and provide a fallback
                    let typeName = (val.type && val.type.name) ? val.type.name : 'N/A'; // Use 'N/A' or any default text you prefer

                    // Append the new row to the table body
                    $('#assignReviewersTableBody').append(
                        '<tr>' +
                        '<td>' + checkBox + '</td>' +
                        '<td>' + val.id + '</td>' +
                        '<td>' + (val.title ? val.title.replace(/<.*?>/g, '') : '') + '</td>' + // Correctly check for title
                        '<td>' + val.user_name + ' ' + val.user_surname + '</td>' +
                        '<td>' + typeName + '</td>' + // Use the safely accessed type name
                        '</tr>'
                    );
                });
            }
        }, 'json').always(function() {
            // Initialize the DataTable after the data is loaded
            $('#assignReviewersTable').DataTable({});
        });
    });



    async function doImport(reviewerImportFile){
        try {
            let formData = new FormData();
            formData.append('reviewerImportFile', reviewerImportFile);
            Swal.fire({
                title: 'Please Wait !',
                html: 'Importing Data...',// add html attribute if you want or remove
                allowOutsideClick: false,
                onBeforeOpen: () => {
                    Swal.showLoading()
                },
            });
            $.ajax({
                url: baseUrlAdmin + 'importReviewers',
                type: 'POST',
                data: formData,
                processData: false, // Don't process the files
                contentType: false, // Set content type to false as FormData will automatically set it
                success: function(response) {
                    response = JSON.parse(response)
                    if(response.status === 200){
                        swal.fire({
                            'title': 'sucess',
                            'html': response.message,
                            'icon': 'success'
                        })
                    }else{
                        swal.fire({
                            'title': 'error',
                            'html': response.message,
                            'icon': 'error'
                        })
                    }
                    $('#reviewerTableBody').html('');
                    getReviewerList();
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        } catch (error) {
            swal.fire({
                'title': 'error',
                'htm': 'Import Failed',
                'icon': 'error'
            })
            console.error(error);
        }
    }

</script>