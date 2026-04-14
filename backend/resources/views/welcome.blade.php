<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FCM Token Generator</title>
</head>

<body>
    <h2>Firebase Cloud Messaging Token Generator</h2>
    <button id="getTokenBtn">Generate FCM Token</button>
    <p id="tokenOutput"></p>

    <script type="module">
        import {
            initializeApp
        } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
        import {
            getMessaging,
            getToken,
            onMessage
        } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging.js";

        const firebaseConfig = {
            apiKey: "AIzaSyDdyw2Y9vcZUlC2kXvw1UNM16XltojZJwo",
            authDomain: "my-laravel-notifications.firebaseapp.com",
            projectId: "my-laravel-notifications",
            storageBucket: "my-laravel-notifications.firebasestorage.app",
            messagingSenderId: "1007419117053",
            appId: "1:1007419117053:web:48095d1c574fa7029e1d94",
            measurementId: "G-97WWLP66ZN"
        };

        const app = initializeApp(firebaseConfig);
        const messaging = getMessaging(app);

        // Generate FCM token
        document.getElementById("getTokenBtn").addEventListener("click", async () => {
            try {
                const permission = await Notification.requestPermission();
                if (permission === "granted") {
                    const token = await getToken(messaging, {
                        vapidKey: "BGo5WDkVvFfjPftD82l6vgeqBlhnrSkabpA1M2Mk0RVprKf4oMKusEQHn0rlVPLzmlX3-33cYhGVdEYmQowto9E",
                    });
                    document.getElementById("tokenOutput").textContent = token;
                    console.log("FCM Token:", token);
                } else {
                    alert("Notification permission denied.");
                }
            } catch (error) {
                console.error("Error getting FCM token:", error);
            }
        });

        // Handle foreground messages (data-only)
        onMessage(messaging, (payload) => {
            console.log("Foreground message received:", payload);

            if (payload.data) {
                const title = payload.data.title || "No title";
                const body = payload.data.body || "No body";
                new Notification(title, {
                    body
                });
            }
        });
    </script>
</body>

</html>
