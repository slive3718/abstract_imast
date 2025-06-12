
<?= view('acceptance/common/menu'); ?>
<body>
<div class="container">
    <?= view('acceptance/common/moderator_menu_shortcut'); ?>
    <?=$presentation_data_view ?? ''?>
    <div class="card mt-2">
        <div class="card-header bg-primary text-white p-3">
            Session Details
        </div>
        <div class="card-body">
            <p class="">
                Here are your session details:
            </p>
            <div id="sessionDetailsDiv">

                <?php if(!empty($scheduler_event)) :?>
                    <?php if(!empty($scheduler_event['talks'])): ?>
                        <?php foreach ($scheduler_event['talks'] as $talk): if(!empty($talk['info'])): ?>
                            <div class="row my-5">
                                <?php
                                $talkStart = date('h:i a', strtotime($talk['time_start']));
                                $talkEnd = date('h:i a', strtotime($talk['time_end']));
                                ?>
                                <div class="col-3"><?=$talkStart.' - '.$talkEnd?></div>
                                <div class="col-9">
                                    <ul style="list-style:none">
                                        <li><?=$talk['info']['name']. ' ' .$talk['info']['surname']?></li>
                                        <li><?=strip_tags($talk['title'])?></li>
                                        <li><a href="<?=$talk['info']['presentation_file_path'].'/'.$talk['info']['presentation_saved_name']?>"><?=$talk['info']['presentation_saved_name']?></a></li>
                                    </ul>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="row my-5">
                                <?php
                                $talkStart = date('h:i a', strtotime($talk['time_start']));
                                $talkEnd = date('h:i a', strtotime($talk['time_end']));
                                ?>
                                <div class="col-3"><?=$talkStart.' - '.$talkEnd?></div>
                                <div class="col-9">
                                    <ul style="list-style:none">
                                        <li><?=$talk['custom_abstract_desc']?></li>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php endforeach ?>
                    <?php endif; ?>
                <?php endif; ?>
                <form id="session_details_form">
                    <div class="form-check">
                        <input type="checkbox" value="1" name="confirm_previewed" id="confirm_previewed" <?= (!empty($acceptanceDetails) && $acceptanceDetails['is_session_previewed'] == 1 ? 'checked' : '')?>>
                        <label for="confirm_previewed"><span class="text-danger">*</span> I confirm that I have previewed my session.</label>
                    </div>

                    <div class="my-4">
                        <button type="submit" class="btn btn-success continueBtn">Save and Continue</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>

<script>
    let acceptanceBaseUrl = `<?=base_url().'acceptance/moderator/'?>`
    $(function() {
        $('button[type="submit"]').on('click', function(e) {

            e.preventDefault();

            let confirmPreviewedStatus = $('input[name="confirm_previewed"]:checked').val();
            if (!confirmPreviewedStatus) {
                toastr.error('Please answer required question.');
                return false;
            }

            const formData = new FormData(document.getElementById('session_details_form'));
            formData.append('scheduler_id', scheduler_id)
            $.ajax({
                url: acceptanceBaseUrl + 'update', // Your server-side endpoint
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if(response.status === 'success') {
                        goNext(scheduler_id)
                    }
                },
                error: function(xhr, status, error) {
                    $('#response').html('<p>Error: ' + error + '</p>');
                }
            });
        });
    });

    function goNext(abstract_id){
        window.location.href = acceptanceBaseUrl+'finalize/' + scheduler_id
    }

</script>

