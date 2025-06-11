<footer class="fixed-bottom">
    <div class="container text-center">
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
</body>
</html>

<script>
    let base_url = "<?=base_url()?>";
    let event_uri = "<?=session('event_uri')?>"
    let abstract_id ="<?=isset($abstract_id) ?$abstract_id:''?>"
</script>