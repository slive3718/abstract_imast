
<?= view('acceptance/common/menu'); ?>
<body>
<div class="container">
    <?= view('acceptance/common/invited_menu_shortcut'); ?>
    <?=$presentation_data_view ?? ''?>
    <div class="card mt-2">
        <div class="card-header bg-primary text-white p-3">
            IMAST Innovation Celebration Page
        </div>
        <div class="card-body">
            <p>
                SRS is pleased to offer complimentary registration for IMAST faculty to attend the IMAST Innovation Celebration on Friday, April 17, 2026 from 17:30-19:00. Please indicate whether you plan to attend this event:
            </p>
           <form id="agreementForm" class="p-4">
               <input type="radio" name="celebration_attendance" id="attending" value="1" <?= !empty($acceptanceDetails) && $acceptanceDetails['celebration_attendance'] === '1' ? 'checked' : ''?> >
               <label for="attending"> Yes, I plan to attend the Innovation Celebration. Please register me for this event.</label>
               <br>
               <input type="radio" name="celebration_attendance" id="not_attending" value="0" <?= !empty($acceptanceDetails) && $acceptanceDetails['celebration_attendance'] === '0' ? 'checked' : ''?> >
               <label for="not_attending"> No, I do NOT plan to attend the Innovation Celebration. Please do not register me for this event.</label>

               <p class="mt-4"><span class="text-danger">*</span> Please Note: Guests of IMAST faculty will not be registered for the Innovation Celebration. Tickets may be purchased on the
                   <a href="https://www.srs.org/Meetings-Conferences/IMAST/IMAST2026#registration" target="_blank"> SRS IMAST Website </a>  for $50, per guest. </p>
               <button type="button" class="btn btn-primary mt-4 continueBtn" >Save and Continue</button>
            </form>

        </div>
    </div>
</div>
</body>

<script>
    let acceptanceBaseUrl = `<?=base_url().'acceptance/'?>`
    $(function() {

        $('.continueBtn').on('click', function(){
            save_innovation_attendance(abstract_id);
        })

        // $('button[type="submit"]').on('click', function(e) {
        //     e.preventDefault();
        //
        //     let travelExpenses = $('input[name="travel_expenses"]:checked').val();
        //     if (!travelExpenses) {
        //         toastr.error('Please answer required question.');
        //         return false;
        //     }
        //
        //     const formData = new FormData(document.getElementById('presentation_agreement_form'));
        //     formData.append('abstract_id', abstract_id)
        //     $.ajax({
        //         url: acceptanceBaseUrl + 'update_acceptance', // Your server-side endpoint
        //         method: 'POST',
        //         data: formData,
        //         processData: false,
        //         contentType: false,
        //         success: function(response) {
        //             if(response.status === 'success') {
        //                 goNext(abstract_id)
        //             }
        //         },
        //         error: function(xhr, status, error) {
        //             $('#response').html('<p>Error: ' + error + '</p>');
        //         }
        //     });
        // });
    });

    async function goNext(abstract_id){
        window.location.href = `${acceptanceBaseUrl}invited_speaker_acceptance_finalize/${abstract_id}`;
    }

    function save_innovation_attendance(abstract_id) {
        const attendance = $('input[name="celebration_attendance"]:checked').val();
        
        if(!attendance){
            toastr.error('Please answer required question.');
            return false;
        }

        return $.ajax({
            url: `${base_url}acceptance/update_acceptance`,
            type: 'POST',
            data: {
                celebration_attendance : attendance,
                abstract_id: abstract_id
            },
            dataType: 'json',
            beforeSend: function(xhr) {
                swal.fire({
                    title: 'Please wait',
                    html: 'Processing your request...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        swal.showLoading();
                    }
                });
                console.log('Request started...');
            },
            success: function(data, textStatus, xhr) {
                if(data.status !== 'success') {
                    let errorMessage = data.message || "Something went wrong.";
                    swal.fire({
                        title: "Error",
                        html: `<p>${errorMessage}</p>`,
                        icon: "error"
                    });
                    return;
                }
                swal.fire({
                    title: "Success",
                    html: `${data.message}`,
                    icon: "success",
                    confirmButtonText: "OK"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `${acceptanceBaseUrl}/invited_speaker_acceptance_finalize/${abstract_id}`;
                    }
                });
            },
            error: function(xhr, status, error) {
                let errorMessage = "Something went wrong.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                swal.fire({
                    title: "Error",
                    html: `<p>${errorMessage}</p>`,
                    icon: "error"
                });
            },
            complete: function(xhr, status) {
                // Always executed after success or error
                console.log('Request completed with status:', status);
            }
        });
    }


</script>

