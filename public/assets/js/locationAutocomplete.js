// //
//
// let g_countryId = null;
// let g_stateId = null;
//
// $(function(){
//     // autocomplete('#authorInstitution',  base_url + '/user/get_institution');
//     // autoCompleteCountries('#institutionCountry',  base_url + '/locations/get_countries');
//
//     // $('#institutionCity').on('focusin', function(){
//     //     autocompleteCity();
//     // })
// })
//
// function autoCompleteCountries(element, url){
//
//     $(element).autocomplete({
//         source: function( request, response ) {
//             $.ajax({
//                 url: url,
//                 dataType: "json",
//                 type:"POST",
//                 data: {
//                     searchValue: request.term
//                 },
//                 success: function( data ) {
//                     response($.map(data.data, function (item) {
//
//                         return {
//                             label: item.name,
//                             value: item.name,
//                             id: item.id,
//
//                         }
//                     }));
//
//                 }
//             });
//         },
//         minlength:2,
//         select: function( event, ui ) {
//             $('#institutionCountryId').val(ui.item.id)
//             autocompleteState(ui.item.id);
//             $('#institutionCityId').val('')
//             $('#institutionCity').val('')
//             $('#institutionState').val('')
//             $('#institutionStateId').val('')
//         },
//         change: function(event, ui ){
//             if (!ui.item) {addAuthorModal
//                 $(this).val("");
//                 $('#institutionCountryId').val("");
//                 alert('Please select country');
//             } else {
//
//             }
//         }
//     } );
// }
//
// function autocompleteState(countryId){
//     $('#institutionState').autocomplete({
//         source: function( request, response ) {
//             $.ajax({
//                 url: base_url + '/' + event_uri + '/locations/get_country_states',
//                 dataType: "json",
//                 type:"POST",
//                 data: {
//                     searchValue: request.term,
//                     country_id: countryId
//                 },
//                 success: function( data ) {
//                     response($.map(data.data, function (item) {
//                         return {
//                             label: item.name,
//                             value: item.name,
//                             id: item.id,
//                         }
//                     }));
//                 }
//             });
//         },
//         minlength:2,
//         select: function( event, ui ) {
//             $('#institutionStateId').val(ui.item.id)
//             g_countryId = countryId;
//             g_stateId = ui.item.id;
//             autocompleteCity(countryId,ui.item.id);
//
//         },
//         change: function(event, ui ){
//             if (!ui.item) {
//                 $(this).val("");
//                 $('#institutionStateId').val('')
//                 alert('Please select state');
//             } else {
//
//             }
//         }
//     } );
// }
//
// function autocompleteCity(countryId = null,stateId = null) {
//     if (countryId == null) {
//         $('#institutionCity').autocomplete({
//             source: function (request, response) {
//                 $.ajax({
//                     url: base_url + '/locations/get_all_cities',
//                     dataType: "json",
//                     type: "POST",
//                     data: {
//                         searchValue: request.term,
//                     },
//                     success: function (data) {
//                         response($.map(data.data, function (item) {
//                             return {
//                                 id: item.id,
//                                 label: item.completeAddress,
//                                 value: item.name,
//                                 city: item.name,
//                                 state: item.state_name,
//                                 state_id : item.state_id,
//                                 country: item.country_name,
//                                 country_id: item.country_id,
//                             }
//                         }));
//                     }
//                 });
//             },
//             minlength: 4,
//             maxShowItems: 10,
//             delay: 1000,
//             select: function (event, ui) {
//                 $('#institutionCityId').val(ui.item.id)
//                 $('#institutionCity').val(ui.item.value)
//                 $('#institutionCountry').val(ui.item.country)
//                 $('#institutionCountryId').val(ui.item.country_id)
//                 $('#institutionState').val(ui.item.state)
//                 $('#institutionStateId').val(ui.item.state_id)
//
//             },
//             search: function () {
//                 $(this).data("ui-autocomplete").menu.bindings = $();
//             },
//             change: function (event, ui) {
//                 if (!ui.item) {
//                     $(this).val("");
//                     $('#institutionCityId').val('')
//                     alert('Please select city');
//                 } else {
//
//                 }
//             }
//         });
//     } else {
//         $('#institutionCity').autocomplete({
//             source: function (request, response) {
//                 $.ajax({
//                     url: base_url + '/' + event_uri + '/locations/get_state_cities',
//                     dataType: "json",
//                     type: "POST",
//                     data: {
//                         searchValue: request.term,
//                         country_id: countryId,
//                         state_id: stateId
//                     },
//                     success: function (data) {
//                         response($.map(data.data, function (item) {
//                             return {
//                                 label: item.name,
//                                 value: item.name,
//                                 id: item.id,
//                             }
//                         }));
//                     }
//                 });
//             },
//             minlength: 3,
//             select: function (event, ui) {
//                 $('#institutionCityId').val(ui.item.id)
//             },
//             change: function (event, ui) {
//                 if (!ui.item) {
//                     $(this).val("");
//                     $('#institutionCityId').val('')
//                     alert('Please select city');
//                 } else {
//
//                 }
//             }
//         });
//     }
// }
// // $(function(){
// //     let country_names = [];
// //     let country_states = [];
// //     let states_cities = [];
// //
// //     autocomplete('#authorInstitution',  base_url + '/' + event_uri + '/user/get_institution');
// //
// // // start try geo name
// //
// //
// //         // fetch('http://api.geonames.org/countryInfoJSON?username=rexter', {
// //         //     method: 'GET', // or 'PUT'
// //         //     // headers: {
// //         //     //     'Content-Type': 'application/json',
// //         //     //     Authorization: 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyIjp7InVzZXJfZW1haWwiOiJyZXh0ZXJkYXl1dGFAZ21haWwuY29tIiwiYXBpX3Rva2VuIjoiTG11bU45VndlcXA5NF9xYWpUN2lONVRicW5sV1UxdVZrY1YtdWxFTkdqMkhZYjU5RFl4LXVET3RmbjJWWUdaTjdEbyJ9LCJleHAiOjE2Njg4NzA2NjJ9.YLx_3T6bWMhK1ApsB8LXRySLg5BZfYRUxLBruvUXi20'
// //         //     // },
// //         // })
// //         //     .then((response) => response.json())
// //         //     .then((data) => {
// //         //         console.log(data);
// //         //         $.each(data.geonames, function(i, item){
// //         //             // console.log(item);
// //         //             country_states.push({
// //         //                 'label':item.countryName,
// //         //                 'value':item.countryName,
// //         //             })
// //         //         })
// //         //
// //         //         console.log(country_states);
// //         //         // console.log(country_states);
// //         //         // autocompleteLocation('#institutionProvince',  'https://www.universal-tutorial.com/api/states/'+country_states, country_states);
// //         //     })
// //         //     .catch((error) => {
// //         //         return error;
// //         //     });
// //
// //
// //
// //     // return false;
// //
// //     // ENd try geo name
// //     //
// //     // $('#institutionCountry').on('change', function(){
// //     //     // fetch('https://www.universal-tutorial.com/api/states/'+$(this).val(), {
// //     //     fetch('https://www.universal-tutorial.com/api/cities/'+$(this).val()+'&country='+$(this).attr('country_code'), {
// //     //         method: 'GET', // or 'PUT'
// //     //         // headers: {
// //     //         //     'Content-Type': 'application/json',
// //     //         //     Authorization: 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyIjp7InVzZXJfZW1haWwiOiJyZXh0ZXJkYXl1dGFAZ21haWwuY29tIiwiYXBpX3Rva2VuIjoiTG11bU45VndlcXA5NF9xYWpUN2lONVRicW5sV1UxdVZrY1YtdWxFTkdqMkhZYjU5RFl4LXVET3RmbjJWWUdaTjdEbyJ9LCJleHAiOjE2Njg4NzA2NjJ9.YLx_3T6bWMhK1ApsB8LXRySLg5BZfYRUxLBruvUXi20'
// //     //         // },
// //     //     })
// //     //         .then((response) => response.json())
// //     //         .then((data) => {
// //     //             console.log(data);
// //     //             $.each(data.geonames, function(i, item){
// //     //                 country_states.push({
// //     //                     'label':item.state_name,
// //     //                     'value':item.state_name,
// //     //                 })
// //     //             })
// //     //             // console.log(country_states);
// //     //             autocompleteLocation('#institutionProvince',  '', country_states);
// //     //         })
// //     //         .catch((error) => {
// //     //             return error;
// //     //         });
// //     // })
// //
// //     $('#institutionProvince').on('change', function(){
// //         fetch('https://www.universal-tutorial.com/api/cities/'+$(this).val()+'&country='+$(this).attr('country_code'), {
// //             method: 'GET', // or 'PUT'
// //             headers: {
// //                 'Content-Type': 'application/json',
// //                 Authorization: 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyIjp7InVzZXJfZW1haWwiOiJyZXh0ZXJkYXl1dGFAZ21haWwuY29tIiwiYXBpX3Rva2VuIjoiTG11bU45VndlcXA5NF9xYWpUN2lONVRicW5sV1UxdVZrY1YtdWxFTkdqMkhZYjU5RFl4LXVET3RmbjJWWUdaTjdEbyJ9LCJleHAiOjE2Njg4NzA2NjJ9.YLx_3T6bWMhK1ApsB8LXRySLg5BZfYRUxLBruvUXi20'
// //             },
// //         })
// //             .then((response) => response.json())
// //             .then((data) => {
// //                 console.log(data);
// //                 $.each(data.geonames, function(i, item){
// //                     states_cities.push({
// //                         'label':item.city_name,
// //                         'value':item.city_name,
// //                     })
// //                 })
// //
// //                 autocompleteLocation('#institutionCity',  'https://www.universal-tutorial.com/api/cities/'+states_cities, states_cities);
// //             })
// //             .catch((error) => {
// //                 return error;
// //             });
// //     })
// //
// //
// //
// //     fetch('https://www.universal-tutorial.com/api/countries/', {
// //     // fetch('http://api.geonames.org/countryInfoJSON?username=rexter', {
// //         method: 'GET', // or 'PUT'
// //         headers: {
// //             'Content-Type': 'application/json',
// //             Authorization: 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyIjp7InVzZXJfZW1haWwiOiJyZXh0ZXJkYXl1dGFAZ21haWwuY29tIiwiYXBpX3Rva2VuIjoiTG11bU45VndlcXA5NF9xYWpUN2lONVRicW5sV1UxdVZrY1YtdWxFTkdqMkhZYjU5RFl4LXVET3RmbjJWWUdaTjdEbyJ9LCJleHAiOjE2Njg4NzA2NjJ9.YLx_3T6bWMhK1ApsB8LXRySLg5BZfYRUxLBruvUXi20'
// //
// //         },
// //         // body: JSON.stringify(data),
// //     })
// //         .then((response) => response.json())
// //         .then((data) => {
// //             $.each(data, function(i, item){
// //                 country_names.push({
// //                     'label':item.country_name,
// //                     'value':item.country_name,
// //                     'code':item.country_short_name,
// //                 })
// //             })
// //             // console.log(country_names);
// //             autocompleteLocation('#institutionCountry',  '', country_names);
// //         })
// //         .catch((error) => {
// //             return error;
// //         });
// //
// //
// // })
// //
// //
// // function autocompleteLocation(inputId, url, source){
// //     let country_states = [];
// //     let state_cities = [];
// //     $( inputId).autocomplete({
// //         source: source,
// //         minLength: 1,
// //         maxShowItems: 5,
// //         select:function(event, ui){
// //
// //             fetch('http://api.geonames.org/searchJSON?username=rexter&country='+ui.item.code, {
// //                 method: 'GET', // or 'PUT'
// //             })
// //                 .then((response) => response.json())
// //                 .then((data) => {
// //
// //                     $.each(data.geonames, function(i, item){
// //
// //                         country_states.push({
// //                             'label':item.name,
// //                             'value':item.name,
// //                         })
// //                     })
// //
// //                     $('#institutionProvince').autocomplete({
// //                         source: country_states,
// //                         minLength: 1,
// //                         maxShowItems: 5,
// //                         select:function(event, ui){
// //                             console.log(ui.item);
// //                             fetch('http://api.geonames.org/searchJSON?username=rexter&country='+ui.item.code, {
// //                                 method: 'GET', // or 'PUT'
// //                             })
// //                                 .then((response) => response.json())
// //                                 .then((data) => {
// //                                     console.log(data);
// //                                     $.each(data.geonames, function (i, item) {
// //                                         console.log(item);
// //                                         // state_cities.push({
// //                                         //     'label': item.name,
// //                                         //     'value': item.name,
// //                                         // })
// //                                     })
// //                                 })
// //                         }
// //                     })
// //                     console.log(country_states);
// //                     // autocompleteLocation('#institutionProvince',  '', country_states);
// //                 })
// //                 .catch((error) => {
// //                     return error;
// //                 });
// //
// //         }
// //     } );
// // }
// //
// //
// // // #authorInstitution
// // // base_url + '/' + event_uri + '/user/get_institution'
// function autocomplete(inputId, url){
//
//     $( inputId, ).autocomplete({
//
//         source: function( request, response ) {
//
//             // console.log(); return false;
//             $.ajax({
//                 url:  url,
//                 dataType: "json",
//                 type: "POST",
//                 data:{
//                     name: request.term
//                 },
//                 success: function(data){
//                     if(data) {
//
//                         data.push({'name': 'Add New', 'btn': 'btnAddNewAuth'})
//                         // data=JSON.parse(data);
//                         response($.map(data, function (item) {
//                             return {
//                                 id: item.id,
//                                 value: item.name,
//                                 label: item.name,
//                                 btn: item.btn
//                             }
//                         }))
//                     }
//                 },error(){
//                     toastr.error('no result found')
//                     data =[{'name': 'Add New', 'btn': 'btnAddNewAuth'}]
//                     // data=JSON.parse(data);
//                     $('#searchId').val('')
//                     response($.map(data, function (item) {
//                         return {
//                             id: null,
//                             value: item.name,
//                             btn: item.btn
//                         }
//                     }))
//                 },
//             })
//         },
//         minLength: 0,  // assign autocomplete min length
//         select: function( event, ui ) {
//             $(this).val(ui.item.value)
//             $('#searchId').val(ui.item.id)
//             if(ui.item.btn === 'btnAddNewAuth'){
//                  $('#addInstitutionModal').modal('show');
//                 // Swal.fire({
//                 //     title: 'Info',
//                 //     text: 'Adding new author will require admin approval, if you wish to continue please click Continue',
//                 //     showCancelButton: true,
//                 //     confirmButtonText: 'Continue',
//                 // }).then((result) => {
//                 //     /* Read more about isConfirmed, isDenied below */
//                 //     if (result.isConfirmed) {
//                 //         $('#addInstitutionModal').modal('show');
//                 //     }
//                 // })
//             }
//         }
//     } );
// }
// //
