<?php echo view('author/common/menu'); ?>

<main>
    <div class="container py-5">
        <!-- Card Section -->
        <div class="card shadow-sm">
            <?= view('author/common/shortcut_link') ?>
            <div class="card-body">
                <!-- Preview Section -->
                <div class="mb-4">
                    <h4 class="mb-4 fw-bold">Preview</h4>
                    <p class="text-secondary">
                        Please click on the <strong>"Finalize Disclosure"</strong> button to complete your disclosure and receive your confirmation email.
                    </p>
                </div>

                <!-- Disclosure Information -->
                <div id="printSection">
                    <h5 class="fw-bold text-primary mb-3">
                        Disclosure Information for <?= ucfirst($author['name']) ?> <?= ucfirst($author['surname']) ?>
                    </h5>

                    <table class="table table-bordered align-middle">
                        <tbody>
                        <!-- Signature -->
                        <tr>
                            <td class="fw-bold bg-light">Signature:</td>
                            <td>
                                <?= !empty($author['disclosure_signature'])
                                    ? htmlspecialchars($author['disclosure_signature'])
                                    : 'N/A'; ?>
                                <?=  !empty($author['signature_signed_date']) ? '<span class="small fw-bolder ml-5 badge  bg-info"> Date: '.date('Y-m-d', strtotime($author['signature_signed_date'])).'</span>' : '' ?>
                            </td>
                            <td style="width: 65px"><a href="<?=base_url().'author/financial_relationship_disclosure/#eSignature'?>" class="btn btn-primary btn-sm btn-edit"><i class="fas fa-edit"> </i> Edit </a></td>
                        </tr>

                        <!-- Organizations and Affiliations -->
                        <tr>
                            <td class="fw-bold bg-light">Organizations and Affiliations:</td>
                            <td>
                                <?php if (!empty($selectedOrganizations)): ?>
                                    <?php
                                    // Create a map of organization IDs for faster lookup
                                    $organizationMap = array_column($organizations, null, 'organization_id');
                                    $affiliationMap = array_column($affiliations, null, 'id');
                                    ?>

                                    <?php foreach ($selectedOrganizations as $org): ?>
                                        <?php
                                        $organizationName = $organizationMap[$org['organization_id']]['name'] ?? 'N/A';
                                        $customOrganization = $org['custom_organization'] ?? 'N/A';
                                        ?>
                                        <p class="mb-1">
                                            <strong>Name of Corporate Organization:</strong>
                                            <?= htmlspecialchars($organizationName) ?>
                                            <?= $organizationName == 'Other' ? ($customOrganization ? " ({$customOrganization})" : '') : '' ?>
                                        </p>

                                        <?php if (!empty($org['affiliations'])): ?>
                                            <ul class="list-unstyled ms-3">
                                                <?php foreach ($org['affiliations'] as $affiliationId): ?>
                                                    <?php
                                                    $affiliationName = $affiliationMap[$affiliationId]['name'] ?? 'N/A';
                                                    ?>
                                                    <li>- <?= htmlspecialchars($affiliationName) ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-secondary">No affiliated organizations.</p>
                                <?php endif; ?>
                            </td>
                            <td> <a href="<?=base_url().'author/financial_relationship_disclosure'?>" class="btn btn-primary btn-sm btn-edit"><i class="fas fa-edit"> </i> Edit </a></td>
                        </tr>

                        <!-- Financial Disclosure -->
                        <tr>
                            <td class="fw-bold bg-light" style="width: 220px;">Financial Disclosure:</td>
                            <td>
                                <?= ($author['financial_relationship'] === 'Yes')
                                    ? 'I have held a financial relationship with an ineligible company within the past 24 months.'
                                    : 'I have NO financial relationship(s) with an ineligible company producing healthcare goods or services.'; ?>
                            </td>
                            <td> <a href="<?=base_url().'author/financial_relationship_disclosure/#yes_relationship'?>" class="btn btn-primary btn-sm btn-edit"><i class="fas fa-edit"> </i> Edit </a></td>
                        </tr>

                        <!-- Disclosure Support -->
                        <tr>
                            <td class="fw-bold bg-light">Disclosure Support:</td>
                            <td>
                                <input type="checkbox" <?= ($author['disclosure_support'] == 1) ? 'checked' : ''; ?> disabled />
                                <label>Practice recommendations that are relevant to the ineligible companies with whom you have relationships/affiliations will be supported by the best available evidence or absent evidence will be consistent with generally accepted medical practice. </label>
                            </td>
                            <td>
                                <a href="<?=base_url().'author/financial_relationship_disclosure/#disclosure_support'?>" class="btn btn-primary btn-sm btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </td>
                        </tr>

                        <tr>
                            <td class="fw-bold bg-light">Disclosure Discussed:</td>
                            <td>
                                <input type="checkbox" <?= ($author['disclosure_discussed'] == 1) ? 'checked' : ''; ?> disabled />
                                <label> All reasonable clinical alternatives will be discussed when making practice recommendations. </label>
                            </td>
                            <td>
                                <a href="<?=base_url().'author/financial_relationship_disclosure/#disclosure_discussed'?>" class="btn btn-primary btn-sm btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </td>
                        </tr>

                        <tr>
                            <td class="fw-bold bg-light">Disclosure Relationship:</td>
                            <td>
                                <input type="checkbox" <?= ($author['disclosure_relationship'] == 1) ? 'checked' : ''; ?> disabled />
                                <label> Relationships with ineligible companies will not bias or otherwise influence your involvement in the CME activity. </label>
                            </td>
                            <td>
                                <a href="<?=base_url().'author/financial_relationship_disclosure/#disclosure_relationship'?>" class="btn btn-primary btn-sm btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </td>
                        </tr>

                        </tbody>
                    </table>

                    <h5 class="fw-bold text-primary mb-3">
                        Attestation for <?= ucfirst($author['name']) ?> <?= ucfirst($author['surname']) ?>
                    </h5>
                    <table class="table table-bordered align-middle">
                        <tbody>
                        <!-- Attestation -->
                        <tr>
                            <td class="fw-bold bg-light" style="width: 220px;">Completed:</td>
                            <td>
                                <?= (!empty($attestation['signature'])) ? '<span class="text-success"> Completed </span>': '<span class="text-danger"> Incomplete </span>'; ?>
                            </td>
                            <td>
                                <a href="<?=base_url().'author/financial_relationship_disclosure/#disclosure_support'?>" class="btn btn-primary btn-sm btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </td>
                        </tr>

                        <tr>
                            <td class="fw-bold bg-light">Signature: </td>
                            <td>
                                <?= (!empty($attestation['signature'])) ? $attestation['signature'] : ''; ?>
                            </td>
                            <td style="width: 65px">
                                <a href="<?=base_url().'author/financial_relationship_disclosure/#disclosure_discussed'?>" class="btn btn-primary btn-sm btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <!-- Finalize and Print Buttons -->
                    <div class="mt-4 mb-5">
