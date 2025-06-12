$(function() {

    let searchValue = $('#searchAuthor').val();
    let checked;
    let correspondent;
    let selected_user_id = [];
    let selected_user_info = [];

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

    getPaperAuthors(paper_id);
    //  ############ Start  Abstract Submission  # #############
    $(function() {
        $('#abstractSubmissionForm').validate({
            rules: {
                division: "required",
                paper_type: "required",
                is_interested: "required",
            },
            messages: {
                division: "Please select a division",
                paper_type: "Please select a paper type",
                is_interested: "Please specify if you are interested",
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") == "content") {
                    error.appendTo(element.parent()); // Appends error message after the Summernote field
                } else {
                    error.insertAfter(element); // Appends error message after the input field
                }
            },
            highlight: function(element) {
                $(element).addClass('error'); // Adds the 'error' class to the input field
            },
            unhighlight: function(element) {
                $(element).removeClass('error'); // Removes the 'error' class from the input field
            },
            submitHandler: function(form) {
                let totalWordsError = 0;
                let emptyError = 0;

                $('.wordsExceed').each(function() {
                    if (!$(this).hasClass('d-none')) {
                        totalWordsError = 1;
                    }
                });

                $(".summernote").each(function() {
                    if ($(this).summernote('isEmpty')) {
                        emptyError = 1;
                    }
                });

                if (totalWordsError > 0) {
                    swal.fire({
                        'title': 'Info',
                        'html': 'Total number of words should not exceed limit',
                        'icon': 'info',
                    });
                    return false;
                }

                if (emptyError > 0) {
                    swal.fire({
                        'title': 'Info',
                        'html': 'Required text field is empty',
                        'icon': 'info',
                    });
                    return false;
                }

                let formData = new FormData(form);
                let abstractTitle = $('#title').val();

                formData.append("user_id", userID);

                Swal.fire({
                    title: "info",
                    showCancelButton: true,
                    confirmButtonText: "Save",
                    icon: 'info',
                    html: 'Can you confirm that your abstract title, '+abstractTitle+' is in title case?  If not, click on ‘cancel’ to return to the page to edit your title.'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: $(form).attr('action'),
                            data: formData,
                            method: "POST",
                            processData: false, // Prevent jQuery from processing the data
                            contentType: false, // Prevent jQuery from setting contentType
                            success: function(response) {
                                response = JSON.parse(response);
                                // console.log(response.data.insert_id);
                                if (response.status == '200') {
                                    window.location.href = base_url + '/user/submission_menu/' + response.data.insert_id + '/new';
                                }else{
                                    toastr.error(response)
                                }
                            }
                        });
                    }
                });
                return false; // Prevent default form submission
            }
        });
    });


    // $('#saveAbstractBtn').on('click', function (e) {
    //     e.preventDefault();
    //     let totalWords = 0
    //     let totalWordsError = 0;
    //     $('.total_sum_check').each(function () {
    //         totalWords = (totalWords) + parseInt($(this).val());
    //         if (totalWords > 400) {
    //             totalWordsError++;
    //         }
    //     })
    //
    //     if (totalWordsError > 0) {
    //         swal.fire({
    //             'title': 'Info',
    //             'html': 'Total number of words should not exceed limit (400) <br> Word(s) count: <span class="text-danger">' + totalWords + '</span>',
    //             'icon': 'info',
    //         })
    //         return false;
    //     }
    //     if ($('#any_error').val() == "1") {
    //         swal.fire(
    //             'Please note',
    //             'Please fix any error before submitting the form',
    //             'warning'
    //         )
    //         return false;
    //     }
    //
    //     if ($("#input[name='primary_topic']").val() === $("#input[name='secondary_topic']").val()) {
    //         swal.fire(
    //             'Please note',
    //             'Primary and Secondary topic should not be same.',
    //             'warning'
    //         )
    //         return false;
    //     }
    //     let data = $('#abstractSubmissionForm').serializeArray();
    //     $.ajax({
    //         url: $('#abstractSubmissionForm').attr('action'),
    //         headers: {'X-Requested-With': 'XMLHttpRequest'},
    //         data: data,
    //         method: $('#abstractSubmissionForm').attr('method'),
    //         dataType: "json",
    //         success: function (response) {
    //             if (response.status === 200) {
    //                 Swal.fire({
    //                     'title': '',
    //                     'text': response.data.message,
    //                     'type': 'success',
    //                     'icon': 'success',
    //                     'confirmButtonText': 'Please ensure that your abstract title is in title case.'
    //                 }).then(function () {
    //                     window.location.href = base_url + '/user/edit_papers_submission/' + response.data.abstract_id
    //                 });
    //             } else {
    //                 Swal.fire(
    //                     'Please note',
    //                     response.reason,
    //                     'warning'
    //                 )
    //             }
    //         }
    //     });
    // });


    //  ############ End  Abstract Submission  # #############

    $('.addAuthorBtn').on('click',function(){
        $('#searchAuthorModal').modal('hide')
        $('#addAuthorModal').find('form')[0].reset();
        $('#addAuthorModal').modal('show');
        $('#saveAuthorBtn').removeClass('updateAuthorBtn')
        $('#saveAuthorBtn').addClass('saveAuthorBtn').text('Save author')
        $('#formSaveAuthor').attr('action', base_url+'/user/add_author_ajax');
        $('#formSaveAuthor').attr('update', 0);
    })

    // Show searchAuthorModal modal after closing the addAuthorModal
    $('#addAuthorModal').on('hide.bs.modal', function (e) {
        let isEdit = $(this).attr('isEdit');
        if(isEdit !== "1"){
            $('#searchAuthorModal').modal('show')
        }
    });

    $('.customInstitutionOption').on('click', function(){

    })

    $('.addNewInstitution').on('click', function(){
        $('#addInstitutionModal').modal('show');
    })

