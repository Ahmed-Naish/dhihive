import {createApp, ref, onMounted} from 'vue';
import {getMessaging, getToken, onMessage} from 'firebase/messaging';
import firebaseApp from '@/utils/initialize'; // Ensure this path is correct
import axios from 'axios';

// Define a new component or extend PermissionComponent with the setup
const PermissionApp = {
    setup() {
        const user = ref(null); // Reactive reference for user data
        const messaging = getMessaging(firebaseApp);
        const requestPermission = () => {
            console.log('Requesting permission...');
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    console.log('Notification permission granted.');
                }
            });
        };

        const fetchUser = () => {
            return axios.get('/api/push-notification/authenticated-user') // Replace with your API endpoint to fetch user data
                .then(response => {
                    user.value = response.data; // Set user data in reactive reference
                })
                .catch(error => {
                    console.error('Error fetching user:', error);
                    throw error; // Handle error in component if necessary
                });
        };

        onMounted(() => {
            fetchUser() // Fetch user data on component mount
                .then(() => {
                    const user_id = user.value.id; // Access user ID from fetched user object

                    getToken(messaging, {
                        vapidKey: import.meta.env.VITE_VAPID_KEY,
                    })
                        .then(currentToken => {
                            if (currentToken) {
                                // Send the token to your server using Axios
                                axios.post(`/api/push-notification/setToken`, {
                                    device_token: currentToken,
                                    user_id: user_id,
                                })
                                    .then(response => {
                                        console.log('Token sent successfully:', response.data);
                                        // Handle UI update if necessary
                                    })
                                    .catch(error => {
                                        console.error('Error sending token:', error);
                                        // Handle error
                                    });
                            } else {
                                // Show permission request UI
                                // console.log('No registration token available. Request permission to generate one.');
                                requestPermission();
                            }
                        })
                        .catch(err => {
                            console.log('An error occurred while retrieving token. ', err);
                            // Handle error
                        });

                    onMessage(messaging, ({notification, data}) => {
                        console.log(notification);
                        console.log('message received');
                        const notificationOptions = {
                            body: notification.body,
                            icon: notification.icon,
                            data: {
                                url: data.url // Custom data field for URL
                            }
                        };

                        const n = new Notification(notification.title, notificationOptions);

                        n.onclick = (event) => {
                            event.preventDefault();
                            window.open(notificationOptions.data.url, '_blank');
                        };
                    });
                })
                .catch(error => {
                    console.error('Error fetching user:', error);
                });
        });

        return {
            user,
        };
    },
};

const app = createApp(PermissionApp);
app.mount('#app');
