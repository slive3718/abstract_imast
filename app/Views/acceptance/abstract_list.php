

<?php echo view('acceptance/common/menu'); ?>
<style>
    table{
        overflow-x:     auto;
    }
</style>
<main>
    <div class="container-fluid">
        <div class="card shadow abstractDiv">
            <div class="card-header text-white"  style="background-color: #2AA69C">
                Presenter Roles
            </div>
            <div class="card-body table-responsive">
                <table id="abstractTable" class="table table-striped ">
                    <thead>
                    <tr>
                        <th class="col-1">ID</th>
                        <th class="col-3">Title</th>
                        <th class="col-1">Accepted for</th>
                   <!--     <th class="col-1">Room</th>-->
                        <th class="col-1">Session Date</th>
                        <th class="col-1">Session Time</th>
                        <th class="col-2">Due Date</th>
                        <th class="col-1">Participation Status</th>
                        <th class="col-1">Option</th>
                    </tr>
                    </thead>
                    <tbody id="abstractTableBody" style="overflow: auto">

                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow mt-4 moderatorDiv">
            <div class="card-header text-white" style="background-color: #2AA69C">
                Moderator Roles
            </div>
            <div class="card-body">
                <!--        if there are moderator access and acceptance -->
                <table id="moderatorTable" class="table table-striped">
                    <thead>
                    <tr>
                        <th class="col-10">Title</th>
                        <th class="col-1">Status</th>
                        <th class="col-1"></th>
                    </tr>
                    </thead>
                    <tbody id="moderatorTableBody">

                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow mt-4 presenterFormsDiv">
            <div class="card-header text-white" style="background-color: #2AA69C">
                Presenter Forms
            </div>
            <div class="card-body">
                <!--        if there are moderator access and acceptance -->
                <table id="presenterFormsTable" class="table ">
                    <thead>
                        <tr>
                            <th class="col-8"></th>
                            <th class="col-2">Due Date</th>
                            <th class="col-1">Status</th>
                            <th class="col-1"></th>
                        </tr>
                    </thead>
                    <tbody id="presenterFormsTableBody">
                    <tr>
                        <?php $frdCurrent = "2025/06/30" ?>
                        <td> Financial Relationship Disclosure</td>
                        <td> <?= date('F d, Y',strtotime($frdCurrent)) ?></td>
                        <td> <?= ( !empty($user_data) && !empty($user_data['signature_signed_date']) ? strtotime($user_data['signature_signed_date']) >= strtotime($frdCurrent)
                                ? '<span class="badge bg-success text-white">Current '.date('m-d-Y',strtotime($user_data['signature_signed_date'])).' </span>'
                                : '<span class="badge bg-warning text-dark"> Outdated </span>'
                                : '<span class="badge bg-danger text-white">Incomplete</span>') ?></td>
                        <td class="text-end"> <a href="<?=base_url().'author/financial_relationship_disclosure/'?>" target="_blank" class="btn btn-success btn-sm w-100"> Open </a></td>
                    </tr>
                   <!-- <tr>
                        <td> Attestation </td>
                        <td> June 30, 2025</td>
                        <td> <?php /*= ($user_data['attestation_date'] ? strtotime($user_data['attestation_date']) > strtotime($disclosure_current['value']) ? 'Current' : 'Outdated' : 'Incomplete') */?></td>
                        <td class="text-end"><a href="<?php /*= base_url()*/?>/acceptance/attestation" target="_blank" class="btn btn-success btn-sm w-100"> Open </a></td>
                    </tr>-->
                    <tr>
                        <?php $NELCurrent = "2025/05/01" ?>
                        <td> Non-Exclusive License</td>
                        <td> <?= $NELCurrent = date('F d, Y',strtotime($NELCurrent)) ?></td>
                        <td> <?= ( !empty($user_data) && !empty($user_data['non_exclusive_license_date']) ? strtotime($user_data['non_exclusive_license_date']) > strtotime($NELCurrent)
                                ? '<span class="badge bg-success text-white">Current  '.date('m-d-Y', strtotime($user_data['non_exclusive_license_date'])).'</span>'
                                : '<span class="badge bg-warning text-dark"> Outdated '.date('m-d-Y', strtotime($user_data['non_exclusive_license_date'])).'</span>'
                                : '<span class="badge bg-danger text-white">Incomplete</span>') ?></td>
                        <td class="text-end"><a href="<?= base_url()?>/acceptance/non_exclusive_license" target="_blank" class="btn btn-success btn-sm w-100"> Open </a></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow mt-4 resourceDiv">
            <div class="card-header text-white" style="background-color: #2AA69C">
               Resources
            </div>
            <div class="card-body">
                <!--        if there are moderator access and acceptance -->
                <table id="resourcesTableBody" class="table ">
                    <tr>
                        <th class="col-9"></th>
                        <th class="col-2"></th>
                        <th class="col-1"></th>
                    </tr>
                    <tbody id="resourcesTableBody">
                        <tr>
                            <td>Meeting Website: Housing, Registration, Program and AV Guidelines </td>
                            <td></td>
                            <td><a href="https://www.srs.org/Meetings-Conferences/Regional-Scientific-Meeting/RSM-2026" target="_blank" class="btn btn-success btn-sm w-100"> Open </a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>