//
//
    $('.showSearchAuthorModal').on('click', function(e){
        e.preventDefault();
        $('#searchAuthorModal').modal('show');
        $('#searchAuthorModal .addMarkedAuthor').show();
        $('#searchAuthorModal .addMarkedPanel').hide();
    })

    $('#searchAuthorModal').on('click', '.searchAuthorBtn', function(){
        let authorName = $('#authorName').val();

        let requiredAuthorDetails = '';
        requiredAuthorDetails = {
            'authorName': authorName
        }

        if( authorName ==='' ){
            Swal.fire(
                'info',
                'Empty search',
                'info'
            )
            return false;
        }else{
            $('#authorResults').css('display', 'block');
            searchAuthor(requiredAuthorDetails)
        }

    })

    function searchAuthor(searchValue){
        $.post(base_url+'/user/search_author_ajax',
            {
                'searchValue' : searchValue
            },
            function(response) {
                if (response.status === "200") {
                    $('#authorResultTableBody').html('<option> </option');
                    $.each(response.data, function(i, val) {
                        $('#authorResultTableBody').append(
                            '<tr>' +
                            '<td user-id="' + val.user_id + '" class="authorDataList" value="' + val.name + '"><input type="checkbox"  name="author_checkbox" class="authorCheckbox" user-info="' + val.name + ' ' + val.surname + ' ( ' + val.institution + ' )" user-id="' + val.user_id + '"></td>' +
                            '<td user-id="' + val.user_id + '" class="authorDataList" value="' + val.name + '">' + val.name + '</td>' +
                            '<td user-id="' + val.user_id + '" class="authorDataList" value="' + val.name + '">' + val.surname + '</td>' +
                            '<td user-id="' + val.user_id + '" class="authorDataList" value="' + val.name + '">' + val.email + '</td>' +
                            '<td user-id="' + val.user_id + '" class="authorDataList" value="' + val.name + '">' + val.province + '</td>' +
                            '<td user-id="' + val.user_id + '" class="authorDataList" value="' + val.name + '">' + val.country + '</td>' +
                            '</tr>'
                        );
                    });
                } else {
                    $('#authorResultTableBody').html(
                        "<tr>" +
                        "<td class='text-center' colspan='7'>No result found.</td>" +
                        "</tr>"
                    );
                }
            }, 'json');
    }

