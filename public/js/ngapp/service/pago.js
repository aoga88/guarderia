function Pago($http, $q) {
    return {
        registrar: function(guarderia, indexPago) {
            var defer     = $q.defer();

            guarderia._id = guarderia._id.$id;
            var postData = {
                guarderia: guarderia,
                indexPago: indexPago
            };

            $http.post('/api/pago', postData)
            .success( function(data) {
                defer.resolve(data);
            })
            .error( function(data) {
                defer.reject(data);
            });

            return defer.promise;
        }
    }
}