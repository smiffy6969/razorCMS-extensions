// configure new module and deps
require.config({
    paths: { "media-element-player": "../../extension/media/razorcms/media-element-player/js/mediaelement-and-player.min" },
    shim: { "media-element-player": { deps: ["jquery"] } }
});

// define new module
define(["angular", "jquery", "media-element-player"], function(angular, $, MediaElementPlayer)
{
    angular.module("extension.media.razorcms.mediaElementPlayer.controller", [])

    .controller("mediaElementPlayer", function($scope)
    {
        $scope.tracks = 0;
        $scope.selected = 1;
    	$scope.playing = null;
        $scope.player = $(".media-element-player").mediaelementplayer();

        // playing
        $(".media-element-player").bind("playing", function()
        {
            $scope.$apply(function()
            {
                $scope.playing = $scope.selected;
            });
        });

        // paused
        $(".media-element-player").bind("pause", function()
        {
            $scope.$apply(function()
            {
                $scope.playing = false;
            });
        });

        // ended
        $(".media-element-player").bind("ended", function()
        {
            $scope.$apply(function()
            {
                $scope.playing = false;
                if ($scope.selected < $scope.tracks.length)
                {
                    $scope.changeTrack($scope.tracks[$scope.selected].url, $scope.selected +1);

                }
            });
        });

        $scope.changeTrack = function(url, index)
        {
        	// change selected
        	$scope.selected = index;

        	// update audio source and play
         	$scope.player[0].setSrc(url);
         	$scope.player[0].play();
        };
    });
});