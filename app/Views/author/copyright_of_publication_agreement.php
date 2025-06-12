
<!--<script  type="text/javascript" src="--><?//=base_url('assets/js/submissionFunction.js')?><!--"></script>-->


<?php // echo "<pre>"; print_r($author_details[0]);exit;?>
<?php echo view('author/common/menu'); ?>
<?php //print_r($papers);exit; ?>
<main>
    <div class="container-fluid pb-5">

        <div class=" card p-5">
            <div class="row">
                <h4 class="fw-bold"> AFS Author's/Speaker's Copyright and Publication Agreement</h4>
                <hr />
                <h6 class="fw-bold"> AFS Copyright Agreement </h6>
                <div class="container custom-container">
                <hr/>
                    <div>
                        <p><span class="fw-bolder"> Submission ID: </span> <?=isset($papers) ? $papers->id:''?></p>
                        <p><span class="fw-bolder"> Submission Title: </span> <?=isset($papers) ? strip_tags($papers->title):''?></p>
                        <p><span class="fw-bolder"> Submitted By: </span> <?=isset($papers) ? UcFirst($papers->user_name).' '. UcFirst($papers->user_surname):''?></p>
                    </div>
                    <div class="mt-5">
                        <p>
                            The following agreement <u>must</u> be signed and returned to AFS <u>before</u> your paper or presentation can be considered for presentation and publication by AFS. Technical papers and presentations submitted to AFS are received with the understanding that they are to be judged for acceptability for verbal presentation at the annual AFS Metalcasting Congress and/or for publication in AFS Proceedings and possibly AFS Transactions. It is further understood that accepted papers, at the discretion of AFS, may be published in any other AFS publication, or entity with a publishing agreement with the Society.
                        </p>
                        <br>
                        <p>
                            Congress papers submitted to AFS shall immediately become the property of American Foundry Society, Inc., subject to Copyright Laws. AFS will willingly re-assign copyright for those desiring to publish via “open access” in the International Journal of Metalcasting at a later date. The author(s) may <u>not</u> submit such papers to any other publishers or society for their use without written prior approval of AFS or notification of the paper's rejection by the Society. This document also provides permission for AFS to include a digital version of PowerPoint presentations in the AFS Metalcasting Congress Proceedings.
                        </p>
                        <br>
                        <p>
                            <i>
                                <strong>Note:</strong> Authors must check that any content freely accessible on search engines is open for reuse without a license. In offering this paper or presentation to the American Foundry Society, as the author, I understand its publication policy and agree to conform with its provisions. I have been duly authorized by my employer to present and publish this paper and have obtained copyright permission to use any artwork that is not my property. If applicable, use the “AFS Author Copyright Permission Form for Rightsholders.
                            </i>
                        </p>

                        <div class="mt-5">
                            <input type="checkbox" id="agreementCheckBox" value="1" <?= (isset($author) && ($author['is_copyright_agreement_accepted'] == 1) ? 'checked':'')?> <label for="agreementCheckBox"> Yes, I agree to the contents of this Copyright Agreement.</label>
                        </div>
                        <div class="mt-2">
                            <label for="electronicSignature"><?=isset($author) ? UcFirst($author['name']).' '. UcFirst($author['surname']):''?> Electronic Signature : </label>
                            <input type="text" id="electronicSignature" class="form-control" value="<?= (isset($author) && !empty($author['electronic_signature']) ? $author['electronic_signature']:'')?>">
                            <p class="small">You agree that your electronic signature is the legal equivalent of your manual signature on this Agreement. By selecting "I Agree" you consent to be legally bound by this Agreement's terms and conditions. </p>
                            <p class="small fw-bolder">Date: <?=date('m/d/y')?></p>
                        </div>

                        <button class="btn btn-sm btn-success mt-5 saveCopyrightAgreementBtn">Save and Continue</button>

                    </div>
                </div>
            </div>
    </div>
</main>

<script>
    $(function(){

        let htmlSuccessMessage = "Thank you for submitting your copyright agreement to the 129th Metalcasting Congress. " +
            "If you have any questions regarding the copyright agreement, please contact:<br><br>" +
            "Kim Perna<br>" +
            "AFS Abstract Administrator<br>" +
            "Email: <a href='mailto:kperna@afsinc.org'>kperna@afsinc.org</a><br><br>" +
            "Click <a href='"+base_url+ "author/view_copyright'>Here</a> to return to the copyright form. <br><br>"+
            "Click <a href='"+base_url+ "home'>Here</a> or OK to return to the Submission System."

        $('.saveCopyrightAgreementBtn').on('click', function(e){

            let agreementCheckBox = $('#agreementCheckBox').prop('checked');
            let signature = $('#electronicSignature').val();
            let paper_id = `<?=$papers->id ? : ''?>`

            if(agreementCheckBox === false){
                toastr.warning("Please check  Yes, I agree to the contents of this Copyright Agreement.")
                return false;
            }
            else if(signature.trim() === ''){
                toastr.warning("Signature is required.")
                return false;
            }

            swal.fire({
                "title": "Saving Agreement...",
                didOpen: () => {
                    Swal.showLoading();
                }
            })

            $.ajax({
                url: base_url+'author/confirm_copyright_ajax',
                data: {
                    'agreementCheckBox': agreementCheckBox,
                    'signature': signature,
                    'paper_id': paper_id
                },
                method: "POST",
                dataType: "json",
                success: function(response, status ) {
                    if (response.status == "200") {
                        swal.fire({
                            'title': 'Success',
                            'html': htmlSuccessMessage,
                            'icon': 'success'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = base_url + 'home';
                            }
                        })
                    }else if(response.status == "201"){
                        toastr.warning('Something went wrong on sending email. <br> Email copy fail to send. Please inform administrator')
                        swal.fire({
                            'title': 'Success',
                            'html': htmlSuccessMessage,
                            'icon': 'success'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = base_url + 'author/view_copyright';
                            }
                        })
                    }
                },error: function(){
                    swal.fire({
                        'title': 'error',
                        'html': 'Something went wrong',
                        'icon': 'error'
                    })
                }
            });
        })

    })

</script>