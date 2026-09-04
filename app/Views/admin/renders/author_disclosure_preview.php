
<div class="p-0">
    <!-- Card Section -->
    <div class="card shadow-sm">
        <div class="card-body">
            <!-- Disclosure Information -->
            <div id="printSection">
                <h5 class="fw-bold text-primary mb-3">
                    Disclosure Information for <?= ucfirst($author['name']) ?> <?= ucfirst($author['surname']) ?>
                </h5>

                <?php
                $isCurrent = false;
                $statusClass = false;
                $statusText = false;
                $signedDate = false;

                if (!empty($disclosure['updated_at']) || !empty($disclosure['created_at'])) {
                    $signedDate = $disclosure['updated_at'] ? date('Y-m-d', strtotime($disclosure['updated_at'])) : date('Y-m-d', strtotime($disclosure['created_at']));
                    $isCurrent = (!empty($currentDisclosureDate) && (strtotime($signedDate) > strtotime($currentDisclosureDate)));
                    $statusClass = $isCurrent ? 'alert-success' : 'alert-danger';
                    $statusText = $isCurrent ? 'Current' : 'Outdated';
                }
                ?>
                <table class="table table-bordered align-middle">
                    <tbody>
                    <!-- Organizations and Affiliations -->
                    <tr>
                        <td>
                            <span class="fw-bold bg-light">Completion Status: </span>
                        </td>
                        <td>
                            <?= (empty($disclosure) || empty($disclosure['financial_relationship']) && $isCurrent) ? '<span class="text-danger fw-bolder">Incomplete</span>' :  '<span class="text-success fw-bolder">Completed</span>' ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Organizations and Affiliations:</td>
                        <td>
                            <?php if (!empty($selectedOrganizations) && $disclosure &&  strtolower($disclosure['financial_relationship']) === 'yes'): ?>
                                <?php
                                // Create a map of organization IDs for faster lookup
                                $organizationMap = array_column($organizations, null, 'organization_id');
                                $affiliationMap = array_column($affiliations, null, 'id');
                                ?>

                                <?php foreach ($selectedOrganizations as $org): ?>
                                    <div class="mb-3">
                                        <?php
                                        $organizationName = $organizationMap[$org['organization_id']]['name'] ?? 'N/A';
                                        $customOrganization = $org['custom_organization'] ?? 'N/A';
                                        ?>
                                        <p class="mb-1">
                                            <strong>
                                                <?= htmlspecialchars($organizationName) ?>
                                                <?= $organizationName == 'Other' ? ($customOrganization ? " ({$customOrganization})" : '') : '' ?>
                                            </strong>
                                        </p>

                                        <?php if (!empty($org['affiliations'])): ?>
                                            <ul class="list-unstyled ms-3">
                                                <?php foreach ($org['affiliations'] as $affiliationId): ?>
                                                    <?php
                                                    $affiliationName = $affiliationMap[$affiliationId]['name'] ?? 'N/A';
                                                    ?>
                                                    <li>- <?= htmlspecialchars($affiliationName) ?></li>
                                                    
                                                    <?php if ($affiliationId == 3 && !empty($org['affiliations_stocks'])): ?>
                                                        <ul class="list-unstyled ms-3 mt-2">
                                                            <?php
                                                            $stockLabels = [
                                                                1 => 'Stock ownership: publicly traded company',
                                                                2 => 'Stock ownership: privately held company',
                                                                3 => 'Stock Options'
                                                            ];
                                                            foreach ($org['affiliations_stocks'] as $stockId): ?>
                                                                <li class="text-muted" style="font-size: 0.95rem;">
                                                                    • <?= htmlspecialchars($stockLabels[$stockId] ?? 'N/A') ?>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($affiliationId == 8 && !empty($org['other_affiliation'])): ?>
                                                        <ul class="list-unstyled ms-3 mt-2">
                                                            <li class="text-muted" style="font-size: 0.95rem;">
                                                                • <?= htmlspecialchars($org['other_affiliation']) ?>
                                                            </li>
                                                        </ul>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>

                                        <?php if (($org['relationship_ended']) !== null): ?>
                                            <p class="mb-1">
                                                <strong>Relationship ended:</strong>
                                                <?= ($org['relationship_ended']) == '1' ? 'Yes' : 'No' ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-secondary">No affiliated organizations.</p>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <!-- Financial Disclosure -->
                    <tr>
                        <td class="fw-bold bg-light" style="width: 220px;">Financial Disclosure:</td>
                        <td>
                            <?php
                            if (!empty($disclosure['financial_relationship'])) {
                                if (strcmp($disclosure['financial_relationship'], 'yes') === 0) {
                                    echo 'I have held a financial relationship(s) with an ineligible company within the past 24 months.';
                                } else {
                                    echo 'I have held NO financial relationship(s) with an ineligible company within the past 24 months.';
                                }
                            } else {
                                echo '';
                            }
                            ?>
                        </td>
                    </tr>

                    <!-- Disclosure Support -->
                    <tr>
                        <td class="fw-bold bg-light">Disclosure Support:</td>
                        <td>
                            <input type="checkbox" <?= ($disclosure && $disclosure['disclosure_support'] == 1) ? 'checked' : ''; ?> disabled />
                            <label>Practice recommendations that are relevant to the ineligible companies with whom you have relationships/affiliations will be supported by the best available evidence or absent evidence will be consistent with generally accepted medical practice. </label>
                        </td>
                    </tr>

                    <tr>
                        <td class="fw-bold bg-light">Disclosure Discussed:</td>
                        <td>
                            <input type="checkbox" <?= ($disclosure && $disclosure['disclosure_discussed'] == 1) ? 'checked' : ''; ?> disabled />
                            <label> All reasonable clinical alternatives will be discussed when making practice recommendations. </label>
                        </td>
                    </tr>

                    <tr>
                        <td class="fw-bold bg-light">Disclosure Relationship:</td>
                        <td>
                            <input type="checkbox" <?= ($disclosure && $disclosure['disclosure_relationship'] == 1) ? 'checked' : ''; ?> disabled />
                            <label> Relationships with ineligible companies will not bias or otherwise influence your involvement in the CME activity. </label>
                        </td>
                    </tr>

                    <!-- Signature -->
                    <!-- Signature Row -->
                    <tr>
                        <td class="fw-bold bg-light">Signature:</td>
                        <td>
                            <?= ($disclosure && $disclosure['disclosure_signature']) ? htmlspecialchars($disclosure['disclosure_signature']) : 'N/A' ?>
                        </td>
                    </tr>

                    <!-- Date Row (only shown if date exists) -->
                    <?php if ($signedDate): ?>
                        <tr>
                            <td class="fw-bold bg-light">Signature Date:</td>
                            <td>
                                <span class="alert <?= $statusClass ?> p-1 align-items-center">
                                    <i class="fas <?= $isCurrent ? 'fa-circle-check' : 'fa-times-circle' ?> me-2"></i>
                                    <strong><?= $statusText ?></strong>
                                    <span class="ms-2"><?= htmlspecialchars($signedDate) ?></span>
                                </span>
                            </td>
                        </tr>
                    <?php endif; ?>
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
                            <?= (!empty($attestation['signature'])) ? '<span class="text-success fw-bolder"> Completed </span>': '<span class="text-danger fw-bolder"> Incomplete </span>'; ?>
                        </td>
                    </tr>

                    <tr>
                        <td class="fw-bold bg-light">Signature: </td>
                        <td>
                            <?= (!empty($attestation['signature'])) ? $attestation['signature'] : ''; ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

