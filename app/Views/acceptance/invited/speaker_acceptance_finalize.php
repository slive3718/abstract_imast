<?php echo view('acceptance/common/menu'); ?>
<body>
<div class="container" >
    <?= view('acceptance/common/invited_menu_shortcut'); ?>
    <?=$presentation_data_view ?? ''?>
    <?php if (1 == 2) : ?>
        <div class="card mt-2">
            <div class="card-header bg-primary text-white p-3">Author Information</div>
            <div class="card-body" style="line-height: 30px">
                <div class="row">
                    <div class="col-4 text-end">
                        <label class="fw-bolder">Author List: </label>
                    </div>
                    <div class="col-8">
                        <?php if(isset($authors) && !empty($authors)):
                            foreach($authors as $index => $author):
                                if($author['is_presenting_author'] === 'Yes'): ?>
                                    <span class='fw-bolder'><?= $index+1 ?>. Presenting-Author: </span><?=$author['info']['name'] . ' ' . $author['info']['surname']?><br>
                                <?php else: ?>
                                    <span class='fw-bolder'><?= $index+1 ?>. Co-Author: </span><?=$author['info']['name'] . ' ' . $author['info']['surname']?><br>
                                <?php endif; endforeach; endif; ?>
                    </div>
                </div>

                <?php if (1 == 2) : ?>
                    <div class="row mt-1">
                        <?php if (isset($authors) && !empty($authors)): ?>
                            <?php foreach ($authors as $index => $author): ?>
                                <?php if ($author['is_presenting_author'] == "No"): ?>
                                    <div class="col-12 mb-4">
                                        <div class="row">
                                            <!-- Co-Author Label -->
                                            <div class="col-md-4 col-sm-12 text-md-end text-start">
                                                <label class="fw-bold">(<?= $index + 1 ?>) Co-Author:</label>
                                            </div>
                                            <div class="col-md-8 col-sm-12 fw-bolder" style="color: #2aa69c">
                                                <p class="mb-1"> <?= ucFirst($author['info']['name']) . ' ' . ucFirst($author['info']['surname']) ?> </p>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4 col-sm-12 text-md-end text-start">
                                                <label class="fw-bold">Information:</label>
                                            </div>
                                            <div class="col-md-8 col-sm-12">
                                                <p class="mb-1"> <strong>Address:</strong> <?= $author['profile']['address'] ?> </p>
                                                <p class="mb-1"> <strong>City:</strong> <?= $author['profile']['city'] ?> </p>
                                                <p class="mb-1"> <strong>Country:</strong> <?= $author['profile']['country'] ?> </p>
                                                <p class="mb-1"> <strong>Work Phone:</strong> <?= $author['profile']['phone'] ?> </p>
                                                <p class="mb-1"> <strong>Email:</strong> <?= $author['info']['email'] ?> </p>
                                                <p class="mb-1"> <strong>Institution:</strong> <?= $author['profile']['institution'] ?> </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif ?>
    <div class="card mt-2">
        <div class="card-header bg-primary text-white p-3">Acceptance Information</div>
        <div class="card-body" style="line-height: 30px">
            <div class="row">
                <div class="col-4 text-end fw-bolder">Participation Status :</div>
                <div class="col-7">
                    <?= isset($author_acceptance) && $author_acceptance['acceptance_confirmation'] == 1 ? "I plan to present at the 33rd IMAST held in Toronto, ON, Canada, April 15-18, 2026." : '' ?>
                    <?= isset($author_acceptance) && $author_acceptance['acceptance_confirmation'] == 2 ? "I am unable to participate in the 129th AFS Metalcasting Congress held in Atlanta, Georgia, April 12-15, 2025. " : '' ?>
                </div>
                <div class="col-1">
                    <span class="float-end"><a class="editBtn btn btn-primary py-0" href="<?=base_url() ?>/acceptance/speaker_acceptance/<?= $abstract_id ?>"><i class="fas fa-edit"></i> Edit</a></span>
                </div>

                <?php if(isset($author_acceptance) && $author_acceptance['acceptance_confirmation'] == 1 ): ?>
                <div class="col-4 text-end fw-bolder">Travel and Expenses : </div>
                <div class="col-7"><?= !empty($author_acceptance) && $author_acceptance['travel_expenses'] == 'yes' ? 'I understand the travel and expenses terms.' : ''?></div>
                <div class="col-1">
                    <span class="float-end"><a class="editBtn btn btn-primary py-0" href="<?=base_url() ?>/acceptance/invited_speaker_travel_expense/<?= $abstract_id ?>"><i class="fas fa-edit"></i> Edit</a></span>
                </div>

                <div class="col-4 text-end fw-bolder">Innovation Celebration : </div>
                <div class="col-7"><?= !empty($author_acceptance) && $author_acceptance['celebration_attendance'] == '1' ? 'Yes, I plan to attend the Innovation Celebration. Please register me for this event.' : ' No, I do NOT plan to attend the Innovation Celebration. Please do not register me for this event.'?></div>
                <div class="col-1">
                    <span class="float-end"><a class="editBtn btn btn-primary py-0" href="<?=base_url() ?>/acceptance/invited_celebration/<?= $abstract_id ?>"><i class="fas fa-edit"></i> Edit</a></span>
                </div>
                <?php endif; ?>
<!--                <div class="col-4 text-end fw-bolder">Presentation Upload: </div>-->
<!--                <div class="col-7 presentationUploaded">-->
<!--                    <a href="--><?php //= base_url().$author_acceptance['presentation_file_path'].'/'.$author_acceptance['presentation_saved_name']?><!--">-->
<!--                        --><?php //= $author_acceptance['presentation_saved_name']?>
<!--                    </a>-->
<!--                </div>-->

            </div>
        </div>
        <div class="mt-3 mb-2 me-3">
            <button class="btn btn-success finalizeBtn float-end">FINALIZE</button>
        </div>
    </div>
</div>
</body>

<script>
    let baseUrlAcceptance = "<?= base_url() ?>/acceptance/";

    $(function(){

        function check_finalize() {
            $.post(baseUrlAcceptance + 'check_finalize_acceptance/'+abstract_id, function(data) {
                Swal.close();
                // let status = (data.status === 'success') ? 'success' : 'warning';
                swal.fire({
                    title: '',
                    icon: data.status,
                    html: data.message,
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = baseUrlAcceptance + "abstract_list";
                    }
                });
            });
        }

        $('.finalizeBtn').on('click', function() {
            Swal.fire({
                title: "Are you sure?",
                text: "",
                icon: "info",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Finalize it."
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait...',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                            check_finalize();
                        }
                    });
                }
            });
        });
    });
</script>