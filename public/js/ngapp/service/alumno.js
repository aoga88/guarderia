function Alumno($http, $q) {
    return {
        save: function(entityApp, id) {
            var defer     = $q.defer();
            entityApp._id = id;

            $http.post('/api/alumno', entityApp)
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

            $http.post('/api/alumno/find', conditions)
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

            $http.get('/api/alumno/current')
            .success( function(data) {
                defer.resolve(data);
            })
            .error( function(data) {
                defer.reject(data);
            });

            return defer.promise;
        },

        asistencia: function(alumno_id, asistencia) {
            var defer = $q.defer();

            $http.post('/api/alumno/' + alumno_id + '/asistencia', asistencia)
            .success( function(data) {
                defer.resolve(data);
            })
            .error( function(data) {
                defer.reject(data);
            });

            return defer.promise;
        },

        salida: function(alumno_id, asistencia) {
            var defer = $q.defer();

            $http.post('/api/alumno/' + alumno_id + '/salida', asistencia)
            .success( function(data) {
                defer.resolve(data);
            })
            .error( function(data) {
                defer.reject(data);
            });

            return defer.promise;
        },

        comentar: function(alumno_id, mensaje) {
            var defer = $q.defer();

            $http.post('/api/alumno/' + alumno_id + '/comment', mensaje)
            .success( function(data) {
                defer.resolve(data);
            })
            .error( function(data) {
                defer.reject(data);
            });

            return defer.promise;
        },
    }
};