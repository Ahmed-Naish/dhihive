importScripts(
    "https://www.gstatic.com/firebasejs/10.0.0/firebase-app-compat.js"
);
importScripts(
    "https://www.gstatic.com/firebasejs/10.0.0/firebase-messaging-compat.js"
);

firebase.initializeApp({
    apiKey: "AIzaSyCG9qpEZ2BffM9V7XDdKqELH_UmfDzxpyw",
    authDomain: "dhihivehr.firebaseapp.com",
    projectId: "dhihivehr",
    storageBucket: "dhihivehr.firebasestorage.app",
    messagingSenderId: "546124613460",
    appId: "1:546124613460:web:2152b1437de3edd612aac1",
    measurementId: "G-C0QYQ0PFMQ"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function (payload) {
    console.log('Received background message ', payload);
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: payload.notification.icon
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});
