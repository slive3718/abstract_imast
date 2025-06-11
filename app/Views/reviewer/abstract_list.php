

<?php echo view('reviewer/common/menu'); ?>

<main>
    <div class="container-fluid" style="padding-bottom:200px">
        <div class="card">
            <div class="card-body">
        <div class="card-title">Welcome, <span class="fw-bold"><?=Ucfirst(session('name')). ' '. Ucfirst(session('surname'))?></span></div>
                <div class="bg-warning bg-opacity-10" >
                    <ul>
                        <li>Reviewer instructions can be viewed by clicking on the links above.</li>
                        <li>If you need any help with this review system please use the email support form link above.</li>
                        <li>Please be sure to save your work and/or make note of information in case of logout. Page will time out after 90 minutes of inactivity.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="my-3 card p-3">
            <div class="text-primary"> <strong>Main Filter Options</strong></div>
            <div>
                <label for="unratedSubmission">Show only unrated submissions</label>
                <input type="checkbox" name="unratedSubmission" id="unratedSubmission" >
            </div>
            <div>
                <label for="divisionFilter"> Division Filter: </label>
                <select name="divisionFilter" id="divisionFilter" class="form-control" style="width:500px">
                    <option value="">All Division Types</option>
                    <?php if(isset($divisions) && !empty($divisions)):
                        foreach($divisions as $division):?>
                    <option><?=$division->name?></option>
                    <?php endforeach; endif ?>
                </select>
            </div>
        </div>

        <div class="card p-2">
            <div class="card-header bg-opacity-50"><p>Papers: (Click on a result below to review)</p></div>
            <div class="card-body">
            <table id="abstractTable" class="table table-striped">
                <thead>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Division</th>
                    <th>Rated?</th>
                    <th>Action</th>
                </thead>
                <tbody id="abstractTableBody">
    <!--               Filled with AJAX-->
                </tbody>
            </table>
            </div>
        </div>

    </div>
</main>


<script>
    let baseUrlReviewer = "<?=base_url().'reviewer/'?>";
    $(function(){

        getAbstracts();

        $("#abstractTableBody").on('click', '.reviewBtn', function(){
            let abstract_id = $(this).attr('abstract_id')

            $.post(baseUrlReviewer+'checkAbstractReviewsCount', {
                'abstract_id':abstract_id
            }, function(response){
                // console.log(response); return false;
                if(response.status == '201'){
                    Swal.fire({
                        title: response.message,
                        text: "Still want to continue?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, continue!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href= baseUrlReviewer+"reviewAbstract/"+abstract_id;
                        }
                    });
                }else if(response.status == '200'){
                    window.location.href= baseUrlReviewer+"reviewAbstract/"+abstract_id;
                }
            },'json')

            //
        })

        $('#divisionFilter').on('change', function() {
            let selectedDivision = $(this).val();
                $('#abstractTable').DataTable().column(2).search(selectedDivision).draw();
        });

        $('#unratedSubmission').on('change', function() {
            var table = $('#abstractTable').DataTable();

            if ($(this).is(':checked')) {
                // Show only rows that match the unrated criteria (adjust regex as needed)
                table.column(3).search('^$', true, false).draw();
            } else {
                // Clear the search on column 3
                table.column(3).search('').draw();
            }
        });


        $('#abstractTable').on('click','.declineBtn', function(){
            let abstract_id = $(this).attr('abstract_id')

            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, decline it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(baseUrlReviewer+'declineReviewerAbstract', {
                        'abstract_id':abstract_id
                    }, function(response){
                        if(response.status == '200'){
                            Swal.fire({
                                title: "Declined!",
                                text: "Paper has been declined.",
                                icon: "success"
                            });
                            getAbstracts();
                        }else{
                            toastr.error(response.message)
                        }
                    },'json')
                }
            });



        })

    })

    function getAbstracts(){
        if ($.fn.DataTable.isDataTable('#abstractTable')) {
            $('#abstractTable').DataTable().destroy();
        }
        $.post(baseUrlReviewer+'getAllReviewerAbstracts', function(response){
            // console.log(response[0]);
            $('#abstractTableBody').html('');
            $.each(response.data, function(i, val){
                 console.log(val.abstracts);
                let reviewBtn  = '<button class="btn btn-success btn-sm reviewBtn" abstract_id='+val.abstracts.id+'> Review </button>'
                let declinedBtn  = '<button class="btn btn-danger btn-sm declineBtn" abstract_id='+val.abstracts.id+'> Decline </button>'

                let approveBtn =

                $('#abstractTableBody').append('<tr>'+
                    '<td>'+val.abstracts.custom_id+'</td>'+
                    '<td>'+val.abstracts.title.replace( /<.*?>/g, '' ) +'</td>'+
                    '<td>'+val.division.name +'</td>'+
                    '<td id="rating_'+val.abstracts.id+'">'+((val.reviews)?val.reviews.average_score:'')+'</td>'+
                    '<td>'+reviewBtn +" "+((val.reviews)?'':declinedBtn)+'</td>'+
                    '</tr>')
            })
            $('#abstractTable').DataTable();
        },'json')
    }

</script>