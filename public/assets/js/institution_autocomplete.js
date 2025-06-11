let g_countryId = null;
let g_stateId = null;

$(function () {
    // Initialize on load
    autocomplete('#authorInstitution', base_url + '/user/get_institution');
    autoCompleteCountries('#institutionCountry', base_url + '/locations/get_countries');
    autocompleteCity(); // Initialize city search directly

    $('#authorInstitution').on('click', function () {
        if ($(this).data('ui-autocomplete')) {
            $(this).autocomplete('search', '');
        }
    });
});

function autoCompleteCountries(element, url) {
    if ($(element).data('ui-autocomplete')) {
        $(element).autocomplete('destroy');
    }

    $(element).autocomplete({
        source: function (request, response) {
            $.ajax({
                url: url,
                dataType: "json",
                type: "POST",
                data: { searchValue: request.term },
                success: function (data) {
                    response($.map(data.data, function (item) {
                        return {
                            label: item.name,
                            value: item.name,
                            id: item.id
                        };
                    }));
                }
            });
        },
        minLength: 2,
        select: function (event, ui) {
            $('#institutionCountryId').val(ui.item.id);
            $('#institutionState, #institutionStateId, #institutionCity, #institutionCityId').val('');
            g_countryId = ui.item.id;
            g_stateId = null;
            autocompleteState(ui.item.id);
            autocompleteCity(g_countryId, null); // Reinitialize city with country ID
        },
        change: function (event, ui) {
            if (!ui.item) {
                $(this).val('');
                $('#institutionCountryId').val('');
                alert('Please select a valid country');
            }
        }
    });
}

function autocompleteState(countryId) {
    if ($('#institutionState').data('ui-autocomplete')) {
        $('#institutionState').autocomplete('destroy');
    }

    $('#institutionState').autocomplete({
        source: function (request, response) {
            $.ajax({
                url: base_url + '/locations/get_country_states',
                dataType: 'json',
                type: 'POST',
                data: {
                    searchValue: request.term,
                    country_id: countryId
                },
                success: function (data) {
                    response($.map(data.data, function (item) {
                        return {
                            label: item.name,
                            value: item.name,
                            id: item.id
                        };
                    }));
                }
            });
        },
        minLength: 2,
        select: function (event, ui) {
            $('#institutionStateId').val(ui.item.id);
            g_countryId = countryId;
            g_stateId = ui.item.id;
            $('#institutionCity, #institutionCityId').val('');
            autocompleteCity(g_countryId, g_stateId);
        },
        change: function (event, ui) {
            if (!ui.item) {
                $(this).val('');
                $('#institutionStateId').val('');
                alert('Please select a valid state');
            }
        }
    });
}

function autocompleteCity(countryId = null, stateId = null) {
    const url = stateId
        ? base_url + '/locations/get_state_cities'
        : base_url + '/locations/get_all_cities';

    if ($('#institutionCity').data('ui-autocomplete')) {
        $('#institutionCity').autocomplete('destroy');
    }

    $('#institutionCity').autocomplete({
        source: function (request, response) {
            $.ajax({
                url: url,
                dataType: 'json',
                type: 'POST',
                data: {
                    searchValue: request.term,
                    country_id: countryId || g_countryId,
                    state_id: stateId || g_stateId
                },
                success: function (data) {
                    response($.map(data.data, function (item) {
                        return {
                            id: item.id,
                            label: item.completeAddress,
                            value: item.name,
                            city: item.name,
                            state: item.state_name,
                            state_id: item.state_id,
                            country: item.country_name,
                            country_id: item.country_id
                        };
                    }));
                }
            });
        },
        minLength: stateId ? 3 : 4,
        delay: 300,
        select: function (event, ui) {
            $('#institutionCityId').val(ui.item.id);
            $('#institutionCity').val(ui.item.value);
            $('#institutionCountry').val(ui.item.country);
            $('#institutionCountryId').val(ui.item.country_id);

            // ✅ Always update state and state ID
            $('#institutionState').val(ui.item.state || ''); // Handle cases where state might be null
            $('#institutionStateId').val(ui.item.state_id || '');

            // ✅ Properly update global state and country IDs
            g_countryId = ui.item.country_id;
            g_stateId = ui.item.state_id || null; // Set to null if not available
        },
        change: function (event, ui) {
            if (!ui.item) {
                $(this).val('');
                $('#institutionCityId').val('');
                alert('Please select a valid city');
            }
        }
    });
}

function autocomplete(inputId, url) {
    if ($(inputId).data('ui-autocomplete')) {
        $(inputId).autocomplete('destroy');
    }

    $(inputId).autocomplete({

        source: function (request, response) {
            $.ajax({
                url: url,
                dataType: 'json',
                type: 'POST',
                data: { name: request.term },
                success: function (data) {
                    if (data) {
                        data.push({ name: 'Add New', btn: 'btnAddNewAuth' });
                        response($.map(data, function (item) {
                            return {
                                id: item.id,
                                value: item.name,
                                label: item.name,
                                btn: item.btn
                            };
                        }));
                    }
                },
                error: function () {
                    toastr.error('No result found');
                    response([{ id: null, value: 'Add New', btn: 'btnAddNewAuth' }]);
                }
            });
        },
        minLength: 4,
        select: function (event, ui) {
            $(this).val(ui.item.value);
            $('#searchId').val(ui.item.id);
            if (ui.item.btn === 'btnAddNewAuth') {
                $('#addInstitutionModal').modal('show');
            }
        }
    });
}
