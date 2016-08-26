/* The author of this code is Geir K. Engdahl, and can be reached
 * at geir.engdahl (at) gmail.com
 * 
 * If you intend to use the code or derive code from it, please
 * consult with the author.
 */

// String trim functions are not made by GKE, but copied from
// http://www.somacon.com/p355.php

String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g,"");
}
String.prototype.ltrim = function() {
	return this.replace(/^\s+/,"");
}
String.prototype.rtrim = function() {
	return this.replace(/\s+$/,"");
}

// Converts the specified latitude or longitude to its corresponding value
// in the WGS84 format (100000ths of a degree).
function getWGS84Degrees(latOrLng) {
  return Math.round(latOrLng * 100000);
}

// Creates a TomTom-compatible itinerary file from the GDirections object.
// dir should be a valid GDirections object, filled with at least one route.
function createTomTomItineraryItn(gdir, addr, labels) {
  if (gdir.legs.length < 1) {
    // This is likely a programmatical error. Alert the user.
    alert("createTomTomItineraryXml(): The driving directions are invalid!");
    return null;
  }
  var startLatLng = gdir.legs[0].start_location;
  var label = "Start";
  if (labels[0] != null)
    label = labels[0];
  else if (addr[0] != null)
    label = addr[0];
  var itnStr = getWGS84Degrees(startLatLng.lng()) + "|"
    + getWGS84Degrees(startLatLng.lat()) + "|" + label.trim() + "|4|\n";
  for (var i = 0; i < gdir.legs.length; i++) {
    var latLng = gdir.legs[i].end_location;
    label = "Destination " + (i+1);
    var offset = (i+1) % addr.length;
    if (labels[offset] != null)
      label = labels[offset];
    else if (addr[offset] != null)
      label = addr[offset];
    itnStr += getWGS84Degrees(latLng.lng()) + "|"
      + getWGS84Degrees(latLng.lat()) + "|" + label.trim() + "|2|\n";
  }
  return itnStr;
}

function createGarminGpx(gdir, addr, labels) {
  if (gdir.legs.length < 1) {
    // This is likely a programmatical error. Alert the user.
    alert("createGarminGpx(): The driving directions are invalid!");
    return null;
  }
  var startLatLng = gdir.legs[0].start_location;
  var label = "OptiMap Start";
  if (labels[0] != null)
    label = labels[0];
  else if (addr[0] != null)
    label = addr[0];
  var gpxStr = '<?xml version="1.0"?><gpx version="1.1" creator="OptiMap" xmlns="http://www.topografix.com/GPX/1/1">';
  gpxStr += "<rte><name>OptiMap</name>";
  gpxStr += "<desc><![CDATA[Route generated by OptiMap]]></desc>";
  gpxStr += "<number>1</number>";
  gpxStr += '<rtept lat="' + startLatLng.lat() + '" lon="' + startLatLng.lng() + '"><name>' + label + '</name><cmt>' + label + '</cmt><desc><![CDATA[' + label + ']]></desc><sym>Start</sym><type><![CDATA[Start]]></type></rtept>';
  for (var i = 0; i < gdir.legs.length; i++) {
    var latLng = gdir.legs[i].end_location;
    label = "Destination " + (i+1);
    var offset = (i+1) % addr.length;
    if (labels[offset] != null)
      label = labels[offset];
    else if (addr[offset] != null)
      label = addr[offset];
    gpxStr += '<rtept lat="' + latLng.lat() + '" lon="' + latLng.lng() + '"><name>' + label + '</name><cmt>' + label + '</cmt><desc><![CDATA[' + label + ']]></desc><sym>Via</sym><type><![CDATA[Via]]></type></rtept>';
  }
  gpxStr += "</rte></gpx>";
  return gpxStr;
}

function zeroPadded(num, numDigits) {
  var ret = "";
  var divBy = Math.pow(10, numDigits - 1);
  while (num / divBy < 1 && divBy >= 10) {
    ret += "0";
    divBy /= 10;
  }
  ret += num;
  return ret;
}

function createGarminGpxWaypoints(gdir, addr, labels) {
  if (gdir.legs.length < 1) {
    // This is likely a programmatical error. Alert the user.
    alert("createGarminGpxWaypoints(): The driving directions are invalid!");
    return null;
  }
  var startLatLng = gdir.legs[0].start_location;
  var label = "OptiMap Start";
  if (labels[0] != null)
    label += "(" + labels[0] + ")";
  else if (addr[0] != null)
    label += "(" + addr[0] + ")";
  var gpxStr = '<?xml version="1.0"?><gpx version="1.1" creator="OptiMap" xmlns="http://www.topografix.com/GPX/1/1">';
  gpxStr += '<wpt lat="' + startLatLng.lat() + '" lon="' + startLatLng.lng() + '"><name>' + label + '</name><cmt>' + label + '</cmt><desc><![CDATA[' + label + ']]></desc><sym>Start</sym><type><![CDATA[Start]]></type></wpt>';
  for (var i = 0; i < gdir.legs.length; i++) {
    var latLng = gdir.legs[i].end_location;
    label = "OptiMap " + zeroPadded((i+1), 3) + " ";
    var offset = (i+1) % addr.length;
    if (labels[offset] != null)
      label += "(" + labels[offset] + ")";
    else if (addr[offset] != null)
      label += "(" + addr[offset] + ")";
    gpxStr += '<wpt lat="' + latLng.lat() + '" lon="' + latLng.lng() + '"><name>' + label + '</name><cmt>' + label + '</cmt><desc><![CDATA[' + label + ']]></desc><sym>Via</sym><type><![CDATA[Via]]></type></wpt>';
  }
  gpxStr += "</gpx>";
  return gpxStr;
}
