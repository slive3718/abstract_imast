
<link href="<?=base_url()?>/assets/css/jquery_ui_style.css" rel="stylesheet">

<?php echo view('event/common/menu'); ?>
<style>
    .table td {
        vertical-align: middle;
    }
    .table .text-end {
        width: 250px;
    }
</style>
<main>
    <div class="container">
        <div class="bg bg-warning p-4 shadow-lg">
            <p class="fw-bolder m-auto">You must click on the 'Finalize Submission' button to complete your panel and receive your confirmation email. Once you have finalized, all panelists will
                receive an email inviting them to the panel. </p>
        </div>
        <div class="card p-lg-5 p-md-2 p-sm-1 p-xs-1 shadow mt-3">
            <?php echo view('event/common/shortcut_link_panel'); ?>
            <div class="card-header fw-bold"> General Information
<!--                <a href="--><?php //=base_url()?><!--/user/edit_papers_submission/--><?php //=$paper_id?><!--" class="btn btn-sm btn-primary float-end"><i class="fas fa-edit"></i> Edit</a>-->
            </div>
            <table class="table">
                <tbody>
                    <tr>
                        <td class="text-end">Submission ID : </td>
                        <td ><?=$papers->id?></td>
                    </tr>

                    <tr>
                        <td style="width:250px" class="text-end">Submission Type Title : </td>
                        <td><?=$papers->submission_type?></td>
                    </tr>
                    <tr>
                        <td style="width:250px" class="text-end">Paper Title : </td>
                        <td><?=$papers->division_name?></td>
                    </tr>
                    <tr>
                        <td style="width:250px" class="text-end">Paper Title : </td>
                        <td><?=$papers->title?></td>
                    </tr>
                    <tr>
                        <td style="width:250px" class="text-end">Paper Title : </td>
                        <td><?=$papers->summary?></td>
                    </tr>
                    <tr>
                        <td style="width:250px" class="text-end">Are you interested in submitting this paper to IJMC as well? : </td>
                        <td><?= ($papers->is_ijmc_interested == 0) ? "I am NOT interested in submitting this paper to IJMC" : (($papers->is_ijmc_interested == 1) ? "I am interested in submitting this paper to IJMC" : "I have already submitted this paper to IJMC") ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card p-lg-5 p-md-2 p-sm-1 p-xs-1 shadow mt-3 shadow mt-3">
            <div class="card-header fw-bold"> Coordinators Information <?=(!isset($coordinators)? '<span class="text-danger text-sm badge " title="Please complete requirements before finalizing abstract."><i class="fas fa-exclamation-circle" > INCOMPLETE </i></span>' :'')?>
<!--                <a href="--><?php //=base_url()?><!--/user/abstract_disclosure/--><?php //=$paper_id?><!--" class="btn btn-sm btn-primary float-end"><i class="fas fa-edit"></i> Edit</a>-->
            </div>
            <div class="card-body">
                <table class="table" style="margin-bottom:0px !important">
                    <tbody>
                        <?php if($coordinators):
                        foreach ($coordinators as $coordinator):?>
                            <tr>
                                <td class="text-end">Coordinator : </td>
                                <td><?=$coordinator['name'].' '.$coordinator['surname']?></td>
                            </tr>
                            <tr>
                                <td class="text-end">Institution : </td>
                                <td><?=$coordinator['institution']?></td>
                            </tr>

                            <tr>
                                <td class="text-end">Email : </td>
                                <td><?=$coordinator['email']?></td>
                            </tr>
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card p-lg-5 p-md-2 p-sm-1 p-xs-1 shadow mt-3 shadow mt-3">
            <div class="card-header fw-bold"> Panelist Information <?=(!isset($panelists)? '<span class="text-danger text-sm badge " title="Please complete requirements before finalizing abstract."><i class="fas fa-exclamation-circle" > INCOMPLETE </i></span>' :'')?>
<!--                <a href="--><?php //=base_url()?><!--/user/abstract_disclosure/--><?php //=$paper_id?><!--" class="btn btn-sm btn-primary float-end"><i class="fas fa-edit"></i> Edit</a>-->
            </div>
            <div class="card-body">
                <table class="table" style="margin-bottom:0px !important">
                    <tbody>
                    <tbody>
                    <?php if($panelists):
                        foreach ($panelists as $panelist):?>
                            <tr>
                                <td class="text-end">Coordinator : </td>
                                <td><?=$panelist['name'].' '.$coordinator['surname']?></td>
                            </tr>
                            <tr>
                                <td class="text-end">Institution : </td>
                                <td><?=$panelist['institution']?></td>
                            </tr>

                            <tr>
                                <td class="text-end">Email : </td>
                                <td><?=$panelist['email']?></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row p-4 my-5">
            <div class="col-md-12">
                  <button class="btn btn-primary" onclick="window.print();" style="max-width:200px"> Print Page</button>
                  <button class="btn btn-success finalizePaperBtn" id="finalizePanelBtn" style="max-width:200px"> Finalize Panel</button>
              </div>
        </div>
    </div>
</main>

<script>
    $(function(){

        $('#finalizePanelBtn').on('click', function(){

         $.ajax({
             url: base_url + 'user/save_finalize_panel',
             headers: {'X-Requested-With': 'XMLHttpRequest'},
             data: {
                 'paper_id': paper_id
             },
             method: "POST",
             dataType: "json",
             beforeSend: function() {
                 Swal.fire({
                     title: 'Please Wait !',
                     html: 'Finalizing...',// add html attribute if you want or remove
                     allowOutsideClick: false,
                     onOpen: () => {
                         Swal.showLoading()
                     }
                 });
             },
             success: function (response, status) {
                 if (response.status == "200") {
                     swal.fire({
                         title:"Submitted",
                         text: "Paper Submission Finalized",
                         type: "success",
                         icon: "success",
                         confirmButtonText: 'Ok',
                     }).then((result)=> {
                         if(result.isConfirmed){
                             window.location.href = base_url+'home';
                         }
                     });
                 }else{
                     Swal.fire(
                         'Sorry',
                         'Something went wrong, please contact administrator',
                         'warning'
                     )
                 }
             }
         });
        })
    })

</script>