<script>
    let paper_types = `<?=json_encode($paper_types) ?? []?>`;
    let baseUrlAcceptance = "<?=base_url().'acceptance/'?>";
    $(function(){
        $('.moderatorDiv').hide();
        getAbstracts();
        getModeratorAcceptance();
        $("#abstractTableBody").on('click', '.openBtn', function(){
            let abstract_id = $(this).attr('abstract_id')
            let acceptance_type = $(this).data('accepted-for')

            if ([3, 4].includes(parseInt(acceptance_type))) {
                viewAcceptanceInvited(abstract_id);
            }else {
                viewAcceptance(abstract_id);
            }
        })

        $('#moderatorTableBody').on('click', '.openModBtn', function(){
            let event_id = $(this).data('schedule-id')
            viewModeratorAcceptance(event_id)
        })
    })

    function getAbstracts(){
        $.post(baseUrlAcceptance+'get_accepted_abstracts', function(response){
            $('#abstractTableBody').html('');

            let abstractDiv = $('.abstractDiv');
            if(response.data.length === 0)
                abstractDiv.hide()
            else
                abstractDiv.show()



            $.each(response.data, function(i, val){
                let openBtn  = `<button class="btn btn-success btn-sm openBtn text-right float-end w-100" abstract_id='${val.paper_data.id}' data-accepted-for="${val.admin_acceptance_data.presentation_preference}"> Open </button>`
                let adminPresentationPref = '';
                let adminAcceptance = '';

                if (val.admin_acceptance_data.acceptance_confirmation == 1) {
                    const acceptanceMap = {
                        1: "Accepted",
                        2: "Rejected",
                        3: "Suggested Revision",
                        4: "Required Revision",
                        5: "Declined/Withdrawn for Participation"
                    };

                    const presentationMap = {};
                    $.map(JSON.parse(paper_types), function(paper_type) {
                        presentationMap[paper_type.id] =  paper_type.acronym
                    });

                    adminAcceptance = acceptanceMap[val.admin_acceptance_data.acceptance_confirmation] || "Unknown Status";
                    if (val.admin_acceptance_data.acceptance_confirmation == 1) {
                        adminPresentationPref = presentationMap[val.admin_acceptance_data.presentation_preference] || "No Preference";
                    }
                }


                if(val.admin_acceptance_data.acceptance_confirmation == 1 && val.admin_acceptance_data.presentation_preference !== '2') {
                    const submissionTypes = {
                        paper: "Paper Presentation",
                        panel: "Panel Presentation"
                    };

                    const submissionTypesId = {
                        paper: val.paper_data.custom_id,
                        panel: val.admin_acceptance_data.custom_id,
                    };



                    const presentationType = submissionTypes[val.paper_data.submission_type] || "";
                    const customId = submissionTypesId[val.paper_data.submission_type] || "";
                    //
                    // const presentationStartTime = val.schedule ?  val.schedule.session_start_time : ''
                    // const presentationEndTime =  val.schedule ? val.schedule.session_end_time : ''

                    const presentationStartTime =  (val.schedule ? new Date(val.schedule.session_start_time).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true }) : '')
                    const presentationEndTime =  (val.schedule ? new Date(val.schedule.session_end_time).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true }) : '')

                    $('#abstractTableBody').append('<tr>' +
                        '<td>' + customId + '</td>' +
                        '<td>' + val.paper_data.title + '</td>' +
                        '<td>' + adminPresentationPref + '</td>' +
                        // '<td>' + (val.room && val.room.name ? val.room.name : '') + '</td>' +
                        '<td>' + (val.schedule ? new Date(val.schedule.session_date).toISOString    ().split('T')[0] : '') + '</td>' +
                        '<td>' + ( presentationStartTime ? presentationStartTime +' - '+ presentationEndTime : '') + '</td>' +
                        '<td>  June 30, 2025 </td>' +
                        '<td>'+(val.author_acceptance_data ? parseInt(val.author_acceptance_data.acceptance_confirmation) == 1 ?
                            "<span class='badge bg-success text-white'>Able to participate</span>"
                            : "<span class='badge bg-warning text-dark'> Unable to Participate </span>"
                            : "<span class='badge bg-danger text-white'> Incomplete </span>")+
                        '</td>' +
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
                    let openBtn = `<button class="btn btn-success btn-sm openModBtn text-right float-end w-100" data-schedule-id="${+val.id}"> Open </button>`
                    let acceptanceStatus = `<span class='badge bg-danger text-white'>Incomplete</span>`


                    if((val.acceptance) && Object.keys(val.acceptance).length > 0){
                        if(val.acceptance.is_finalized === '1') {
                            acceptanceStatus = `<span class='badge bg-success text-white'>Complete</span>`
                        }
                    }

                    $('#moderatorTableBody').append('<tr>' +
                        '<td> ' + val.session_title + ' </td>' +
                        '<td>' + acceptanceStatus + '</td>' +
                        '<td>' + openBtn + '</td>' +
                        '</tr>')
                })
            }
        })
    }

    function viewAcceptance(abstract_id) {
        $.post(baseUrlAcceptance + 'getAuthorAcceptance/' + abstract_id, function (response) {
            if (response.length == 1) {
                window.location.href = baseUrlAcceptance + "acceptance_menu/" + abstract_id;
            } else {
                window.location.href = baseUrlAcceptance + "speaker_acceptance/" + abstract_id;
            }
        })
    }


    function viewAcceptanceInvited(abstract_id){
        $.post(baseUrlAcceptance+'getAuthorAcceptance/'+abstract_id, function(response){
            window.location.href= baseUrlAcceptance+"invited_speaker_acceptance/"+abstract_id;
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