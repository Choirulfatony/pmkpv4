-- =====================================================
-- MENU TRIAS MUTU
-- Jalankan SQL ini untuk menambahkan menu Trias Mutu
-- =====================================================

-- 1. Parent menu "Trias Mutu"
INSERT INTO siimut_menus (nama_menu, url, icon, parent_id, role_access, urutan)
VALUES ('Trias Mutu', '#', 'bi bi-file-earmark-text', NULL, 'ADMINISTRATOR,KENDALI_MUTU', 7);

-- Dapatkan ID menu yang baru saja dibuat
SET @parent_id = LAST_INSERT_ID();

-- 2. Submenu: Pengukuran Indikator
INSERT INTO siimut_menus (nama_menu, url, icon, parent_id, role_access, urutan)
VALUES ('Pengukuran Indikator', 'siimut/trias-mutu/pengukuran', 'bi bi-bar-chart', @parent_id, 'ADMINISTRATOR,KENDALI_MUTU', 1);

-- 3. Submenu: Analisis Penyebab
INSERT INTO siimut_menus (nama_menu, url, icon, parent_id, role_access, urutan)
VALUES ('Analisis Penyebab', 'siimut/trias-mutu/analisis-penyebab', 'bi bi-diagram-3', @parent_id, 'ADMINISTRATOR,KENDALI_MUTU', 2);

-- 4. Submenu: PDSA
INSERT INTO siimut_menus (nama_menu, url, icon, parent_id, role_access, urutan)
VALUES ('PDSA', 'siimut/trias-mutu/pdsa', 'bi bi-arrow-repeat', @parent_id, 'ADMINISTRATOR,KENDALI_MUTU', 3);

-- 5. Submenu: Cetak Trias Mutu
INSERT INTO siimut_menus (nama_menu, url, icon, parent_id, role_access, urutan)
VALUES ('Cetak Trias Mutu', 'siimut/trias-mutu/cetak', 'bi bi-printer', @parent_id, 'ADMINISTRATOR,KENDALI_MUTU', 4);
