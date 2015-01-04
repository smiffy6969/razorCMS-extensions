define(["angular", "cookie-monster", "jquery", "summernote", "ui-bootstrap", "razor/services/rars", "razor/directives/notification"], function(angular, monster)
{
    angular.module("extension.social.razorcms.blog.controller", ["razor.services.rars", "razor.directives.notification", "ui.bootstrap"])

    .controller("blog", function($scope, $rootScope, $sce, rars, $timeout, $modal, $location)
    {
        $scope.newItemTitle = null;
        $scope.blogName = null;
        $scope.itemQuantity = 11;

        $scope.init = function(blogName, blogId)
        {
            $scope.blogName = blogName;

            if (!blogId)
            {
                // fetch blog dump for main view
                rars.get("extension/social/razorcms/blog/book", blogName).success(function(data)
                {
                    $scope.items = data.items;
                }).error(function(data, code)
                {
                    if (code == 404)
                    {
                        // attempt to create blog name
                        rars.put("extension/social/razorcms/blog/book", {"blog_name": blogName}, monster.get("token")).success(function(data)
                        {
                             // fetch blog dump for main view
                            rars.get("extension/social/razorcms/blog/book", blogName).success(function(data)
                            {
                                $scope.items = data.items;
                            }).error(function(data, code)
                            {
                                $scope.blogError = true;
                            });                    
                        }).error(function(data, code)
                        {
                            $scope.blogError = true;
                        });                       
                    }
                    else
                    {
                        $scope.blogError = true;
                    }
                });
            }
            else
            {
                // search for blog entry
                rars.get("extension/social/razorcms/blog/item", blogId).success(function(data)
                {
                    $scope.item = data;
                }).error(function(data)
                {
                    $scope.blogError = true;
                });
            }
        };

        $scope.viewItem = function(item)
        {
            location.href = "?id=" +  item.id + "&blog=" + item.title;
        };

        $scope.editBlogItem = function()
        {
            // cache title so we can revert if cancelled
            $scope.newItemTitle = $scope.item.title;

            // start summernote and ensure callback for file uploading
            $("#blog-content").summernote({
                onImageUpload: function(files, editor, welEditable) 
                {
                    rars.post("file/image", {"files": files}, monster.get("token")).success(function(data)
                    {
                        for (var i = 0; i < data.files.length; i++) 
                        {
                            editor.insertImage(welEditable, data.files[i].url);
                        };                      
                    }).error(function(data)
                    {
                        $rootScope.$broadcast("global-notification", {"type": "danger", "text": "Could not upload image, please try again."});
                    });
                },
                onImageUploadError: function() 
                {
                    $rootScope.$broadcast("global-notification", {"type": "danger", "text": "Could not upload image, please try again."});
                }
            });  

            $scope.editBlogTitle = true;  
        };

        $scope.saveBlogItem = function()
        {
            // ensure we are no longer adding new
            $scope.newBlogEntry = false;

            // catch editor changes
            $scope.item.content = $("#blog-content").code();
            $scope.item.title = $scope.newItemTitle;

            // save all content for page
            rars.put("extension/social/razorcms/blog/item", {"item": $scope.item, "blog_name": $scope.blogName}, monster.get("token")).success(function(data)
            { 
                $scope.item = data;

                // stop edit
                $scope.viewBlogItem();
            }).error(function()
            {
                
            }); 
        };

        $scope.viewBlogItem = function(cancel)
        {
            // ensure content is up to date
            $("#blog-content").code($scope.item.content);
            if (!cancel) $scope.item.title = $scope.newItemTitle;

            // close editor
            if (cancel && $scope.newBlogEntry) delete $scope.item;
            $("#blog-content").destroy();
            $scope.editBlogTitle = false;
            $scope.newBlogEntry = false;
        };

        $scope.viewAllItems = function()
        {
            location.href = "?";
        }

        $scope.createBlogEntry = function()
        {
            // hide multi layout
            $scope.newBlogEntry = true;

            // show blog edit
            $scope.item = {"title": "", "content": ""};
            $timeout($scope.editBlogItem, 1); // need to push this to end of stack
        };

        $scope.showMore = function()
        {
            $scope.itemQuantity += 12;
        };

        $scope.resetQuantity = function()
        {
            $scope.itemQuantity = 11;
        };

        $scope.bindHtml = function(html)
        {
            // required due to deprecation of html-bind-unsafe
            return $sce.trustAsHtml(html);
        };

        $scope.deleteBlogItem = function()
        {           
            $modal.open(
            {
                template: "<div class=\"modal-body\">" +    
                            "<p class=\"text-center\">Are you sure you want to delete this blog entry?</p>" +
                        "</div>" +
                        "<div class=\"modal-footer\">" +
                            "<button class=\"btn btn-default\" ng-click=\"cancel()\"><i class=\"fa fa-times\"></i> Cancel</button>" +
                            "<button class=\"btn btn-primary\" ng-click=\"yes()\"><i class=\"fa fa-check\"></i> Yes</button>" +
                        "</div>",
                controller: "areYourSure"
            }).result.then(function(answer)
            {
                rars.delete("extension/social/razorcms/blog/item", $scope.item.id, monster.get("token")).success(function(data)
                {
                    location.href = "?";
                }).error(function(data, code)
                {
                    $rootScope.$broadcast("global-notification", {"type": "danger", "text": "Could not remove blog item, please try again."});
                });  
            });
        };

        $scope.shareLink = function()
        {
            return $location.absUrl();
        };

        $scope.urlEncode = function(text)
        {
            return encodeURIComponent(text);
        };

        $scope.loadDisqus = function(shortname)
        {
            if ($scope.editBlogTitle) return;

            // disqus load code pushing to end of stack
            $timeout(function()
            {
                var dsq = document.createElement('script');
                dsq.type = 'text/javascript'; 
                dsq.async = true;
                dsq.src = '//' + shortname + '.disqus.com/embed.js';
                (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
            }, 1);
        };
    })

    .controller("areYourSure", function($scope, $modalInstance)
    {
        $scope.cancel = function()
        {
            $modalInstance.dismiss('cancel');
        };

        $scope.yes = function()
        {
            $modalInstance.close(true);
        };  
    });
});