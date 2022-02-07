!function(jQuery,undefined){function createCookie(e,t,a){var o;if(a){var i=new Date;i.setTime(i.getTime()+24*a*60*60*1e3),o="; expires="+i.toGMTString()}else o="";document.cookie=encodeURIComponent(e)+"="+encodeURIComponent(t)+o+"; path=/"}function readCookie(e){for(var t=encodeURIComponent(e)+"=",a=document.cookie.split(";"),o=0;o<a.length;o++){for(var i=a[o];" "===i.charAt(0);)i=i.substring(1,i.length);if(0===i.indexOf(t))return decodeURIComponent(i.substring(t.length,i.length))}return null}function eraseCookie(e){createCookie(e,"",-1)}function checkAvailableFilters(){}function checkMoreToLoad(e,t){var a=new Array;fidlist=new Array,searchchanged=jQuery(t.filterGroupClass+".esg-filter-wrapper.eg-search-wrapper .eg-justfilteredtosearch").length,forcesearch=jQuery(t.filterGroupClass+".esg-filter-wrapper.eg-search-wrapper .eg-forcefilter").length,jQuery(t.filterGroupClass+".esg-filter-wrapper .esg-filterbutton.selected, "+t.filterGroupClass+" .esg-filter-wrapper .esg-filterbutton.selected").each(function(){var e=jQuery(this).data("fid");-1==jQuery.inArray(e,fidlist)&&(a.push(e),fidlist.push(e))}),0==jQuery(t.filterGroupClass+".esg-filter-wrapper .esg-filterbutton.selected, "+t.filterGroupClass+" .esg-filter-wrapper .esg-filterbutton.selected").length&&a.push(-1);for(var o=new Array,i=0;i<t.loadMoreItems.length;i++)jQuery.each(t.loadMoreItems[i][1],function(e,r){jQuery.each(a,function(e,a){a==r&&-1!=t.loadMoreItems[i][0]&&(0==forcesearch||1==forcesearch&&"cat-searchresult"===t.loadMoreItems[i][2])&&o.push(t.loadMoreItems[i])})});return addCountSuffix(e,t),o}function addCountSuffix(e,t){var a=jQuery(t.filterGroupClass+".esg-filter-wrapper.eg-search-wrapper .eg-justfilteredtosearch").length,o=jQuery(t.filterGroupClass+".esg-filter-wrapper.eg-search-wrapper .eg-forcefilter").length;jQuery(t.filterGroupClass+".esg-filter-wrapper.eg-show-amount .esg-filterbutton").each(function(){var i=jQuery(this);if(0==i.find(".eg-el-amount").length||a>0){var r=i.data("fid"),n=i.data("filter");o>0&&(n+=".cat-searchresult");for(var s=e.find("."+n).length,l=0;l<t.loadMoreItems.length;l++){0==o?jQuery.each(t.loadMoreItems[l][1],function(e,a){a===r&&-1!=t.loadMoreItems[l][0]&&s++}):-1!=jQuery.inArray(r,t.loadMoreItems[l][1])&&"cat-searchresult"===t.loadMoreItems[l][2]&&s++}0==i.find(".eg-el-amount").length&&i.append('<span class="eg-el-amount">0</span>'),countToTop(i,s)}}),jQuery(t.filterGroupClass+".esg-filter-wrapper.eg-search-wrapper .eg-justfilteredtosearch").removeClass("eg-justfilteredtosearch")}function countToTop(e,t){function a(e,t){o.html(Math.round(e.target[t]))}var o=e.find(".eg-el-amount"),i={value:parseInt(o.text(),0)};punchgs.TweenLite.to(i,2,{value:t,onUpdate:a,onUpdateParams:["{self}","value"],ease:punchgs.Power3.easeInOut})}function buildLoader(e,t,a){return e.find(".esg-loader").length>0?!1:(e.append('<div class="esg-loader '+t.spinner+'"><div class="dot1"></div><div class="dot2"></div><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>'),("spinner1"==t.spinner||"spinner2"==t.spinner)&&e.find(".esg-loader").css({backgroundColor:t.spinnerColor}),("spinner3"==t.spinner||"spinner4"==t.spinner)&&e.find(".bounce1, .bounce2, .bounce3, .dot1, .dot2").css({backgroundColor:t.spinnerColor}),void(a||punchgs.TweenLite.to(e,.3,{minHeight:"100px",zIndex:0})))}function setKeyToNull(e,t){jQuery.each(e.loadMoreItems,function(a,o){o[0]==t&&(e.loadMoreItems[a][0]=-1,e.loadMoreItems[a][2]="already loaded")})}function loadMoreEmpty(e){for(var t=!0,a=0;a<e.loadMoreItems.length;a++)-1!=e.loadMoreItems[a][0]&&(t=!1);return t}function loadMoreItems(e,t){var a=checkMoreToLoad(e,t),o=new Array;jQuery.each(a,function(e,a){o.length<t.loadMoreAmount&&(o.push(a[0]),setKeyToNull(t,a[0]))});var i=checkMoreToLoad(e,t).length;if(o.length>0){var r=e.find(".esg-loadmore");r.length>0&&(punchgs.TweenLite.to(r,.4,{autoAlpha:.2}),r.data("loading",1));var n={action:t.loadMoreAjaxAction,client_action:"load_more_items",token:t.loadMoreAjaxToken,data:o,gridid:t.gridID};jQuery.ajax({type:"post",url:t.loadMoreAjaxUrl,dataType:"json",data:n}).success(function(a){if(a.success){var o=jQuery(a.data);jQuery(t.filterGroupClass+".esg-filter-wrapper.eg-search-wrapper .eg-forcefilter").length>0&&o.addClass("cat-searchresult"),e.find("ul").first().append(o),checkAvailableFilters(e,t),prepareItemsInGrid(t,!0),setItemsOnPages(t),setTimeout(function(){if(t.animDelay="off",organiseGrid(t),prepareSortingAndOrders(e),loadMoreEmpty(t))e.find(".esg-loadmore").remove();else{var a=e.find(".esg-loadmore"),o=t.loadMoreTxt+" ("+i+")";"off"==t.loadMoreNr&&(o=t.loadMoreTxt),a.html(0==i?t.loadMoreEndTxt:o),a.length>0&&(punchgs.TweenLite.to(a,.4,{autoAlpha:1,overwrite:"all"}),a.data("loading",0))}setTimeout(function(){t.animDelay="on"},500)},10)}}).error(function(t,a){e.find(".esg-loadmore").html("FAILURE: "+a)})}else loadMoreEmpty(t)?e.find(".esg-loadmore").remove():e.find(".esg-loadmore").html(t.loadMoreEndTxt)}function killOldCustomAjaxContent(e){var t=e.data("lastposttype"),a=e.data("oldajaxsource"),o=e.data("oldajaxtype"),i=e.data("oldajaxvideoaspect"),r=e.data("oldselector");if(t!=undefined&&""!=t)try{jQuery.each(jQuery.fn.tpessential.defaults.ajaxTypes,function(n,s){s!=undefined&&s.type!=undefined&&s.type==t&&s.killfunc!=undefined&&setTimeout(function(){s.killfunc.call(this,{id:a,type:o,aspectratio:i,selector:r})&&e.empty()},250)})}catch(n){console.log(n)}e.data("lastposttype","")}function addAjaxNavigagtion(e,t){function a(e){var t=new Array;return jQuery.each(e,function(e,a){jQuery(a).closest(".itemtoshow.isvisiblenow").length>0&&t.push(a)}),t}var o=" eg-acp-"+e.ajaxClosePosition;o=o+" eg-acp-"+e.ajaxCloseStyle,o=o+" eg-acp-"+e.ajaxCloseType,loc="eg-icon-left-open-1",roc="eg-icon-right-open-1",xoc='<i class="eg-icon-cancel"></i>',"type1"==e.ajaxCloseType&&(loc="eg-icon-left-open-big",roc="eg-icon-right-open-big",e.ajaxCloseTxt="",xoc="X"),("true"==e.ajaxCloseInner||1==e.ajaxCloseInner)&&(o+=" eg-acp-inner");var i='<div class="eg-ajax-closer-wrapper'+o+'">';switch("tr"==e.ajaxClosePosition||"br"==e.ajaxClosePosition?("on"==e.ajaxNavButton&&(i=i+'<div class="eg-ajax-left eg-ajax-navbt"><i class="'+loc+'"></i></div><div class="eg-ajax-right eg-ajax-navbt"><i class="'+roc+'"></i></div>'),"on"==e.ajaxCloseButton&&(i=i+'<div class="eg-ajax-closer eg-ajax-navbt">'+xoc+e.ajaxCloseTxt+"</div>")):("on"==e.ajaxCloseButton&&(i=i+'<div class="eg-ajax-closer eg-ajax-navbt">'+xoc+e.ajaxCloseTxt+"</div>"),"on"==e.ajaxNavButton&&(i=i+'<div class="eg-ajax-left eg-ajax-navbt"><i class="'+loc+'"></i></div><div class="eg-ajax-right eg-ajax-navbt"><i class="'+roc+'"></i></div>')),i+="</div>",e.ajaxClosePosition){case"tl":case"tr":case"t":t.prepend(i);break;case"bl":case"br":case"b":t.append(i)}t.find(".eg-ajax-closer").click(function(){showHideAjaxContainer(t,!1,null,null,.25,!0)}),t.find(".eg-ajax-right").click(function(){var e=t.data("container").find(".lastclickedajax").closest("li"),o=e.nextAll().find(".eg-ajax-a-button"),i=e.prevAll().find(".eg-ajax-a-button");o=a(o),i=a(i),o.length>0?o[0].click():i[0].click()}),t.find(".eg-ajax-left").click(function(){var e=t.data("container").find(".lastclickedajax").closest("li"),o=e.nextAll().find(".eg-ajax-a-button"),i=e.prevAll().find(".eg-ajax-a-button");o=a(o),i=a(i),i.length>0?i[i.length-1].click():o[o.length-1].click()})}function showHideAjaxContainer(e,t,a,o,i,r){i=i==undefined?.25:i;var n=e.data("container").data("opt"),s=e.data("lastheight")!=undefined?e.data("lastheight"):"100px";t?(i+=1.2,addAjaxNavigagtion(n,e),punchgs.TweenLite.set(e,{height:"auto"}),punchgs.TweenLite.set(e.parent(),{minHeight:0,maxHeight:"none",height:"auto",overwrite:"all"}),punchgs.TweenLite.from(e,i,{height:s,ease:punchgs.Power3.easeInOut,onStart:function(){punchgs.TweenLite.to(e,i,{autoAlpha:1,ease:punchgs.Power3.easeOut})},onComplete:function(){e.data("lastheight",e.height()),jQuery(window).trigger("resize.essg"),0==e.find(".eg-ajax-closer-wrapper").length&&addAjaxNavigagtion(n,e)}}),"off"!=n.ajaxScrollToOnLoad&&jQuery("html, body").animate({scrollTop:e.offset().top-o},{queue:!1,speed:.5})):(r&&(killOldCustomAjaxContent(e),s="0px"),punchgs.TweenLite.to(e.parent(),i,{height:s,ease:punchgs.Power2.easeInOut,onStart:function(){punchgs.TweenLite.to(e,i,{autoAlpha:0,ease:punchgs.Power3.easeOut})},onComplete:function(){setTimeout(function(){r&&e.html("")},300)}}))}function removeLoader(e){e.closest(".eg-ajaxanimwrapper").find(".esg-loader").remove()}function ajaxCallBack(opt,a){if(opt.ajaxCallback==undefined||""==opt.ajaxCallback||opt.ajaxCallback.length<3)return!1;var splitter=opt.ajaxCallback.split(")"),splitter=splitter[0].split("("),callback=splitter[0],arguments=splitter.length>1&&""!=splitter[1]?splitter[1]+",":"",obj=new Object;try{obj.containerid="#"+opt.ajaxContentTarget,obj.postsource=a.data("ajaxsource"),obj.posttype=a.data("ajaxtype"),eval("on"==opt.ajaxCallbackArgument?callback+"("+arguments+"obj)":callback+"("+arguments+")")}catch(e){console.log("Callback Error"),console.log(e)}}function loadMoreContent(e,t,a){e.find(".lastclickedajax").removeClass("lastclickedajax"),a.addClass("lastclickedajax");var o=jQuery("#"+t.ajaxContentTarget).find(".eg-ajax-target"),i=a.data("ajaxsource"),r=a.data("ajaxtype"),n=a.data("ajaxvideoaspect");if(o.data("container",e),n="16:9"==n?"widevideo":"normalvideo",showHideAjaxContainer(o,!1),o.length>0)switch(t.ajaxJsUrl!=undefined&&""!=t.ajaxJsUrl&&t.ajaxJsUrl.length>3&&jQuery.getScript(t.ajaxJsUrl).done(function(){t.ajaxJsUrl=""}).fail(function(){console.log("Loading Error on Ajax jQuery File. Please doublecheck if JS File and Path exist:"+t.ajaxJSUrl),t.ajaxJsUrl=""}),t.ajaxCssUrl!=undefined&&""!=t.ajaxCssUrl&&t.ajaxCssUrl.length>3&&(jQuery("<link>").appendTo("head").attr({type:"text/css",rel:"stylesheet"}).attr("href",t.ajaxCssUrl),""==t.ajaxCssUrl),buildLoader(o.closest(".eg-ajaxanimwrapper"),t),o.data("ajaxload")!=undefined&&o.data("ajaxload").abort(),killOldCustomAjaxContent(o),r){case"postid":var s={action:t.loadMoreAjaxAction,client_action:"load_more_content",token:t.loadMoreAjaxToken,postid:i};setTimeout(function(){o.data("ajaxload",jQuery.ajax({type:"post",url:t.loadMoreAjaxUrl,dataType:"json",data:s})),o.data("ajaxload").success(function(e){e.success&&(jQuery(o).html(e.data),showHideAjaxContainer(o,!0,t.ajaxScrollToOnLoad,t.ajaxScrollToOffset),removeLoader(o),ajaxCallBack(t,a))}),o.data("ajaxload").error(function(e,t){"abort"!=t&&(jQuery(o).append("<p>FAILURE: <strong>"+t+"</strong></p>"),removeLoader(o))})},300);break;case"youtubeid":setTimeout(function(){o.html('<div class="eg-ajax-video-container '+n+'"><iframe width="560" height="315" src="//www.youtube.com/embed/'+i+'?autoplay=1&vq=hd1080" frameborder="0" allowfullscreen></iframe></div>'),removeLoader(o),showHideAjaxContainer(o,!0,t.ajaxScrollToOnLoad,t.ajaxScrollToOffset),ajaxCallBack(t,a)},300);break;case"vimeoid":setTimeout(function(){o.html('<div class="eg-ajax-video-container '+n+'"><iframe src="//player.vimeo.com/video/'+i+'?portrait=0&autoplay=1" width="500" height="281" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>'),removeLoader(o),showHideAjaxContainer(o,!0,t.ajaxScrollToOnLoad,t.ajaxScrollToOffset),ajaxCallBack(t,a)},300);break;case"wistiaid":setTimeout(function(){o.html('<div class="eg-ajax-video-container '+n+'"><iframe src="//fast.wistia.net/embed/iframe/'+i+'"allowtransparency="true" frameborder="0" scrolling="no" class="wistia_embed" name="wistia_embed" allowfullscreen mozallowfullscreen webkitallowfullscreen oallowfullscreen msallowfullscreen width="640" height="388"></iframe></div>'),removeLoader(o),showHideAjaxContainer(o,!0,t.ajaxScrollToOnLoad,t.ajaxScrollToOffset),ajaxCallBack(t,a)},300);break;case"html5vid":i=i.split("|"),setTimeout(function(){o.html('<video autoplay="true" loop="" class="rowbgimage" poster="" width="100%" height="auto"><source src="'+i[0]+'" type="video/mp4"><source src="'+i[1]+'" type="video/webm"><source src="'+i[2]+'" type="video/ogg"></video>'),removeLoader(o),showHideAjaxContainer(o,!0,t.ajaxScrollToOnLoad,t.ajaxScrollToOffset),ajaxCallBack(t,a)},300);break;case"soundcloud":case"soundcloudid":setTimeout(function(){o.html('<iframe width="100%" height="250" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/'+i+'&auto_play=true&hide_related=false&show_comments=true&show_user=true&show_reposts=false&visual=true"></iframe>'),removeLoader(o),showHideAjaxContainer(o,!0,t.ajaxScrollToOnLoad,t.ajaxScrollToOffset),ajaxCallBack(t,a)},300);break;case"imageurl":setTimeout(function(){var e=new Image;e.onload=function(){var e=jQuery(this);o.html(""),e.css({width:"100%",height:"auto"}),o.append(jQuery(this)),removeLoader(o),showHideAjaxContainer(o,!0,t.ajaxScrollToOnLoad,t.ajaxScrollToOffset),ajaxCallBack(t,a)},e.onerror=function(){o.html("Error"),removeLoader(o),showHideAjaxContainer(o,!0,t.ajaxScrollToOnLoad,t.ajaxScrollToOffset)},e.src=i},300);break;default:jQuery.each(jQuery.fn.tpessential.defaults.ajaxTypes,function(e,a){a.openAnimationSpeed==undefined&&(a.openAnimationSpeed=0),a!=undefined&&a.type!=undefined&&a.type==r&&setTimeout(function(){o.data("lastposttype",r),o.data("oldajaxsource",i),o.data("oldajaxtype",r),o.data("oldajaxvideoaspect",n),o.data("oldselector","#"+t.ajaxContentTarget+" .eg-ajax-target"),showHideAjaxContainer(o,!0,t.ajaxScrollToOnLoad,t.ajaxScrollToOffset,0),o.html(a.func.call(this,{id:i,type:r,aspectratio:n})),removeLoader(o)},300)})}}function resetFiltersFromCookies(e,t,a){if("on"==e.cookies.filter){var o=a!==undefined?a:readCookie("grid_"+e.girdID+"_filters");if(o!==undefined&&null!==o&&o.length>0){var i=0;jQuery.each(o.split(","),function(a,o){o!==undefined&&-1!==o&&"-1"!==o&&jQuery(e.filterGroupClass+".esg-filterbutton,"+e.filterGroupClass+" .esg-filterbutton").each(function(){jQuery(this).data("fid")!=o&&parseInt(jQuery(this).data("fid"),0)!==parseInt(o,0)||jQuery(this).hasClass("esg-pagination-button")||(t?jQuery(this).click():jQuery(this).addClass("selected"),i++)})}),i>0&&jQuery(e.filterGroupClass+".esg-filterbutton.esg-allfilter,"+e.filterGroupClass+" .esg-filterbutton.esg-allfilter").removeClass("selected")}}}function resetPaginationFromCookies(e,t){if("on"===e.cookies.pagination){var a=t!==undefined?t:readCookie("grid_"+e.girdID+"_pagination");a!==undefined&&null!==a&&a.length>0&&jQuery(e.filterGroupClass+".esg-filterbutton.esg-pagination-button,"+e.filterGroupClass+" .esg-filterbutton.esg-pagination-button").each(function(){parseInt(jQuery(this).data("page"),0)!==parseInt(a,0)||jQuery(this).hasClass("selected")||jQuery(this).click()})}}function resetSearchFromCookies(e){if("on"===e.cookies.search){var t=readCookie("grid_"+e.gridID+"_search");t!==undefined&&null!=t&&t.length>0&&(jQuery(e.filterGroupClass+".eg-search-wrapper .eg-search-input").val(t).trigger("change"),e.cookies.searchjusttriggered=!0)}}function mainPreparing(e,t){function a(){if(1==t.lastsearchtimer)return!1;t.lastsearchtimer=1,buildLoader(jQuery(t.filterGroupClass+".eg-search-wrapper"),{spinner:"spinner3",spinnerColor:"#fff"},!0),punchgs.TweenLite.fromTo(jQuery(t.filterGroupClass+".eg-search-wrapper").find(".esg-loader"),.3,{autoAlpha:0},{autoAlpha:1,ease:punchgs.Power3.easeInOut});var a=jQuery(t.filterGroupClass+".eg-search-wrapper .eg-search-input"),o=a.val(),i=jQuery(t.filterGroupClass+".eg-search-wrapper.esg-filter-wrapper .hiddensearchfield");if(a.attr("disabled","true"),o.length>0){a.trigger("searchstarting");var r={search:o,id:t.gridID},n={action:t.loadMoreAjaxAction,client_action:"get_grid_search_ids",token:t.loadMoreAjaxToken,data:r};jQuery.ajax({type:"post",url:t.loadMoreAjaxUrl,dataType:"json",data:n}).success(function(a){if("on"===t.cookies.search&&createCookie("grid_"+t.gridID+"_search",o,t.cookies.timetosave*(1/60/60)),t.cookies.searchjusttriggered===!0){var r=readCookie("grid_"+t.girdID+"_pagination"),n=readCookie("grid_"+t.girdID+"_filters");setTimeout(function(){resetFiltersFromCookies(t,!0,n),resetPaginationFromCookies(t,r)},200),t.cookies.searchjusttriggered=!1}setTimeout(function(){t.lastsearchtimer=0,jQuery(t.filterGroupClass+".eg-search-wrapper .eg-search-input").attr("disabled",!1),punchgs.TweenLite.to(jQuery(t.filterGroupClass+".eg-search-wrapper").find(".esg-loader"),.5,{autoAlpha:1,ease:punchgs.Power3.easeInOut,onComplete:function(){jQuery(t.filterGroupClass+".eg-search-wrapper").find(".esg-loader").remove()}}),jQuery(t.filterGroupClass+".eg-search-wrapper .eg-search-input").trigger("searchended")},1e3);var s=new Array;a&&jQuery.each(a,function(e,t){t!=undefined&&jQuery.isNumeric(t)&&s.push(t)}),e.find(".cat-searchresult").removeClass("cat-searchresult");var l=0;jQuery.each(t.loadMoreItems,function(e,t){t[2]="notsearched",jQuery.each(s,function(e,a){return parseInt(t[0],0)===parseInt(a,0)&&-1!=parseInt(t[0],0)?(t[2]="cat-searchresult",l++,!1):void 0})}),jQuery.each(s,function(t,a){e.find(".eg-post-id-"+a).addClass("cat-searchresult")}),i.addClass("eg-forcefilter").addClass("eg-justfilteredtosearch"),jQuery(t.filterGroupClass+".esg-filter-wrapper .esg-allfilter").trigger("click")}).error(function(e,a){console.log("FAILURE: "+a),setTimeout(function(){t.lastsearchtimer=0,jQuery(t.filterGroupClass+".eg-search-wrapper .eg-search-input").attr("disabled",!1),punchgs.TweenLite.to(jQuery(t.filterGroupClass+".eg-search-wrapper").find(".esg-loader"),.5,{autoAlpha:1,ease:punchgs.Power3.easeInOut,onComplete:function(){jQuery(t.filterGroupClass+".eg-search-wrapper").find(".esg-loader").remove()}}),jQuery(t.filterGroupClass+".eg-search-wrapper .eg-search-input").trigger("searchended")},1e3)})}else{jQuery.each(t.loadMoreItems,function(e,t){t[2]="notsearched"}),e.find(".cat-searchresult").removeClass("cat-searchresult");var i=jQuery(t.filterGroupClass+".eg-search-wrapper.esg-filter-wrapper .hiddensearchfield");i.removeClass("eg-forcefilter").addClass("eg-justfilteredtosearch"),"on"===t.cookies.search&&createCookie("grid_"+t.gridID+"_search","",-1),jQuery(t.filterGroupClass+".esg-filter-wrapper .esg-allfilter").trigger("click"),setTimeout(function(){t.lastsearchtimer=0,jQuery(t.filterGroupClass+".eg-search-wrapper .eg-search-input").attr("disabled",!1),punchgs.TweenLite.to(jQuery(t.filterGroupClass+".eg-search-wrapper").find(".esg-loader"),.5,{autoAlpha:1,ease:punchgs.Power3.easeInOut,onComplete:function(){jQuery(t.filterGroupClass+".eg-search-wrapper").find(".esg-loader").remove()}}),jQuery(t.filterGroupClass+".eg-search-wrapper .eg-search-input").trigger("searchended")},1e3)}}resetFiltersFromCookies(t);var o=e.find(".eg-leftright-container"),i=getBestFitColumn(t,jQuery(window).width(),"id");if(t.column=i.column,t.columnindex=i.index,prepareItemsInGrid(t),organiseGrid(t),jQuery(t.filterGroupClass+".eg-search-wrapper").length>0){var r=t.filterGroupClass.replace(".",""),n="Search Result",s=jQuery(t.filterGroupClass+".eg-search-wrapper .eg-search-submit"),l=jQuery(t.filterGroupClass+".eg-search-wrapper .eg-search-clean");jQuery(t.filterGroupClass+".esg-filter-wrapper.eg-search-wrapper").append('<div style="display:none !important" class="esg-filterbutton hiddensearchfield '+r+'" data-filter="cat-searchresult"><span>'+n+"</span></div>"),t.lastsearchtimer=0,s.click(a),jQuery(t.filterGroupClass+".eg-search-wrapper .eg-search-input").on("change",a),l.click(function(){"on"===t.cookies.search&&createCookie("grid_"+t.gridID+"_search","",-1),jQuery.each(t.loadMoreItems,function(e,t){t[2]="notsearched"}),e.find(".cat-searchresult").removeClass("cat-searchresult");var a=jQuery(t.filterGroupClass+".eg-search-wrapper.esg-filter-wrapper .hiddensearchfield");jQuery(t.filterGroupClass+".eg-search-wrapper .eg-search-input").val(""),a.removeClass("eg-forcefilter").addClass("eg-justfilteredtosearch"),jQuery(t.filterGroupClass+".esg-filter-wrapper .esg-allfilter").trigger("click"),setTimeout(function(){t.lastsearchtimer=0,jQuery(t.filterGroupClass+".eg-search-wrapper .eg-search-input").attr("disabled",!1),punchgs.TweenLite.to(jQuery(t.filterGroupClass+".eg-search-wrapper").find(".esg-loader"),.5,{autoAlpha:1,ease:punchgs.Power3.easeInOut,onComplete:function(){jQuery(t.filterGroupClass+".eg-search-wrapper").find(".esg-loader").remove()}}),jQuery(t.filterGroupClass+".eg-search-wrapper .eg-search-input").trigger("searchended")},1e3)})}addCountSuffix(e,t),jQuery(t.filterGroupClass+".esg-filter-wrapper,"+t.filterGroupClass+" .esg-filter-wrapper").each(function(){var e=jQuery(this);e.hasClass("dropdownstyle")&&(e.find(".esg-filter-checked").each(function(){jQuery(this).prependTo(jQuery(this).parent())}),is_mobile()?e.find(".esg-selected-filterbutton").click(function(){var t=e.find(".esg-selected-filterbutton");t.hasClass("hoveredfilter")?(setTimeout(function(){t.removeClass("hoveredfilter")},200),e.find(".esg-dropdown-wrapper").stop().hide()):(setTimeout(function(){t.addClass("hoveredfilter")},200),e.find(".esg-dropdown-wrapper").stop().show())}):"click"==t.showDropFilter?(e.click(function(){var e=jQuery(this).closest(".esg-filter-wrapper");e.find(".esg-selected-filterbutton").addClass("hoveredfilter"),e.find(".esg-dropdown-wrapper").stop().show()}),e.on("mouseleave",function(){var e=jQuery(this).closest(".esg-filter-wrapper");e.find(".esg-selected-filterbutton").removeClass("hoveredfilter"),e.find(".esg-dropdown-wrapper").stop().hide()})):e.hover(function(){var e=jQuery(this).closest(".esg-filter-wrapper");e.find(".esg-selected-filterbutton").addClass("hoveredfilter"),e.find(".esg-dropdown-wrapper").stop().show()},function(){var e=jQuery(this).closest(".esg-filter-wrapper");e.find(".esg-selected-filterbutton").removeClass("hoveredfilter"),e.find(".esg-dropdown-wrapper").stop().hide()}))}),jQuery("body").on("click",t.filterGroupClass+".esg-left,"+t.filterGroupClass+" .esg-left",function(){t=getOptions(e),t.oldpage=t.currentpage,t.currentpage--,t.currentpage<0&&(t.currentpage=t.realmaxpage-1);var a=getBestFitColumn(t,jQuery(window).width(),"id");t.column=a.column,t.columnindex=a.index,setItemsOnPages(t),organiseGrid(t,"right"),setOptions(e,t),stopAllVideos(!0)}),jQuery("body").on("click",t.filterGroupClass+".esg-right,"+t.filterGroupClass+" .esg-right",function(){t=getOptions(e),t.oldpage=t.currentpage,t.currentpage++,t.currentpage>=t.realmaxpage&&(t.currentpage=0);var a=getBestFitColumn(t,jQuery(window).width(),"id");t.column=a.column,t.columnindex=a.index,setItemsOnPages(t),organiseGrid(t,"right"),setOptions(e,t),stopAllVideos(!0)}),jQuery(t.filterGroupClass+".esg-filterbutton, "+t.filterGroupClass+" .esg-filterbutton").each(function(){jQuery(this).hasClass("esg-pagination-button")||jQuery(this).click(function(){var t=getOptions(e);stopAllVideos(!0);var a=jQuery(this);a.hasClass("esg-pagination-button")||(jQuery(t.filterGroupClass+".esg-allfilter, "+t.filterGroupClass+" .esg-allfilter").removeClass("selected"),a.hasClass("esg-allfilter")&&jQuery(t.filterGroupClass+".esg-filterbutton, "+t.filterGroupClass+" .esg-filterbutton").each(function(){jQuery(this).removeClass("selected")})),a.closest(".esg-filters").hasClass("esg-singlefilters")||"single"==t.filterType?(jQuery(t.filterGroupClass+".esg-filterbutton, "+t.filterGroupClass+" .esg-filterbutton").each(function(){jQuery(this).removeClass("selected")}),a.addClass("selected")):a.hasClass("selected")?a.removeClass("selected"):a.addClass("selected");var o=jQuery(t.filterGroupClass+".esg-filter-wrapper .hiddensearchfield");o.hasClass("eg-forcefilter")&&o.addClass("selected");var i=0,r="";jQuery(t.filterGroupClass+".esg-filterbutton.selected,"+t.filterGroupClass+" .esg-filterbutton.selected").each(function(){jQuery(this).hasClass("selected")&&!jQuery(this).hasClass("esg-pagination-button")&&(i++,r=0===i?jQuery(this).data("fid"):r+","+jQuery(this).data("fid"))}),"on"===t.cookies.filter&&t.cookies.searchjusttriggered!==!0&&createCookie("grid_"+t.girdID+"_filters",r,t.cookies.timetosave*(1/60/60)),0==i&&jQuery(t.filterGroupClass+".esg-allfilter,"+t.filterGroupClass+" .esg-allfilter").addClass("selected"),t.filterchanged=!0,t.currentpage=0,1==t.maxpage?(jQuery(t.filterGroupClass+".navigationbuttons,"+t.filterGroupClass+" .navigationbuttons").css({display:"none"}),jQuery(t.filterGroupClass+".esg-pagination,"+t.filterGroupClass+" .esg-pagination").css({display:"none"})):(jQuery(t.filterGroupClass+".navigationbuttons,"+t.filterGroupClass+" .navigationbuttons").css({display:"inline-block"}),jQuery(t.filterGroupClass+".esg-pagination,"+t.filterGroupClass+" .esg-pagination").css({display:"inline-block"}));var n=e.find(".esg-loadmore");if(n.length>0){var s=checkMoreToLoad(e,t).length;n.html(s>0?"off"==t.loadMoreNr?t.loadMoreTxt:t.loadMoreTxt+" ("+s+")":t.loadMoreEndTxt)}setItemsOnPages(t),organiseGrid(t),setOptions(e,t)})});var u;jQuery(window).on("resize.essg",function(){if(clearTimeout(u),"on"==t.forceFullWidth||"on"==t.forceFullScreen){var a=e.parent().parent().find(".esg-relative-placeholder").offset().left;e.closest(".esg-container-fullscreen-forcer").css({left:0-a,width:jQuery(window).width()})}else e.closest(".esg-container-fullscreen-forcer").css({left:0,width:"auto"});if(o.length>0){var i=o.outerWidth(!0);punchgs.TweenLite.set(e.find(".esg-overflowtrick"),{width:e.width()-i,overwrite:"all"})}var r=getBestFitColumn(t,jQuery(window).width(),"id");t.column=r.column,t.columnindex=r.index,setOptions(e,t),u=setTimeout(function(){t=getOptions(e),setItemsOnPages(t),organiseGrid(t),setOptions(e,t)},200)}),e.on("itemsinposition",function(){var e=jQuery(this),t=getOptions(e),a=e.find(".eg-leftright-container");if(clearTimeout(e.data("callednow")),t.maxheight>0&&t.maxheight<9999999999){t.inanimation=!1;var o=e.find(".esg-overflowtrick").first(),i=e.find("ul").first(),a=e.find(".eg-leftright-container"),r=parseInt(o.css("paddingTop"),0);r=r==undefined?0:r,r=null==r?0:r;var n=parseInt(o.css("paddingBottom"),0);n=n==undefined?0:n,n=null==n?0:n;var s=t.maxheight+t.overflowoffset+r+n;if("on"==t.forceFullScreen){var l=jQuery(window).height();if(t.fullScreenOffsetContainer!=undefined)try{var u=t.fullScreenOffsetContainer.split(",");jQuery.each(u,function(e,a){l-=jQuery(a).outerHeight(!0),l<t.minFullScreenHeight&&(l=t.minFullScreenHeight)})}catch(d){}s=l}var c=.3;i.height()<200&&(c=1),punchgs.TweenLite.to(i,c,{force3D:"auto",height:s,ease:punchgs.Power3.easeInOut,clearProps:"transform"}),punchgs.TweenLite.to(o,c,{force3D:!0,height:s,ease:punchgs.Power3.easeInOut,clearProps:"transform",onComplete:function(){e.closest(".eg-grid-wrapper, .myportfolio-container").css({height:"auto"}).removeClass("eg-startheight")}}),a.length>0&&punchgs.TweenLite.set(a,{minHeight:s,ease:punchgs.Power3.easeInOut});var p=jQuery(t.filterGroupClass+".esg-navbutton-solo-left,"+t.filterGroupClass+" .esg-navbutton-solo-left"),h=jQuery(t.filterGroupClass+".esg-navbutton-solo-right,"+t.filterGroupClass+" .esg-navbutton-solo-right");p.length>0&&p.css({marginTop:0-p.height()/2}),h.length>0&&h.css({marginTop:0-h.height()/2})}else if(0==t.maxheight){var o=e.find(".esg-overflowtrick").first(),i=e.find("ul").first();punchgs.TweenLite.to(i,1,{force3D:"auto",height:0,ease:punchgs.Power3.easeInOut,clearProps:"transform"}),punchgs.TweenLite.to(o,1,{force3D:!0,height:0,ease:punchgs.Power3.easeInOut,clearProps:"transform"})}e.data("callednow",setTimeout(function(){e.find(".itemtoshow.isvisiblenow").each(function(){hideUnderElems(jQuery(this))})},250)),t.firstLoadFinnished===undefined&&(e.trigger("essential_grid_ready_to_use"),resetSearchFromCookies(t),resetPaginationFromCookies(t),t.firstLoadFinnished=!0)}),prepareSortingAndOrders(e),prepareSortingClicks(e)}function prepareSortingAndOrders(e){var t=getOptions(e);e.find(".tp-esg-item").each(function(){var e=new Date(jQuery(this).data("date"));jQuery(this).data("date",e.getTime()/1e3)}),jQuery(t.filterGroupClass+".esg-sortbutton-order,"+t.filterGroupClass+" .esg-sortbutton-order").each(function(){var e=jQuery(this);e.removeClass("tp-desc").addClass("tp-asc"),e.data("dir","asc")})}function prepareSortingClicks(e){opt=getOptions(e);var t;jQuery(opt.filterGroupClass+".esg-sortbutton-wrapper .esg-sortbutton-order,"+opt.filterGroupClass+" .esg-sortbutton-wrapper .esg-sortbutton-order").click(function(){var a=jQuery(this);a.hasClass("tp-desc")?(a.removeClass("tp-desc").addClass("tp-asc"),a.data("dir","asc")):(a.removeClass("tp-asc").addClass("tp-desc"),a.data("dir","desc"));var o=a.data("dir");stopAllVideos(!0,!0),jQuery(opt.filterGroupClass+".esg-sorting-select,"+opt.filterGroupClass+" .esg-sorting-select").each(function(){var a=jQuery(this).val();clearTimeout(t),e.find(".tp-esg-item").tsort({data:a,forceStrings:"false",order:o}),t=setTimeout(function(){opt=getOptions(e),setItemsOnPages(opt),organiseGrid(opt),setOptions(e,opt)},200)})}),jQuery(opt.filterGroupClass+".esg-sorting-select,"+opt.filterGroupClass+" .esg-sorting-select").each(function(){var a=jQuery(this);a.change(function(){e.find("iframe").css({visibility:"hidden"}),e.find(".video-eg").css({visibility:"hidden"});var o=jQuery(this).closest(".esg-sortbutton-wrapper").find(".esg-sortbutton-order"),i=a.val(),r=a.find("option:selected").text(),n=o.data("dir");stopAllVideos(!0,!0),clearTimeout(t),a.parent().parent().find(".sortby_data").text(r),e.find(".tp-esg-item").tsort({data:i,forceStrings:"false",order:n}),t=setTimeout(function(){opt=getOptions(e),setItemsOnPages(opt),organiseGrid(opt),setOptions(e,opt)},500)})})}function fixCenteredCoverElement(e,t,a){if(t==undefined&&(t=e.find(".esg-entry-cover")),a==undefined&&(a=e.find(".esg-entry-media")),t&&a){var o=a.height();punchgs.TweenLite.set(t,{height:o});var i=e.find(".esg-cc");punchgs.TweenLite.set(i,{top:(o-i.height())/2})}}function getBestFitColumn(e,t,a){var o=t,i=0,r=9999,n=0,s=e.column,l=(e.column,e.column),u=0,d=0;e.responsiveEntries!=undefined&&e.responsiveEntries.length>0&&jQuery.each(e.responsiveEntries,function(e,t){var a=t.width!=undefined?t.width:0,c=t.amount!=undefined?t.amount:0;r>a&&(r=a,s=c,d=e),a>n&&(n=a,lamount=c),a>i&&o>=a&&(i=a,l=c,u=e)}),r>t&&(l=s,u=d);var c=new Object;return c.index=u,c.column=l,"id"==a?c:l}function getOptions(e){return e.data("opt")}function setOptions(e,t){e.data("opt",t)}function checkMediaListeners(e){e.find("iframe").each(function(){var e=jQuery(this);e.attr("src").toLowerCase().indexOf("youtube")>0?prepareYT(e):e.attr("src").toLowerCase().indexOf("vimeo")>0?prepareVimeo(e):e.attr("src").toLowerCase().indexOf("wistia")>0?prepareWs(e):e.attr("src").toLowerCase().indexOf("soundcloud")>0&&prepareSoundCloud(e)}),e.find("video").each(function(){prepareVideo(jQuery(this))})}function waitMediaListeners(e){var t=setInterval(function(){e.find("iframe").each(function(){var e=jQuery(this);e.attr("src").toLowerCase().indexOf("youtube")>0?prepareYT(e)&&clearInterval(t):e.attr("src").toLowerCase().indexOf("vimeo")>0?prepareVimeo(e)&&clearInterval(t):e.attr("src").toLowerCase().indexOf("wistia")>0?prepareWs(e)&&clearInterval(t):e.attr("src").toLowerCase().indexOf("soundcloud")>0?prepareSoundCloud(e)&&clearInterval(t):clearInterval(t)}),e.find("video").each(function(){prepareVideo(jQuery(this))&&clearInterval(t)})},50)}function directionPrepare(e,t,a,o,i){var r=new Object;switch(e){case 0:r.x=0,r.y="in"==t?0-o:10+o,r.y=i&&"in"==t?r.y-5:r.y;break;case 1:r.y=0,r.x="in"==t?a:-10-a,r.x=i&&"in"==t?r.x+5:r.x;break;case 2:r.y="in"==t?o:-10-o,r.x=0,r.y=i&&"in"==t?r.y+5:r.y;break;case 3:r.y=0,r.x="in"==t?0-a:10+a,r.x=i&&"in"==t?r.x-5:r.x}return r}function getDir(e,t){var a=e.width(),o=e.height(),i=(t.x-e.offset().left-a/2)*(a>o?o/a:1),r=(t.y-e.offset().top-o/2)*(o>a?a/o:1),n=Math.round((Math.atan2(r,i)*(180/Math.PI)+180)/90+3)%4;return n}function hideUnderElems(e){e.find(".eg-handlehideunder").each(function(){var t=jQuery(this),a=t.data("hideunder"),o=t.data("hideunderheight"),i=t.data("hidetype");t.data("knowndisplay")==undefined&&t.data("knowndisplay",t.css("display")),e.width()<a&&a!=undefined||e.height()<o&&o!=undefined?"visibility"==i?t.addClass("forcenotvisible"):"display"==i&&t.addClass("forcenotdisplay"):"visibility"==i?t.removeClass("forcenotvisible"):"display"==i&&t.removeClass("forcenotdisplay")