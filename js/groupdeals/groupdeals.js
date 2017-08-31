//google maps functions
var geocoder;  
var map;

function initializeGoogleMaps() {
    geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(0, 0);
    var myOptions = {
        zoom: 14,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
}

function codeAddress(address, letter, imagesPath) {
    geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            map.setCenter(results[0].geometry.location);
        	document.getElementById("view_large").href = 'http://maps.google.com/maps?z=14&q='+address;
        	document.getElementById("get_directions").href = 'http://maps.google.com/maps?f=d&daddr='+address;
            var marker = new google.maps.Marker({
                map: map, 
        		title: 'A',
        		icon: imagesPath+'/marker'+letter+'.png',
                position: results[0].geometry.location
            });
        } else {
            alert("Geocode was not successful for the following reason: " + status);
        }
    });
}

function submitProductAddToCartFormGift() {
	if (document.getElementById('product_addtocart_form')) {
		var form = document.getElementById('product_addtocart_form');
    	form.action = form.action + 'gift/1/';
    
   		productAddToCartForm.submit(this);
    }
}

//update discount value on configurable/custom options change
function updateDiscount(productId) {
	var configurableOptions = $$('.super-attribute-select');
	configurableOptions.each(function(element){
	    Event.observe(element, 'change', function() { 
	    	if($('product-price-'+productId)){
	    		var price = $('product-price-'+productId).innerHTML;
	    	} else {
	    		var price = $('price-including-tax-'+productId).innerHTML;
	    	}
	    	priceParsed = parseFloat(price.replace(/\D+/g, '' ).replace(',', '.'));
	    	var oldPrice = $('old-price-'+productId).innerHTML;
	    	oldPriceParsed = parseFloat(oldPrice.replace(/\D+/g, '' ).replace(',', '.'));
	    	
	    	var discount = ((oldPriceParsed-priceParsed)*100/oldPriceParsed).toFixed(0) + '%';
	    	
	    	if($('discount-'+productId)){
	    	    $('discount-'+productId).innerHTML = discount;
	    	}
	    });
	});

	var customOptions = $$('.product-custom-option');
	customOptions.each(function(element){
	    Event.observe(element, 'change', function() { 
	    	if($('product-price-'+productId)){
	    		var price = $('product-price-'+productId).innerHTML;
	    	} else {
	    		var price = $('price-including-tax-'+productId).innerHTML;
	    	}
	    	priceParsed = parseFloat(price.replace(/\D+/g, '' ).replace(',', '.'));
	    	var oldPrice = $('old-price-'+productId).innerHTML;
	    	oldPriceParsed = parseFloat(oldPrice.replace(/\D+/g, '' ).replace(',', '.'));
	    	
	    	var discount = ((oldPriceParsed-priceParsed)*100/oldPriceParsed).toFixed(0) + '%';
	    	
	    	if($('discount-'+productId)){
	    	    $('discount-'+productId).innerHTML = discount;
	    	}
	    });
	});

	var bundleOptions = $$('.bundle-option-select');
	bundleOptions.each(function(element){
	    Event.observe(element, 'change', function() { 
	    	if($('product-price-'+productId)){
	    		var price = $('product-price-'+productId).innerHTML;
	    	} else {
	    		var price = $('price-including-tax-'+productId).innerHTML;
	    	}
	    	priceParsed = parseFloat(price.replace(/\D+/g, '' ).replace(',', '.'));
	    	var oldPrice = $('old-price-'+productId).innerHTML;
	    	oldPriceParsed = parseFloat(oldPrice.replace(/\D+/g, '' ).replace(',', '.'));
	    	
	    	var discount;
	    	if (oldPriceParsed!=0 || priceParsed!=0) {
		    	discount = ((oldPriceParsed-priceParsed)*100/oldPriceParsed).toFixed(0) + '%';
		    } else {
			    discount = 0 + '%';
		    }
	    	
	    	if($('discount-'+productId)){
	    	    $('discount-'+productId).innerHTML = discount;
	    	}
	    });
	});	
}
