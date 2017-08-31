WysiwygWidget.Widget.prototype.loadOptions = function() {
    if (!this.widgetEl.value) {
        this.switchOptionsContainer();
        return;
    }

    var optionsContainerId = this.getOptionsContainerId();
    if ($(optionsContainerId) != undefined) {
        this.switchOptionsContainer(optionsContainerId);
        return;
    }

    this._showWidgetDescription();

    var params = {widget_type: this.widgetEl.value, values: this.optionValues, editor: this.widgetTargetId};
    new Ajax.Request(this.optionsUrl,
        {
            parameters: {widget: Object.toJSON(params)},
            onSuccess: function(transport) {
                try {
                    widgetTools.onAjaxSuccess(transport);
                    this.switchOptionsContainer();
                    if ($(optionsContainerId) == undefined) {
                        this.widgetOptionsEl.insert({bottom: widgetTools.getDivHtml(optionsContainerId, transport.responseText)});
                    } else {
                        this.switchOptionsContainer(optionsContainerId);
                    }
                } catch(e) {
                    alert(e.message);
                }
            }.bind(this)
        }
    );
}

/**
 * config custom widgetPlaceholderExist
 */
tinyMceWysiwygSetup.prototype.customWidgetPlaceholderExist = function(filename) {
	return this.config.glace_widget_placeholders.indexOf(filename) != -1;
}


tinyMceWysiwygSetup.prototype.encodeWidgets = function(content) {
	return content.gsub(/\{\{widget(.*?)\}\}/i, function(match){

    var placeholderFilename = this.config.custom_image_filename;
    if (!this.customWidgetPlaceholderExist(placeholderFilename)) {
        placeholderFilename = 'default.gif';
    }
    if(this.customWidgetPlaceholderExist(placeholderFilename)) {var imageSrc = this.config.glace_widget_images_url + placeholderFilename;}
    else {var imageSrc = this.config.widget_images_url + placeholderFilename;}
    
    var imageHtml = '<div class="widget"><img';
        imageHtml+= ' id="' + Base64.idEncode(match[0]) + '"';
        imageHtml+= ' src="' + imageSrc + '"';
        imageHtml+= ' title="' + match[0].replace(/\{\{/g, '{').replace(/\}\}/g, '}').replace(/\"/g, '&quot;') + '"';
        imageHtml+= '></div>';

    return imageHtml;

    }.bind(this));
}

tinyMceWysiwygSetup.prototype.decodeWidgets = function(content) {
    return content.gsub(/<div class="widget"><img([^>]+id=\"[^>]+)><\/div>/i, function(match) {
        var attributes = this.parseAttributesString(match[1]);
        if(attributes.id) {
            var widgetCode = Base64.idDecode(attributes.id);
            if (widgetCode.indexOf('{{widget') != -1) {
                return widgetCode;
            }
            return match[0];
        }
        return match[0];
    }.bind(this));
};
function glaceReplace(re, str, content){
	return content.replace(re, str);
}
varienGlobalEvents.attachEventHandler("tinymceBeforeSetContent", function(o){
	/*encode*/
	var content = o.content;
	content = glaceReplace(/{{var MY_LOGO}}/gi,"<img class=\"smartpdf-logo\" src=\""+glaceDefaultLogoUrl+"\" alt=\"\" />",content);
	content = glaceReplace(/{{barcode (.*?)}}/gi,"<img class=\"smartpdf-barcode\" src=\""+glaceDefaultBarcode+"\" alt=\"$1\" />",content);
	content = glaceReplace(/{{var (.*?)}}/gi,"<span class=\"smartpdf-var\">{{var $1}}</span>",content);
	o.content = content;
});
varienGlobalEvents.attachEventHandler("tinymceSaveContent", function(o){
	/*Decode*/
	var content = o.content;
	content = glaceReplace(/<img class="smartpdf-logo" src="(.*?)" alt="" \/>/gi,"{{var MY_LOGO}}",content);
	content = glaceReplace(/<img class="smartpdf-barcode" src="(.*?)" alt="(.*?)" \/>/gi,"{{barcode $2}}",content);
	content = glaceReplace(/<span class="smartpdf-var">{{var (.*?)}}<\/span>/gi,"{{var $1}}",content);
	o.content = content;
});
varienGlobalEvents.attachEventHandler("tinymceExecCommand", function(l){
	tinyMCE.editors.each(function(glaceEditor){
		if(!glaceEditor.dom.hasClass(glaceEditor.dom.select('body'),currentTemplate)){
			glaceEditor.dom.addClass(glaceEditor.dom.select('body'), currentTemplate);
		}
	});
});



/**
 * 
 * fix encodeDirective and decodeDirective for upload image
 * and undo,redo button in wysiwyg editor
 * 
 **/

//tinyMceWysiwygSetup.prototype.makeDirectiveUrl = function(directive) {
//    return this.config.directives_url.replace('directive', 'directive/___directive/' + directive);
//}

tinyMceWysiwygSetup.prototype.encodeDirectives =  function(content) {	
	// collect all HTML tags with attributes that contain directives
						//			<div class="test" title=""></div>
						//			<img src="{{media url="wysiwyg/931185_472389846180947_760850344_n.jpg"}} />
						//			{{var order.test}}
    return content.gsub(/<([a-z0-9\-\_]+.+?)([a-z0-9\-\_]+=".*?\{\{.+?\}\}.*?".+?)>/i, function(match) {
        var attributesString = match[2];
        // process tag attributes string
        //string not start with var .Ex {{var order.xxx}} is not matching
        //string in {{#string}} start with media
        attributesString = attributesString.gsub(/([a-z0-9\-\_]+)="(.*?)(\{\{media.*\}\})(.*?)"/i, function(m) {
            return m[1] + '="' + m[2] + this.makeDirectiveUrl(Base64.mageEncode(m[3])) + m[4] + '"';
        }.bind(this));
       return '<' + match[1] + attributesString + '>';

    }.bind(this));
}


//tinyMceWysiwygSetup.prototype.decodeDirectives = function(content) {
//	        // escape special chars in directives url to use it in regular expression
//	        var url = this.makeDirectiveUrl('%directive%').replace(/([$^.?*!+:=()\[\]{}|\\])/g, '\\$1');
//	        var reg = new RegExp(url.replace('%directive%', '([a-zA-Z0-9,_-]+)'));
//	        return content.gsub(reg, function(match) {
//	        	console.log(match[1]);
//	            return Base64.mageDecode(match[1]);
//	        }.bind(this));
//}