//
    $('.addMarkedAuthor').on('click', function(){
        let user_id = '';
        let user_info = '';
        selected_user_id = [];
        selected_user_info = [];
        $('.authorCheckbox').each(function(){
            let user_id = $(this).attr('user-id');
            let  user_info = $(this).attr('user-info');
            if($(this).prop('checked')===true){
                selected_user_id.push(user_id);
                selected_user_info.push(user_info);
            }
        })

        selected_user_info = selected_user_info.join('<br>');

        if($.isEmptyObject(selected_user_id)){
            toastr.warning('no author assigned')
            return false;
        }

        Swal.fire({
            title: 'Confirm to add:',
            html: selected_user_info,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirm, add it!'
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    url: base_url+'/user/assign_abstract_author',
                    headers: {'X-Requested-With': 'XMLHttpRequest'},
                    data: {
                        'author_ids':selected_user_id,
                        'paper_id': paper_id
                    },
                    method: "POST",
                    dataType: "json",
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Please Wait !',
                            html: 'Loading...',// add html attribute if you want or remove
                            allowOutsideClick: false,
                            onOpen: () => {
                                Swal.showLoading()
                            }
                        });
                    },
                    success: function(response, status ) {
                        // console.log((response.data == null)?'null':'not null');return false;
                        if(response.status == "200"){
                            console.log(response.data.duplicate.length);
                            swal.close();
                            if(response.data.duplicate.length == 0){
                                Swal.fire(
                                    'Success',
                                    response.message,
                                    'success'
                                )
                            }else if(response.data.duplicate.length > 0){
                                let auhtors = '';
                                $.each(response.data.duplicate, function(i, val){
                                    auhtors += val.name+' '+val.surname +'<br>'
                                })
                                Swal.fire(
                                    'Updated',
                                    'Authors added! <br> <span class="fw-bolder">'+ response.data.duplicate.length+' Duplicate author(s): </span>: <br>'+auhtors,
                                    'success'
                                )
                            }

                            $('#authorResultModal').modal('hide');
                            $('#searchAuthorModal').modal('hide');
                        }else{
                            Swal.fire(
                                'error',
                                response.reason,
                                'error'
                            )
                        }
                        getPaperAuthors(paper_id);
                    }
                });
            }
        })
    })

//
//
    $('#formSaveAuthor').on('submit', function(e){
        e.preventDefault();
        let $this = $(this)
        let missing_fields = [];
        if($('#authorEmail').val() !== $('#authorConfirmEmail').val()){
            toastr.error('Email does not match');
            return false;
        }

        $('#formSaveAuthor .required').each(function(){
            if($(this).val() == ''){
                missing_fields.push($(this).attr('title'))
                required_field_missing($(this).attr('title'));
            }
        })

        if(missing_fields.length > 0){
            return false;
        }

        let formData = new FormData($(this)[0]);
        // formData.append('headShot', $('#headShot').prop('files')[0]);
        formData.append('paper_id', paper_id)
        $.ajax({
            url: $(this).attr('action'),
            // headers: {'X-Requested-With': 'XMLHttpRequest'},
            data: formData,
            method: "POST",
            dataType: "json",
            processData: false,
            contentType: false,
            beforeSend: function() {
                Swal.fire({
                    title: 'Please Wait !',
                    html: 'Saving changes...',// add html attribute if you want or remove
                    allowOutsideClick: false,
                    onOpen: () => {
                        Swal.showLoading()
                    }
                });
            },
            success: function(response, status ) {
                if (response){
                    console.log(response)
                    if (response.status === "400") {
                        Swal.fire({
                            icon: "info",
                            html: "This Author is already in the sytem.<br>" +
                                "Name: <label class='text-info'>" + response.data.name + " " + response.data.surname + "</label> <br>" +
                                "<a href='#' onclick='addSearchedAuthor(paper_id, [" + response.data.id + "])'>Add this author</a>"
                        });
                        return false;
                    }else {
                        if (response) {
                            $('#addAuthorModal').attr('isEdit', 1);
                            Swal.fire("success", response.message, "success");
                            $("#authorResultModal").modal("hide");
                            $("#addAuthorModal").modal("hide");
                            $("#searchAuthorModal").modal("hide");
                            if($this.attr('update') == "0") {
                                addSearchedAuthor(paper_id, [response.data])
                            }
                        } else {
                            toastr.error("Please fill all the required fields");
                        }
                    }

                    getPaperAuthors(paper_id);
                }
            }, error: function(xhr, status, error){
                reject(error);
            }
        });
    })



