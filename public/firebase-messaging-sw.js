// Import Firebase scripts for service worker
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js');

// Initialize Firebase in service worker
firebase.initializeApp({
  apiKey: "AIzaSyDVeij-m4rQh2WBXvWNkR4eQgFcQ8xVfoI",
  authDomain: "lion-roaring-2fea7.firebaseapp.com",
  projectId: "lion-roaring-2fea7",
  storageBucket: "lion-roaring-2fea7.firebasestorage.app",
  messagingSenderId: "363437614699",
  appId: "1:363437614699:web:9e9fe51c290580164a732e",
  measurementId: "G-1CT4JW9SY5"
});

// Retrieve Firebase Messaging object
const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage(function(payload) {
  console.log('[firebase-messaging-sw.js] Received background message:', payload);

  const notificationTitle = payload.notification?.title || 'Lion Roaring';
  const notificationOptions = {
    body: payload.notification?.body || 'You have a new notification',
    icon: '/images/notification-icon.png',
    badge: '/images/badge-icon.png',
    tag: payload.data?.type || 'general',
    data: payload.data,
    requireInteraction: true,
    actions: [
      {
        action: 'view',
        title: 'View',
        icon: '/images/view-icon.png'
      },
      {
        action: 'dismiss',
        title: 'Dismiss',
        icon: '/images/dismiss-icon.png'
      }
    ]
  };

  self.registration.showNotification(notificationTitle, notificationOptions);
});

// Handle notification click in service worker
self.addEventListener('notificationclick', function(event) {
  console.log('[firebase-messaging-sw.js] Notification click received.');

  event.notification.close();

  if (event.action === 'dismiss') {
    return;
  }

  // Handle the click based on notification data
  const data = event.notification.data;
  let url = '/';

  switch (data?.type) {
    case 'chat':
      url = `/chat/${data.sender_id}`;
      break;
    case 'team_chat':
      url = `/team-chat/${data.team_id}`;
      break;
    case 'email':
      url = `/email/${data.email_id}`;
      break;
    default:
      url = '/notifications';
  }

  event.waitUntil(
    clients.openWindow(url)
  );
});
