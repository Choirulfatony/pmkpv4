<!--begin::Container-->
<div class="container-fluid">
	<!--begin::Row-->
	<div class="row align-items-center">

		<div class="col-sm-6">
			<h3 class="mb-0" style="caret-color: transparent; user-select: none;">
				<?= $icon ?? '' ?>
				<?= esc($judul ?? 'Dashboard') ?>
			</h3>
		</div>

		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-end mb-0">

				<!-- Home -->
				<li class="breadcrumb-item">
					<a href="<?= site_url('dashboard') ?>">Home</a>
				</li>

				<?php
				$uri = service('uri');
				$segments = $uri->getSegments();
				$path = '';
				?>

				<?php foreach ($segments as $i => $segment): ?>
					<?php
					$path .= $segment . '/';
					$isLast = ($i === array_key_last($segments));
					$label  = ucwords(str_replace('-', ' ', $segment));
					?>

					<?php if ($isLast): ?>
						<li class="breadcrumb-item active" aria-current="page">
							<?= esc($label) ?>
						</li>
					<?php else: ?>
						<li class="breadcrumb-item">
							<a href="<?= site_url($path) ?>">
								<?= esc($label) ?>
							</a>
						</li>
					<?php endif; ?>
				<?php endforeach; ?>

				<?php if (!empty($deskripsi)): ?>
					<li class="breadcrumb-item">
						<?= esc($deskripsi) ?>
					</li>
				<?php endif; ?>

			</ol>
		</div>

	</div>
	<!--end::Row-->
</div>
<!--end::Container-->