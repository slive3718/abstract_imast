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
            <div class="form-check" data-affiliation-id="<?=$affiliation['id']?>">
                <input type="checkbox" class="form-check-input organizationAffiliation" name="organization[${organizationCount}][affiliation][]" value="<?= $affiliation['id'] ?>">
                <label class="form-check-label"><?= htmlspecialchars($affiliation['name']) ?></label>

                <?php if($affiliation['id'] == 3): ?>
                <div class="affiliations-stock" id="affiliations-stock-${organizationCount}" style="display: none">
                    <div>
                        <input type="checkbox" class="stock-required" id="${organizationCount}-stock-opt-1" name="organization[${organizationCount}][affiliations_stocks][]" value="1"> <label for="${organizationCount}-stock-opt-1">Stock ownership: publicly traded company</label>
                    </div>
                    <div>
                        <input type="checkbox" class="stock-required" id="${organizationCount}-stock-opt-2" name="organization[${organizationCount}][affiliations_stocks][]" value="2"> <label for="${organizationCount}-stock-opt-2">Stock ownership: privately held company</label>
                    </div>
                    <div>
                        <input type="checkbox" class="stock-required" id="${organizationCount}-stock-opt-3" name="organization[${organizationCount}][affiliations_stocks][]" value="3"> <label for="${organizationCount}-stock-opt-3">Stock Options</label>
                    </div>
                </div>
                <?php endif ?>
            </div>
        <?php endforeach; ?>
        <?php endif; ?>

    </div>
    <div class="mb-3 mt-2">
            <label class="form-label">Has this financial relationship ended?</label>
        <div class="options">
            <input type="radio" name="organization[${organizationCount}][relationship_ended]" value="1" id="relationship_ended_yes"> <label for="relationship_ended_yes"> Yes</label> <br>
            <input type="radio" name="organization[${organizationCount}][relationship_ended]" value="0" id="relationship_ended_no"> <label for="relationship_ended_no"> No</label>
        </div>
    </div>
    <button type="button" class="btn btn-danger btn-sm remove-organization" data-id="${organizationCount}">Remove</button>
</div>
