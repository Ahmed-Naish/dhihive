// resources/js/utils/initialize.js
import { initializeApp } from 'firebase/app';

// TODO: Replace the following with your app's Firebase project configuration
const firebaseConfig = {
    apiKey: "AIzaSyDGxJEpFBHtnpCZE2Hw9eHQ9Edpc9S4Xn8",
    authDomain: "notification-ce4c5.firebaseapp.com",
    projectId: "notification-ce4c5",
    storageBucket: "notification-ce4c5.appspot.com",
    messagingSenderId: "346720125375",
    appId: "1:346720125375:web:cc4774ec949fb2f2ace10a",
};

const app = initializeApp(firebaseConfig);

export default app;
