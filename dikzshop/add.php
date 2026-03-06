<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Content-Type: application/json');

$file = __DIR__ . '/data.json';

// Init file kalau belum ada
if (!file_exists($file) || file_get_contents($file) == '' || file_get_contents($file) == 'null') {
    file_put_contents($file, json_encode([]));
}

$read = file_get_contents($file);
$json = json_decode($read, true);
if (!is_array($json)) $json = [];

$mail = isset($_GET['mail']) ? trim($_GET['mail']) : (isset($_POST['mail']) ? trim($_POST['mail']) : '');

if (empty($mail) || !filter_var($mail, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => '400', 'msg' => 'Email tidak valid']);
    exit();
}

// Cek duplikat
foreach ($json as $item) {
    if (isset($item['email']) && strtolower($item['email']) == strtolower($mail)) {
        echo json_encode(['status' => '200', 'msg' => 'Email sudah ada']);
        exit();
    }
}

$add = [
    'email'   => $mail,
    'tanggal' => date('Y-m-d H:i:s'),
    'source'  => 'DikzShop-Jasteb'
];

array_unshift($json, $add);
file_put_contents($file, json_encode($json, JSON_PRETTY_PRINT));

echo json_encode(['status' => '200', 'msg' => 'Email berhasil ditambahkan']);
?>
