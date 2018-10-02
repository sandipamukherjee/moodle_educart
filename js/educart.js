var app = angular.module("educartapp", ["ngRoute"]);
/*app.factory('notepadFact', function($http) {
	return {
        get_user : function($userid, $url) {
            //console.log($url);
            var ajaxData_after_user_msg = $http.post($url+'get_current_user_message.php');
            return (ajaxData_after_user_msg.then());
        },
		save : function ($userid, $message, $url) {
            //console.log($url);
            return $http({      
              method: 'POST',
              url:  $url+'save.php',
              data: {userid: $userid, message: $message}
                     
            }).then(function (response) {// on success
                //alert('Save successfully');
                return response;
            }, function (response) {
                console.log(response.data,response.status);       
            });
        },
	}
});
app.controller('notepadCtrl', ['$scope', 'notepadFact', '$interval', function($scope, notepadFact, $interval) {
    $scope.saving = false;
    console.log($scope.saving);
    $scope.$watch('url', function(){
    //alert($scope.url);
    });
    //Watch you ngInit value
    $scope.$watch('userid', function(){
    	//When your value is set, you can retrieve it

    	notepadFact.get_user($scope.userid, $scope.url).then( function(response){
            
            if(response.data) {
                $scope.message = response.data;
                console.log(response.data);
            } else {
                $scope.message = "....";
            }
        });
    });

    $scope.save = function($userid, $message, $url) {
        $scope.saving = true;
        console.log($scope.saving);
        notepadFact.save($userid, $message, $url).then( function(response){
            if(response.data) {
                $scope.saving = false;
            }
        });
    }
    $interval( function(){ 
        
        $scope.save($scope.userid, $scope.message, $scope.url); 
    }, 5000);
}]);*/