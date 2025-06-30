<?php echo view('author/common/menu'); ?>

<main style="padding-bottom: 200px">
    <div class="container py-5">
        <div class="card shadow-sm">
            <?= view('author/common/shortcut_link') ?>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <br>
                        <p>The following speaker/presenter has disclosed a financial relationship that may affect his/her presentations at 2026 IMAST. Please fill out the sections below and sign the Presenter Attestation.</p>

                        <p>For all presentations presented at the 2026 IMAST, I attest that:</p>

                        <ul>
                            <li>I will present content that is balanced in its discussion of therapeutic options and products.</li>
                            <li>I will present content that presents either generic names of products or trade names of multiple companies, unless the use of the term directly influences learners’ understanding of the presentation or data.</li>
                            <li>I will present content that is scientifically rigorous.</li>
                            <li>I will present content that is free from commercial bias.</li>
                            <li>I will ensure that neither the content nor the format of the educational materials advance the proprietary interest of any commercial interest.</li>
                            <li>I will ensure that the scientific research discussed in the context conforms to accepted standards of medical practice, experimental design, data collection, and analysis.</li>
                            <li>I will ensure that the clinical practice and patient care recommendations presented in the content are based on the best available evidence.</li>
                        </ul>

                        <form action="" method="POST" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="attestation_signature" class="fw-bolder">Signature :</label>
                                <input type="text" name="attestation_signature" value="<?=$author['attestation_signature'] ?? ''?>" id="attestation_signature" class="form-control" required>
                                <div class="invalid-feedback">Please provide your signature.</div>
                            </div>
                            <div class="mb-3">
                                <label for="attestation_date" class="fw-bolder">Date :</label>
                                <input type="date" name="attestation_date" value="<?= !empty($author['attestation_date']) ? date('Y-m-d', strtotime($author['attestation_date'])) : date('Y-m-d') ?>" id="attestation_date" class="form-control" readonly required>
                                <div class="invalid-feedback">Please select a date.</div>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    $(function () {
        $('form').on('submit', function (e) {
            e.preventDefault();

            // Validate the form
            if (!this.checkValidity()) {
                $(this).addClass('was-validated');
                return;
            }

            let formData = $(this).serialize();

            $.ajax({
                url: '<?= base_url('author/submit_attestation') ?>',
                type: 'POST',
                data: formData,
                success: function (response) {
                    if (response.success) {
                        alert('Attestation submitted successfully.');
                        window.location.href = '<?= base_url('author/view_copyright') ?>';
                    } else {
                        alert('Failed to submit attestation.');
                    }
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    alert('An error occurred while submitting the form.');
                }
            });
        });
    });
</script>
