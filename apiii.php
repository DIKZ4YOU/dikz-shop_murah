<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Content-Type: application/json');

include __DIR__ . '/dikzshop/data.php';

$subjek = isset($_POST['subjek']) ? trim($_POST['subjek']) : (isset($_GET['subjek']) ? trim($_GET['subjek']) : '');
$pesan  = isset($_POST['pesan'])  ? trim($_POST['pesan'])  : (isset($_GET['pesan'])  ? trim($_GET['pesan'])  : '');

if (empty($subjek) || empty($pesan)) {
    echo json_encode(['status' => '400', 'msg' => 'subjek dan pesan wajib diisi']);
    exit();
}

$fromName  = $nik;
$fromEmail = $sender;

$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
$headers .= 'From: ' . $fromName . ' <' . $fromEmail . '>' . "\r\n";

$file = __DIR__ . '/dikzshop/data.json';
$read = file_get_contents($file);
$json = json_decode($read, true);
if (!is_array($json)) $json = [];

$count = 0;
$errors = [];

foreach ($json as $item) {
    if (isset($item['email']) && filter_var($item['email'], FILTER_VALIDATE_EMAIL)) {
        $result = @mail($item['email'], $subjek, $pesan, $headers);
        if ($result) {
            $count++;
        } else {
            $errors[] = $item['email'];
        }
    }
}

echo json_encode([
    'status'  => '200',
    'msg'     => 'Email terkirim ke ' . $count . ' penerima',
    'total'   => count($json),
    'success' => $count,
    'failed'  => count($errors),
]);
?>
