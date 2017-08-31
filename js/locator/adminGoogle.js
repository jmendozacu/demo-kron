function onePagegoogle() {
	if(document.getElementById("hideGoogle").value == ""){
   		if(document.getElementById("lat").value != "") {
			document.getElementById("hideGoogle").value = "1";
			document.getElementById("map_wrapper").style.display='block';
	var storeName   = document.getElementById("store_name").value;
	var address     = document.getElementById("address").value;
	var city        = document.getElementById("city").value;
	var state       = document.getElementById("state").value;
	var country     = document.getElementById("country").value;
	var zip_code    = document.getElementById("zip_code").value;
	var lat 	= document.getElementById("lat").value;
	var long 	= document.getElementById("long").value;
    		var mapOptions = {
    			zoom: 4,
    			center: new google.maps.LatLng(lat,long),
  		};
    		document.getElementById("map_canvas").innerHTML='';              
    		var map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
		var message = '<table width="300px" height="" style="padding:10px;"><tr><td width="60%" valign="top" align="left"><table width="100%" height=""><tr><td valign="top" align="left"><h3>Head Office</h3></td></tr><tr><td valign="top" align="left"><p>'+storeName+'<br>'+address+',<br>'+city+', '+state+', '+country+'- '+zip_code+'</p></td> </tr></table></td></tr></table>';
var bounds = new google.maps.LatLngBounds();
  
    		var infowindow = new google.maps.InfoWindow({
      			content: message
  		});
        	var position = new google.maps.LatLng(lat, long);
        		marker = new google.maps.Marker({
            		position: position,
            		map: map,
        	});   
   		infowindow.open(map,marker);
  }
}
}

setInterval(function(){onePagegoogle()},10000);

function getLatNLong(){
   
	document.getElementById("hideGoogle").value = "1";
	document.getElementById("map_wrapper").style.display='block';
	var storeName   = document.getElementById("store_name").value;
	var address     = document.getElementById("address").value;
	var city        = document.getElementById("city").value;
	var state       = document.getElementById("state").value;
	var country     = document.getElementById("country").value;
	var zip_code    = document.getElementById("zip_code").value;
	var geocoder 	= new google.maps.Geocoder();
	var address 	= address+" "+city+" "+state+" "+country;
		geocoder.geocode( { 'address': address}, function(results, status) {

  		if (status == google.maps.GeocoderStatus.OK) {
    			var latitude = results[0].geometry.location.lat();
    			var longitude = results[0].geometry.location.lng();


			document.getElementById("lat").value=latitude;
			document.getElementById("long").value=longitude;

    		var myLatlng = new google.maps.LatLng(latitude,longitude);
  var mapOptions = {
    zoom: 4,
    center: myLatlng
  }
  var map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);

  var marker = new google.maps.Marker({
      position: myLatlng,
      map: map,
      title: 'Hello World!'
  });

} 
}); 
}


