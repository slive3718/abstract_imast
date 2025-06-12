
<?= view('acceptance/common/menu'); ?>
<body>
<div class="container">
    <?= view('acceptance/common/moderator_menu_shortcut'); ?>
    <?=$presentation_data_view ?? ''?>
    <div class="card mt-2">
        <div class="card-header bg-primary text-white p-3">
            Session Chair Participation
        </div>
        <div class="card-body">
            <p class="fw-bold">Congratulations!</p>
            <p>You have been scheduled as a Session Chair at the <strong>129th AFS Metalcasting Congress</strong> held in Atlanta, Georgia April 12-15, 2025. Please confirm below if you are able to participate in the 2025 AFS Metalcasting Congress.</p>
            <p class="text-danger">
                Once you accept/decline your presentation status, you will not be able to change your decision online. To change your participation decision, please contact Kimberly Perna at <a href="mailto:kperna@afsinc.org" class="text-danger">kperna@afsinc.org</a>.
            </p>
            <form id="presentation_agreement_form">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="participation" id="accept" value="1" <?= !empty($acceptanceDetails) && $acceptanceDetails['acceptance_confirmation'] == 1 ? 'checked' : ''?>>
                    <label class="form-check-label" for="accept">
                        I will participate as a Session Chair at the 129th AFS Metalcasting Congress held in Atlanta, Georgia April 12-15, 2025.
                    </label>
                </div>
                <div class="form-check mt-2">
                    <input class="form-check-input" type="radio" name="participation" id="decline" value="2" <?= !empty($acceptanceDetails) && $acceptanceDetails['acceptance_confirmation'] == 2 ? 'checked' : ''?>>
                    <label class="form-check-label" for="decline">
                        I am unable to participate in the 129th AFS Metalcasting Congress held in Atlanta, Georgia April 12-15, 2025.
                    </label>
                </div>
                <button type="submit" class="btn btn-primary mt-4" >Save and Continue</button>
            </form>
        </div>
    </div>
</div>
</body>

<script>
    let acceptanceBaseUrl = `<?=base_url().'acceptance/moderator/'?>`
    $(function() {
        const participationValueSaved = $('input[name="participation"]:checked').val();
        $('input[name="participation"]').on('change', function(){
            if(participationValueSaved.trim() !== '' && $(this).val()){
                toastr.info('Presentation confirmation cannot be updated. Please contact admin for assistance.');
                $('input[name="participation"][value="' + participationValueSaved + '"]').prop('checked', true);
                return false;
            }
        });



        $('button[type="submit"]').on('click', function(e) {
            e.preventDefault();

            let participationValue = $('input[name="participation"]:checked').val();
            if (!participationValue) {
                toastr.error('Please answer required question.');
                return false;
            }

            const formData = new FormData(document.getElementById('presentation_agreement_form'));
            formData.append('scheduler_id', scheduler_id)
            $.ajax({
                url: acceptanceBaseUrl + 'save', // Your server-side endpoint
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if(response.status === 'success') {
                        goNext(scheduler_id)
                    }
                },
                error: function(xhr, status, error) {
                    $('#response').html('<p>Error: ' + error + '</p>');
                }
            });
        });
    });

    function goNext(){
        window.location.href = acceptanceBaseUrl+'breakfast_attendance/'+scheduler_id
    }

</script>

