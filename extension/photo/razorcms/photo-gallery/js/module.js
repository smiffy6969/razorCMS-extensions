require(["angular", "../../extension/photo/razorcms/photo-gallery/js/controller"], function(angular)
{
    angular.module("extension.photo.razorcms.photoGallery", ["extension.photo.razorcms.photoGallery.controller"]);
    angular.bootstrap(document.querySelectorAll(".photo-razorcms-photo-gallery"), ["extension.photo.razorcms.photoGallery"]);
});