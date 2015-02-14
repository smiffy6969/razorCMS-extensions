define(["angular", "cookie-monster", "ui-bootstrap", "razor/services/rars", "razor/directives/form-controls", "razor/directives/notification"], function(angular, monster)
{
    angular.module("extension.photo.razorcms.photoGallery.controller", ["ui.bootstrap", "razor.services.rars", "razor.directives.formControls", "razor.directives.notification"])

    .controller("photoGallery", function($scope, $rootScope, $timeout, $modal, rars)
    {
        $scope.photos = [];
        $scope.photoFrame = null;
        $scope.photoFrameHelper = null;
        $scope.position = 0;
        $scope.sliderListStyle = {"width": "0px", "margin-left": "10px"};
        $scope.helperCanvasStyle = {"z-index": "-1px"};

        $scope.init = function(albumName)
        {
            rars.get("extension/photo/razorcms/photo-gallery/image", albumName, monster.get("token")).success(function(data)
            {
                $scope.photos = data.images;
                $scope.setSliderWidth();
                if ($scope.photos.length > 0) $scope.photoFrame = $scope.photos[0];
            });
        };

        $scope.setSliderWidth = function()
        {
            $scope.sliderListStyle["width"] = ($scope.photos.length < 1 ? "0px" :  ($scope.photos.length * 85) + "px");
        };

        $scope.scrollThumbs = function(direction)
        {
        	var sliderFrameWidth = document.querySelector(".photo-gallery-slider").offsetWidth;
            var sliderWidth = $scope.sliderListStyle["width"].substring(0, $scope.sliderListStyle["width"].length - 2)
            var margin = parseInt($scope.sliderListStyle["margin-left"].substring(0, $scope.sliderListStyle["margin-left"].length - 2))

            if (direction == "right" && sliderWidth > sliderFrameWidth && margin > (sliderWidth - sliderWidth - sliderWidth) + sliderFrameWidth)
            {
                $scope.sliderListStyle["margin-left"] = margin - 85 + "px";
            }

            if (direction == "left" && margin < 0)
            {
                $scope.sliderListStyle["margin-left"] = margin + 85 + "px";
            }
        };

        $scope.scrollPhotos = function(direction)
        {
            if (direction == "left" && $scope.position > 0)
            {
                $scope.position--;
                $scope.changePhoto();
            }
            else if (direction == "right" && $scope.position < $scope.photos.length - 1)
            {
                $scope.position++;
                $scope.changePhoto();
            }
        };

        $scope.selectPhoto = function(index)
        {
            $scope.position = index;
            $scope.changePhoto();
        };

        $scope.changePhoto = function()
        {
            $scope.showBox = false;
            $scope.turnPhoto = true;
            
            // change helper and reset
            $timeout(function() 
            {
                $scope.photoFrame = $scope.photos[$scope.position];
                    $timeout(function()
                    {
                        $scope.turnPhoto = false;
                        $scope.showBox = true;
                    }, 300);
            }, 300);
            
        };

        $scope.manageImages = function(albumName)
        {
            $modal.open(
            {
                templateUrl: RAZOR_BASE_URL + "extension/photo/razorcms/photo-gallery/partial/modal/manage-images.html",
                controller: "manageImagesModal",
                resolve: {
                    albumName: function(){ return albumName; }
                }
            }).result.then(function()
            {
                $scope.init(albumName);
            });
        };

        $scope.removeImage = function(albumName, index)
        {
            rars.delete("extension/photo/razorcms/photo-gallery/image", albumName + "|" + $scope.photos[index].name, monster.get("token")).success(function(data)
            {
                $scope.photos.splice(index, 1);
                $scope.position = 0;
                if ($scope.photos.length > 0) $scope.photoFrame = $scope.photos[0];
                else $scope.photoFrame = null;
            }).error(function(data, header)
            {
                $rootScope.$broadcast("global-notification", {"type": "danger", "text": "Could not remove file, please try again."}); 
            });
        };
    })

    .controller("manageImagesModal", function($scope, $rootScope, $modalInstance, rars, albumName)
    {
        $scope.albumName = albumName;
        $scope.newImages = null;

        $scope.cancel = function()
        {
            $modalInstance.dismiss('cancel');
        }; 

        $scope.uploadImages = function()
        {
            rars.post("extension/photo/razorcms/photo-gallery/image", {"album_name": albumName, "files": $scope.newImages}, monster.get("token")).success(function(data)
            {
                $rootScope.$broadcast("global-notification", {"type": "success", "text": "Files uploaded."}); 
                $modalInstance.close();
            }).error(function(data, header)
            {
                if (header == 406) $rootScope.$broadcast("global-notification", {"type": "danger", "text": "Only images files (jpg, png, gif) less than 8Mb allowed."}); 
                else $rootScope.$broadcast("global-notification", {"type": "danger", "text": "Could not upload images to gallery, please try again."});
            });
        };

        $scope.overLimit = function()
        {
            var total = 0;
            angular.forEach($scope.newImages, function(image) { total += image.size; });
            if (total > 7500000) return true;
            else return false;            
        };
    });
});