var webposReceipt = Class.create(
        {
            initialize: function (receiptTemplateId,receiptData)
            {
                this.receiptTemplateId = receiptTemplateId;
                this.receiptData = receiptData;
                this.receiptBody = '';
                this.priceElementIds = ['subtotal','tax','shipping','grandtotal','cashforpos','change','ccforpos','cp1forpos','cp2forpos','codforpos','balance','price-container','row-total','tax','change'];
                this.prepareTemplateData();
            },
            print: function ()
            {
				var print_window = window.open('', 'print_offline', 'status=1,width=500,height=700');
				print_window.document.write(this.receiptBody);
				print_window.print();
            },
            prepareTemplateData: function ()
            {
				var receiptBody = items = '';
				if($(this.receiptTemplateId)){
					var receiptData = this.receiptData;
					if(receiptData != null && typeof receiptData != 'undefined'){
						for(var elKeyId in receiptData){
							if(elKeyId == 'items'){
								continue;
							}
							if($D('#'+this.receiptTemplateId+' #offline_receipt_'+elKeyId)){
								if(this.priceElementIds.indexOf(elKeyId) >= 0){
									receiptData[elKeyId] = convertLongNumber(receiptData[elKeyId]);
								}
								var data = (this.priceElementIds.indexOf(elKeyId) >= 0)?getPriceFormatedHtml(receiptData[elKeyId]):receiptData[elKeyId];
								$D('#'+this.receiptTemplateId+' #offline_receipt_'+elKeyId).html(data);
								if(this.priceElementIds.indexOf(elKeyId) >= 0){
									if($D('#'+this.receiptTemplateId+' #offline_receipt_row_'+elKeyId)){
										if(convertLongNumber(receiptData[elKeyId]) <= 0){
											console.log('#'+this.receiptTemplateId+' #offline_receipt_row_'+elKeyId);
											$D('#'+this.receiptTemplateId+' #offline_receipt_row_'+elKeyId).addClass('hide');
										}else{
											$D('#'+this.receiptTemplateId+' #offline_receipt_row_'+elKeyId).removeClass('hide');
										}
									}
								}
							}
						}
					}
					if(typeof receiptData['items'] != 'undefined'){
						var itemsHtml = '';
						var items = receiptData['items'];
						var priceElementIds = this.priceElementIds;
						var itemsData = [];
						$D.each(items, function(i, item) {
							var itemData = {};
							$D.each(item, function(elKeyId, itemValue) {
								if(priceElementIds.indexOf(elKeyId) >= 0){
									itemValue = convertLongNumber(itemValue);
								}
								var data = (priceElementIds.indexOf(elKeyId) >= 0)?getPriceFormatedHtml(itemValue):itemValue;
								itemData[elKeyId] = data;
							});
							itemsData.push(itemData);
						});
						if(itemsData.length > 0){
							for(var i = 0;i < itemsData.length; i++){
								itemsHtml += this.getReceiptCartItemHtml(itemsData[i]);
							}
						}
						 
						if($D('#'+this.receiptTemplateId+' #offline_receipt_items')){
							$D('#'+this.receiptTemplateId+' #offline_receipt_items').html(itemsHtml);
						}
					}
					receiptBody = $(this.receiptTemplateId).innerHTML;
				}
				this.receiptBody = receiptBody;
            },
			getReceiptCartItemHtml:function(data){
				var html = '';
				html += "\
				 <tr>\
					<td class='item-name' data-metadata='item-name' align='left'>\
						<span class='name'>"+data.name+"</span>\
						<span class='sku'>"+data.sku+"</span>\
					</td>\
					<td data-metadata='qty' class='qty'>"+data.qty+"</td>\
					<td class='price-container' data-metadata='price'>"+data['price-container']+"</td>\
					<td class='tax' data-metadata='tax-amount'>"+data.name+"</td>\
					<td class='row-total' data-metadata='row-total' align='right'>"+data['row-total']+"</td>\
				</tr>\
				";
				return html;
			}
        });