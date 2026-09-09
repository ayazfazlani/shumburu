const urlBase64ToUint8Array = (base64String) => {
	const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
	const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
	const rawData = window.atob(base64);

	return Uint8Array.from([...rawData].map((character) => character.charCodeAt(0)));
};

window.enableWebPushNotifications = async () => {
	if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
		throw new Error('This browser does not support push notifications.');
	}

	const publicKey = document.querySelector('meta[name="vapid-public-key"]')?.content;
	if (!publicKey) {
		throw new Error('Web Push is not configured on the server.');
	}

	const registration = await navigator.serviceWorker.ready;
	const permission = await window.Notification.requestPermission();
	if (permission !== 'granted') {
		throw new Error('Notification permission was not granted.');
	}

	const subscription = await registration.pushManager.subscribe({
		userVisibleOnly: true,
		applicationServerKey: urlBase64ToUint8Array(publicKey),
	});

	const response = await window.fetch('/push-subscriptions', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			'Accept': 'application/json',
			'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
		},
		body: JSON.stringify(subscription.toJSON()),
	});

	if (!response.ok) {
		throw new Error('The push subscription could not be saved.');
	}

	return subscription;
};

if ('serviceWorker' in navigator) {
	window.addEventListener('load', () => navigator.serviceWorker.register('/sw.js'));
}

window.addEventListener('beforeinstallprompt', (event) => {
	event.preventDefault();
	window.deferredInstallPrompt = event;
	window.dispatchEvent(new CustomEvent('pwa-install-available'));
});
