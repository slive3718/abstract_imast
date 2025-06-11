<div class="card p-3 mb-3 organization-item" data-id="${organizationCount}">
    <div class="mb-3">
        <label class="form-label">#${organizationCount} Name of Corporate Organization</label>
        <select class="form-select" name="organization[${organizationCount}][name]" required>
            <option value="">Select an organization</option>
            <?php if(!empty($organizations)) : ?>
                <?php foreach ($organizations as $organization) : ?>
                    <option value="<?= $organization['organization_id'] ?>">
                        <?= $organization['name'] ?>
                    </option>
                <?php endforeach; ?>
            <?php endif ?>
        </select>

        <div class="form-floating mt-2 other-organization-input-div" style="display: none">
            <input type="text" class="form-control other-organization-input"
                   name="organization[${organizationCount}][other_name]"
                   id="organization-other-${organizationCount}"
                   placeholder="Specify Other"
                   data-org="${organizationCount}"
            />
            <label for="organization-other-${organizationCount}">Specify Other</label>
        </div>


    </div>
    <div class="mb-3 mt-2">
        <label class="form-label">Type of Affiliation/Financial Interest</label>
        <?php if(!empty($affiliations)): ?>
        <?php foreach ($affiliations as $affiliation): ?>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" name="organization[${organizationCount}][affiliation][]" value="<?= $affiliation['affiliation_id'] ?>">
                <label class="form-check-label"><?= htmlspecialchars($affiliation['name']) ?></label>
            </div>
        <?php endforeach; ?>
        <?php endif; ?>

    </div>
    <button type="button" class="btn btn-danger btn-sm remove-organization" data-id="${organizationCount}">Remove</button>
</div>
