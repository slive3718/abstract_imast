

$(function() {

    getPanelist(paper_id);

    $('.showSearchPanelModal').on('click', function(e){
        e.preventDefault();
        $('#searchAuthorModal').modal('show');
        $('#searchAuthorModal .addMarkedAuthor').hide();
        $('#searchAuthorModal .addMarkedPanel').show();

    })

    $('.addMarkedPanel').on('click', function(){

        selected_user_id = [];
        selected_user_info = [];

        let panelPaperID = $('#panelPaperID').val()

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
            title: 'Confirm to add panel:',
            html: selected_user_info,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirm, add it!'
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    url: base_url+'/user/assign_panelist',
                    headers: {'X-Requested-With': 'XMLHttpRequest'},
                    data: {
                        'author_ids':selected_user_id,
                        'panel_paper_id': panelPaperID
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
                            console.log(response);
                            swal.close();
                            if(response.data == 0){
                                Swal.fire(
                                    'Updated',
                                    'Author added successfully',
                                    'success'
                                )
                            }else if(response.data.action > 0){
                                Swal.fire(
                                    'Updated',
                                    'This author is already added to your list',
                                    'success'
                                )
                            }

                            $('#authorResultModal').modal('hide');
                            $('#searchAuthorModal').modal('hide');

                            window.location.href = base_url+'/admin/panelist/'+response.data;
                        }else{
                            Swal.fire(
                                'error',
                                response.reason,
                                'error'
                            )
                        }

                    }
                });
            }
        })
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


//
    $('.panelistList').on('click','.editAuthorListedBtn', function(){

        let paper_id = $(this).attr('paper_id');
        let author_id = $(this).attr('author_id')
        $('#formSaveAuthor').attr('action', base_url+'user/update_author_details');
        $('#addAuthorModal').attr('isEdit', 1);
        $('#addAuthorModal').find('form')[0].reset();
        $.ajax({
            url: base_url + '/user/get_author_info',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            data: {
                'author_id': author_id
            },
            method: "POST",
            dataType: "json",
            success: function (response, status) {

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

    $('.panelistList').on('click', '.removePaperAuthor', function(){
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


    $('#savePanelist').on('click', function(){

        let author_orders = [];

        $('.author_order').each(function(index){
            author_orders.push($(this).attr('author_id'));
        })

        if(author_orders.length !== 0) {

            $.ajax({
                url: base_url + '/user/update_panelist_submitted',
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                data: {
                    'paper_id': paper_id,
                    'author_orders': author_orders
                },
                method: "POST",
                dataType: "json",
                success: function (response, status) {
                    if (response.status == "200") {
                        $('#authorResultModal').modal('hide');
                        swal.fire({
                            title: "Updated",
                            text: "Author(s) Saved",
                            type: "success",
                            icon: "success",
                            confirmButtonText: 'Ok, Next step',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = base_url + '/admin/view_abstract_panel/' + paper_id;
                            }
                        });
                    } else if(response.status == "400"){
                        swal.fire({
                            title: "Updated",
                            text: "Author(s) saved but Something went wrong while sending email. Please contact administrator.",
                            type: "info",
                            icon: "info",
                            confirmButtonText: 'Ok, Next step',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = base_url + '/admin/view_abstract_panel/' + paper_id;
                            }
                        });
                    } else if (response.status == 500) {
                        Swal.fire(
                            'info',
                            response.message,
                            'warning'
                        )
                    } else {
                        Swal.fire(
                            'Sorry',
                            'Something went wrong, please contact administrator',
                            'warning'
                        )
                        $('#authorResultModal').modal('hide');
                    }
                }
            });
        }
    });

    $('#panelOverviewForm').validate({
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
            formData.append("user_id", userID);

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
                        window.location.href = base_url + '/admin/view_abstract_panel/' + response.data.insert_id + '/new';
                    }else{
                        toastr.error(response)
                    }
                }
            });
            return false; // Prevent default form submission
        }
    });


