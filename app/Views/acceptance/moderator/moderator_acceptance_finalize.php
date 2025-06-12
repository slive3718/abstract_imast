<?php echo view('acceptance/common/menu'); ?>
<body>
    <div class="container" >
        <?= view('acceptance/common/moderator_menu_shortcut'); ?>
        <?=$presentation_data_view ?? ''?>
        <div class="card mt-2">
            <div class="card-header bg-primary text-white p-3">Moderator Information</div>
            <div class="card-body" style="line-height: 30px">
                <div class="row mt-1">
                    <?php if (isset($moderator_acceptance) && !empty($moderator_acceptance)): ?>
                        <div class="col-12 mb-4">
                            <div class="row">
                                <div class="col-md-4 col-sm-12 text-md-end text-start">
                                    <label class="fw-bold">Moderator:</label>
                                </div>
                                <div class="col-md-8 col-sm-12">
                                    <p class="mb-1">  <?= $moderator_acceptance['name'] . ' ' . $moderator_acceptance['surname'] ?> </p>
                                </div>
                                <div class="col-md-4 col-sm-12 text-md-end text-start">
                                    <label class="fw-bold">Email:</label>
                                </div>
                                <div class="col-md-8 col-sm-12">
                                    <p class="mb-1">  <?= $moderator_acceptance['email'] ?> </p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="card mt-2">
            <div class="card-header bg-primary text-white p-3">Acceptance Information</div>
            <div class="card-body" style="line-height: 30px">
                <div class="row">
                    <div class="col-4 text-end fw-bolder">Participation Status:</div>
                    <div class="col-7">
                        <?= isset($moderator_acceptance) && $moderator_acceptance['acceptance_confirmation'] == 1 ? "I will moderate at the SRS Asia Pacific Meeting scheduled for February 6-7, 2026 in Fukuoka, Japan." : '' ?>
                        <?= isset($moderator_acceptance) && $moderator_acceptance['acceptance_confirmation'] == 2 ? "I will NOT moderate at the SRS Asia Pacific Meeting scheduled for February 6-7, 2026 in Fukuoka, Japan." : '' ?>
                    </div>
<!--                    <div class="col-1">-->
<!--                        <span class="float-end"><a class="editBtn btn btn-primary py-0" href="--><?php //=base_url() ?><!--/acceptance/moderator/acceptance/--><?php //= $scheduler_id ?><!--"><i class="fas fa-edit"></i> Edit</a></span>-->
<!--                    </div>-->
<!--                    <div class="col-4 text-end fw-bolder">Breakfast: </div>-->
<!--                    <div class="col-7">--><?php //= !empty($moderator_acceptance) && $moderator_acceptance['breakfast_attendance'] ? $moderator_acceptance['breakfast_attendance'] :''?><!--</div>-->
<!--                    <div class="col-1">-->
<!--                        <span class="float-end"><a class="editBtn btn btn-primary py-0" href="--><?php //=base_url() ?><!--/acceptance/moderator/breakfast_attendance/--><?php //= $scheduler_id ?><!--"><i class="fas fa-edit"></i> Edit</a></span>-->
<!--                    </div>-->
<!--                    <div class="col-4 text-end fw-bolder">Session Details Confirmation: </div>-->
<!--                    <div class="col-7">--><?php //= isset($moderator_acceptance) && $moderator_acceptance['is_session_previewed'] == 1 ? 'Confirmed' : ''?><!--</div>-->
<!--                    <div class="col-1">-->
<!--                        <span class="float-end"><a class="editBtn btn btn-primary py-0" href="--><?php //=base_url() ?><!--/acceptance/moderator/session_details/--><?php //= $scheduler_id ?><!--"><i class="fas fa-edit"></i> Edit</a></span>-->
<!--                    </div>-->
                </div>
            </div>
            <div class="mt-3 mb-2 me-3">
                <button class="btn btn-success finalizeBtn float-end">FINALIZE ACCEPTANCE</button>
            </div>
        </div>
    </div>
</body>

<script>
    let baseUrlAcceptance = "<?= base_url() ?>/acceptance/moderator/";

    $(function(){
        function check_finalize() {
            $.post(baseUrlAcceptance + 'check_finalize_acceptance/' + scheduler_id, function(response) {
                Swal.close();

                let data;
                try {
                    data = typeof response === "string" ? JSON.parse(response) : response;
                } catch (error) {
                    console.error("Invalid JSON response", error);
                    toastr.error("Invalid response received");
                    return;
                }

                if (!$.isEmptyObject(data)) {
                    if(data.status === 'failed'){
                        swal.fire({
                            title: 'error',
                            icon: "error",
                            html: data.msg,
                        })
                        return false;
                    }
                    swal.fire({
                        title: data.status === 'success' ? "Acceptance Submitted!" : "Success",
                        icon: "success",
                        html: data.msg,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "<?= base_url() ?>/acceptance/abstract_list";
                        }
                    });
                } else {
                    toastr.error("Something went wrong");
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX error:", textStatus, errorThrown);
                toastr.error("Failed to check finalization. Please try again.");
            });
        }

        $('.finalizeBtn').on('click', function() {
            Swal.fire({
                title: "Are you sure?",
                text: "I confirm that all information are complete and correct.",
                icon: "info",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Finalize it."
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait...',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                            check_finalize();
                        }
                    });
                }
            });
        });
    });
</script>