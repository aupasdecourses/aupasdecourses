!function(e){var t;if("function"==typeof define&&define.amd)define(["leaflet"],e);else if("undefined"!=typeof module)t=require("leaflet"),module.exports=e(t);else{if(void 0===window.L)throw"Leaflet must be loaded first";e(window.L)}}(function(e){"use strict";return e.Control.Geocoder=e.Control.extend({options:{showResultIcons:!1,collapsed:!0,expand:"click",position:"topright",placeholder:"Search...",errorMessage:"Nothing found."},_callbackId:0,initialize:function(t){e.Util.setOptions(this,t),this.options.geocoder||(this.options.geocoder=new e.Control.Geocoder.Nominatim)},onAdd:function(t){var o,n="leaflet-control-geocoder",r=e.DomUtil.create("div",n),s=e.DomUtil.create("div","leaflet-control-geocoder-icon",r),i=this._form=e.DomUtil.create("form",n+"-form",r);return this._map=t,this._container=r,o=this._input=e.DomUtil.create("input"),o.type="text",o.placeholder=this.options.placeholder,e.DomEvent.addListener(o,"keydown",this._keydown,this),this._errorElement=document.createElement("div"),this._errorElement.className=n+"-form-no-error",this._errorElement.innerHTML=this.options.errorMessage,this._alts=e.DomUtil.create("ul",n+"-alternatives leaflet-control-geocoder-alternatives-minimized"),i.appendChild(o),i.appendChild(this._errorElement),r.appendChild(this._alts),e.DomEvent.addListener(i,"submit",this._geocode,this),this.options.collapsed?"click"===this.options.expand?e.DomEvent.addListener(s,"click",function(e){0===e.button&&2!==e.detail&&this._toggle()},this):(e.DomEvent.addListener(s,"mouseover",this._expand,this),e.DomEvent.addListener(s,"mouseout",this._collapse,this),this._map.on("movestart",this._collapse,this)):this._expand(),e.DomEvent.disableClickPropagation(r),r},_geocodeResult:function(t){if(e.DomUtil.removeClass(this._container,"leaflet-control-geocoder-throbber"),1===t.length)this._geocodeResultSelected(t[0]);else if(t.length>0){this._alts.innerHTML="",this._results=t,e.DomUtil.removeClass(this._alts,"leaflet-control-geocoder-alternatives-minimized");for(var o=0;o<t.length;o++)this._alts.appendChild(this._createAlt(t[o],o))}else e.DomUtil.addClass(this._errorElement,"leaflet-control-geocoder-error")},markGeocode:function(t){return this._map.fitBounds(t.bbox),this._geocodeMarker&&this._map.removeLayer(this._geocodeMarker),this._geocodeMarker=new e.Marker(t.center).bindPopup(t.html||t.name).addTo(this._map).openPopup(),this},_geocode:function(t){return e.DomEvent.preventDefault(t),e.DomUtil.addClass(this._container,"leaflet-control-geocoder-throbber"),this._clearResults(),this.options.geocoder.geocode(this._input.value,this._geocodeResult,this),!1},_geocodeResultSelected:function(e){this.options.collapsed?this._collapse():this._clearResults(),this.markGeocode(e)},_toggle:function(){this._container.className.indexOf("leaflet-control-geocoder-expanded")>=0?this._collapse():this._expand()},_expand:function(){e.DomUtil.addClass(this._container,"leaflet-control-geocoder-expanded"),this._input.select()},_collapse:function(){this._container.className=this._container.className.replace(" leaflet-control-geocoder-expanded",""),e.DomUtil.addClass(this._alts,"leaflet-control-geocoder-alternatives-minimized"),e.DomUtil.removeClass(this._errorElement,"leaflet-control-geocoder-error")},_clearResults:function(){e.DomUtil.addClass(this._alts,"leaflet-control-geocoder-alternatives-minimized"),this._selection=null,e.DomUtil.removeClass(this._errorElement,"leaflet-control-geocoder-error")},_createAlt:function(t,o){var n=document.createElement("li"),r=e.DomUtil.create("a","",n),s=this.options.showResultIcons&&t.icon?e.DomUtil.create("img","",r):null,i=t.html?void 0:document.createTextNode(t.name);return s&&(s.src=t.icon),r.href="#",r.setAttribute("data-result-index",o),t.html?r.innerHTML=t.html:r.appendChild(i),e.DomEvent.addListener(n,"click",function(o){e.DomEvent.preventDefault(o),this._geocodeResultSelected(t)},this),n},_keydown:function(t){var o=this,n=function(t){o._selection&&(e.DomUtil.removeClass(o._selection.firstChild,"leaflet-control-geocoder-selected"),o._selection=o._selection[t>0?"nextSibling":"previousSibling"]),o._selection||(o._selection=o._alts[t>0?"firstChild":"lastChild"]),o._selection&&e.DomUtil.addClass(o._selection.firstChild,"leaflet-control-geocoder-selected")};switch(t.keyCode){case 27:this.options.collapsed&&this._collapse();break;case 38:n(-1),e.DomEvent.preventDefault(t);break;case 40:n(1),e.DomEvent.preventDefault(t);break;case 13:if(this._selection){var r=parseInt(this._selection.firstChild.getAttribute("data-result-index"),10);this._geocodeResultSelected(this._results[r]),this._clearResults(),e.DomEvent.preventDefault(t)}}return!0}}),e.Control.geocoder=function(t,o){return new e.Control.Geocoder(t,o)},e.Control.Geocoder.callbackId=0,e.Control.Geocoder.jsonp=function(t,o,n,r,s){var i="_l_geocoder_"+e.Control.Geocoder.callbackId++;o[s||"callback"]=i,window[i]=e.Util.bind(n,r);var l=document.createElement("script");l.type="text/javascript",l.src=t+e.Util.getParamString(o),l.id=i,document.getElementsByTagName("head")[0].appendChild(l)},e.Control.Geocoder.getJSON=function(t,o,n){var r=new XMLHttpRequest;r.open("GET",t+e.Util.getParamString(o),!0),r.send(null),r.onreadystatechange=function(){4==r.readyState&&(200!=r.status&&304!=req.status||n(JSON.parse(r.response)))}},e.Control.Geocoder.template=function(t,o,n){return t.replace(/\{ *([\w_]+) *\}/g,function(t,n){var r=o[n];return void 0===r?r="":"function"==typeof r&&(r=r(o)),e.Control.Geocoder.htmlEscape(r)})},e.Control.Geocoder.htmlEscape=function(){function e(e){return o[e]}var t=/[&<>"'`]/,o={"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#x27;","`":"&#x60;"};return function(o){return null==o?"":o?(o=""+o,t.test(o)?o.replace(/[&<>"'`]/g,e):o):o+""}}(),e.Control.Geocoder.Nominatim=e.Class.extend({options:{serviceUrl:"//nominatim.openstreetmap.org/",geocodingQueryParams:{},reverseQueryParams:{},htmlTemplate:function(t){var o=t.address,n=[];return(o.road||o.building)&&n.push("{building} {road} {house_number}"),(o.city||o.town||o.village)&&n.push('<span class="'+(n.length>0?"leaflet-control-geocoder-address-detail":"")+'">{postcode} {city} {town} {village}</span>'),(o.state||o.country)&&n.push('<span class="'+(n.length>0?"leaflet-control-geocoder-address-context":"")+'">{state} {country}</span>'),e.Control.Geocoder.template(n.join("<br/>"),o,!0)}},initialize:function(t){e.Util.setOptions(this,t)},geocode:function(t,o,n){e.Control.Geocoder.jsonp(this.options.serviceUrl+"search/",e.extend({q:t,limit:5,format:"json",addressdetails:1},this.options.geocodingQueryParams),function(t){for(var r=[],s=t.length-1;s>=0;s--){for(var i=t[s].boundingbox,l=0;l<4;l++)i[l]=parseFloat(i[l]);r[s]={icon:t[s].icon,name:t[s].display_name,html:this.options.htmlTemplate?this.options.htmlTemplate(t[s]):void 0,bbox:e.latLngBounds([i[0],i[2]],[i[1],i[3]]),center:e.latLng(t[s].lat,t[s].lon),properties:t[s]}}o.call(n,r)},this,"json_callback")},reverse:function(t,o,n,r){e.Control.Geocoder.jsonp(this.options.serviceUrl+"reverse/",e.extend({lat:t.lat,lon:t.lng,zoom:Math.round(Math.log(o/256)/Math.log(2)),addressdetails:1,format:"json"},this.options.reverseQueryParams),function(t){var o,s=[];t&&t.lat&&t.lon&&(o=e.latLng(t.lat,t.lon),s.push({name:t.display_name,html:this.options.htmlTemplate?this.options.htmlTemplate(t):void 0,center:o,bounds:e.latLngBounds(o,o),properties:t})),n.call(r,s)},this,"json_callback")}}),e.Control.Geocoder.nominatim=function(t){return new e.Control.Geocoder.Nominatim(t)},e.Control.Geocoder.Bing=e.Class.extend({initialize:function(e){this.key=e},geocode:function(t,o,n){e.Control.Geocoder.jsonp("//dev.virtualearth.net/REST/v1/Locations",{query:t,key:this.key},function(t){for(var r=[],s=t.resourceSets[0].resources.length-1;s>=0;s--){var i=t.resourceSets[0].resources[s],l=i.bbox;r[s]={name:i.name,bbox:e.latLngBounds([l[0],l[1]],[l[2],l[3]]),center:e.latLng(i.point.coordinates)}}o.call(n,r)},this,"jsonp")},reverse:function(t,o,n,r){e.Control.Geocoder.jsonp("//dev.virtualearth.net/REST/v1/Locations/"+t.lat+","+t.lng,{key:this.key},function(t){for(var o=[],s=t.resourceSets[0].resources.length-1;s>=0;s--){var i=t.resourceSets[0].resources[s],l=i.bbox;o[s]={name:i.name,bbox:e.latLngBounds([l[0],l[1]],[l[2],l[3]]),center:e.latLng(i.point.coordinates)}}n.call(r,o)},this,"jsonp")}}),e.Control.Geocoder.bing=function(t){return new e.Control.Geocoder.Bing(t)},e.Control.Geocoder.RaveGeo=e.Class.extend({options:{querySuffix:"",deepSearch:!0,wordBased:!1},jsonp:function(t,o,n){var r="_l_geocoder_"+e.Control.Geocoder.callbackId++,s=[];t.prepend=r+"(",t.append=")";for(var i in t)s.push(i+"="+escape(t[i]));window[r]=e.Util.bind(o,n);var l=document.createElement("script");l.type="text/javascript",l.src=this._serviceUrl+"?"+s.join("&"),l.id=r,document.getElementsByTagName("head")[0].appendChild(l)},initialize:function(t,o,n){e.Util.setOptions(this,n),this._serviceUrl=t,this._scheme=o},geocode:function(t,o,n){e.Control.Geocoder.jsonp(this._serviceUrl,{address:t+this.options.querySuffix,scheme:this._scheme,outputFormat:"jsonp",deepSearch:this.options.deepSearch,wordBased:this.options.wordBased},function(t){for(var r=[],s=t.length-1;s>=0;s--){var i=t[s],l=e.latLng(i.y,i.x);r[s]={name:i.address,bbox:e.latLngBounds([l]),center:l}}o.call(n,r)},this)}}),e.Control.Geocoder.raveGeo=function(t,o,n){return new e.Control.Geocoder.RaveGeo(t,o,n)},e.Control.Geocoder.MapQuest=e.Class.extend({options:{serviceUrl:"//www.mapquestapi.com/geocoding/v1"},initialize:function(t,o){this._key=decodeURIComponent(t),e.Util.setOptions(this,o)},_formatName:function(){var e,t=[];for(e=0;e<arguments.length;e++)arguments[e]&&t.push(arguments[e]);return t.join(", ")},geocode:function(t,o,n){e.Control.Geocoder.jsonp(this.options.serviceUrl+"/address",{key:this._key,location:t,limit:5,outFormat:"json"},function(t){var r,s,i=[];if(t.results&&t.results[0].locations)for(var l=t.results[0].locations.length-1;l>=0;l--)r=t.results[0].locations[l],s=e.latLng(r.latLng),i[l]={name:this._formatName(r.street,r.adminArea4,r.adminArea3,r.adminArea1),bbox:e.latLngBounds(s,s),center:s};o.call(n,i)},this)},reverse:function(t,o,n,r){e.Control.Geocoder.jsonp(this.options.serviceUrl+"/reverse",{key:this._key,location:t.lat+","+t.lng,outputFormat:"json"},function(t){var o,s,i=[];if(t.results&&t.results[0].locations)for(var l=t.results[0].locations.length-1;l>=0;l--)o=t.results[0].locations[l],s=e.latLng(o.latLng),i[l]={name:this._formatName(o.street,o.adminArea4,o.adminArea3,o.adminArea1),bbox:e.latLngBounds(s,s),center:s};n.call(r,i)},this)}}),e.Control.Geocoder.mapQuest=function(t,o){return new e.Control.Geocoder.MapQuest(t,o)},e.Control.Geocoder.Mapbox=e.Class.extend({options:{service_url:"https://api.tiles.mapbox.com/v4/geocode/mapbox.places-v1/"},initialize:function(e){this._access_token=e},geocode:function(t,o,n){e.Control.Geocoder.getJSON(this.options.service_url+encodeURIComponent(t)+".json",{access_token:this._access_token},function(t){var r,s,i,l=[];if(t.features&&t.features.length)for(var a=0;a<=t.features.length-1;a++)r=t.features[a],s=e.latLng(r.center.reverse()),i=r.hasOwnProperty("bbox")?e.latLngBounds(e.latLng(r.bbox.slice(0,2).reverse()),e.latLng(r.bbox.slice(2,4).reverse())):e.latLngBounds(s,s),l[a]={name:r.place_name,bbox:i,center:s};o.call(n,l)})},suggest:function(e,t,o){return this.geocode(e,t,o)},reverse:function(t,o,n,r){e.Control.Geocoder.getJSON(this.options.service_url+encodeURIComponent(t.lng)+","+encodeURIComponent(t.lat)+".json",{access_token:this._access_token},function(t){var o,s,i,l=[];if(t.features&&t.features.length)for(var a=0;a<=t.features.length-1;a++)o=t.features[a],s=e.latLng(o.center.reverse()),i=o.hasOwnProperty("bbox")?e.latLngBounds(e.latLng(o.bbox.slice(0,2).reverse()),e.latLng(o.bbox.slice(2,4).reverse())):e.latLngBounds(s,s),l[a]={name:o.place_name,bbox:i,center:s};n.call(r,l)})}}),e.Control.Geocoder.mapbox=function(t){return new e.Control.Geocoder.Mapbox(t)},e.Control.Geocoder.Google=e.Class.extend({options:{service_url:"https://maps.googleapis.com/maps/api/geocode/json"},initialize:function(e){this._key=e},geocode:function(t,o,n){var r={address:t};this._key&&this._key.length&&(r.key=this._key),e.Control.Geocoder.getJSON(this.options.service_url,r,function(t){var r,s,i,l=[];if(t.results&&t.results.length)for(var a=0;a<=t.results.length-1;a++)r=t.results[a],s=e.latLng(r.geometry.location),i=e.latLngBounds(e.latLng(r.geometry.viewport.northeast),e.latLng(r.geometry.viewport.southwest)),l[a]={name:r.formatted_address,bbox:i,center:s};o.call(n,l)})},reverse:function(t,o,n,r){var s={latlng:encodeURIComponent(t.lat)+","+encodeURIComponent(t.lng)};this._key&&this._key.length&&(s.key=this._key),e.Control.Geocoder.getJSON(this.options.service_url,s,function(t){var o,s,i,l=[];if(t.results&&t.results.length)for(var a=0;a<=t.results.length-1;a++)o=t.results[a],s=e.latLng(o.geometry.location),i=e.latLngBounds(e.latLng(o.geometry.viewport.northeast),e.latLng(o.geometry.viewport.southwest)),l[a]={name:o.formatted_address,bbox:i,center:s};n.call(r,l)})}}),e.Control.Geocoder.google=function(t){return new e.Control.Geocoder.Google(t)},e.Control.Geocoder.Photon=e.Class.extend({options:{serviceUrl:"//photon.komoot.de/api/"},initialize:function(t){e.setOptions(this,t)},geocode:function(t,o,n){var r=e.extend({q:t},this.options.geocodingQueryParams);e.Control.Geocoder.getJSON(this.options.serviceUrl,r,function(t){var r,s,i,l,a,c,d=[];if(t&&t.features)for(r=0;r<t.features.length;r++)s=t.features[r],i=s.geometry.coordinates,l=e.latLng(i[1],i[0]),a=s.properties.extent,c=a?e.latLngBounds([a[1],a[0]],[a[3],a[2]]):e.latLngBounds(l,l),d.push({name:s.properties.name,center:l,bbox:c});o.call(n,d)})},suggest:function(e,t,o){return this.geocode(e,t,o)},reverse:function(e,t,o){t.call(o,[])}}),e.Control.Geocoder.photon=function(t){return new e.Control.Geocoder.Photon(t)},e.Control.Geocoder});
