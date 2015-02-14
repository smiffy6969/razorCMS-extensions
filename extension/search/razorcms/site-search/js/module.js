require(["angular", "../../extension/search/razorcms/site-search/js/controller"], function(angular)
{
    angular.module("extension.search.razorcms.siteSearch", ["extension.search.razorcms.siteSearch.controller"]);
    angular.bootstrap(document.querySelectorAll(".search-razorcms-site-search"), ["extension.search.razorcms.siteSearch"]);
});