function Actividad($http, $q) {
    return {
        save: function(entityApp, id) {
            var defer     = $q.defer();
            entityApp._id = id;

            $http.post('/api/actividad', entityApp)
            .success( function(data) {
                defer.resolve(data);
            })
            .error( function(data) {
                defer.reject(data);
                console.log('error');
            });

            return defer.promise;
        },

        current: function() {
            var defer = $q.defer();

            $http.get('/api/actividad/current')
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