//
    $('#savePaperAuthors').on('click', function(){

        let incomplete = 0;
        let selectedCorrespondents = [];
        let author_orders = [];
        let presenting_authors = [];
        let missing_fields = [];


        $('.markedCorrespondent').each(function(){
            let authorId = $(this).attr('author-id');
            if($(this).prop('checked')===true){
                selectedCorrespondents.push(authorId);
            }
        })
        $('.presentingAuthor').each(function(){
            let authorId = $(this).attr('author-id');
            if($(this).prop('checked')===true){
                presenting_authors.push(authorId);
            }
        })

        $('.author_order').each(function(index){
            author_orders.push($(this).attr('author_id'));
        })

        $('.tdCompleteStatus').each(function(i, val){
            if($(this).attr('status') == 0){
                incomplete = 1;
            }
        })

        if(incomplete == 1){
            swal.fire({
                'title':'info',
                'icon': 'info',
                'text': 'Please complete all author information before saving.'
            });
            return false;
        }

        if(missing_fields.length > 0){
            return false;
        }

        if(author_orders.length !== 0 && presenting_authors.length !== 0) {

            $.ajax({
                url: base_url + '/user/update_paper_authors',
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                data: {
                    'selectedCorrespondents': selectedCorrespondents,
                    'author_orders': author_orders,
                    'presenting_authors': presenting_authors,
                    'paper_id': paper_id
                },
                method: "POST",
                dataType: "json",
                success: function (response, status) {
                    if (response.status == "200") {
                        $('#authorResultModal').modal('hide');
                        swal.fire({
                            title:"Updated",
                            text: "Author(s) Saved",
                            type: "success",
                            icon: "success",
                            confirmButtonText: 'Ok, Next step',
                        }).then((result)=> {
                            if(result.isConfirmed){
                                window.location.href = base_url+'/user/submission_menu/'+paper_id;
                            }
                        });
                    }
                    else if(response.status == 500){
                        Swal.fire(
                            'info',
                            response.message,
                            'warning'
                        )
                    }
                    else{
                        Swal.fire(
                            'Sorry',
                            'Something went wrong, please contact administrator',
                            'warning'
                        )
                        $('#authorResultModal').modal('hide');
                    }
                }
            });
        }else{
            toastr.warning('Author correspondent or Presenting author cannot be empty')
        }
    })


    $('.authorList').on('click','.moveUp', function(){
        let row = $(this).parents("tr:first");
        let rftd = row.find('.order_num'),
            id = row.find('.order_num').text(),
            tar, tftd;
        if ($(this).is(".moveUp")) {
            tar = row.prev();
            row.insertBefore(row.prev());
        } else {
            tar = row.next();
            row.insertAfter(row.next());
        }
        if (tar.length) {
            tftd = tar.find('.order_num');
            rftd.text(tftd.text());
            tftd.text(id)
        }
    })
//
    $('.authorList').on('click','.moveDown', function(){
        let row = $(this).parents("tr:first");
        let rftd = row.find('.order_num'),
            id = row.find('.order_num').text(),
            tar, tftd;
        if ($(this).is(".moveDown")) {
            tar = row.next();
            row.insertAfter(row.next());
        } else {
            tar = row.prev();
            row.insertBefore(row.prev());
        }
        if (tar.length) {
            tftd = tar.find('.order_num');
            rftd.text(tftd.text());
            tftd.text(id)
        }
    })
//
    $('.authorList').on('click','.editAuthorListedBtn', function(){

        let paper_id = $(this).attr('paper_id');
        let author_id = $(this).attr('author_id')
        $('#formSaveAuthor').attr('action', base_url+'user/update_author_details');
        $('#formSaveAuthor').attr('update', 1);
        $('#addAuthorModal').find('form')[0].reset();
        $('#addAuthorModal').attr('isEdit', 1);
        $.ajax({
            url: base_url + '/user/get_author_info',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            data: {
                'author_id': author_id
            },
            method: "POST",
            dataType: "json",
            success: function (response, status) {
                console.log(response)

                if(response.status == 200){

                    $('#addAuthorModal').modal('show');
                    $('#authorFName').val(response.data.name)
                    $('#authorMName').val(response.data.middle_name)
                    $('#authorLName').val(response.data.surname)
                    $('#authorEmail').val(response.data.email)
                    $('#authorConfirmEmail').val(response.data.email)

                    // Affiliation
                    $('#authorDegree').val(response.data.deg)
                    $('#authorPosition').val(response.data.position)
                    $('#authorTitle').val(response.data.title)
                    $('#authorInstitution').val(response.data.institution)
                    $('#authorPhone').val((response.data.phone) ?response.data.phone:'' )
                    $('#searchId').val(response.data.institution_id)

                    // Address
                    $('#authorAddress').val(response.data.address)
                    $('#authorCountry').val(response.data.country)
                    $('#authorCity').val(response.data.city)
                    $('#authorProvince').val(response.data.province)
                    $('#authorZipcode').val(response.data.zipcode)
                    $('#authorDeg').val(response.data.deg)
                    $('#authorPhone').val(response.data.phone)

                    $('#addAuthorModal #author_id').val(author_id);

                }else{
                    swal.fire(
                        'info',
                        'Sorry you cannot edit this author',
                        'info',
                    )
                }
            },error(){
                toastr.error('Something went wrong, please contact administrator')
            }
        })
    })
