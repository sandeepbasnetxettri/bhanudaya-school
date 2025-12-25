// Service Worker for Push Notifications
const CACHE_NAME = 'school-app-cache-v1';
const urlsToCache = [
  '/',
  '/css/style.css',
  '/js/main.js'
];

// Install event - cache static assets
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Opened cache');
        return cache.addAll(urlsToCache);
      })
  );
});

// Fetch event - serve cached content when offline
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        // Return cached version or fetch from network
        return response || fetch(event.request);
      })
  );
});

// Push notification event
self.addEventListener('push', function(event) {
  console.log('[Service Worker] Push Received.');
  console.log(`[Service Worker] Push had this data: "${event.data.text()}"`);

  let notificationData;
  try {
    notificationData = event.data.json();
  } catch (e) {
    // If JSON parsing fails, treat as plain text
    notificationData = {
      title: 'New Notification',
      body: event.data.text(),
      icon: '/images/school-icon.png'
    };
  }

  const title = notificationData.title || 'School Notification';
  const options = {
    body: notificationData.body || 'You have a new notification',
    icon: notificationData.icon || '/images/school-icon.png',
    badge: '/images/school-badge.png',
    data: {
      url: notificationData.url || '/'
    }
  };

  event.waitUntil(self.registration.showNotification(title, options));
});

// Notification click event
self.addEventListener('notificationclick', function(event) {
  console.log('[Service Worker] Notification click received.');
  
  event.notification.close();
  
  const urlToOpen = event.notification.data.url || '/';
  
  event.waitUntil(
    clients.matchAll({ type: 'window' }).then(clientList => {
      // If there's already a window open, focus it
      for (let i = 0; i < clientList.length; i++) {
        const client = clientList[i];
        if (client.url === urlToOpen && 'focus' in client) {
          return client.focus();
        }
      }
      
      // If not, open a new window
      if (clients.openWindow) {
        return clients.openWindow(urlToOpen);
      }
    })
  );
});