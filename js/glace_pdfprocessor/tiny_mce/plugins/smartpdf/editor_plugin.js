(function() {
        tinymce.create('tinymce.plugins.SmartpdfPlugin', {
                init : function(ed, url) {
                    var t = this;
					ed.onBeforeSetContent.add(function(ed, o) {
						o.content = t['_smartpdf2html'](o.content,url);
					});
					ed.onPostProcess.add(function(ed, o) {
						if (o.get)
							o.content = t['_html2smartpdf'](o.content,url);
					});
                },
				_smartpdf2html : function(s,url) {
					s = tinymce.trim(s);

					function rep(re, str) {
						s = s.replace(re, str);
					};

					rep(/{{var MY_LOGO}}/gi,"<img class=\"smartpdf-logo\" src=\""+url+"/images/logo_bg.gif\" alt=\"\" />");
					/*rep(/{{var (.*?)}}/gi,"<span class=\"var-statement\">{{var $1}}</span>");
					rep(/{{if (.*?)}}/gi,"<div class=\"if-statement\">{{if $1}}</div>");
					rep(/{{else}}/gi,"<div class=\"if-statement\">{{else}}</div>");
					rep(/{{\/if}}/gi,"<div class=\"if-statement\">{{/if}}</div>");
					rep(/{{depend (.*?)}}/gi,"<div class=\"if-statement\">{{depend $1}}</div>");
					rep(/{{\/depend}}/gi,"<div class=\"if-statement\">{{/depend}}</div>");
					*/
					return s; 
				},
				_html2smartpdf : function(s,url) {
					s = tinymce.trim(s);

					function rep(re, str) {
						s = s.replace(re, str);
					};
					rep('<img class="smartpdf-logo" src="'+url+'/images/logo_bg.gif" alt="" />',"{{var MY_LOGO}}");
					/*
					rep(/<span class="var-statement">{{var (.*?)}}<\/span>/gi,"{{var $1}}");
					rep(/<div class="if-statement">{{if (.*?)}}<\/div>/gi,"{{if $1}}");
					rep(/<div class="if-statement">{{else}}<\/div>/gi,"{{else}}");
					rep(/<div class="if-statement">{{\/if}}<\/div>/gi,"{{/if}}");
					rep(/<div class="if-statement">{{depend (.*?)}}<\/div>/gi,"{{depend $1}}");
					rep(/<div class="if-statement">{{\/depend}}<\/div>/gi,"{{/depend}}");
					*/
					return s;
				},
                getInfo : function() {
                    return {
                        longname : 'PDF Customiser plugin',
                        author : 'PDF Customiser Invoice',
                        authorurl : 'http://www.cmsideas.net',
                        infourl : 'http://www.cmsideas.net',
                        version : "1.0"
                    };
                }
        });

        // Register plugin
        tinymce.PluginManager.add('smartpdf', tinymce.plugins.SmartpdfPlugin);
})();