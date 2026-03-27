<div id="ikprs-wrapper">
    <div class="text-center p-3">Loading...</div>
</div>

<script>
    $("#ikprs-wrapper").load("<?= site_url('ikprs/data-login') ?>", function() {
        $(document).trigger("ikprs:ready");
    });
</script>