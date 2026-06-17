<?php
$db = new PDO('mysql:host=localhost;dbname=siimut', 'root', '');
$stmt = $db->query('SELECT id, file_name, file_type, file_size, is_folder FROM siimut_file_manager ORDER BY id');
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($items, JSON_PRETTY_PRINT);
