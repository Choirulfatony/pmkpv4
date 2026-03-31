<!-- _menu_item.php - Render menu recursive -->
<?php if (!empty($menus)): ?>
    <?php foreach ($menus as $menu): ?>
        <?php if (empty($menu['children'])): ?>
            <li class="nav-item">
                <a href="<?= $menu['url'] == '#' ? 'javascript:void(0)' : site_url($menu['url']) ?>" class="nav-link">
                    <i class="nav-icon <?= $menu['icon'] ?>"></i>
                    <p><?= $menu['nama_menu'] ?></p>
                </a>
            </li>
        <?php else: ?>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon <?= $menu['icon'] ?>"></i>
                    <p>
                        <?= $menu['nama_menu'] ?>
                        <i class="bi bi-arrow-left-short"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <?= view('_layout/_menu_item', ['menus' => $menu['children']]) ?>
                </ul>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>
