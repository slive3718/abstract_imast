
<?= view('acceptance/common/menu'); ?>
<body>
<div class="container">
    <div class="card mt-2">
        <div class="card-header bg-primary text-white p-3">
            Non-Exclusive License
        </div>
        <div class="card-body">
            <p>
                For all presentations presented at the SRS Asia Pacific Meeting, I attest that:
            </p>
            <p>
                I have created, and I am the current copyright holder of a paper/poster/e-poster/invited presentation and all contents therein (hereinafter referred to as the "Work").
                I hereby license the Work to the Scoliosis Research Society ("SRS") according to the following terms:
            </p>

                <ul>
                    <li>The license shall begin on the date I sign this Agreement.</li>
                    <li>I hereby grant the SRS a non-exclusive, perpetual license for use by SRS of the Work. The SRS is not obligated to use the Work in any way.</li>
                    <li>I am giving this license to the SRS as a contribution. I specifically release the SRS from any obligation to pay money or otherwise perform services for this license.</li>
                    <li>
                        The SRS may use the Work in fulfillment of its organizational purposes only.
                        I understand and agree that such use may include, but is not limited to, the sale and advertisement of the
                        Work, printing, exhibition, broadcast, internet use, publication, reproduction, distribution and use of excerpts or abstracts of
                        the Work on a stand-alone basis or in combination with other material on any and all audio and visual media, whether paper-based, film or electronic.
                    </li>
                    <li>
                        I represent to you that I am the sole author of the Work and that the Work does not contain any material that is copyrighted by any other person or entity or that,
                        if the Work does contain material copyrighted by others, that I have obtained written permission to utilize same in the Work and to license the Work as provided hereby.
                        I warrant that the Work does not infringe the copyright, trademark or any other intellectual property right of any other person or entity. I agree to indemnify the SRS,
                        its directors, officers, employees, and agents against any and all loss, cost or damages (including, without limitation, liability for payment of claims, judgments or settlements,
                        for violation or infringement of the copyright, the trademark or other intellectual property rights of another) arising out of the granting of his license or SRS’ use of the Work,
                        including, without limitation, any attorney’s fees and costs that SRS incurs in connection therewith .
                    </li>
                </ul>

            <p><span class="text-danger">*</span>I have read the foregoing license and agreement before signing below, and I fully understand the contents.</p>
            <form id="non-exclusive-license-form">
            <div class="d-inline">
                <p>If the Work has a registered copyright, the registration date and number is: <input type="text" value="<?= ( !empty($userProfile['registered_copyright']) ? $userProfile['registered_copyright'] : '')?>" name="registered_copyright"></p>
            </div>
                <!-- Signature Section -->
                <div class="mb-4">
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-2 text-md-end">
                            <strong>Electronic Signature:</strong>
                        </div>
                        <div class="col-md-8">
                            <input id="non_exclusive_license_signature" type="text" class="form-control" name="non_exclusive_license_signature" value="<?= ( !empty($userProfile['non_exclusive_license_signature']) ? $userProfile['non_exclusive_license_signature'] : '')?>" required>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <div class="col-md-2 text-md-end">
                            <strong>Date:</strong>
                        </div>
                        <div class="col-md-8">
                            <input name="non_exclusive_license_date" type="date" value="<?=!empty($userProfile && $userProfile['non_exclusive_license_date']) ? date('Y-m-d', strtotime($userProfile['non_exclusive_license_date'])) : date('Y-m-d')?>" readonly>
                        </div>
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
            if($('#non_exclusive_license_signature').val().trim() == '') {
                toastr.warning('Please fill required fields.')
                return false;
            }
            const formData = new FormData(document.getElementById('non-exclusive-license-form'));
            $.ajax({
                url: acceptanceBaseUrl + 'update_profile', // Your server-side endpoint
                type: 'POST',
                data: formData,
                processData: false, // Important for FormData
                contentType: false, // Important for FormData
                success: function(response) {
                    if(response.status === 'success') {
                        goNext()
                    }
                },
                error: function(xhr, status, error) {
                    $('#response').html('<p>Error: ' + error + '</p>');
                }
            });
        });
    });

    function goNext(){
        Swal.fire({
            title: 'Success',
            html: 'Data saved successfully! Please click OK to close this window.',
            icon: 'success'
        }).then((result) => {
            if(result.isConfirmed){
                window.close()
            }
        });
    }

</script>

