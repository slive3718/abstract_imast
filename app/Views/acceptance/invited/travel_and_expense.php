
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
            <form id="agreementForm" class="p-4">
            <?php if($admin_acceptance['acceptance_confirmation'] == '1' && $admin_acceptance['presentation_preference'] == '6'): ?>
                <?= view('acceptance/invited/renders/travel_expense_invited_speaker'); ?>
            <?php elseif($admin_acceptance['acceptance_confirmation'] == '1' && $admin_acceptance['presentation_preference'] == '5'): ?>
                <?= view('acceptance/invited/renders/travel_expense_invited_presenter'); ?>
            <?php else: ?>
                <?= view('acceptance/invited/renders/travel_expense_invited_faculty'); ?>
            <?php endif?>
                <button type="button" class="btn btn-primary mt-4 continueBtn" >Save and Continue</button>
            </form>
        </div>
    </div>
</div>
</body>

<script>
    let acceptanceBaseUrl = `<?=base_url().'acceptance/'?>`
    const presPref = `<?= $admin_acceptance['presentation_preference'] ?>`
    $(function() {
        $('.continueBtn').on('click', function(){
            save_travel_expenses(abstract_id);
        })
    });

    async function goNext(abstract_id){
        window.location.href = `${acceptanceBaseUrl}invited_speaker_acceptance_finalize/${abstract_id}`;
    }

    function save_travel_expenses(abstract_id) {
        const travel_and_expense_terms = $('#travel_and_expense_terms:checked').val();

        if((presPref === '4' || presPref === '5') && !travel_and_expense_terms){
            toastr.error('Please agree to the travel and expenses terms.');
            return false;
        }

        return $.ajax({
            url: `${base_url}acceptance/update_acceptance`,
            type: 'POST',
            data: {
                travel_expenses : travel_and_expense_terms,
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
                        window.location.href = `${acceptanceBaseUrl}/invited_celebration/${abstract_id}`;
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

