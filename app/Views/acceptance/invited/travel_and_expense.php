
<?= view('acceptance/common/menu'); ?>
<body>
<div class="container">
    <?= view('acceptance/common/invited_menu_shortcut'); ?>
    <?=$presentation_data_view ?? ''?>
    <div class="card mt-2">
        <div class="card-header bg-primary text-white p-3">
            Travel and Expenses
        </div>
        <div class="card-body">
            <p>
                Faculty for the SRS Asia Pacific Meeting will receive the following:
                <ul>
                    <li><strong>Registration </strong>: Faculty will receive complimentary registration for the Asia Pacific Meeting, to be completed by SRS staff.</li>
                    <li class="mt-3"><strong>Housing </strong>: SRS will provide up to 3 nights of hotel accommodation in Fukuoka, arranged by SRS staff.</li>
                    <li class="mt-3"><strong>Travel Expenses </strong>:Please refer to your invitation for travel reimbursement details.</li>
<!--                    <li class="mt-3"><strong> </strong>: </li>-->
                </ul>
            </p>
<!--            <form id="presentation_agreement_form">-->
<!--                <div class="form-check">-->
<!--                    <input class="form-check-input" type="radio" name="travel_expenses" id="accept" value="yes" --><?php //= !empty($acceptanceDetails) && $acceptanceDetails['travel_expenses'] == "yes" ? 'checked' : ''?>
<!--                    <label class="form-check-label" for="accept">-->
<!--                        Yes-->
<!--                    </label>-->
<!--                </div>-->
<!--                <div class="form-check mt-2">-->
<!--                    <input class="form-check-input" type="radio" name="travel_expenses" id="decline" value="no" --><?php //= !empty($acceptanceDetails) && $acceptanceDetails['travel_expenses'] == 'no' ? 'checked' : ''?>
<!--                    <label class="form-check-label" for="decline">-->
<!--                        No-->
<!--                    </label>-->
<!--                </div>-->
<!--                <button type="submit" class="btn btn-primary mt-4" >Save and Continue</button>-->
<!--            </form>-->
            <button type="button" class="btn btn-primary mt-4 continueBtn" >Save and Continue</button>
        </div>
    </div>
</div>
</body>

<script>
    let acceptanceBaseUrl = `<?=base_url().'acceptance/'?>`
    $(function() {

        $('.continueBtn').on('click', function(){
            goNext(abstract_id);
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
        check_finalize()
    }

    function check_finalize() {
        swal.fire({
            title: 'Please wait',
            html: 'Processing your request...',
            allowOutsideClick: false,
            didOpen: () => {
                swal.showLoading();
            }
        });

        return $.post(`${base_url}acceptance/check_finalize_acceptance/${abstract_id}`)
            .done(function(data) {
                swal.fire({
                    title: "Success",
                    html: `<p>Thank you for confirming your participation in the SRS Asia Pacific Meeting scheduled for February 6-7, 2025 in Fukuoka, Japan.<br>
                      If you have any questions, please direct them to <a href='mailto:education@srs.org'>education@srs.org</a></p>`,
                    icon: "success",
                    confirmButtonText: "OK"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `${acceptanceBaseUrl}/abstract_list`;
                    }
                });
            })
            .fail(function(xhr, status, error) {
                let errorMessage = "Something went wrong.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                swal.fire({
                    title: "Error",
                    html: `<p>${errorMessage}</p>`,
                    icon: "error"
                });
            });
    }


</script>

