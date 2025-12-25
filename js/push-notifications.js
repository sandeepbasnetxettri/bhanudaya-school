// Push Notifications Handler
(function() {
  'use strict';

  // Check if service worker is supported
  if ('serviceWorker' in navigator) {
    // Register service worker
    navigator.serviceWorker.register('/js/service-worker.js')
      .then(registration => {
        console.log('Service Worker registered with scope:', registration.scope);
        
        // Check for push manager support
        if ('PushManager' in window) {
          console.log('Push Manager supported');
        } else {
          console.log('Push Manager not supported');
        }
      })
      .catch(error => {
        console.log('Service Worker registration failed:', error);
      });
  } else {
    console.log('Service Workers not supported');
  }

  // Function to subscribe to push notifications
  function subscribeToPush() {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
      console.log('Push notifications not supported');
      return Promise.reject('Push notifications not supported');
    }

    return navigator.serviceWorker.ready
      .then(registration => {
        // Check if already subscribed
        return registration.pushManager.getSubscription()
          .then(subscription => {
            if (subscription) {
              console.log('Already subscribed to push notifications');
              return subscription;
            }

            // Subscribe to push notifications
            const vapidPublicKey = 'YOUR_VAPID_PUBLIC_KEY_HERE'; // Replace with actual VAPID public key
            const convertedVapidKey = urlBase64ToUint8Array(vapidPublicKey);

            return registration.pushManager.subscribe({
              userVisibleOnly: true,
              applicationServerKey: convertedVapidKey
            });
          });
      })
      .then(subscription => {
        // Send subscription to server
        return sendSubscriptionToServer(subscription);
      })
      .then(response => {
        if (response.success) {
          console.log('Successfully subscribed to push notifications');
          return true;
        } else {
          console.log('Failed to subscribe to push notifications:', response.error);
          return false;
        }
      })
      .catch(error => {
        console.log('Error subscribing to push notifications:', error);
        return false;
      });
  }

  // Function to unsubscribe from push notifications
  function unsubscribeFromPush() {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
      return Promise.reject('Push notifications not supported');
    }

    return navigator.serviceWorker.ready
      .then(registration => {
        return registration.pushManager.getSubscription();
      })
      .then(subscription => {
        if (subscription) {
          // Remove subscription from server
          return removeSubscriptionFromServer(subscription)
            .then(() => {
              // Unsubscribe from push notifications
              return subscription.unsubscribe();
            });
        }
      })
      .then(() => {
        console.log('Successfully unsubscribed from push notifications');
        return true;
      })
      .catch(error => {
        console.log('Error unsubscribing from push notifications:', error);
        return false;
      });
  }

  // Convert base64 URL to Uint8Array
  function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding)
      .replace(/\-/g, '+')
      .replace(/_/g, '/');

    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; ++i) {
      outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
  }

  // Send subscription to server
  function sendSubscriptionToServer(subscription) {
    const subscriptionData = {
      endpoint: subscription.endpoint,
      p256dh: btoa(String.fromCharCode.apply(null, new Uint8Array(subscription.getKey('p256dh')))),
      auth: btoa(String.fromCharCode.apply(null, new Uint8Array(subscription.getKey('auth'))))
    };

    return fetch('/api/save-push-subscription.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(subscriptionData)
    })
    .then(response => response.json())
    .catch(error => {
      console.log('Error sending subscription to server:', error);
      return { success: false, error: error.message };
    });
  }

  // Remove subscription from server
  function removeSubscriptionFromServer(subscription) {
    return fetch('/api/remove-push-subscription.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ endpoint: subscription.endpoint })
    })
    .then(response => response.json())
    .catch(error => {
      console.log('Error removing subscription from server:', error);
      return { success: false, error: error.message };
    });
  }

  // Request notification permission
  function requestNotificationPermission() {
    if (!('Notification' in window)) {
      console.log('This browser does not support notifications.');
      return Promise.reject('Notifications not supported');
    }

    return Notification.requestPermission()
      .then(permission => {
        if (permission === 'granted') {
          console.log('Notification permission granted.');
          return true;
        } else {
          console.log('Notification permission denied.');
          return false;
        }
      });
  }

  // Public API
  window.PushNotifications = {
    subscribe: subscribeToPush,
    unsubscribe: unsubscribeFromPush,
    requestPermission: requestNotificationPermission
  };

})();