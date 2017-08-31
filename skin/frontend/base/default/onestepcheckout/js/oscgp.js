/**
 * OneStepCheckout
 * 
 * NOTICE OF LICENSE
 *
 * This source file is subject to One Step Checkout AS software license.
 *
 * License is available through the world-wide-web at this URL:
 * https://www.onestepcheckout.com/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to mail@onestepcheckout.com so we can send you a copy immediately.
 *
 * @category   Idev
 * @package    Idev_OneStepCheckout
 * @copyright  Copyright (c) 2009 OneStepCheckout  (https://www.onestepcheckout.com/)
 * @license    https://www.onestepcheckout.com/LICENSE.txt
 */

/*modif*/
var oscAutocomplete = Class
    .create({
        initialize: function(type, config) {
            this.config = config;
            this.bComponentForm = {
                street_number: {
                    type: 'short_name',
                    target: 'billing:street1'
                },
                route: {
                    type: 'short_name',
                    target: 'billing:street1'
                },
                locality: {
                    type: 'short_name',
                    target: 'billing:city'
                },
                administrative_area_level_1: {
                    type: 'short_name',
                    target: 'billing:region'
                },
                administrative_area_level_2: {
                    type: 'short_name',
                    target: 'billing:region'
                },
                country: {
                    type: 'short_name',
                    target: 'billing:country_id'
                },
                postal_code: {
                    type: 'short_name',
                    target: 'billing:postcode'
                }
            };
            this.sComponentForm = {
                street_number: {
                    type: 'short_name',
                    target: 'shipping:street1'
                },
                route: {
                    type: 'short_name',
                    target: 'shipping:street1'
                },
                locality: {
                    type: 'short_name',
                    target: 'shipping:city'
                },
                administrative_area_level_1: {
                    type: 'short_name',
                    target: 'shipping:region'
                },
                administrative_area_level_2: {
                    type: 'short_name',
                    target: 'shipping:region'
                },
                country: {
                    type: 'short_name',
                    target: 'shipping:country_id'
                },
                postal_code: {
                    type: 'short_name',
                    target: 'shipping:postcode'
                }
            };

            var funcField = false;
            if (type === 'billing') {
                funcField = $('billing:street1');
            }

            if (type === 'shipping') {
                funcField = $('shipping:street1');
            }

            if (funcField) {
                funcField.observe('focus', function(e){
                    e.element().writeAttribute('autocomplete', 'false');
                });
                var _self = this;
                if (type === 'billing') {
                    this.bautocomplete = new google.maps.places.Autocomplete((funcField), {
                        types: ['geocode']
                    });
                    google.maps.event.addListener(_self.bautocomplete, 'place_changed', function() {
                        _self.fillInAddress(type, _self.bautocomplete);
                    });
                }
                if (type === 'shipping') {
                    this.sautocomplete = new google.maps.places.Autocomplete((funcField), {
                        types: ['geocode']
                    });
                    google.maps.event.addListener(_self.sautocomplete, 'place_changed', function() {
                        _self.fillInAddress(type, _self.sautocomplete);
                    });
                }

            }

        },

        fillInAddress: function(type, autocomplete) {

            var place = autocomplete.getPlace();
            var subpremise = ''; // appartment number
            var streetNum = '';
            var streetName = '';
            var streetTargetField = null;
            var regionId = '';
            var regionName = '';
            var regionTargetField = null;
            var country = '';
            var componentForm = this.bComponentForm;
            var levelTwo = false;
            var adm_lev_1 = '';
            var adm_lev_1_long = '';
            var adm_lev_2 = '';
            var adm_lev_2_long = '';

            if (type === 'shipping') {
                componentForm = this.sComponentForm;
            }

            var regionUpdater = billingRegionUpdater;

            if (type === 'shipping') {
                regionUpdater = shippingRegionUpdater;
            }

            var addressComponents = place.address_components;

            if(addressComponents){

                for (element in componentForm) {
                    var elementTarget = $(componentForm[element]['target']);
                    if (elementTarget) {
                        elementTarget.value = '';
                    }
                }

                for (var i = 0; i < place.address_components.length; i++) {
                    var addressType = place.address_components[i].types[0];
                    
                    if (addressType === 'subpremise') {
                        subpremise = place.address_components[i].long_name;
                    }  
                    
                    var field = componentForm[addressType];
                    if (field) {
                        var val = place.address_components[i][field.type];
                      
                        
                        if (addressType === 'street_number') {
                            streetNum = val;
                        }
                        if (addressType === 'route') {
                            streetName = val;
                            streetTargetField = $(field.target);
                            val = val + ' ' + streetNum;
                        }

                        if (addressType === 'administrative_area_level_2') {
                            levelTwo = true;
                            regionId = val;
                            regionName = place.address_components[i]['long_name'];
                            adm_lev_2 = val;
                            adm_lev_2_long = regionName;
                            continue;
                        }

                        if (addressType === 'administrative_area_level_1') {
                            adm_lev_1 = val;
                            adm_lev_1_long = place.address_components[i]['long_name'];
                        }

                        if (addressType === 'administrative_area_level_1' && !levelTwo) {
                            regionId = val;
                            regionName = place.address_components[i]['long_name'];
                            continue;
                        }

                        targetField = $(field.target);
                        if (targetField) {
                            targetField.value = val;
                        }

                        if (addressType === 'country') {

                            // remember for later
                            country=val;

                            // for some countries region is in adm_lev_1
                            if (country == "AU" ||country == "CA") {
                                regionId=adm_lev_1;
                                regionName=adm_lev_1_long;
                            }

                            if (typeof regionUpdater === 'object') {
                                regionUpdater.update();
                                if(regionUpdater.regions[val]) {
                                    for (region in regionUpdater.regions[val]) {
                                        if (regionUpdater.regions[val][region]['name'] === regionId || 
                                        		regionUpdater.regions[val][region]['code'] === regionId ||
                                        		regionUpdater.regions[val][region]['code'] === country+"-"+regionId) {
                                            var regionIdTarget = $(type + ':region_id');
                                            if (regionIdTarget) {
                                                regionIdTarget.value = region;
                                            }
                                        }
                                    }
                                } else {
                                    var regionIdTarget = $(type + ':region_id');
                                    if (regionIdTarget) {
                                        regionIdTarget.value = '';
                                    }
                                    var regionTarget = $(type + ':region')
                                    if (regionTarget && regionName) {
                                        regionTarget.value = regionName;
                                    }
                                }

                            }

                        }

                    }
                }

                // for some countries street NAME+NR, others NR+NAME
                var streetCombined = '';
                if (country == "AU" ||country == "US" || country == "GB" || country == "CA" ) {
                	if (subpremise!='') {
                		streetCombined = subpremise + "/";
                	}
                    streetCombined = streetCombined + streetNum + ' ' + streetName;
                } else {
                    streetCombined = streetName + ' ' + streetNum;
                }
                streetTargetField.value = streetCombined;


            } else {

            }

            if(this.config.enableajaxsavebilling){
                get_save_billing_function(this.config.sburl, this.config.smuorl, this.updatepayment , true)();
            }
        }

    });