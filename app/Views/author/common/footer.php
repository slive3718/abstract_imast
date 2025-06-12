<footer class="fixed-bottom">
    <div class="container text-center">
        <small class="position-absolute text-muted" style="left: 10px; bottom: 15px;">V.0.03</small>

        <span class="text-muted">Abstract Suite <?= date("Y") ?> &copy; <a class="text-decoration-none" href="https://owpm.com/" target="_blank">One World</a></span>
    </div>
</footer>

<link href="<?=base_url()?>/assets/css/footer.css" rel="stylesheet">

</body>
</html>

<script>
    let base_url = "<?=base_url()?>";
    let event_uri = "<?=session('event_uri')?>"
    let abstract_id ="<?=isset($abstract_id) ?$abstract_id:''?>"
</script>