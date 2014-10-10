function Notificacion($http, $q) {
    return {
        current: function() {
            var defer     = $q.defer();

            $http.get('/api/notifications')
            .success( function(data) {
                defer.resolve(data);
            })
            .error( function(data) {
                defer.reject(data);
            });

            return defer.promise;
        },

        leido: function(notificacion) {
            var defer     = $q.defer();

            $http.get('/api/notifications/' + notificacion._id.$id + '/read')
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