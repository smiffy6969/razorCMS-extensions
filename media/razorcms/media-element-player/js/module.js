require(["angular", "../../extension/media/razorcms/media-element-player/js/controller"], function(angular)
{
    angular.module("extension.media.razorcms.mediaElementPlayer", ["extension.media.razorcms.mediaElementPlayer.controller"]);
    angular.bootstrap(document.querySelectorAll(".media-razorcms-media-element-player"), ["extension.media.razorcms.mediaElementPlayer"]);
});