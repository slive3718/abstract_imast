

<?php echo view('acceptance/common/menu'); ?>

<main>
    <div class="container-fluid">
        <div class="card shadow abstractDiv">
            <div class="card-header text-white"  style="background-color: #2AA69C">
                My Meeting Activity
            </div>
            <div class="card-body">
                <table id="abstractTable" class="table table-striped">
                    <thead>
                    <th>ID</th>
<!--                    <th>Custom ID</th>-->
                    <th>Title</th>
                    <th>Accepted for</th>
                    <th>Submission type</th>
                    <th>Room</th>
                    <th>Presentation Date</th>
                    <th>Presentation Time</th>
                    <th></th>
                    </thead>
                    <tbody id="abstractTableBody">

                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow mt-4 moderatorDiv">
            <div class="card-header text-white" style="background-color: #2AA69C">
                Session Chair Roles
            </div>
            <div class="card-body">
                <!--        if there are moderator access and acceptance -->
                <table id="moderatorTable" class="table table-striped">
                    <thead>
                    <th>Title</th>
                    <th>Room</th>
                    <th>Presentation Date</th>
                    <th>Presentation Time</th>
                    </thead>
                    <tbody id="moderatorTableBody">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>


<script>
    let baseUrlAcceptance = "<?=base_url().'acceptance/'?>";
    $(function(){
        $('.moderatorDiv').hide();
        getAbstracts();
        getModeratorAcceptance();
        $("#abstractTableBody").on('click', '.openBtn', function(){
            let abstract_id = $(this).attr('abstract_id')
            viewAcceptance(abstract_id);
        })

        $('#moderatorTableBody').on('click', '.openModBtn', function(){
            let event_id = $(this).data('schedule-id')
            viewModeratorAcceptance(event_id)
        })
    })

    function getAbstracts(){
        $.post(baseUrlAcceptance+'get_accepted_abstracts', function(response){
            $('#abstractTableBody').html('');
            $.each(response.data, function(i, val){
                console.log(val)
                let buttonText = 'Open';
                if(val.author_acceptance && val.author_acceptance.acceptance_confirmation == 1){
                    buttonText = 'Confirmed';
                }else if(val.author_acceptance && val.author_acceptance.acceptance_confirmation == 2){
                    buttonText = 'Declined';
                }

                let openBtn  = `<button class="btn btn-success btn-sm openBtn text-right float-end" abstract_id='${val.paper_data.id}' style="width:200px" > ${buttonText}</button>`
                let adminPresentationPref = '';
                let adminAcceptance = '';

                if (val.acceptance_data.acceptance_confirmation == 1) {
                    const acceptanceMap = {
                        1: "Accepted",
                        2: "Rejected",
                        3: "Suggested Revision",
                        4: "Required Revision",
                        5: "Declined/Withdrawn for Participation"
                    };

                    const presentationMap = {
                        1: "Presentation Only",
                        2: "Publication Only",
                        3: "Presentation and Publication"
                    };
                    adminAcceptance = acceptanceMap[val.acceptance_data.acceptance_confirmation] || "Unknown Status";
                    if (val.acceptance_data.acceptance_confirmation == 1) {
                        adminPresentationPref = presentationMap[val.acceptance_data.presentation_preference] || "No Preference";
                    }
                }


                if(val.acceptance_data.acceptance_confirmation == 1 && val.acceptance_data.presentation_preference !== '2') {
                    const submissionTypes = {
                        paper: "Paper Presentation",
                        panel: "Panel Presentation"
                    };

                    const submissionTypesId = {
                        paper: val.paper_data.custom_id,
                        panel: val.acceptance_data.custom_id,
                    };



                    const presentationType = submissionTypes[val.paper_data.submission_type] || "";
                    const customId = submissionTypesId[val.paper_data.submission_type] || "";
                    //
                    // const presentationStartTime = val.schedule ?  val.schedule.session_start_time : ''
                    // const presentationEndTime =  val.schedule ? val.schedule.session_end_time : ''

                    const presentationStartTime =  (val.schedule ? new Date(val.schedule.session_start_time).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true }) : '')
                    const presentationEndTime =  (val.schedule ? new Date(val.schedule.session_end_time).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true }) : '')

                    $('#abstractTableBody').append('<tr>' +
                        // '<td> Paper: #' + val.paper_data.id + '</td>' +
                        '<td>' +customId + '</td>' +
                        '<td>' + val.paper_data.title + '</td>' +
                        '<td>' + adminPresentationPref + '</td>' +
                        '<td>' + presentationType + '</td>' +
                        '<td>' + (val.room && val.room.name ? val.room.name : '') + '</td>' +
                        '<td>' + (val.schedule ? new Date(val.schedule.session_date).toISOString().split('T')[0] : '') + '</td>' +
                        '<td>' + ( presentationStartTime ? presentationStartTime +' - '+ presentationEndTime : '') + '</td>' +
                        '<td>' + openBtn + '</td>' +
                        '</tr>')
                }
            })
        },'json')   
    }

    function getModeratorAcceptance(){
        $.get(baseUrlAcceptance+'moderator/schedules', function(response){
            if(response.data) {
                $('.moderatorDiv').show();
                $('#moderatorTableBody').html();
                $.each(response.data, function (i, val) {
                    let openBtn = '<button class="btn btn-success btn-sm openModBtn text-right float-end" data-schedule-id="'+val.id+'" style="width:200px"> Open </button>'
                    $('#moderatorTableBody').append('<tr>' +
                        '<td> ' + val.session_title + ' </td>' +
                        '<td> ' + val.room_name + ' </td>' +
                        '<td> ' + (new Date(val.session_date).toISOString().split('T')[0])  + ' </td>' +
                        '<td> ' + val.session_start_time+' - '+val.session_end_time + ' </td>' +
                        '<td>' + openBtn + '</td>' +
                        '</tr>')
                })
            }
        })
    }

    function viewAcceptance(abstract_id){
        $.post(baseUrlAcceptance+'getAuthorAcceptance/'+abstract_id, function(response){
            if(response.length == 1){
                 window.location.href= baseUrlAcceptance+"acceptance_menu/"+abstract_id;
            }else{
                 window.location.href= baseUrlAcceptance+"speaker_acceptance/"+abstract_id;
            }
        })
    }

    function viewModeratorAcceptance(id){
        $.get(baseUrlAcceptance+'moderator/acceptance_data/'+id, function(response){
            if(response.length > 0){
                window.location.href= baseUrlAcceptance+"moderator/acceptance_menu/"+id;
            }else{
                window.location.href= baseUrlAcceptance+"moderator/acceptance/"+id;
            }
        })
    }
</script>