
<?= view('acceptance/common/menu'); ?>
<body>
<div class="container">
    <?= view('acceptance/common/menu_shortcut'); ?>
    <?=$presentation_data_view ?? ''?>
    <div class="card mt-2">
        <div class="card-header bg-primary text-white p-3">
            Breakfast Attendance
        </div>
        <div class="card-body">
            <p>
                We encourage you to attend our complimentary "Author/Chair Breakfast" at 7:00 am on the day of your session. This will allow you to become acquainted with the authors or panelists, your session chair and student monitor to help assure a smooth-running session.
            </p>
            <p><span class="text-danger">*</span> Will you be attending the author/chair breakfast on the morning of your presentation? </p>
            <form id="presentation_agreement_form">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="breakfast_attendance" id="accept" value="Yes" <?= !empty($acceptanceDetails) && $acceptanceDetails['breakfast_attendance'] == "yes" ? 'checked' : ''?>
                    <label class="form-check-label" for="accept">
                        Yes
                    </label>
                </div>
                <div class="form-check mt-2">
                    <input class="form-check-input" type="radio" name="breakfast_attendance" id="decline" value="No" <?= !empty($acceptanceDetails) && $acceptanceDetails['breakfast_attendance'] == 'no' ? 'checked' : ''?>
                    <label class="form-check-label" for="decline">
                        No
                    </label>
                </div>
                <button type="submit" class="btn btn-primary mt-4" >Save and Continue</button>
            </form>
        </div>
    </div>
</div>
</body>

<script>
    let acceptanceBaseUrl = `<?=base_url().'acceptance/'?>`
    $(function() {
        $('button[type="submit"]').on('click', function(e) {
            e.preventDefault();

            let breakfastAttendanceValue = $('input[name="breakfast_attendance"]:checked').val();
            if (!breakfastAttendanceValue) {
                toastr.error('Please answer required question.');
                return false;
            }

            const formData = new FormData(document.getElementById('presentation_agreement_form'));
            formData.append('abstract_id', abstract_id)
            $.ajax({
                url: acceptanceBaseUrl + 'update_acceptance', // Your server-side endpoint
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if(response.status === 'success') {
                        goNext(abstract_id)
                    }
                },
                error: function(xhr, status, error) {
                    $('#response').html('<p>Error: ' + error + '</p>');
                }
            });
        });
    });

    function goNext(abstract_id){
        window.location.href = acceptanceBaseUrl+'biography/' + abstract_id
    }

</script>

