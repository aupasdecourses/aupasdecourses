!function(t){if("function"==typeof define&&define.amd)define(["leaflet"],t);else if("undefined"!=typeof module)module.exports=t(require("leaflet"));else{if(void 0===window.L)throw"Leaflet must be loaded first";t(window.L)}}(function(t){function e(t,e){var i=e.split("."),o=i.pop(),s=i.length,n=i[0],r=1;if(s>0)for(;(t=t[n])&&r<s;)n=i[r++];if(t)return t[o]}function o(t){return"[object Object]"===Object.prototype.toString.call(t)}return t.Control.Search=t.Control.extend({includes:t.Mixin.Events,options:{url:"",layer:null,sourceData:null,jsonpParam:null,propertyLoc:"loc",propertyName:"title",formatData:null,filterData:null,moveToLocation:null,buildTip:null,container:"",zoom:null,minLength:1,initial:!0,casesensitive:!1,autoType:!0,delayType:400,tooltipLimit:-1,tipAutoSubmit:!0,firstTipSubmit:!1,autoResize:!0,collapsed:!0,autoCollapse:!1,autoCollapseTime:1200,textErr:"Location not found",textCancel:"Cancel",textPlaceholder:"Search...",position:"topleft",hideMarkerOnCollapse:!1,marker:{icon:!1,animate:!0,circle:{radius:10,weight:3,color:"#e03",stroke:!0,fill:!1}}},initialize:function(e){t.Util.setOptions(this,e||{}),this._inputMinSize=this.options.textPlaceholder?this.options.textPlaceholder.length:10,this._layer=this.options.layer||new t.LayerGroup,this._filterData=this.options.filterData||this._defaultFilterData,this._formatData=this.options.formatData||this._defaultFormatData,this._moveToLocation=this.options.moveToLocation||this._defaultMoveToLocation,this._autoTypeTmp=this.options.autoType,this._countertips=0,this._recordsCache={},this._curReq=null},onAdd:function(e){return this._map=e,this._container=t.DomUtil.create("div","leaflet-control-search"),this._input=this._createInput(this.options.textPlaceholder,"search-input"),this._tooltip=this._createTooltip("search-tooltip"),this._cancel=this._createCancel(this.options.textCancel,"search-cancel"),this._button=this._createButton(this.options.textPlaceholder,"search-button"),this._alert=this._createAlert("search-alert"),!1===this.options.collapsed&&this.expand(this.options.collapsed),this.options.marker&&(this.options.marker instanceof t.Marker||this.options.marker instanceof t.CircleMarker?this._markerSearch=this.options.marker:o(this.options.marker)&&(this._markerSearch=new t.Control.Search.Marker([0,0],this.options.marker)),this._markerSearch._isMarkerSearch=!0),this.setLayer(this._layer),e.on({resize:this._handleAutoresize},this),this._container},addTo:function(e){return this.options.container?(this._container=this.onAdd(e),this._wrapper=t.DomUtil.get(this.options.container),this._wrapper.style.position="relative",this._wrapper.appendChild(this._container)):t.Control.prototype.addTo.call(this,e),this},onRemove:function(t){this._recordsCache={}},setLayer:function(t){return this._layer=t,this._layer.addTo(this._map),this},showAlert:function(t){t=t||this.options.textErr,this._alert.style.display="block",this._alert.innerHTML=t,clearTimeout(this.timerAlert);var e=this;return this.timerAlert=setTimeout(function(){e.hideAlert()},this.options.autoCollapseTime),this},hideAlert:function(){return this._alert.style.display="none",this},cancel:function(){return this._input.value="",this._handleKeypress({keyCode:8}),this._input.size=this._inputMinSize,this._input.focus(),this._cancel.style.display="none",this._hideTooltip(),this},expand:function(e){return e="boolean"!=typeof e||e,this._input.style.display="block",t.DomUtil.addClass(this._container,"search-exp"),!1!==e&&(this._input.focus(),this._map.on("dragstart click",this.collapse,this)),this.fire("search:expanded"),this},collapse:function(){return this._hideTooltip(),this.cancel(),this._alert.style.display="none",this._input.blur(),this.options.collapsed&&(this._input.style.display="none",this._cancel.style.display="none",t.DomUtil.removeClass(this._container,"search-exp"),this.options.hideMarkerOnCollapse&&this._map.removeLayer(this._markerSearch),this._map.off("dragstart click",this.collapse,this)),this.fire("search:collapsed"),this},collapseDelayed:function(){if(!this.options.autoCollapse)return this;var t=this;return clearTimeout(this.timerCollapse),this.timerCollapse=setTimeout(function(){t.collapse()},this.options.autoCollapseTime),this},collapseDelayedStop:function(){return clearTimeout(this.timerCollapse),this},_createAlert:function(e){var i=t.DomUtil.create("div",e,this._container);return i.style.display="none",t.DomEvent.on(i,"click",t.DomEvent.stop,this).on(i,"click",this.hideAlert,this),i},_createInput:function(e,i){var o=t.DomUtil.create("label",i,this._container),s=t.DomUtil.create("input",i,this._container);return s.type="text",s.size=this._inputMinSize,s.value="",s.autocomplete="off",s.autocorrect="off",s.autocapitalize="off",s.placeholder=e,s.style.display="none",s.role="search",s.id=s.role+s.type+s.size,o.htmlFor=s.id,o.style.display="none",o.value=e,t.DomEvent.disableClickPropagation(s).on(s,"keydown",this._handleKeypress,this).on(s,"blur",this.collapseDelayed,this).on(s,"focus",this.collapseDelayedStop,this),s},_createCancel:function(e,i){var o=t.DomUtil.create("a",i,this._container);return o.href="#",o.title=e,o.style.display="none",o.innerHTML="<span>&otimes;</span>",t.DomEvent.on(o,"click",t.DomEvent.stop,this).on(o,"click",this.cancel,this),o},_createButton:function(e,i){var o=t.DomUtil.create("a",i,this._container);return o.href="#",o.title=e,t.DomEvent.on(o,"click",t.DomEvent.stop,this).on(o,"click",this._handleSubmit,this).on(o,"focus",this.collapseDelayedStop,this).on(o,"blur",this.collapseDelayed,this),o},_createTooltip:function(e){var i=t.DomUtil.create("ul",e,this._container);i.style.display="none";var o=this;return t.DomEvent.disableClickPropagation(i).on(i,"blur",this.collapseDelayed,this).on(i,"mousewheel",function(e){o.collapseDelayedStop(),t.DomEvent.stopPropagation(e)},this).on(i,"mouseover",function(t){o.collapseDelayedStop()},this),i},_createTip:function(e,i){var o;if(this.options.buildTip){if("string"==typeof(o=this.options.buildTip.call(this,e,i))){var s=t.DomUtil.create("div");s.innerHTML=o,o=s.firstChild}}else o=t.DomUtil.create("li",""),o.innerHTML=e;return t.DomUtil.addClass(o,"search-tip"),o._text=e,this.options.tipAutoSubmit&&t.DomEvent.disableClickPropagation(o).on(o,"click",t.DomEvent.stop,this).on(o,"click",function(t){this._input.value=e,this._handleAutoresize(),this._input.focus(),this._hideTooltip(),this._handleSubmit()},this),o},_getUrl:function(t){return"function"==typeof this.options.url?this.options.url(t):this.options.url},_defaultFilterData:function(t,e){var i,o,s,n={};if(""===(t=t.replace(/[.*+?^${}()|[\]\\]/g,"")))return[];i=this.options.initial?"^":"",o=this.options.casesensitive?void 0:"i",s=new RegExp(i+t,o);for(var r in e)s.test(r)&&(n[r]=e[r]);return n},showTooltip:function(t){if(this._countertips=0,this._tooltip.innerHTML="",this._tooltip.currentSelection=-1,this.options.tooltipLimit)for(var e in t){if(this._countertips===this.options.tooltipLimit)break;this._countertips++,this._tooltip.appendChild(this._createTip(e,t[e]))}return this._countertips>0?(this._tooltip.style.display="block",this._autoTypeTmp&&this._autoType(),this._autoTypeTmp=this.options.autoType):this._hideTooltip(),this._tooltip.scrollTop=0,this._countertips},_hideTooltip:function(){return this._tooltip.style.display="none",this._tooltip.innerHTML="",0},_defaultFormatData:function(i){var o,s=this.options.propertyName,n=this.options.propertyLoc,r={};if(t.Util.isArray(n))for(o in i)r[e(i[o],s)]=t.latLng(i[o][n[0]],i[o][n[1]]);else for(o in i)r[e(i[o],s)]=t.latLng(e(i[o],n));return r},_recordsFromJsonp:function(e,i){t.Control.Search.callJsonp=i;var o=t.DomUtil.create("script","leaflet-search-jsonp",document.getElementsByTagName("body")[0]),s=t.Util.template(this._getUrl(e)+"&"+this.options.jsonpParam+"=L.Control.Search.callJsonp",{s:e});return o.type="text/javascript",o.src=s,{abort:function(){o.parentNode.removeChild(o)}}},_recordsFromAjax:function(e,i){void 0===window.XMLHttpRequest&&(window.XMLHttpRequest=function(){try{return new ActiveXObject("Microsoft.XMLHTTP.6.0")}catch(t){try{return new ActiveXObject("Microsoft.XMLHTTP.3.0")}catch(t){throw new Error("XMLHttpRequest is not supported")}}});var o=t.Browser.ie&&!window.atob&&document.querySelector,s=o?new XDomainRequest:new XMLHttpRequest,n=t.Util.template(this._getUrl(e),{s:e});s.open("GET",n);return s.onload=function(){i(JSON.parse(s.responseText))},s.onreadystatechange=function(){4===s.readyState&&200===s.status&&this.onload()},s.send(),s},_recordsFromLayer:function(){var i,o={},s=this.options.propertyName;return this._layer.eachLayer(function(n){if(!n.hasOwnProperty("_isMarkerSearch"))if(n instanceof t.Marker||n instanceof t.CircleMarker)try{if(e(n.options,s))i=n.getLatLng(),i.layer=n,o[e(n.options,s)]=i;else{if(!e(n.feature.properties,s))throw new Error("propertyName '"+s+"' not found in marker");i=n.getLatLng(),i.layer=n,o[e(n.feature.properties,s)]=i}}catch(t){console&&console.warn(t)}else if(n.hasOwnProperty("feature"))try{if(!n.feature.properties.hasOwnProperty(s))throw new Error("propertyName '"+s+"' not found in feature");i=n.getBounds().getCenter(),i.layer=n,o[n.feature.properties[s]]=i}catch(t){console&&console.warn(t)}else n instanceof t.LayerGroup&&n.eachLayer(function(t){i=t.getLatLng(),i.layer=t,o[t.feature.properties[s]]=i})},this),o},_autoType:function(){var t=this._input.value.length,e=this._tooltip.firstChild?this._tooltip.firstChild._text:"",i=e.length;if(0===e.indexOf(this._input.value))if(this._input.value=e,this._handleAutoresize(),this._input.createTextRange){var o=this._input.createTextRange();o.collapse(!0),o.moveStart("character",t),o.moveEnd("character",i),o.select()}else this._input.setSelectionRange?this._input.setSelectionRange(t,i):this._input.selectionStart&&(this._input.selectionStart=t,this._input.selectionEnd=i)},_hideAutoType:function(){var t;if((t=this._input.selection)&&t.empty)t.empty();else if(this._input.createTextRange){t=this._input.createTextRange(),t.collapse(!0);var e=this._input.value.length;t.moveStart("character",e),t.moveEnd("character",e),t.select()}else this._input.getSelection&&this._input.getSelection().removeAllRanges(),this._input.selectionStart=this._input.selectionEnd},_handleKeypress:function(t){switch(t.keyCode){case 27:this.collapse();break;case 13:(1==this._countertips||this.options.firstTipSubmit&&this._countertips>0)&&this._handleArrowSelect(1),this._handleSubmit();break;case 38:this._handleArrowSelect(-1);break;case 40:this._handleArrowSelect(1);break;case 8:case 45:case 46:this._autoTypeTmp=!1;break;case 37:case 39:case 16:case 17:case 35:case 36:break;default:if(this._input.value.length?this._cancel.style.display="block":this._cancel.style.display="none",this._input.value.length>=this.options.minLength){var e=this;clearTimeout(this.timerKeypress),this.timerKeypress=setTimeout(function(){e._fillRecordsCache()},this.options.delayType)}else this._hideTooltip()}this._handleAutoresize()},searchText:function(e){var i=e.charCodeAt(e.length);this._input.value=e,this._input.style.display="block",t.DomUtil.addClass(this._container,"search-exp"),this._autoTypeTmp=!1,this._handleKeypress({keyCode:i})},_fillRecordsCache:function(){var e,i=this._input.value,o=this;this._curReq&&this._curReq.abort&&this._curReq.abort(),t.DomUtil.addClass(this._container,"search-load"),this.options.layer?(this._recordsCache=this._recordsFromLayer(),e=this._filterData(this._input.value,this._recordsCache),this.showTooltip(e),t.DomUtil.removeClass(this._container,"search-load")):(this.options.sourceData?this._retrieveData=this.options.sourceData:this.options.url&&(this._retrieveData=this.options.jsonpParam?this._recordsFromJsonp:this._recordsFromAjax),this._curReq=this._retrieveData.call(this,i,function(i){o._recordsCache=o._formatData(i),e=o.options.sourceData?o._filterData(o._input.value,o._recordsCache):o._recordsCache,o.showTooltip(e),t.DomUtil.removeClass(o._container,"search-load")}))},_handleAutoresize:function(){this._input.style.maxWidth!=this._map._container.offsetWidth&&(this._input.style.maxWidth=t.DomUtil.getStyle(this._map._container,"width")),this.options.autoResize&&this._container.offsetWidth+45<this._map._container.offsetWidth&&(this._input.size=this._input.value.length<this._inputMinSize?this._inputMinSize:this._input.value.length)},_handleArrowSelect:function(e){var o=this._tooltip.hasChildNodes()?this._tooltip.childNodes:[];for(i=0;i<o.length;i++)t.DomUtil.removeClass(o[i],"search-tip-select");if(1==e&&this._tooltip.currentSelection>=o.length-1)t.DomUtil.addClass(o[this._tooltip.currentSelection],"search-tip-select");else if(-1==e&&this._tooltip.currentSelection<=0)this._tooltip.currentSelection=-1;else if("none"!=this._tooltip.style.display){this._tooltip.currentSelection+=e,t.DomUtil.addClass(o[this._tooltip.currentSelection],"search-tip-select"),this._input.value=o[this._tooltip.currentSelection]._text;var s=o[this._tooltip.currentSelection].offsetTop;s+o[this._tooltip.currentSelection].clientHeight>=this._tooltip.scrollTop+this._tooltip.clientHeight?this._tooltip.scrollTop=s-this._tooltip.clientHeight+o[this._tooltip.currentSelection].clientHeight:s<=this._tooltip.scrollTop&&(this._tooltip.scrollTop=s)}},_handleSubmit:function(){if(this._hideAutoType(),this.hideAlert(),this._hideTooltip(),"none"==this._input.style.display)this.expand();else if(""===this._input.value)this.collapse();else{var t=this._getLocation(this._input.value);!1===t?this.showAlert():(this.showLocation(t,this._input.value),this.fire("search:locationfound",{latlng:t,text:this._input.value,layer:t.layer?t.layer:null}))}},_getLocation:function(t){return!!this._recordsCache.hasOwnProperty(t)&&this._recordsCache[t]},_defaultMoveToLocation:function(t,e,i){this.options.zoom?this._map.setView(t,this.options.zoom):this._map.panTo(t)},showLocation:function(t,e){var i=this;return i._map.once("moveend zoomend",function(e){i._markerSearch&&i._markerSearch.addTo(i._map).setLatLng(t)}),i._moveToLocation(t,e,i._map),i.options.autoCollapse&&i.collapse(),i}}),t.Control.Search.Marker=t.Marker.extend({includes:t.Mixin.Events,options:{icon:new t.Icon.Default,animate:!0,circle:{radius:10,weight:3,color:"#e03",stroke:!0,fill:!1}},initialize:function(e,i){t.setOptions(this,i),!0===i.icon&&(i.icon=new t.Icon.Default),t.Marker.prototype.initialize.call(this,e,i),o(this.options.circle)&&(this._circleLoc=new t.CircleMarker(e,this.options.circle))},onAdd:function(e){t.Marker.prototype.onAdd.call(this,e),this._circleLoc&&(e.addLayer(this._circleLoc),this.options.animate&&this.animate())},onRemove:function(e){t.Marker.prototype.onRemove.call(this,e),this._circleLoc&&e.removeLayer(this._circleLoc)},setLatLng:function(e){return t.Marker.prototype.setLatLng.call(this,e),this._circleLoc&&this._circleLoc.setLatLng(e),this},_initIcon:function(){this.options.icon&&t.Marker.prototype._initIcon.call(this)},_removeIcon:function(){this.options.icon&&t.Marker.prototype._removeIcon.call(this)},animate:function(){if(this._circleLoc){var t=this._circleLoc,e=parseInt(t._radius/5),i=this.options.circle.radius,o=2*t._radius,s=0;t._timerAnimLoc=setInterval(function(){s+=.5,e+=s,o-=e,t.setRadius(o),o<i&&(clearInterval(t._timerAnimLoc),t.setRadius(i))},200)}return this}}),t.Map.addInitHook(function(){this.options.searchControl&&(this.searchControl=t.control.search(this.options.searchControl),this.addControl(this.searchControl))}),t.control.search=function(e){return new t.Control.Search(e)},t.Control.Search});