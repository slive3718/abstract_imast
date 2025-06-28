

<?php //print_r($author_details);exit;?>
<?php echo view('author/common/menu'); ?>

<main>
    <div class="container-fluid" style="padding-bottom:200px">
        <div class="row mt-5">
        <h5> Abstract Disclosure System Main Menu</h5>
        <hr />
        </div>

        <div class="row mt-5">
            <div class="col-md-12">
                <div id="landing-page-contents" class="container-fluid p-4">
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
                                    Current date: <span class=""> <?=$author['signature_signed_date'] ?? ''?></span> &nbsp; | &nbsp;
                                    Expires: <span class=""><?= $author['signature_signed_date'] ? date('Y-m-d', strtotime($author['signature_signed_date'] . ' +1 year')) : ''; ?></span> &nbsp; | &nbsp;
                                   <?= !empty($author['disclosure_signature']) && $author['signature_signed_date'] ? '<span class="text-success fw-bold">Completed </span>' : '<span class="text-danger fw-bold">Incomplete</span>' ?>
                                </div>
                            </a>

                            <!-- Row 1 -->
                            <a href="<?= base_url().'author/attestation/'?>"  class="btn btn-light border w-100 text-start d-flex align-items-center mb-2">
                                <div class="bg-warning text-white px-3 py-2 fw-bold"><?= $stepNumber ++ ?></div>
                                <div class="flex-grow-1 px-2">
                                    <strong>Attestation for IMAST 2026</strong>
                                </div>
                                <div class="text-end">
                                    <?= !empty($attestation['signature']) && $attestation['date'] ? '<span class="text-success fw-bold">Completed </span>' : '<span class="text-danger fw-bold">Incomplete</span>' ?>
                                </div>
                            </a>

                            <!-- Row 3 -->
                            <a href="<?= base_url().'author/preview_finalize/'?>" class="btn btn-light border w-100 text-start d-flex align-items-center">
                                <div class="bg-warning text-white px-3 py-2 fw-bold"><?= $stepNumber ++ ?></div>
                                <div class="flex-grow-1 px-2">
                                    <strong>Print/Preview</strong>
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
