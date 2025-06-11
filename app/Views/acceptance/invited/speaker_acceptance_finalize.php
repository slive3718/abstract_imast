<?php echo view('acceptance/common/menu'); ?>
<body>
    <div class="container" >
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
                    <div class="col-4 text-end fw-bolder">Participation Status:</div>
                    <div class="col-7">
                        <?= isset($author_acceptance) && $author_acceptance['acceptance_confirmation'] == 1 ? "I plan to present at the 129th AFS Metalcasting Congress held in Atlanta, Georgia, April 12-15, 2025. " : '' ?>
                        <?= isset($author_acceptance) && $author_acceptance['acceptance_confirmation'] == 2 ? "I am unable to participate in the 129th AFS Metalcasting Congress held in Atlanta, Georgia, April 12-15, 2025. " : '' ?>
                    </div>
                    <div class="col-1">
                        <span class="float-end"><a class="editBtn btn btn-primary py-0" href="<?=base_url() ?>/acceptance/speaker_acceptance/<?= $abstract_id ?>"><i class="fas fa-edit"></i> Edit</a></span>
                    </div>
                    <div class="col-4 text-end fw-bolder">Breakfast Attendance: </div>
                    <div class="col-7"><?= !empty($author_acceptance) && $author_acceptance['breakfast_attendance'] ? $author_acceptance['breakfast_attendance'] :''?></div>
                    <div class="col-1">
                        <span class="float-end"><a class="editBtn btn btn-primary py-0" href="<?=base_url() ?>/acceptance/breakfast_attendance/<?= $abstract_id ?>"><i class="fas fa-edit"></i> Edit</a></span>
                    </div>
                    <div class="col-4 text-end fw-bolder">Edit Biography: </div>
                    <div class="col-7"><?= isset($author_acceptance) && $author_acceptance['author_bio'] ? $author_acceptance['author_bio'] : ''?></div>
                    <div class="col-1">
                        <span class="float-end"><a class="editBtn btn btn-primary py-0" href="<?=base_url() ?>/acceptance/biography/<?= $abstract_id ?>"><i class="fas fa-edit"></i> Edit</a></span>
                    </div>
                    <div class="col-4 text-end fw-bolder">Presentation Upload: </div>
                    <div class="col-7 presentationUploaded">
                        <a href="<?= base_url().$author_acceptance['presentation_file_path'].'/'.$author_acceptance['presentation_saved_name']?>">
                            <?= $author_acceptance['presentation_saved_name']?>
                        </a>
                    </div>
                    <div class="col-1">
                        <span class="float-end"><a class="editBtn btn btn-primary py-0" href="<?=base_url() ?>/acceptance/presentation_upload/<?= $abstract_id ?>"><i class="fas fa-edit"></i> Edit</a></span>
                    </div>
                </div>
            </div>
            <div class="mt-3 mb-2 me-3">
                <button class="btn btn-success finalizeBtn float-end">FINALIZE ACCEPTANCE</button>
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
                if (data.status === 'success') {
                    swal.fire({
                        title: "Acceptance Submitted!",
                        icon: "success",
                        html: data.msg,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = baseUrlAcceptance + "/abstract_list";
                        }
                    });
                } else {
                    swal.fire({
                        title: "Success",
                        icon: "success",
                        html: data.msg,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = baseUrlAcceptance + "/abstract_list";
                        }
                    });
                }
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