<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$nick   = isset($_GET['nick'])   ? htmlspecialchars(trim($_GET['nick']))   : '';
$sender = isset($_GET['sender']) ? htmlspecialchars(trim($_GET['sender'])) : '';

if (empty($nick) || empty($sender)) {
    echo json_encode(['status' => '400', 'msg' => 'nick dan sender wajib diisi']);
    exit();
}

if (!filter_var($sender, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => '400', 'msg' => 'Email sender tidak valid']);
    exit();
}

$file = __DIR__ . '/data.php';
$content  = "<?php \n";
$content .= '$nik = "' . $nick . '";' . "\n";
$content .= '$sender = "' . $sender . '";' . "\n";
$content .= "?>\n";

file_put_contents($file, $content);
echo json_encode(['status' => '200', 'msg' => 'Data berhasil diperbarui']);
?>
