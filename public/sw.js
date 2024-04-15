self.addEventListener('install', function(event) {
    // Perform installation steps
    event.waitUntil(
        caches.open('weather-cache').then(function(cache) {
            return cache.add ([
                '/user/main'
            ]).catch(function(error) {
                console.error('Failed to cache some resources:' , error);
            });
        })
    );
});
  
self.addEventListener('fetch', function(event) {
    event.respondWith(
        fetch(event.request)
            .then(function(response) {
                const clonedResponse = response.clone();

                caches.open('weather-cache').then(function(cache) {
                    cache.put(event.request, clonedResponse);
                });

                return response;
            })
            .catch(function() {
                return caches.match(event.request);
            })
    );    
});