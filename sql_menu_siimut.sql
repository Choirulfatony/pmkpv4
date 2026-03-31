-- Data menu SIIMUT
INSERT INTO siimut_menus (nama_menu, url, icon, parent_id, role_access, urutan) VALUES
('Dashboard', '#', 'bi bi-speedometer2', NULL, 'APP', 1),
('Beranda', 'dashboard', 'bi bi-circle', 1, 'APP', 1),
('Profil', 'dashboard', 'bi bi-person', 1, 'APP', 2),
('Settings', 'dashboard', 'bi bi-gear', 1, 'APP', 3);