<!--                        <button class="btn btn-primary finalizeAuthorDisclosureBtn me-2">-->
<!--                            Finalize Disclosure-->
<!--                        </button>-->
                        <button class="btn btn-primary" onclick="printDiv('printSection')">
                            Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- JavaScript -->
<script>
    $(function () {
        $('.finalizeAuthorDisclosureBtn').on('click', function (e) {
            e.preventDefault();

            let hasUnchecked = false;
            $('.declarationCheckbox').each(function () {
                if (!this.checked) {
                    toastr.error('All declarations must be accepted.');
                    hasUnchecked = true;
                }
            });

            if (hasUnchecked) return;

            $.ajax({
                url: '<?= base_url('/author/confirm_copyright_ajax') ?>',
                method: 'POST',
                data: {},
                success: function (response) {
                    if (response.status === 'success') {
                        alert('Disclosure finalized successfully!');
                        window.location.reload();
                    } else {
                        alert(response.message || 'An error occurred during finalization.');
                    }
                },
                error: function (xhr) {
                    console.error('Error:', xhr.responseText);
                    alert('A server error occurred.');
                }
            });
        });
    });

    function printDiv(divId) {
        const originalContent = document.body.innerHTML;
        const printContent = document.getElementById(divId).innerHTML;

        // Hide edit buttons before printing
        $('.btn-edit').hide();

        document.body.innerHTML = printContent;
        window.print();

        // Restore after printing
        document.body.innerHTML = originalContent;
        location.reload();
    }
</script>

<!-- CSS for Print -->
<style>
    @media print {
        .btn-edit {
            display: none !important;
        }
    }
</style>
