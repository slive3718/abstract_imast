$('.submitNewInstitutionBtn').on('click', function() {
    let institution_name = $('#institutionName').val();
    let institution_country = $('#institutionCountryId').val();
    let institution_state = $('#institutionStateId').val();
    let institution_city = $('#institutionCityId').val();

    submitNewInstitution(institution_name, institution_country, institution_state, institution_city);
});

function submitNewInstitution(institution_name, institution_countryId, institution_stateId, institution_cityId) {
    $.ajax({
        url: base_url + 'institution/add_new',
        dataType: 'json',
        type: 'POST',
        data: {
            'institution_name': institution_name,
            'institution_country_id': institution_countryId,
            'institution_state_id': institution_stateId,
            'institution_city_id': institution_cityId,
        },
        success: function (data, status, xhr) {
            console.log(data);

            switch (xhr.status) {
                case 200:
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Institution was successfully submitted.'
                    }).then(() => {
                        $('#addInstitutionModal').modal('hide');
                        $("#authorInstitution").val(data.institution_name);
                        $("#searchId").val(data.institution_id);
                    });
                    break;

                case 400:
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error || 'Missing field.'
                    });
                    break;

                case 409:
                    Swal.fire({
                        icon: 'info',
                        title: 'Duplicate Entry',
                        text: data.error || 'Institution already exists or is awaiting approval.'
                    });
                    break;

                default:
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An unknown error occurred.'
                    });
                    break;
            }
        },
        error: function (jqXhr, textStatus, errorMessage) {
            console.error('Error:', jqXhr);
            Swal.fire({
                icon: 'error',
                title: 'Request Failed',
                text: jqXhr.responseJSON?.error || 'Failed to submit the institution.'
            });
        }
    });
}
