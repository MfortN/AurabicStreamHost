<!DOCTYPE html>
<html lang="en">
<?php
$servername = "localhost";
$username = "username";
$password = "password";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
?>
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Aurabic live test</title>
</head>

<body>
    <h2></h2>
    <div id="statusMessage"></div>
    <p id="autoPlayMessage">جاري التشغيل تلقائيا...</p>
    <video id="video" width="1280" height="720" controls autoplay muted playsinline></video>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <script>
        var video = document.getElementById("video");
        var statusMessage = document.getElementById("statusMessage");
        var autoPlayMessage = document.getElementById("autoPlayMessage");
        var videoSrc = "/hls/mfortn.m3u8";
        var isVideoPaused = false; // تتبع حالة توقف الفيديو

        document.addEventListener('DOMContentLoaded', function() {
        // تحميل قيمة startTimeInSeconds من Local Storage
        var startTimeInSeconds = localStorage.getItem('startTimeInSeconds') || 20;

        // انتظار حدوث حدث التحميل
        video.addEventListener('loadedmetadata', function() {
            // تحديد بداية الفيديو
            video.currentTime = startTimeInSeconds;

            // بدء تشغيل الفيديو
            video.play();
        });

        // حفظ startTimeInSeconds في Local Storage
        video.addEventListener('timeupdate', function() {
            localStorage.setItem('startTimeInSeconds', video.currentTime);
        });
    });

        function updateVideoSource() {
            if (Hls.isSupported()) {
                var hls = new Hls();
                hls.loadSource(videoSrc);
                hls.attachMedia(video);

                hls.on(Hls.Events.MANIFEST_PARSED, function (event, data) {
                    parsed();
                    console.log(startTimeInSeconds);
                });

                hls.on(Hls.Events.LEVEL_LOADED, function (event, data) {
                    console.log("تم البث بنجاح");
                    console.log(startTimeInSeconds);
                    // checkStreamStatus();
                });

                hls.on(Hls.Events.ERROR, function (event, data) {
                    if (data.details === "manifestLoadError") {
                        parsed();
                    }
                    else if (data.fatal) {
                        // console.log("حدث خطأ: " + data.type + " - " + data.details);
                    }
                });

                // video.addEventListener("timeupdate", function () {
                //   // إذا كان الفيديو قد وصل إلى نهايته
                //   if (video.currentTime >= video.duration - 1) {
                //     console.log("انتهى البث. جاري إعادة تشغيل...");
                //     video.load(); // إعادة تحميل الفيديو عند الانتهاء
                //   }
                // });

                video.addEventListener("pause", function () {
                    isVideoPaused = true;
                });

                video.addEventListener("play", function () {
                    isVideoPaused = false;
                });
            } else {
                statusMessage.innerText = "المتصفح غير مدعوم";
            }
        }

        updateVideoSource();

        function parsed() {
            console.log("جاري البث...");
            autoPlayMessage.style.display = "none";
        }

        function getFilesList(folderPath) {
            // يمكننا استخدام XMLHttpRequest للقراءة الغير متزامنة، أو fetch للقراءة المتزامنة
            var xhr = new XMLHttpRequest();
            xhr.open("GET", folderPath, false);
            xhr.send();

            if (xhr.status === 200) {
                // التحقق من أن الطلب نجح ثم تحويل النص إلى كائن JSON
                var files = JSON.parse(xhr.responseText);
                return files;
            } else {
                console.error('Error reading folder:', xhr.statusText);
                return [];
            }
        }

        function checkStreamStatus() {
            var folderPath = 'data'; // تحديد المسار إلى المجلد
            var expectedFileCount = 3; // تحديد العدد المتوقع للملفات

            // حصول على قائمة الملفات في المجلد
            var files = getFilesList(folderPath);

            var fileCount = files.length;

            console.log(fileCount);
        }

        // setInterval(checkStreamStatus, 2000); // يقوم بالتحقق كل 5 ثوانٍ

    </script>
</body>

</html>