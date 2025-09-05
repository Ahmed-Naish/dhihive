<template>
    <div class="min-h-screen bg-gray-100">
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h4>Push Notification App</h4>
            </div>
        </header>
    </div>
</template>

<script>
    import { ref, onMounted } from 'vue';
    import { getMessaging, getToken, onMessage } from 'firebase/messaging';
    import app from '@/utils/initialize';
    import axios from 'axios';

    export default {
        setup() {
            const user = ref(null); // Reactive reference for user data

            const messaging = getMessaging(app);

            const requestPermission = () => {
                console.log('Requesting permission...');
                Notification.requestPermission().then(permission => {
                    if (permission === 'granted') {
                        console.log('Notification permission granted.');
                    }
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
                                    axios.post(`/api/setToken`, {
                                        fcm_token: currentToken,
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
                                    console.log('No registration token available. Request permission to generate one.');
                                    requestPermission();
                                }
                            })
                            .catch(err => {
                                console.log('An error occurred while retrieving token. ', err);
                                // Handle error
                            });

                        onMessage(messaging, ({ notification }) => {
                            new Notification(notification.title, {
                                body: notification.body,
                            });
                            // Handle notification
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching user:', error);
                        // Handle error in fetching user
                    });
            });

            const fetchUser = () => {
                return axios.get('/api/authenticated-user') // Replace with your API endpoint to fetch user data
                    .then(response => {
                        console.log(response)
                        user.value = response.data; // Set user data in reactive reference
                    })
                    .catch(error => {
                        console.error('Error fetching user:', error);
                        throw error; // Handle error in component if necessary
                    });
            };
            return {
                user,
            };
        },
    };
</script>
