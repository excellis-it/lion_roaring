// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getMessaging, getToken, onMessage } from "firebase/messaging";

// Your web app's Firebase configuration
const firebaseConfig = {
  apiKey: "AIzaSyDVeij-m4rQh2WBXvWNkR4eQgFcQ8xVfoI",
  authDomain: "lion-roaring-2fea7.firebaseapp.com",
  projectId: "lion-roaring-2fea7",
  storageBucket: "lion-roaring-2fea7.firebasestorage.app",
  messagingSenderId: "363437614699",
  appId: "1:363437614699:web:9e9fe51c290580164a732e",
  measurementId: "G-1CT4JW9SY5"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);

// Initialize Firebase Cloud Messaging and get a reference to the service
const messaging = getMessaging(app);

// Request permission and get token
export async function requestNotificationPermission() {
  try {
    const permission = await Notification.requestPermission();
    if (permission === 'granted') {
      console.log('Notification permission granted.');

      // Get registration token
      const token = await getToken(messaging, {
        vapidKey: 'BBiapQqOmiMH8GaZAvtWCbOq2u4icIQEhjrRjDsqIyBBXjpA7tnTUI3lZmgosMA0gVeYrYXlExKMR3yCZ62mcMk' // Get this from Firebase Console
      });

      if (token) {
        console.log('FCM Token:', token);
        // Send token to your server
        await updateFCMToken(token);
        return token;
      } else {
        console.log('No registration token available.');
      }
    } else {
      console.log('Unable to get permission to notify.');
    }
  } catch (error) {
    console.error('An error occurred while retrieving token:', error);
  }
}

// Update FCM token on server
async function updateFCMToken(token) {
  try {
    const response = await fetch('/api/fcm/update-token', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + localStorage.getItem('auth_token'), // Adjust based on your auth system
      },
      body: JSON.stringify({ fcm_token: token })
    });

    if (response.ok) {
      console.log('FCM token updated successfully');
    } else {
      console.error('Failed to update FCM token');
    }
  } catch (error) {
    console.error('Error updating FCM token:', error);
  }
}

// Handle foreground messages
onMessage(messaging, (payload) => {
  console.log('Message received in foreground:', payload);

  // Customize notification handling based on message type
  const notificationTitle = payload.notification?.title || 'New Notification';
  const notificationOptions = {
    body: payload.notification?.body || 'You have a new message',
    icon: '/images/notification-icon.png', // Add your app icon
    badge: '/images/badge-icon.png',
    tag: payload.data?.type || 'general',
    data: payload.data
  };

  // Show notification
  if (Notification.permission === 'granted') {
    const notification = new Notification(notificationTitle, notificationOptions);

    notification.onclick = function(event) {
      event.preventDefault();

      // Handle notification click based on type
      handleNotificationClick(payload.data);

      notification.close();
    };
  }
});

// Handle notification clicks
function handleNotificationClick(data) {
  switch (data?.type) {
    case 'chat':
      // Navigate to specific chat
      window.location.href = `/chat/${data.sender_id}`;
      break;
    case 'team_chat':
      // Navigate to team chat
      window.location.href = `/team-chat/${data.team_id}`;
      break;
    case 'email':
      // Navigate to email
      window.location.href = `/email/${data.email_id}`;
      break;
    default:
      // Navigate to notifications page
      window.location.href = '/notifications';
  }
}

// Initialize FCM when page loads
document.addEventListener('DOMContentLoaded', function() {
  requestNotificationPermission();
});
