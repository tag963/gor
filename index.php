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
            margin-top: 20px;
            font-size: 24px;
            color: #007bff;
        }
        /* الطريق */
        .road {
            position: relative;
            margin: 100px auto;
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
            font-size: 100px; /* حجم كبير */
        }
        .truck.move {
            animation: drive 10s linear 5; /* ببطء وتتكرر 5 مرات */
        }
        @keyframes drive {
            from { left: -150px; }
            to { left: calc(100% - 120px); }
        }
        /* الزر */
        button { 
            padding: 15px 30px; 
            font-size: 20px; 
            cursor: pointer; 
            background: #007bff; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            transition: 0.3s; 
            margin-top: 20px;
        }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>

<h2>لتحقق أنك لست روبوت اضغط هنا</h2>
<button onclick="startProcess()">ابدأ التحقق</button>

<div class="road">
    <div class="truck">🚚</div>
</div>

<audio id="engineSound" src="https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3"></audio>

<script>
async function startProcess() {
    try {
        // تشغيل حركة السيارة + الصوت