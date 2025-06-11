
<?php echo view('deputy/common/menu'); ?>
<main style="">
    <div class="container-fluid">
        <div class="card shadow" style="height:100%">
            <div class="card-body">
                <br>
                <strong>Welcome  <?=Ucfirst(session('name')). ' ' . Ucfirst(session('surname'))?>,</strong>
                <i>
                    Please choose to review either papers or panels. You may switch between the two submission types by clicking on the Program Chair Options and choosing
                    the type you want to review. More instructions are included on the next page.</i>
                <br><br>

                <div class="buttonContainer text-center mt-5">
                    <a href="<?=base_url('deputy/papers_list')?>" class="btn btn-primary shadow-lg" style="padding:30px; width:400px; margin:auto" > <h2>Papers</h2> </a><br><br>
                    <a href="<?=base_url('deputy/panels_list')?>" class="btn btn-info shadow-lg" style="padding:30px; width:400px; margin:auto" >  <h2>Panels</h2>  </a>
                </div>
            </div>
        </div>
    </div>
</main>


