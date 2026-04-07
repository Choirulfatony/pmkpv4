-- INSTRUCTIONS FOR UPDATING MENU STRUCTURE
-- Based on your request:
-- Rekap Laporan INM
--    ├── Per Bulan
--    └── Per Periode
-- Plus a separate menu: Rekap Periode INM

-- STEP 1: Backup current menu (optional but recommended)
-- CREATE TABLE siimut_menus_backup AS SELECT * FROM siimut_menus;

-- STEP 2: Update existing Forms Rekap menu to become "Rekap Laporan INM" parent
UPDATE siimut_menus 
SET nama_menu = 'Rekap Laporan INM',
    url = 'siimut/rekap-laporan-inm',
    icon = 'bi bi-bar-chart'
WHERE id_menu = 4;

-- STEP 3: Add child menu items under Rekap Laporan INM (id_menu = 4)
-- Per Bulan (points to index method - same URL as parent but without additional path)
INSERT INTO siimut_menus (nama_menu, url, icon, parent_id, role_access, urutan) 
VALUES ('Per Bulan', 'siimut/rekap-laporan-inm', 'bi bi-file-earmark', 4, 'ADMINISTRATOR, KOMITE, KENDALI_MUTU', 1);

-- Per Periode (points to rekapPeriode method)
INSERT INTO siimut_menus (nama_menu, url, icon, parent_id, role_access, urutan) 
VALUES ('Per Periode', 'siimut/rekap-laporan-inm/rekap-periode', 'bi bi-calendar-range', 4, 'ADMINISTRATOR, KOMITE, KENDALI_MUTU', 2);

-- STEP 4: Add separate top-level menu for Rekap Periode INM
-- Find next available urutan (let's use 99 for now, adjust as needed)
INSERT INTO siimut_menus (nama_menu, url, icon, parent_id, role_access, urutan) 
VALUES ('Rekap Periode INM', 'siimut/rekap-laporan-inm/rekap-periode', 'bi bi-calendar-range', NULL, 'ADMINISTRATOR, KOMITE, KENDALI_MUTU', 99);

-- STEP 5: Verify the changes
SELECT * FROM siimut_menus 
WHERE nama_menu IN ('Rekap Laporan INM', 'Rekap Periode INM', 'Per Bulan', 'Per Periode') 
   OR url LIKE '%rekap%' 
ORDER BY urutan, parent_id;

-- STEP 6: Clean up temporary files
-- DELETE FROM siimut_menus WHERE nama_menu = 'Test Menu'; -- if you created any test entries