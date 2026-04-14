importScripts("https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging-compat.js");

// Firebase config (نفس config تبعك)
firebase.initializeApp({
  apiKey: "AIzaSyDdyw2Y9vcZUlC2kXvw1UNM16XltojZJwo",
  authDomain: "my-laravel-notifications.firebaseapp.com",
  projectId: "my-laravel-notifications",
  storageBucket: "my-laravel-notifications.firebasestorage.app",
  messagingSenderId: "1007419117053",
  appId: "1:1007419117053:web:48095d1c574fa7029e1d94",
});

// Get messaging instance
const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage(function (payload) {
  console.log("Background message received:", payload);

  if (payload.data) {
    const title = payload.data.title || "No title";
    const options = {
      body: payload.data.body || "No body",
      icon: "/firebase-logo.png",
    };
    self.registration.showNotification(title, options);
  }
});
