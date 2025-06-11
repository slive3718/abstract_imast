let gs_countryId = null;
let gs_stateId = null;

$(function(){
    console.log('address');
    $('#authorCountry').on('click change input', function(){
        autoCompleteAuthorCountries('#authorCountry', base_url + '/locations/get_countries');
    });
    $('#authorCity').on('focusin', function(){
        autoCompleteAuthorCity(gs_countryId, gs_stateId);
    });
    $('#authorProvince').on('focusin', function(){
        autoCompleteAuthorState(gs_countryId);
    });
});

function autoCompleteAuthorCountries(element, url){
    $(element).autocomplete({
        source: function(request, response) {
            $.ajax({
                url: url,
                dataType: "json",
                type: "POST",
                data: {
                    searchValue: request.term
                },
                success: function(data) {
                    response($.map(data.data, function(item) {
                        return {
                            label: item.name,
                            value: item.name,
                            id: item.id,
                        };
                    }));
                }
            });
        },
        minlength: 2,
        select: function(event, ui) {
            console.log(ui.item.id);
            $('#authorCountryId').val(ui.item.id);
            gs_countryId = ui.item.id;
            autoCompleteAuthorState(ui.item.id);
        },
        change: function(event, ui) {
            if (!ui.item) {
                gs_countryId = null;
            }
        }
    });
}

function autoCompleteAuthorState(countryId){
    $('#authorProvince').autocomplete({
        source: function(request, response) {
            $.ajax({
                url: base_url + '/locations/get_country_states',
                dataType: "json",
                type: "POST",
                data: {
                    searchValue: request.term,
                    country_id: countryId
                },
                success: function(data) {
                    response($.map(data.data, function(item) {
                        return {
                            label: item.name,
                            value: item.name,
                            id: item.id,
                        };
                    }));
                }
            });
        },
        minlength: 2,
        select: function(event, ui) {
            $('#authorProvinceId').val(ui.item.id);
            gs_stateId = ui.item.id;
            autoCompleteAuthorCity(gs_countryId, ui.item.id);
        },
        change: function(event, ui) {
            if (!ui.item) {
                gs_stateId = null;
            }
        }
    });
}

function autoCompleteAuthorCity(countryId = null, stateId = null) {
    $('#authorCity').autocomplete({
        source: function(request, response) {
            let url = base_url + '/locations/';
            let data = { searchValue: request.term };

            if (countryId && stateId) {
                url += 'get_state_cities';
                data.country_id = countryId;
                data.state_id = stateId;
            } else if (countryId) {
                url += 'get_country_cities';
                data.country_id = countryId;
            } else {
                url += 'get_all_cities';
            }

            $.ajax({
                url: url,
                dataType: "json",
                type: "POST",
                data: data,
                success: function(data) {
                    response($.map(data.data, function(item) {
                        return {
                            id: item.id,
                            label: item.completeAddress,
                            value: item.name,
                        };
                    }));
                }
            });
        },
        minlength: 4,
        maxShowItems: 10,
        delay: 1000,
        select: function(event, ui) {
            $('#authorCityId').val(ui.item.id);
            $('#authorCity').val(ui.item.value);
            gs_countryId = ui.item.country_id;
            gs_stateId = ui.item.state_id;
        },
        change: function(event, ui) {
            if (!ui.item) {
                gs_countryId = null;
                gs_stateId = null;
            }
        },
        search: function() {
            $(this).data("ui-autocomplete").menu.bindings = $();
        }
    });
}
