
<?php //print_r($value);exit; ?>
<div class="form-check form-check-inline">
    <input class="form-check-input" type="radio" name="choices[]" id="relevantYes" value="yes" <?= !empty($value) && $value == 'yes'  ? 'checked' : ''?>>
    <label class="form-check-label" for="relevantYes" >Yes</label>
</div>
<div class="form-check form-check-inline">
    <input class="form-check-input" type="radio" name="choices[]" id="relevantNo" value="no" <?= !empty($value) && $value == 'no'  ? 'checked' : ''?>>
    <label class="form-check-label" for="relevantNo" >No</label>
</div>
<div class="form-check form-check-inline">
    <input class="form-check-input" type="radio" name="choices[]" id="relevantNA" value="na" <?= !empty($value) && $value == 'n/a'  ? 'checked' : ''?>>
    <label class="form-check-label" for="relevantNA" >N/A</label>
</div>