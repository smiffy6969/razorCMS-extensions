define(["angular", "razor/services/rars", ], function(angular)
{
    angular.module("extension.communication.razorcms.contactForm.controller", ["razor.services.rars"])

    .controller("main", function($scope, rars)
    {
        $scope.extension = {"type": "communication", "handle": "razorcms", "extension": "contact-form"};
        $scope.processing = null;
        $scope.robot = null;
        $scope.error = null;

        $scope.init = function()
        {
            // contruct function for extension
        };

        $scope.send = function()
        {
            $scope.processing = true;
            $scope.error = false;

            rars.post("extension/communication/razorcms/contact-form/email", {"signature": $scope.signature, "email": $scope.email, "message": $scope.message, "human": $scope.human, "extension": $scope.extension})
                .success(function(data)
                {
                    $scope.response = true;
                })
                .error(function(data, header)
                {
                    if (data.response == "robot") $scope.robot = true;
                    else
                    {
                        $scope.error = true;
                        $scope.processing = null;
                    }
                }
            );
        };
    });
});
