

<?php echo view('admin/common/menu'); ?>

<main>
    <div class="container-fluid">
        <div class="customButtonsDiv float-end mx-3 mb-5">
        <a href="<?=base_url()?>admin/exportScores" class="btn btn-success text-white position-relative" title="Export all abstract scores to excel">Export All Abstract Scores </a>
        </div>
        <table id="abstractTable" class="table table-striped">
            <thead>
                <th>ID</th>
                <th>Author List</th>
                <th>Topic</th>
                <th>Population</th>
                <th>Submitter</th>
                <th>Reviewer</th>
                <th>Status</th>
                <th>Action</th>
            </thead>
            <tbody id="abstractTableBody">
                <!-- This will be filled by jQuery and Datatables -->
            </tbody>
        </table>

    </div>
</main>


<script>
    let baseUrlAdmin = "<?=base_url().'/'.$event->uri.'/admin/'?>";
    $(function(){
        getAbstracts();
        

        $("#abstractTableBody").on('click', '.addReviewerBtn', function(){ // Submit Reviews
            let abstract_id = $(this).attr('abstract_id')
            window.location.href= baseUrlAdmin+"assign_reviewer_view/"+abstract_id;
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
    })

    function getAbstracts(){ // Fetching All abstracts and populating the table

        if ($.fn.DataTable.isDataTable('#abstractTable')) {
                    $('#abstractTable').DataTable().destroy();
                }

        $.get(baseUrlAdmin+'getAllAbstracts', function(response){
            $('#abstractTableBody').html('');
            $.each(response, function(i, val){
                 console.log(val)
                substance_area = [];
                let assignBtn  = '<button class="btn btn-success btn-sm addReviewerBtn" abstract_id='+val.id+'><i class="fas fa-plus"></i> Add Reviewer </button>';
                let deleteAbstractBtn = '<button class="btn btn-danger btn-sm deleteAbstractBtn mt-2 text-nowrap" abstract_id='+val.id+'><i class="fas fa-times"></i> Delete Abstract </button>';
                // let complete_status = ()
                $('#abstractTableBody').append('<tr>'+
                '<td>'+val.id+'</td>'+
                '<td id="authorList_'+val.id+'" class="author_td"></td>'+
                '<td id="topics_'+val.id+'"></td>'+
                '<td id="population_'+val.id+'"></td>'+
                '<td class="text-nowrap">'+val.user_name+' '+val.user_surname+'</td>'+
                '<td id="reviewer_'+val.id+'"></td>'+
                '<td>'+((val.is_finalized == 1)?'<span class="text-success">Complete</span>' : '<span class="text-danger">Incomplete</span>')+'</td>'+
                '<td style="min-width:96px">'+assignBtn +'<br>'+deleteAbstractBtn+'</td>'+
                '</tr>')
            })
        },'json').then(function(r){
           $.each(r, function(i, item){
             let author_institution ='';
                $.each(item.authors, function(i, author){
                    if(author){
                        if(author.institution)
                            author_institution =  " <br><i class='badge bg-info'>("+ author.institution.name+")</i>";
                    }
                    $('#authorList_'+author.abstract_id).append('<div class="">'+((author.is_presenting_author == 'Yes')?'<span class="fw-bolder">Lead Presenter: </span>':'<span class="fw-bolder">Co Presenter: </span>')+author.name+' '+author.surname+ author_institution +'</div><br>')
                })
           })
            $.each(r, function(i, item){
                  let reviewer_institution ='';
                $.each(item.reviewers, function(i, reviewer){
                    console.log(reviewer)
                     if(reviewer){
                        if(reviewer.institution)
                            reviewer_institution = " <br><i class='badge bg-info badge-sm w-100'>("+ reviewer.institution.name+")</i>";
                    }
                    // console.log(reviewer)
                    $('#reviewer_'+reviewer.abstract_id).append('<div class=""><span reviewer_="'+reviewer.id+'">'+((reviewer.abstract_review == 1)? '<i class="fas fa-check text-success h5"></i> ':(reviewer.abstract_review == 2)?'<span class="text-danger h5"> COI </span> ':'<i class="fas fa-check opacity-0 h5"></i> ')+ reviewer.name+' '+reviewer.surname + reviewer_institution+'</span></div><br>')
                })
           })
            $.each(r, function(i, item){
                $.each(item.substance_area, function(i, topic){
                    $('#topics_'+item.id).append(topic.value+'<br>')
                })
           })
           $.each(r, function(i, item){
                $.each(item.populations, function(i, population){
                    $('#population_'+item.id).append(population.value+'<br>')
                })
           })

            $('#abstractTable').dataTable({
                paging: false,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });

        })

       
    }
</script>