function Registro($http, $q) {
    return {
        save: function(entityApp, id) {
            var defer     = $q.defer();
            entityApp._id = id;

            $http.post('/api/registro', entityApp)
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

            $http.post('/api/registro/find', conditions)
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

            $http.get('/api/registro/current')
            .success( function(data) {
                defer.resolve(data);
            })
            .error( function(data) {
                defer.reject(data);
            });

            return defer.promise;
        },

        actividades: function(info) {
            var defer = $q.defer();

            $http.pos('/api/registro/find', info)
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