//
//     $('.submitNewInstitutionBtn').on('click', function(){
//         let institution_name = $('#institutionName').val();
//         let institution_country = $('#institutionCountryId').val();
//         let institution_state = $('#institutionStateId').val();
//         let institution_city = $('#institutionCityId').val();
//
//         submitNewInstitution(institution_name,institution_country,institution_state,institution_city);
//     })
//
    $('.authorList').on('click', '.removePaperAuthor', function(){
        let paper_author_id = $(this).attr('paper_authors_id');
        Swal.fire({
            title: 'Are you sure?',
            text: "This author will be removed",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.isConfirmed) {
                removePaperAuthor(paper_author_id);
            }
        })
    })
//
    $('.authorList').on('click', '.resendEmailBtn', function(){
        let paper_id = $(this).attr('paper_id')
        Swal.fire({
            title: 'Are you sure?',
            text: "An email will be sent to this author",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Continue'
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    url: base_url + '/user/resend_disclosure_email',
                    data: {
                        'user_id': $(this).attr('user_id'),
                        'paper_id': paper_id,
                        'author_id': $(this).attr('author_id'),
                        'paper_authors_id': $(this).attr('paper_authors_id'),
                    },
                    method: "POST",
                    dataType: "json",
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Please Wait !',
                            html: 'Sending email to author...',// add html attribute if you want or remove
                            allowOutsideClick: false,
                            onOpen: () => {
                                Swal.showLoading()
                            }
                        });
                    },
                    success: function (response, status) {
                        if(response.status == 200){
                            swal.close();
                            swal.fire(
                                'success',
                                'Email sent successfully',
                                'success',
                            )
                            getPaperAuthors(paper_id)
                        }else{
                            swal.fire(
                                'info',
                                'Sorry you cannot edit this author, reason: '+ response.reason,
                                'info',
                            )
                        }
                    },error(){
                        toastr.error('Something went wrong, please contact administrator')
                    }
                });
            }
        })

    })
//
    $('.quickAddAuthorTable').on('click', '.quickAddAuthorBtn', function(e){
        e.preventDefault();

        let author_id = $(this).attr('author_id');
        $.ajax({
            url: base_url+'user/quick_add_author',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            data: {
                'author_id':author_id,
                'paper_id': paper_id
            },
            method: "POST",
            dataType: "json",
            beforeSend: function() {
                Swal.fire({
                    title: 'Please Wait !',
                    html: 'Loading...',// add html attribute if you want or remove
                    allowOutsideClick: false,
                    onOpen: () => {
                        Swal.showLoading()
                    }
                });
            },
            success: function(response, status ) {
                // console.log((response.data == null)?'null':'not null');return false;
                if(response.status === 200){
                    swal.close();
                    if(response.data !== null){
                        Swal.fire(
                            'Updated',
                            response.data.message,
                            'success'
                        )
                    }
                    Swal.fire(
                        'Updated',
                        'Author added successfully',
                        'success'
                    )
                    $('#authorResultModal').modal('hide');
                    $('#searchAuthorModal').modal('hide');
                }else{
                    Swal.fire(
                        'Please note',
                        response.reason,
                        'warning'
                    )
                }
                getPaperAuthors(paper_id);
            }
        });

    })

    $('.authorList').on('click','.discloseNowBtn', function(){
        let abstract_id = $(this).attr('abstract_id');
        window.location.href = base_url + '/author/view_copyright';
    })