//######### Authors #####################################################

    $('.addAuthorBtn').on('click',function(){
        $('#searchAuthorModal').modal('hide')
        $('#addAuthorModal').find('form')[0].reset();
        $('#addAuthorModal').modal('show');
        $('#saveAuthorBtn').removeClass('updateAuthorBtn')
        $('#saveAuthorBtn').addClass('saveAuthorBtn').text('Save author')
        $('#formSaveAuthor').attr('action', base_url+'/user/add_author_ajax');
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

    $('#formSaveAuthor').on('submit', function(e){
        e.preventDefault();
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
                        // console.log(response)
                        Swal.fire({
                            "icon": "info",
                            "html":"Sorry, Author email already exists, Please try to search to add the author <br>"+
                                "Name: <label class='text-info'>"+ response.data.name +" "+ response.data.surname+"</label>"
                        });
                        return false;
                    } else {
                        if (response) {
                            Swal.fire("success", response.message, "success");
                            $("#authorResultModal").modal("hide");
                            $("#addAuthorModal").modal("hide");
                            $("#searchAuthorModal").modal("hide");
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

    $('.quickAddAuthorTable').on('click', '.quickAddAuthorBtn', function(e){
        e.preventDefault();

        let author_id = $(this).attr('author_id');
        $.ajax({
            url: base_url+'user/quick_add_author',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            data: {
                'author_id':author_id,
                'paper_id': paper_id,
                'author_type': 'panelist'
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
                console.log(response)
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
                getPanelist(paper_id);
            }
        });

    })

    $('.panelistList').on('click','.moveUp', function(){
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
    $('.panelistList').on('click','.moveDown', function(){
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

})

function getPanelist(paper_id = null){
    $('.panelistList').html('');
    $.post(base_url+'/user/get_paper_authors',
        {
            'paper_id':paper_id,
            'author_type': 'panelist'
        },

        function(response){
            // console.log(response);
            $('.panelistList').html('');
            $.each(response.data, function(index, val){
                let emailed = "<small class='small'>" + ((val.mailLogs) ? (val.mailLogs.updated_at !== null) ? val.mailLogs.updated_at.split(' ')[0]: (val.mailLogs.created_at !== null) ? val.mailLogs.created_at.split(' ')[0] : 'none' : 'none') + "</small>";

                $('.panelistList').append('<tr class="author_order" author_id="'+val.author_id+'" order="'+val.author_order+'" name="'+val.name+'" paper_authors_id="'+val.ID+'" paper_id="'+val.paper_id+'">'+
                    '<td><span class="order_num">'+(index+1)+'</span></td>'+
                    '<td class="text-nowrap">'+val.name+' '+val.surname+'</td>'+
                    '<td class="text-nowrap"><a class=" btn btn-sm btn-primary text-white moveUp"><i class="fa-solid fa-arrow-up "></i> </a> <a class=" btn btn-sm btn-primary text-white moveDown"><i class="fa-solid fa-arrow-down"></i> </a></td>'+
                    '<td class="text-nowrap">' +
                    '<a class="btn btn-sm btn-info text-white editAuthorListedBtn"  author_id="'+val.author_id+'"  paper_authors_id="'+val.id+'" paper_id="'+val.paper_id+'">' +
                    '<i class="fa-solid fa-user-pen"></i>' +
                    '</a>' +
                    '<a class="btn btn-sm btn-danger text-white removePaperAuthor ms-3" author_id="'+val.author_id+'"  paper_authors_id="'+val.id+'" paper_id="'+val.paper_id+'"><i class="fa-solid fa-user-xmark"></i> </a>'+
                    ' </td>'+
                    '<td>'+emailed+'</td>'+
                    '</tr>'
                )
            })
        },'json')
}


function searchAuthor(searchValue){
    $.post(base_url+'/user/search_author_ajax',
        {
            'searchValue' : searchValue
        },
        function(response){
            // console.log(response)
            $('#authorResultTableBody').html('<option> </option');
            $.each(response.data, function(i, val){
                console.log(val)

                $('#authorResultTableBody').append(
                    '<tr>' +
                    '<td user-id="'+val.user_id+'" class="authorDataList" value="'+val.name+'"><input type="checkbox"  name="author_checkbox" class="authorCheckbox" user-info="'+val.name+' '+val.surname+' ( '+val.institution+' )" user-id="'+val.user_id+'"></td>' +
                    '<td user-id="'+val.user_id+'" class="authorDataList" value="'+val.name+'">'+val.name+'</td>' +
                    '<td user-id="'+val.user_id+'" class="authorDataList" value="'+val.name+'">'+val.surname+'</td>' +
                    '<td user-id="'+val.user_id+'" class="authorDataList" value="'+val.name+'">'+val.email+'</td>' +
                    '<td user-id="'+val.user_id+'" class="authorDataList" value="'+val.name+'">'+val.province+'</td>' +
                    '<td user-id="'+val.user_id+'" class="authorDataList" value="'+val.name+'">'+val.country+'</td>' +
                    '</tr>'
                )
            })
        },'json')
}

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
                    getPanelist(paper_id)
                }
            }
        });
}




