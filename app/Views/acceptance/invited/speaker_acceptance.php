
<?= view('acceptance/common/menu'); ?>
<body>
<div class="container">
    <?= view('acceptance/common/invited_menu_shortcut'); ?>
    <?=$presentation_data_view ?? ''?>
    <div class="card mt-2">
        <div class="card-header bg-primary text-white p-3">
            Speaker Acceptance
        </div>
        <div class="card-body">
            <p class="fw-bold">Congratulations!</p>
            <p>It is the distinct pleasure of the Scoliosis Research Society to invite you to join the faculty at the 33rd International Meeting on Advanced Spine Techniques (IMAST), scheduled for April 15-18, 2026 in Toronto, ON, Canada.</p>

            <p>
                You have been invited to participate in the following session:
            </p>
               <ul>
                <li><span class="fw-bolder">Session Date: </span> <?=!empty($abstract_schedule['event']) ? date('F d, Y', strtotime($abstract_schedule['event']['session_date'])) : ''?></li>
                <li><span class="fw-bolder">Session Title: </span>  <?=!empty($abstract_schedule['event']) ? ($abstract_schedule['event']['session_title']) : ''?></li>
                <li><span class="fw-bolder">Session Time:  </span> <?=!empty($abstract_schedule) ? date('H:i', strtotime($abstract_schedule['event']['session_start_time'])) .' - '.date('H:i', strtotime($abstract_schedule['event']['session_end_time'])) : ''?></li>
                <li><span class="fw-bolder">Your Presentation Title: </span>  <?=!empty($abstract_details) ? $abstract_details->title : ''?></li>
                <li><span class="fw-bolder">Moderator(s): </span>  <?= !empty($abstract_schedule['moderators'])
                        ? implode(', ', array_map(fn($moderator) => $moderator['name'].' '.$moderator['surname'], $abstract_schedule['moderators']))
                        : '' ?>
                </li>
            </ul>

            <form id="presentation_agreement_form">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="participation" id="accept" value="1" <?= !empty($acceptanceDetails) && $acceptanceDetails['acceptance_confirmation'] == 1 ? 'checked' : ''?>>
                    <label class="form-check-label" for="accept">
                        Yes, I confirm my participation, for this assignment, at the 33rd IMAST.
                    </label>
                </div>
                <div class="form-check mt-2">
                    <input class="form-check-input" type="radio" name="participation" id="decline" value="2" <?= !empty($acceptanceDetails) && $acceptanceDetails['acceptance_confirmation'] == 2 ? 'checked' : ''?>>
                    <label class="form-check-label" for="decline">
                        No, I am declining this invitation for the 33rd IMAST.
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
        const participationValueSaved = $('input[name="participation"]:checked').val() ?? false;
        $('input[name="participation"]').on('change', function() {
            // Get the originally saved value each time the event fires
            console.log(participationValueSaved)
            if(participationValueSaved) {
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

            console.log(participationValue); // Log the selected value
            const formData = new FormData(document.getElementById('presentation_agreement_form'));
            formData.append('abstract_id', abstract_id)
            $.ajax({
                url: acceptanceBaseUrl + 'save_acceptance_confirmation', // Your server-side endpoint
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    Swal.fire({
                        title: 'Saving...',
                        text: '',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading()
                        }
                    });
                },
                success: function(response) {
                    if(response.status === 'success') {
                        goNext(abstract_id)
                        toastr.success(response.message)
                        swal.close();
                    }
                },
                error: function(xhr, status, error) {
                    $('#response').html('<p>Error: ' + error + '</p>');
                    toastr.error(error)
                    swal.close();
                }
            });
        });
    });

    function goNext(abstract_id){
        window.location.href = acceptanceBaseUrl+'invited_speaker_travel_expense/'+abstract_id
    }

</script>

