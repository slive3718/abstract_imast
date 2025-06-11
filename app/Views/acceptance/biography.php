
<?= view('acceptance/common/menu'); ?>
<body>
<div class="container mt-5">
    <?= view('acceptance/common/menu_shortcut'); ?>
    <?=$presentation_data_view ?? ''?>
    <div class="card mt-2">
        <div class="card-header bg-primary text-white p-3">
            Biography
        </div>
        <div class="card-body">
            <p>
                We encourage you to attend our complimentary "Author/Chair Breakfast" at 7:00 am on the day of your session. This will allow you to become acquainted with the authors or panelists, your session chair and student monitor to help assure a smooth-running session.
            </p>
            <form id="presentation_agreement_form">
                <div id="authorBio" class="mt-4">
                    <div class="mb-3">
                        <label for="author_bio" class="form-label fw-bold">
                            <span class="text-danger ">*</span> Presenting Author Biography (Updated biography text, if any):
                        </label>
                        <textarea name="author_bio" id="au_extra2" cols="80" rows="8" class="form-control"><?= !empty($acceptanceDetails)? $acceptanceDetails['author_bio']: ''?></textarea>
                    </div>
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
            const formData = new FormData(document.getElementById('presentation_agreement_form'));
            formData.append('abstract_id', abstract_id)

            $.ajax({
                url: acceptanceBaseUrl + 'update_acceptance', // Your server-side endpoint
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    goNext(abstract_id)
                },
                error: function(xhr, status, error) {
                    $('#response').html('<p>Error: ' + error + '</p>');
                }
            });
        });
    });


    function goNext(abstract_id){
        window.location.href = acceptanceBaseUrl+'presentation_upload/' + abstract_id
    }

</script>

