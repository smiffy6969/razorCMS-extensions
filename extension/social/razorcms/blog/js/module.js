require(["angular", "../../extension/social/razorcms/blog/js/controller"], function(angular)
{
    angular.module("extension.social.razorcms.blog", ["extension.social.razorcms.blog.controller"]);
    angular.bootstrap(document.querySelectorAll(".social-razorcms-blog"), ["extension.social.razorcms.blog"]);
});