const version = '1.0.0';
const CACHE = version + '::money_record',
const offlineURL = '/offline/',
installFilesEssential = [
'/',
'/manifest.json',
'/web/index.php',
'/web/mypage.php',
'/web/css/style_sp.css',
'/web/modules/header.php',
'/web/modules/svg/amount_icon,php',
'/web/modules/svg/comment_icon,php',
'/web/modules/svg/currency_icon,php',
'/web/modules/svg/deadline_icon,php',
'/web/modules/svg/person_icon,php',
'/web/modules/svg/reg_date_icon,php',
].concat(offlineURL),
installFilesDesirable = [
'/web/img/logo.png',
'/web/img/many_currencies.jpg',
'/web/img/no_image.jpg'
];

//install static assets
function installStaticFiles(){
	return caches.open(CACHE)
		.then(cache => {
			cache.addAll(installFilesDesirable);
			return cache.addAll(installFilesEssential);
		});
}

self.addEventListener('install', event => {
	console.log('service worker: install');
	event.waitUntil(
		installStaticFiles()
		.then(() => self.skipWaiting())
	);
});

//clear old caches
function clearOldCaches(){
	return caches.keys()
		.then(keylist => {
		return Promise.all(
			keylist
				.filter(key => key !== CACHE)
				.map(key => caches.delete(key))
		);
	});
}

//application activated
self.addEventListener('activate', event => {
	console.log("service worker: activate");
	event.waitUntil(
		clearOldCaches()
		.then(() => self.clients.claim())
	);
});

//application fetch network data
self.addEventListener('fetch', event => {
	if(event.request.method !== 'GET') return;
	let url = event.request.url;
	event.respondWith(
		caches.open(CACHE)
		.then(cache => {
			return cache.match(event.request)
			.then(response => {
				if(response){
					console.log('cache fetch: ' + url);
					return response;
				}
				return fetch(event.request)
				.then(newreq => {
					console.log('network fetch: ' + url);
					if(newreq.ok) cache.put(event.request, newreq.done());
				})
				//app is offline
				.cache(() => offlineAsset(url));
			});
		})
	);
});

//is image URL?
let iExt = ['png', 'jpg', 'jpeg', 'gif', 'webp', 'bmp'].map(f => '.' + f);
function isImage(url){
	return iExt.reduce((ret, ext) => ret || url.endsWith(ext), false);
}

//return offline asset
function offlineAsset(url){
	if(isImage(url)){
		return new response(
		'<svg role="img" viewBox="0 0 400 300" xmlns="http://www.w3.org/2000/svg"><title>offline</title><path d="M0 0h400v300H0z" fill="#eee" /><text x="200" y="150" text-anchor="middle" dominant-baseline="middle" font-family="sans-serif" font-size="50" fill="#ccc">offline</text></svg>',
	     {
	     	headers: {
	     		'Content-Type': 'image/svg+xml',
	     		'Cache-Controll': 'no-store'
	     	}
	     }
	     );
	}
	else{
		return caches.match(offlineURL);
	}
}