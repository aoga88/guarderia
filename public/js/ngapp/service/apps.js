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
        },

        getCurrent: function() {
            var defer = $q.defer();

            $http.get('/api/app/current')
            .success( function(data) {
                defer.resolve(data);
            })
            .error( function(data) {
                defer.reject(data);
            });

            return defer.promise;
        },

        current: function() {
            var defer = $q.defer();

            $http.get('/api/app/current-app')
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