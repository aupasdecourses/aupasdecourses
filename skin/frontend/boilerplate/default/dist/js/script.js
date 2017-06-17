function msieversion(){var t=window.navigator.userAgent,i=t.indexOf("MSIE "),e=t.indexOf("Edge/");return i>0||navigator.userAgent.match(/Trident.*rv\:11\./)?$j("body").addClass("ie"):e>0&&$j("body").addClass("edge"),!1}!function(t,i){"function"==typeof define&&define.amd?define(["jquery"],i):"object"==typeof exports?module.exports=i(require("jquery")):t.lightbox=i(t.jQuery)}(this,function(t){function i(i){this.album=[],this.currentImageIndex=void 0,this.init(),this.options=t.extend({},this.constructor.defaults),this.option(i)}return i.defaults={albumLabel:"Image %1 of %2",alwaysShowNavOnTouchDevices:!1,fadeDuration:600,fitImagesInViewport:!0,imageFadeDuration:600,positionFromTop:50,resizeDuration:700,showImageNumberLabel:!0,wrapAround:!1,disableScrolling:!1,sanitizeTitle:!1},i.prototype.option=function(i){t.extend(this.options,i)},i.prototype.imageCountLabel=function(t,i){return this.options.albumLabel.replace(/%1/g,t).replace(/%2/g,i)},i.prototype.init=function(){var i=this;t(document).ready(function(){i.enable(),i.build()})},i.prototype.enable=function(){var i=this;t("body").on("click","a[rel^=lightbox], area[rel^=lightbox], a[data-lightbox], area[data-lightbox]",function(e){return i.start(t(e.currentTarget)),!1})},i.prototype.build=function(){var i=this;t('<div id="lightboxOverlay" class="lightboxOverlay"></div><div id="lightbox" class="lightbox"><div class="lb-outerContainer"><div class="lb-container"><img class="lb-image" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" /><div class="lb-nav"><a class="lb-prev" href="" ></a><a class="lb-next" href="" ></a></div><div class="lb-loader"><a class="lb-cancel"></a></div></div></div><div class="lb-dataContainer"><div class="lb-data"><div class="lb-details"><span class="lb-caption"></span><span class="lb-number"></span></div><div class="lb-closeContainer"><a class="lb-close"></a></div></div></div></div>').appendTo(t("body")),this.$lightbox=t("#lightbox"),this.$overlay=t("#lightboxOverlay"),this.$outerContainer=this.$lightbox.find(".lb-outerContainer"),this.$container=this.$lightbox.find(".lb-container"),this.$image=this.$lightbox.find(".lb-image"),this.$nav=this.$lightbox.find(".lb-nav"),this.containerPadding={top:parseInt(this.$container.css("padding-top"),10),right:parseInt(this.$container.css("padding-right"),10),bottom:parseInt(this.$container.css("padding-bottom"),10),left:parseInt(this.$container.css("padding-left"),10)},this.imageBorderWidth={top:parseInt(this.$image.css("border-top-width"),10),right:parseInt(this.$image.css("border-right-width"),10),bottom:parseInt(this.$image.css("border-bottom-width"),10),left:parseInt(this.$image.css("border-left-width"),10)},this.$overlay.hide().on("click",function(){return i.end(),!1}),this.$lightbox.hide().on("click",function(e){return"lightbox"===t(e.target).attr("id")&&i.end(),!1}),this.$outerContainer.on("click",function(e){return"lightbox"===t(e.target).attr("id")&&i.end(),!1}),this.$lightbox.find(".lb-prev").on("click",function(){return 0===i.currentImageIndex?i.changeImage(i.album.length-1):i.changeImage(i.currentImageIndex-1),!1}),this.$lightbox.find(".lb-next").on("click",function(){return i.currentImageIndex===i.album.length-1?i.changeImage(0):i.changeImage(i.currentImageIndex+1),!1}),this.$nav.on("mousedown",function(t){3===t.which&&(i.$nav.css("pointer-events","none"),i.$lightbox.one("contextmenu",function(){setTimeout(function(){this.$nav.css("pointer-events","auto")}.bind(i),0)}))}),this.$lightbox.find(".lb-loader, .lb-close").on("click",function(){return i.end(),!1})},i.prototype.start=function(i){function e(t){n.album.push({link:t.attr("href"),title:t.attr("data-title")||t.attr("title")})}var n=this,o=t(window);o.on("resize",t.proxy(this.sizeOverlay,this)),t("select, object, embed").css({visibility:"hidden"}),this.sizeOverlay(),this.album=[];var a,r=0,s=i.attr("data-lightbox");if(s){a=t(i.prop("tagName")+'[data-lightbox="'+s+'"]');for(var h=0;h<a.length;h=++h)e(t(a[h])),a[h]===i[0]&&(r=h)}else if("lightbox"===i.attr("rel"))e(i);else{a=t(i.prop("tagName")+'[rel="'+i.attr("rel")+'"]');for(var d=0;d<a.length;d=++d)e(t(a[d])),a[d]===i[0]&&(r=d)}var l=o.scrollTop()+this.options.positionFromTop,c=o.scrollLeft();this.$lightbox.css({top:l+"px",left:c+"px"}).fadeIn(this.options.fadeDuration),this.options.disableScrolling&&t("body").addClass("lb-disable-scrolling"),this.changeImage(r)},i.prototype.changeImage=function(i){var e=this;this.disableKeyboardNav();var n=this.$lightbox.find(".lb-image");this.$overlay.fadeIn(this.options.fadeDuration),t(".lb-loader").fadeIn("slow"),this.$lightbox.find(".lb-image, .lb-nav, .lb-prev, .lb-next, .lb-dataContainer, .lb-numbers, .lb-caption").hide(),this.$outerContainer.addClass("animating");var o=new Image;o.onload=function(){var a,r,s,h,d,l,c;n.attr("src",e.album[i].link),a=t(o),n.width(o.width),n.height(o.height),e.options.fitImagesInViewport&&(c=t(window).width(),l=t(window).height(),d=c-e.containerPadding.left-e.containerPadding.right-e.imageBorderWidth.left-e.imageBorderWidth.right-20,h=l-e.containerPadding.top-e.containerPadding.bottom-e.imageBorderWidth.top-e.imageBorderWidth.bottom-120,e.options.maxWidth&&e.options.maxWidth<d&&(d=e.options.maxWidth),e.options.maxHeight&&e.options.maxHeight<d&&(h=e.options.maxHeight),(o.width>d||o.height>h)&&(o.width/d>o.height/h?(s=d,r=parseInt(o.height/(o.width/s),10),n.width(s),n.height(r)):(r=h,s=parseInt(o.width/(o.height/r),10),n.width(s),n.height(r)))),e.sizeContainer(n.width(),n.height())},o.src=this.album[i].link,this.currentImageIndex=i},i.prototype.sizeOverlay=function(){this.$overlay.width(t(document).width()).height(t(document).height())},i.prototype.sizeContainer=function(t,i){function e(){n.$lightbox.find(".lb-dataContainer").width(r),n.$lightbox.find(".lb-prevLink").height(s),n.$lightbox.find(".lb-nextLink").height(s),n.showImage()}var n=this,o=this.$outerContainer.outerWidth(),a=this.$outerContainer.outerHeight(),r=t+this.containerPadding.left+this.containerPadding.right+this.imageBorderWidth.left+this.imageBorderWidth.right,s=i+this.containerPadding.top+this.containerPadding.bottom+this.imageBorderWidth.top+this.imageBorderWidth.bottom;o!==r||a!==s?this.$outerContainer.animate({width:r,height:s},this.options.resizeDuration,"swing",function(){e()}):e()},i.prototype.showImage=function(){this.$lightbox.find(".lb-loader").stop(!0).hide(),this.$lightbox.find(".lb-image").fadeIn(this.options.imageFadeDuration),this.updateNav(),this.updateDetails(),this.preloadNeighboringImages(),this.enableKeyboardNav()},i.prototype.updateNav=function(){var t=!1;try{document.createEvent("TouchEvent"),t=this.options.alwaysShowNavOnTouchDevices?!0:!1}catch(i){}this.$lightbox.find(".lb-nav").show(),this.album.length>1&&(this.options.wrapAround?(t&&this.$lightbox.find(".lb-prev, .lb-next").css("opacity","1"),this.$lightbox.find(".lb-prev, .lb-next").show()):(this.currentImageIndex>0&&(this.$lightbox.find(".lb-prev").show(),t&&this.$lightbox.find(".lb-prev").css("opacity","1")),this.currentImageIndex<this.album.length-1&&(this.$lightbox.find(".lb-next").show(),t&&this.$lightbox.find(".lb-next").css("opacity","1"))))},i.prototype.updateDetails=function(){var i=this;if("undefined"!=typeof this.album[this.currentImageIndex].title&&""!==this.album[this.currentImageIndex].title){var e=this.$lightbox.find(".lb-caption");this.options.sanitizeTitle?e.text(this.album[this.currentImageIndex].title):e.html(this.album[this.currentImageIndex].title),e.fadeIn("fast").find("a").on("click",function(){void 0!==t(this).attr("target")?window.open(t(this).attr("href"),t(this).attr("target")):location.href=t(this).attr("href")})}if(this.album.length>1&&this.options.showImageNumberLabel){var n=this.imageCountLabel(this.currentImageIndex+1,this.album.length);this.$lightbox.find(".lb-number").text(n).fadeIn("fast")}else this.$lightbox.find(".lb-number").hide();this.$outerContainer.removeClass("animating"),this.$lightbox.find(".lb-dataContainer").fadeIn(this.options.resizeDuration,function(){return i.sizeOverlay()})},i.prototype.preloadNeighboringImages=function(){if(this.album.length>this.currentImageIndex+1){var t=new Image;t.src=this.album[this.currentImageIndex+1].link}if(this.currentImageIndex>0){var i=new Image;i.src=this.album[this.currentImageIndex-1].link}},i.prototype.enableKeyboardNav=function(){t(document).on("keyup.keyboard",t.proxy(this.keyboardAction,this))},i.prototype.disableKeyboardNav=function(){t(document).off(".keyboard")},i.prototype.keyboardAction=function(t){var i=27,e=37,n=39,o=t.keyCode,a=String.fromCharCode(o).toLowerCase();o===i||a.match(/x|o|c/)?this.end():"p"===a||o===e?0!==this.currentImageIndex?this.changeImage(this.currentImageIndex-1):this.options.wrapAround&&this.album.length>1&&this.changeImage(this.album.length-1):("n"===a||o===n)&&(this.currentImageIndex!==this.album.length-1?this.changeImage(this.currentImageIndex+1):this.options.wrapAround&&this.album.length>1&&this.changeImage(0))},i.prototype.end=function(){this.disableKeyboardNav(),t(window).off("resize",this.sizeOverlay),this.$lightbox.fadeOut(this.options.fadeDuration),this.$overlay.fadeOut(this.options.fadeDuration),t("select, object, embed").css({visibility:"visible"}),this.options.disableScrolling&&t("body").removeClass("lb-disable-scrolling")},new i}),jQuery.noConflict(),function(t){"use strict";function i(){this.windowLoaded=!1}i.prototype={constructor:i,start:function(){var i=this;t(window).load(function(){i.windowLoaded=!0}),this.attach()},attach:function(){this.attachBootstrapPrototypeCompatibility(),this.attachMedia()},attachBootstrapPrototypeCompatibility:function(){t("*").on("show.bs.dropdown show.bs.collapse",function(i){t(i.target).addClass("bs-prototype-override")}),t("*").on("hidden.bs.collapse",function(i){t(i.target).removeClass("bs-prototype-override")})},attachMedia:function(){var i=t('[data-toggle="media"]');i.length&&i.on("click",function(i){i.preventDefault();var e=t(this),n=t(e.attr("href")),o=n.find(".carousel"),a=parseInt(e.data("index"));return o.carousel(a),n.modal("show"),!1})}},jQuery(document).ready(function(){var t=new i;t.start()})}(jQuery),function(){var t=!1;if(window.jQuery){var i=jQuery("*");jQuery.each(["hide.bs.dropdown","hide.bs.collapse","hide.bs.modal","hide.bs.tooltip","hide.bs.popover"],function(e,n){i.on(n,function(){t=!0})})}var e=Element.hide;Element.addMethods({hide:function(i){return t?(t=!1,i):e(i)}})}();var color_menu={Primeur:"#3ab64b",Boucher:"#f14556",Fromager:"#f8a564",Poissonnier:"#5496d7",Caviste:"#e83632",Boulanger:"#f3a71c","Epicerie Fine":"#2f4da8",Epicier:"#2f4da8","Café & Thé":"#f36520",Traiteur:"#f36520"};$j(document).ready(function(){for(var t in color_menu)$j("#custommenu .parentMenu span:contains('"+t+"')").parent().css("background",color_menu[t]);msieversion()});