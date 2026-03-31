  <footer class="app-footer" style="font-size: 12px;">
  	<!--begin::To the end-->
  	<div class="float-end d-none d-sm-inline"><b>Version</b>&nbsp; 2.0</div>
  	<!--end::To the end-->
  	<!--begin::Copyright-->
  	<strong> Copyright &copy; 2022
  		<?php if (date('Y') > 2022) {
 				echo ' - ' . date('Y');
 			} ?>&nbsp;
  		<a href="<?php echo base_url('Home'); ?>"> SI-imut</a>.</strong>
  	Sistem Informasi Indikator Mutu, RSUD dr. Soedono Prov Jawa Timur
  	<div class="float-right d-none d-sm-inline-block">
  	</div>
  	<!--end::Copyright-->
  </footer>

  <script>
    if (performance.getEntriesByType("navigation")[0]?.type === "back_forward") {
      window.location.href = '<?= site_url('auth') ?>';
    }
  </script>