//
//     $('#authorInstitution').on('click', function() { // Search for institution onclick without any value
//         $(this).autocomplete('search', '');
//     });
//
//
//     $('.cvUploadBtn').on('click', function(e){
//         let abstract_id = $(this).attr('abstract_id');
//         e.preventDefault();
//         formUpload = new FormData();
//         formUpload.append('cv_upload', $('input[name="cv_upload"]')[0].files[0])
//         formUpload.append('abstract_id', abstract_id)
//
//         let valid_upload_filter = ['pdf'];
//         let file = $('input[name="cv_upload"]')[0].files[0];
//         let extension = file.name.split('.').pop();
//         if($.inArray(extension, valid_upload_filter) === -1){
//             swal.fire({
//                 'title': 'Invalid file',
//                 'text': 'CV must be a pdf file',
//                 'icon':'info'
//             })
//             return false;
//         }
//         // console.log(extension); // Output: "gz"
//         // return false
//         $.ajax({
//             url: base_url+'/'+event_uri+'/disclosures/cv_upload',
//             headers: {'X-Requested-With': 'XMLHttpRequest'},
//             data: formUpload,
//             method: "POST",
//             processData: false,
//             contentType: false,
//             beforeSend: function() {
//                 Swal.fire({
//                     title: 'Please Wait !',
//                     html: 'Uploading...',// add html attribute if you want or remove
//                     allowOutsideClick: false,
//                     onOpen: () => {
//                         Swal.showLoading()
//                     }
//                 });
//             },
//             success: function(response, status ) {
//                 response = JSON.parse(response);
//                 console.log(response.data)
//                 if(response.status == 200){
//                     swal.fire({
//                         'title': 'Success',
//                         'text': 'CV uploaded',
//                         'icon':'success'
//                     })
//
//                     $('#cv_preview').html('<a href="'+base_url+'/uploads/cv/'+response.data[0].new_name+'" target="_blank">'+response.data[0].file_name+'</a>')
//                 }else{
//                     swal.fire({
//                         'title': 'Error',
//                         'text': 'Something went wrong uploading cv, please contact administrator',
//                         'icon':'error'
//                     })
//                 }
//             }
//         });
//
//         return false;
//     })
// //    End jquery Event
// })
//
// function searchAuthor(searchValue){
//     $.post(base_url+'/'+event_uri+'/user/search_author_ajax',
//         {
//             'searchValue' : searchValue
//         },
//         function(response){
//             $('#authorResultTableBody').html('<option> </option');
//             $.each(response[0].data, function(i, val){
//
//                 // console.log(val)
//                 // console.log(val.email)
//                 $('#authorResultTableBody').append(
//                     '<tr>' +
//                     '<td user-id="'+val.user_id+'" class="authorDataList" value="'+val.name+'"><input type="checkbox"  name="author_checkbox" class="authorCheckbox" user-info="'+val.name+' '+val.surname+' ( '+val.institution+' )" user-id="'+val.user_id+'"></td>' +
//                     '<td user-id="'+val.user_id+'" class="authorDataList" value="'+val.name+'">'+val.name+'</td>' +
//                     '<td user-id="'+val.user_id+'" class="authorDataList" value="'+val.name+'">'+((val.mname)?val.mname:'')+'</td>' +
//                     '<td user-id="'+val.user_id+'" class="authorDataList" value="'+val.name+'">'+val.surname+'</td>' +
//                     '<td user-id="'+val.user_id+'" class="authorDataList" value="'+val.name+'">'+val.institution+'</td>' +
//                     '</tr>'
//                 )
//             })
//         },'json')
// }
//
//
    $('.authorList').on('click', '.presentingAuthor', function(){
        let id = $(this).attr('author-id')
        $('#correspondent_' + id).prop('checked', true);
    })

});

