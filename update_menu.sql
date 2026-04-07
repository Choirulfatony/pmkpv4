-- Backup current menu structure first if needed
-- Insert new menu items for the requested structure

-- First, let's add the new top-level menu: Rekap Periode INM
INSERT INTO siimut_menus (nama_menu, url, icon, parent_id, role_access, urutan) 
VALUES ('Rekap Periode INM', 'siimut/rekap-laporan-inm/rekap-periode', 'bi bi-calendar-range', NULL, 'ADMINISTRATOR, KOMITE, KENDALI_MUTU', 99);

-- Get the ID of the newly inserted menu
SET @rekap_periode_id = LAST_INSERT_ID();

-- Now update the existing Forms Rekap menu to be a parent with children
-- We need to check what the current Forms Rekap menu ID is
-- Based on earlier query, it was ID 4

-- Update Forms Rekap to be a parent (it already is, but let's make sure it has the right properties)
UPDATE siimut_menus 
SET nama_menu = 'Rekap Laporan INM', 
    url = 'siimut/rekap-laporan-inm',
    icon = 'bi bi-bar-chart'
WHERE id_menu = 4;

-- Add child menu items under Rekap Laporan INM
-- Per Bulan (this should point to the index method)
INSERT INTO siimut_menus (nama_menu, url, icon, parent_id, role_access, urutan) 
VALUES ('Per Bulan', 'siimut/rekap-laporan-inm', 'bi bi-file-earmark', 4, 'ADMINISTRATOR, KOMITE, KENDALI_MUTU', 1);

-- Per Periode (this should point to the rekapPeriode method)  
INSERT INTO siimut_menus (nama_menu, url, icon, parent_id, role_access, urutan) 
VALUES ('Per Periode', 'siimut/rekap-laporan-inm/rekap-periode', 'bi bi-calendar-range', 4, 'ADMINISTRATOR, KOMITE, KENDALI_MUTU', 2);

-- Update the alternative Rekap Periode INM menu to point to the same rekapPeriode method
-- Actually, let's make it point to a cleaner URL
UPDATE siimut_menus 
SET url = 'siimut/rekap-laporan-inm/rekap-periode',
    nama_menu = 'Rekap Periode INM'
WHERE nama_menu = 'Rekap Periode INM' AND url = 'siimut/rekap-laporan-inm/rekap-periode';

-- Let's also add a route alias for cleaner URL if needed
-- But first, let's check current structure

SELECT * FROM siimut_menus WHERE nama_menu IN ('Rekap Laporan INM', 'Rekap Periode INM', 'Per Bulan', 'Per Periode') OR url LIKE '%rekap%' ORDER BY urutan;