-- Tambah menu "Daftar Unit/Bagian" di bawah "Master" (parent_id = 3)
-- Pastikan parent_id sesuai dengan menu "Master" di database Anda

-- Cek dulu ID menu "Master"
SELECT id_menu, nama_menu FROM siimut_menus WHERE nama_menu = 'Master' LIMIT 1;

-- Insert menu Daftar Unit/Bagian (ganti ? dengan ID menu "Master" dari hasil di atas)
-- INSERT INTO siimut_menus (nama_menu, url, icon, parent_id, role_access, urutan)
-- VALUES ('Daftar Unit/Bagian', 'siimut/unit', 'bi bi-building', ?, 'ADMINISTRATOR', 2);
