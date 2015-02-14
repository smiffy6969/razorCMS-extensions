define(["angular", "ui-bootstrap", "razor/services/rars"], function(angular)
{
    angular.module("extension.search.razorcms.siteSearch.controller", ["ui.bootstrap", "razor.services.rars"])

    .controller("siteSearch", function($scope, $modal, rars)
    {
        $scope.searchPhrase = null;
        $scope.searchResults = null;

        $scope.search = function()
        {
            // tweak the width of the result box
            angular.forEach(document.querySelectorAll('.search-razorcms-site-search-form'), function(el)
            {
                angular.element(el).next().children()[0].style.width = el.offsetWidth + 'px';
            });
            // document.querySelector('#search-razorcms-site-search-results').style.width = formWidth + 'px';

            $scope.searchResults = null;

            rars.get("extension/search/razorcms/site-search/find", $scope.searchPhrase).success(function(data)
            {
                $scope.searchResults = data.results;
            }).error(function(data, header)
            {
                // if (header == 406) $rootScope.$broadcast("global-notification", {"type": "danger", "text": "Only images files (jpg, png, gif) less than 8Mb allowed."}); 
                // else $rootScope.$broadcast("global-notification", {"type": "danger", "text": "Could not upload images to gallery, please try again."});
            });
        }

        $scope.searchUrl = function(link)
        {
            return RAZOR_BASE_URL + link;
        }
    });
});