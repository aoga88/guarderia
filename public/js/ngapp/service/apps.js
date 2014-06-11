function Apps($http, $q) {
    return {
        save: function(entityApp, id) {
            var defer     = $q.defer();
            entityApp._id = id;

            $http.post('/api/app', entityApp)
            .success( function(data) {
                defer.resolve(data);
            })
            .error( function(data) {
                defer.reject(data);
                console.log('error');
            });

            return defer.promise;
        },

        find: function(conditions) {
            var defer = $q.defer();

            $http.post('/api/app/find', conditions)
            .success( function(data) {
                defer.resolve(data);
            })
            .error( function(data) {
                defer.reject(data);
            });

            return defer.promise;
        }
    }
};