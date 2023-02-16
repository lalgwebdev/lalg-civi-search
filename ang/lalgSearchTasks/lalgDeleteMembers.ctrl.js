(function(angular, $, _) {
  'use strict';
  
console.log('Entering lalgDeleteMembers.ctrl.js');

  angular.module('lalgSearchTasks').controller('lalgDeleteMembers', function($scope, $http,
    searchTaskBaseTrait, $timeout, $interval) {
	  
console.log('Entering Controller');	  

    var ts = $scope.ts = CRM.ts('org.civicrm.search_kit'),
      // Combine this controller with model properties (ids, entity, entityInfo) and searchTaskBaseTrait
      ctrl = angular.extend(this, $scope.model, searchTaskBaseTrait);
	  
	this.entityTitle = this.getEntityTitle();
    this.progress = null;

    this.doDelete = function() {
      ctrl.progress = 0;
//      $('.ui-dialog-titlebar button').hide();
      // Show the user something is happening (even though it doesn't accurately reflect progress)
      var incrementer = $interval(function() {
        if (ctrl.progress < 90) {
          ctrl.progress += 10;
        }
      }, 1000);
	  
	  
	  
          $timeout(function() {
            CRM.alert(ts('Contacts have been deleted.'), 'success');
          }, 1000);	  

    };



    $scope.details = 'Details in $scope.details';
	ctrl.details = 'Details in ctrl.details';
	
console.log(ctrl);
console.log($scope);
console.log(this);	
	  

  });

})(angular, CRM.$, CRM._);
