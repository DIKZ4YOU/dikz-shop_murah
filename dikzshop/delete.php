<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Content-Type: application/json');

$file = __DIR__ . '/data.json';

if (!file_exists($file)) {
    echo json_encode(['status' => '404', 'msg' => 'File tidak ditemukan']);
    exit();
}

$read = file_get_contents($file);
$json = json_decode($read, true);
if (!is_array($json)) $json = [];

// ─── Mode 1: Hapus by EMAIL (dipakai otomatis oleh DikzShop saat jasteb habis)
// GET /delete.php?mail=email@contoh.com
if (isset($_GET['mail']) || isset($_POST['mail'])) {
    $mail = strtolower(trim(isset($_GET['mail']) ? $_GET['mail'] : $_POST['mail']));
    if (empty($mail) || !filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => '400', 'msg' => 'Email tidak valid']);
        exit();
    }
    $before = count($json);
    $json   = array_values(array_filter($json, function($item) use ($mail) {
        return strtolower($item['email'] ?? '') !== $mail;
    }));
    $after  = count($json);
    file_put_contents($file, json_encode($json, JSON_PRETTY_PRINT));
    if ($before !== $after) {
        echo json_encode(['status' => '200', 'msg' => 'Email '.$mail.' berhasil dihapus']);
    } else {
        echo json_encode(['status' => '200', 'msg' => 'Email tidak ditemukan, tidak ada yang dihapus']);
    }
    exit();
}

// ─── Mode 2: Hapus by INDEX (dipakai panel admin manual)
// GET /delete.php?keys=0
if (isset($_GET['keys'])) {
    $keys = (int)$_GET['keys'];
    if (!isset($json[$keys])) {
        echo json_encode(['status' => '400', 'msg' => 'Index tidak valid']);
        exit();
    }
    unset($json[$keys]);
    $json = array_values($json);
    file_put_contents($file, json_encode($json, JSON_PRETTY_PRINT));
    echo json_encode(['status' => '200', 'msg' => 'Email berhasil dihapus']);
    exit();
}

echo json_encode(['status' => '400', 'msg' => 'Parameter mail atau keys diperlukan']);
?>
