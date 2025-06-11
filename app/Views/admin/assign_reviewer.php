    

<?php echo view('admin/common/menu'); ?>
<?php // echo '<pre>';print_r( $authors);exit;?>
<main>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
            <h4> Assign Author to Abstract </h4>    
            </div>
            <div class="card-body">
                <table id="userTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Checkbox</th>
                            <!-- <th>ID</th> -->
                            <th>Name</th>
                            <th>Institution</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody">
                        
                        <?php foreach ($authors as $index => $author) : ?>
                            <tr>
                                <td user_id="<?= $author['id'] ?>">
                                    <?php
                                    $authorId = $author['id'];
                                    $abstractId = $abstract_id;

                                    $isAssigned = false;
                                    if(isset($assigned_reviewer)){
                                        if(!empty($reviewer)){
                                            foreach ($assigned_reviewer as $reviewer) {
                                                if ($authorId == $reviewer['reviewer'] && $abstractId == $reviewer['abstract_id']) {
                                                    $isAssigned = true;
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                    <input type="checkbox" <?php if ($isAssigned) echo 'checked'; ?>>
                                </td>
                                <td user_id="<?= $author['id'] ?>"><?= $author['name'] . ' ' . $author['surname'] ?></td>
                                <td ><?= isset($author['institution']['name']) ? $author['institution']['name'] :''?></td>
                                <td><?= $author['email']?></td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>

                <button class="btn btn-success" id="btnGetChecked" abstract_id="<?=$abstract_id?>">Submit</button>
                
            </div>
        </div>
    </div>
</main>


<script>
    let baseUrlAdmin = "<?=base_url().'admin/'?>";
    $(function(){
         
    //    getAuthors();
    // $('#userTable').DataTable();
        
    var table = $('#userTable').DataTable({
        paging: false
    });

    $('#btnGetChecked').on('click', function() {
        var selectedRows = [];
        let abstract_id = $(this).attr('abstract_id')
        table.rows().every(function() {
            var data = this.data();
        
            var rowNode = this.node();
            var checkbox = $('input[type="checkbox"]', rowNode);
            // var user_id = $('input[type="checkbox"]').attr('user_id');

            if (checkbox.is(':checked')) {
                var userId = $(rowNode).find('td[user_id]').attr('user_id');
                selectedRows.push(userId);
            }
        });
        
        // if(selectedRows.length < 1) {
        //     toastr.warning('No Selected Reviewer');
        //     return false;
        // }
        // console.log(selectedRows);
        $.post(baseUrlAdmin+'assign_reviewer',{
            'selected':selectedRows,
            'abstract_id':abstract_id
        }, function(response){
            // console.log(response)
            if(response.success){
                  swal.fire(
                    'success',
                    'Reviewer Assigned',
                    'success'
                )
            }else{
                 swal.fire(
                    '',
                    'Error',
                    'error'
                )
            }
        },'json')
    });
    
    })
   

   
</script>