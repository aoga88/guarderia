function User($http, $q) {
    return {
        login: function(user) {
            var defer = $q.defer();

            var postUser = {
                email: user.email,
                password: SHA1(user.password)
            };

            $http.post('/api/login', postUser)
            .success( function(data) {
                defer.resolve(data);
            })
            .error( function(data) {
                defer.reject(data);
            });

            return defer.promise;
        },

        logout: function() {
            var defer = $q.defer();

            $http.get('/api/logout')
            .success( function(data) {
                defer.resolve(data);
            })
            .error( function(data) {
                defer.reject(data);
            });

            return defer.promise;
        },

        fromApp: function(appId) {
            var defer = $q.defer();

            $http.get('/api/app/' + appId + '/users')
            .success( function(data) {
                defer.resolve(data);
            })
            .error( function(data) {
                defer.reject(data);
            });

            return defer.promise;
        },

        adminsFromApp: function(appId) {
            var defer = $q.defer();

            $http.get('/api/app/' + appId + '/admins')
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

            $http.post('/api/user/find', conditions)
            .success( function(data) {
                defer.resolve(data);
            })
            .error( function(data) {
                defer.reject(data);
            });

            return defer.promise;
        },

        new: function(entity) {
            var defer     = $q.defer();

            $http.post('/api/user/new', entity)
            .success( function(data) {
                defer.resolve(data);
            })
            .error( function(data) {
                defer.reject(data);
            });

            return defer.promise;
        },

        save: function(entity) {
            var defer     = $q.defer();

            $http.post('/api/user', entity)
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

            $http.get('/api/user/current')
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