function getPaperAuthors(paper_id = null){
    $('.authorList').html('');
    $.post(base_url+'/user/get_paper_authors',
        {
            'paper_id':paper_id,
        },

        function(response){
            // console.log(response);
            $('.authorList').html('');
            $.each(response.data, function(index, val){
                console.log(val)
                let complete_status = 1;
                let presenting = (val.is_presenting_author === 'Yes')?'checked':'unchecked';
                let correspondent = (val.is_correspondent === 'Yes')?'checked':'unchecked';

                let copyright_agreement_date = new Date(val.copyright_agreement_date).toISOString().split('T')[0].replace(/-/g, '/');
                let copyrightStatus = (val.is_copyright_agreement_accepted == 1)? '<small class="text-success text-wrap"> current : <br>'+copyright_agreement_date+' </small>':'<small class="text-warning "> none </a>';
                let emailed = "<small class='small'>" + ((val.mailLogs) ? (val.mailLogs.updated_at !== null) ? val.mailLogs.updated_at.split(' ')[0]: (val.mailLogs.created_at !== null) ? val.mailLogs.created_at.split(' ')[0] : 'none' : 'none') + "</small>";
                let resendEmailBtn = ((val.is_copyright_agreement_accepted == null)?'<a class=" btn btn-sm btn-primary text-white resendEmailBtn" author_id="'+val.author_id+'" paper_authors_id="'+val.id+'" paper_id="'+val.paper_id+'" user_id="'+val.user_id+'"><i class="fa-solid fa-square-envelope"></i> Resend </a>':'<span class="small"> none </span>');
                let disclose_now ='<a class=" btn btn-sm btn-info text-white discloseNowBtn" author_id="'+val.author_id+'" paper_authors_id="'+val.id+'" paper_id="'+val.paper_id+'" user_id="'+val.user_id+'"><i class="fa-solid fa-square-envelope"></i> Disclose Now</a>';
                let required_info = ['email', 'institution', 'deg', 'city', 'country', 'province'];

                let submitNowBtn = '<a href="submitNowBtn" author_id="'+val.author_id+'" paper_authors_id="'+val.id+'" paper_id="'+val.paper_id+'" user_id="'+val.user_id+'">Submit Copyright Now</a>'

                $.each(required_info, function(index, arr){
                   /* console.log(val[arr] );*/
                    if(val[arr] == '' || val[arr] == null){
                        complete_status = 0;
                    }
                })

                // console.log(val)

                $('.authorList').append('<tr class="author_order" author_id="'+val.author_id+'" order="'+val.author_order+'" name="'+val.name+'" paper_authors_id="'+val.ID+'" paper_id="'+val.paper_id+'">'+
                    '<td><span class="order_num">'+(index+1)+'.</span></td>'+
                    '<td class="text-nowrap">'+val.name+' '+val.surname+'</td>'+
                    '<td class="text-nowrap"><input id="correspondent_'+val.author_id+'" type="checkbox" class="correspondent markedCorrespondent" author-id="'+val.author_id+'" '+correspondent+'> <label for="correspondent_'+val.author_id+'"> Correspondent</label></td>'+
                    '<td class="text-nowrap"><input id="presentingAuthor_'+val.author_id+'" type="checkbox" class="presentingAuthor" name="presentingAuthor" author-id="'+val.author_id+'" id="presenting_author_'+val.author_id+'" '+presenting+' author_name="'+val.name+'" author_surname="'+val.surname+'"> <label for="presentingAuthor_'+val.author_id+'"> Presenting Author </label></td>'+
                    '<td class="text-nowrap"><input id="coAuthor_'+val.author_id+'" type="checkbox" class="co-author" name="co-author" author-id="'+val.author_id+'" id="presenting_author_'+val.author_id+'" '+presenting+' author_name="'+val.name+'" author_surname="'+val.surname+'"> <label  for="coAuthor_'+val.author_id+'"> Co-Author </label></td>'+
                    '<td class="text-nowrap"><a class=" btn btn-sm btn-primary text-white moveUp"><i class="fa-solid fa-arrow-up "></i> </a> </td>'+
                    '<td class="text-nowrap"><a class=" btn btn-sm btn-primary text-white moveDown"><i class="fa-solid fa-arrow-down"></i> </a></td>'+
                    '<td class="text-nowrap"><a class=" btn btn-sm btn-info text-white editAuthorListedBtn"  author_id="'+val.author_id+'"  paper_authors_id="'+val.id+'" paper_id="'+val.paper_id+'"><i class="fa-solid fa-user-pen"></i> </a> </td>'+
                    '<td class="text-nowrap"><a class=" btn btn-sm btn-danger text-white removePaperAuthor" author_id="'+val.author_id+'"  paper_authors_id="'+val.id+'" paper_id="'+val.paper_id+'"><i class="fa-solid fa-user-xmark"></i> </a></td>'+
                    '<td class="tdCompleteStatus" status="'+complete_status+'">'+(complete_status == 1 ? '<span class="text-success small">Completed</span>':'<span class="text-danger small">Incomplete</span>' )+'</td>'+
                    '<td style="text-align:center" class="copyrightStatus text-wrap"> '+copyrightStatus+'</td>'+
                    '<td class="text-nowrap"><span class="">'+emailed+' </span></td>'+
                    '<td class="text-nowrap">'+
                    ((val.is_copyright_agreement_accepted == null && val.author_id == current_user_id) ? disclose_now :
                        ((val.is_copyright_agreement_accepted == null && val.author_id != current_user_id) ? resendEmailBtn :
                            '<i class="fa-sharp fa-solid fa-circle-check text-success"></i>'))+
                    '</td>'+
                    '</tr>'
                )
            })
        },'json')
}
//
//
// function submitNewInstitution(institution_name,institution_countryId,institution_stateId,institution_cityId){
//     $.ajax(
//         {
//             url: base_url + '/user/add_new_institution',
//             dataType: 'json', // type of response data
//             type: 'POST',
//             data: {
//                 'institution_name': institution_name,
//                 'institution_country_id': institution_countryId,
//                 'institution_state_id': institution_stateId,
//                 'institution_city_id': institution_cityId,
//
//             },
//             success: function (data,status,xhr) {   // success callback function
//                 console.log(data);
//                 if(data.status === 409){
//                     swal.fire(
//                         'info',
//                         'Institution already exist or same institution is already submitted and waiting for approval.',
//                         'info'
//                     )
//                     return false;
//                 }
//                 else if(data.status === 200){
//                     swal.fire(
//                         'success',
//                         'Institution was successfully submitted.',
//                         'success'
//                     )
//                     $('#addInstitutionModal').modal('hide');
//                     $("#authorInstitution").val(data.data.institution_name)
//                     $("#searchId").val(data.data.institution_id);
//                     return false;
//                 }
//                 swal.fire(
//                     'error',
//                     'Missing field.',
//                     'error'
//                 )
//                 return false;
//             },
//             error: function (jqXhr, textStatus, errorMessage) { // error callback
//                 console.log('jq'+jqXhr);
//             }
//         });
// }
//
function removePaperAuthor(paper_author_id){
    $.ajax(
        {
            url: base_url + '/user/remove_paper_author',
            dataType: 'json', // type of response data
            type: 'POST',
            data: {
                paper_author_id: paper_author_id
            },
            success: function(response){
                if(response.status === 200){
                    swal.fire(
                        'Removed',
                        'Author has been removed',
                        'success'
                    )
                    getPaperAuthors(paper_id)
                    console.log(getPaperAuthors(paper_id))
                }
            }
        });
}
//
function required_field_missing(field){
    Swal.fire({
        title: '',
        html: 'Please fill all required fields, <span class="text-danger">'+ field +'</span> is empty',// add html attribute if you want or remove
        allowOutsideClick: true,
        icon:'info'
    });
    return false;
}
//
// function countWords(inputText){
//     let wordCount = inputText.trim().split(/\s+/).length;
//
//     return wordCount;

