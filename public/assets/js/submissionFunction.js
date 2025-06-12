$(function() {


    $('.addNewSubmissionBtn').on('click', function (e) {
        e.preventDefault();
        $('#submissionOptionModal').modal('show');

    })

    $('.saveCommentToUploadBtn').on('click', function(){
        let review_id = $(this).attr('review_id')
        let comment = $('#submitterComment_'+review_id).val();
        let paper_id = $(this).attr('paper_id')
        saveCommentToUpload(review_id, comment, paper_id);
        $('#submitterSpanComment_'+review_id).html(comment)
    })


    //  ############ Start  Abstract Submission  # #############
    $(function() {
        $('#abstractSubmissionForm').validate({
            rules: {
                previous_presentation: "required",
                basic_science_format: "required",
                abstract_category: "required",
                abstract_title: "required",
                hypothesis: "required",
                study_design: "required",
                introduction: "required",
                methods: "required",
                results: "required",
                conclusions: "required",
            },
            messages: {
                previous_presentation: "Please specify if this has been previously presented.",
                basic_science_format: "Please select whether this is a Basic Science format.",
                abstract_category: "Please choose an abstract category.",
                abstract_title: "Please provide the abstract title.",
                hypothesis: "Please enter your hypothesis.",
                study_design: "Please describe the study design.",
                introduction: "Please write the introduction.",
                methods: "Please explain the methods used.",
                results: "Please summarize the results.",
                conclusions: "Please provide the conclusions.",
            },

            errorPlacement: function(error, element) {
                if (element.hasClass("summernote")) {
                    error.insertAfter(element.siblings('.note-editor')); // Place error below Summernote editor
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function(element) {
                $(element).addClass('error');
            },
            unhighlight: function(element) {
                $(element).removeClass('error');
            },
            submitHandler: function(form) {

                let button = $(event.submitter); // Get the clicked button
                let attributes = {};

                $.each(button[0].attributes, function(index, attr) {
                    attributes[attr.name] = attr.value;
                });

                let abstract_body_count = parseInt($('#abstract_body_count').text());
                let totalWordsCount = abstract_body_count + + parseInt($('#image_caption_body_count').text())

                let formData = new FormData(form);
                formData.append('abstract_body_count', abstract_body_count)
                let abstractTitle = $('#abstract_title').val();

                if (typeof userID === 'undefined') {
                    console.error("userID is not defined!");
                    return false;
                }

                if(totalWordsCount > 2500){
                    toastr.error('Total words exceed 2500 limit.')
                    return false;
                }

                formData.append("user_id", userID);

                Swal.fire({
                    title: "Info",
                    html: 'Can you confirm that your abstract title, <strong>' + abstractTitle + '</strong>, is in title case? If not, click ‘Cancel’ to edit your title.',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: "Save",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: $(form).attr('action'),
                            data: formData,
                            method: "POST",
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                if (response.status === 200) {

                                    if((previousPage === 'finalize_paper' || previousPage === 'submission_menu') && previousUrl) {
                                        return window.location.href = previousUrl
                                    }

                                    if(attributes.id == 'updateContinuePapers' || attributes.id == 'saveContinuePapers')
                                        return window.location.href = base_url + '/user/authors_and_copyright/' + response.data.abstract_id;
                                    else
                                        return window.location.href = base_url + '/user/edit_papers_submission/' + response.data.abstract_id;
                                } else {
                                    $.each(response.msg, function(i, val){
                                        toastr.error(val);
                                    })
                                }
                            },
                            error: function(xhr) {
                                toastr.error("Submission failed! " + (xhr.responseJSON?.msg || xhr.statusText));
                            }
                        });
                    }
                });
                return false; // Prevent default form submission
            }
        });
    });


    //  ############ End  Abstract Submission  # #############

});
