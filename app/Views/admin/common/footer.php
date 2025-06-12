<footer class="fixed-bottom">
    <div class=" text-center">
        <small class="position-absolute text-muted" style="left: 10px; bottom: 15px;">V.0.03</small>

        <span class="text-muted">Abstract Suite <?= date("Y") ?> &copy; <a class="text-decoration-none" href="https://owpm.com/" target="_blank">One World</a></span>

        <!-- <a class="text-decoration-none" href="https://owpm.com/" target="_blank">
            <img class="position-absolute d-none d-sm-block" src="https://owpm.com/img/logo.png" alt="One World Logo" style="width: 120px; right: 0; bottom: 15px;">
        </a> -->
    </div>
</footer>

<link href="<?=base_url()?>/assets/css/footer.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.css" />
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>

 <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css">
<!-- DataTables Buttons -->
  <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script  type="text/javascript" src="<?=base_url('assets/js/admin/submissionFunction.js?v=1')?>"></script>
</body>
</html>

<script>
    let base_url = "<?=base_url()?>";
    let event_uri = "<?=session('event_uri')?>"
    let abstract_id ="<?=isset($abstract_id) ?$abstract_id:''?>"
    let paper_id = "<?=isset($paper_id)?$paper_id:''?>"
    let notificationStatus = "<?= (session()->getFlashData('status')) ?>";
    let notificationMessage = "<?= (session()->getFlashData('notification')) ?>";
</script>