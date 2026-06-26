

<?php //print_r($author_details);exit;?>
<?php echo view('author/common/menu'); ?>

<main>
    <div class="container-fluid" style="padding-bottom:200px">
        <div class="row">
            <div class="col-md-12">
                <div id="landing-page-contents" class="container-fluid p-4">
                    <div class="container">
                        <h5> Abstract Disclosure System Main Menu</h5>
                    </div>
                    <div class="submission-menu">
                        <div class="container mt-3">
                            <?php $stepNumber = 1; ?>
                            <!-- Row 1 -->
                            <a href="<?= base_url().'author/financial_relationship_disclosure/'?>"  class="btn btn-light border w-100 text-start d-flex align-items-center mb-2">
                                <div class="bg-warning text-white px-3 py-2 fw-bold"> <?= $stepNumber ++ ?></div>
                                <div class="flex-grow-1 px-2">
                                    <strong>Financial Relationship Disclosure</strong>
                                </div>
                                <div class="text-end">
                                    <span>Due Date :  October 23, 2025</span>
                                    <span class="ms-5"> Status :
                                    <?= ( !empty($author) && $author['financial_relationship'] !== NULL && !empty($author['signature_signed_date']) ? strtotime($author['signature_signed_date']) > strtotime($disclosure_current)
                                        ? '<span class="badge bg-success text-white">Current '.date('m-d-Y',strtotime($author['signature_signed_date'])).' </span>'
                                        : '<span class="badge bg-warning text-dark"> Outdated '.date('m-d-Y',strtotime($author['signature_signed_date'])).' </span>'
                                        : '<span class="badge bg-danger text-white">Incomplete</span>') ?>
                                    </span>

<!--                                    Current date: <span class=""> --><?php //=$disclosure_current_date ?? ''?><!--</span> &nbsp; | &nbsp;-->
<!--                                    Expires: <span class="">--><?php //= $disclosure_expire_date ?? '' ?><!--</span> &nbsp; | &nbsp;-->
<!--                                   --><?php //= (isset($isExpired) && $isExpired == 0) ? '<span class="text-success fw-bold">Completed </span>' : '<span class="text-danger fw-bold">Incomplete</span>' ?>
                                </div>
                            </a>

                            <!-- Row 1 -->
                            <a href="<?= base_url().'author/attestation/'?>"  class="btn btn-light border w-100 text-start d-flex align-items-center mb-2">
                                <div class="bg-warning text-white px-3 py-2 fw-bold"><?= $stepNumber ++ ?></div>
                                <div class="flex-grow-1 px-2">
                                    <strong>Attestation for IMAST 2027</strong>
                                </div>
                                <div class="text-end">
                                    <span >Due Date :  October 23, 2025</span>
                                    <span class="ms-5"> Status :
                                    <?= ( !empty($attestation) && !empty($attestation['date']) ? strtotime($attestation['date']) > strtotime($attestation_current)
                                        ? '<span class="badge bg-success text-white">Current  '.date('m-d-Y', strtotime($attestation['date'])).'</span>'
                                        : '<span class="badge bg-warning text-dark"> Outdated '.date('m-d-Y', strtotime($attestation['date'])).'</span>'
                                        : '<span class="badge bg-danger text-white">Incomplete</span>') ?>
                                    </span>
<!--                                    --><?php //= !empty($attestation['signature']) && $attestation['date'] ? '<span class="text-success fw-bold">Completed </span>' : '<span class="text-danger fw-bold">Incomplete</span>' ?>
                                </div>
                            </a>

                            <!-- Row 3 -->
                            <a href="<?= base_url().'author/preview_finalize/'?>" class="btn btn-light border w-100 text-start d-flex align-items-center">
                                <div class="bg-warning text-white px-3 py-2 fw-bold"><?= $stepNumber ++ ?></div>
                                <div class="flex-grow-1 px-2">
                                    <strong>Print/Preview/Finalize</strong>
                                </div>
                                <div class="text-end">
                                </div>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</main>