function saveCommentToUpload(review_id, comment, paper_id){  //Saving Submitter Comment/Response on reviewers re-upload
    $.ajax(
        {
            url: base_url + '/user/saveCommentToUpload',
            dataType: 'json', // type of response data
            type: 'POST',
            data: {
                review_id: review_id,
                comment:comment,
                paper_id:paper_id
            },
            success: function(response){
                if(response.status === "200"){
                    swal.fire(
                        'Saved',
                        'Comment has been saved',
                        'success'
                    )
                }

                $('#collapseExample_'+review_id).collapse('hide')
            }
        });
}

function addSearchedAuthor(paper_id, selected_user_id){
    $.ajax({
        url: base_url+'/user/assign_abstract_author',
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        data: {
            'author_ids':selected_user_id,
            'paper_id': paper_id
        },
        method: "POST",
        dataType: "json",
        beforeSend: function() {
            Swal.fire({
                title: 'Please Wait !',
                html: 'Loading...',// add html attribute if you want or remove
                allowOutsideClick: false,
                onOpen: () => {
                    Swal.showLoading()
                }
            });
        },
        success: function(response, status ) {
            // console.log((response.data == null)?'null':'not null');return false;
            if(response.status == "200"){
                console.log(response.data.duplicate.length);
                swal.close();
                if(response.data.duplicate.length == 0){
                    Swal.fire(
                        'Success',
                        response.message,
                        'success'
                    )
                }else if(response.data.duplicate.length > 0){
                    let auhtors = '';
                    $.each(response.data.duplicate, function(i, val){
                        auhtors += val.name+' '+val.surname +'<br>'
                    })
                    Swal.fire(
                        'Updated',
                        'Authors added! <br> <span class="fw-bolder">'+ response.data.duplicate.length+' Duplicate author(s): </span>: <br>'+auhtors,
                        'success'
                    )
                }

                $('#authorResultModal').modal('hide');
                $('#searchAuthorModal').modal('hide');
            }else{
                Swal.fire(
                    'error',
                    response.reason,
                    'error'
                )
            }
            getPaperAuthors(paper_id);
        }
    });
}