
// EventSnowflakes Class
// based on http://pastebin.com/0j0h2Lws

EventSnowflakes = Class.create({
    initialize: function(options) {
        this.options = {
            flakes: 35,
            color: ['#e9f0e0', '#d1e8f0', '#bbb', '#ddd', '#fff'],
            text: ['&#x2744'],
            image: [''],
            speed: 2,
            textsize: {'max': 50, 'min': 10},
            imagesize: {'max': 80, 'min': 60},	
			effectsize: {},
        };
        Object.extend(this.options, options || {});
        this.elementsText = [];
        this.elementsImage = [];
        if (this.options.image.length !== 0)
            for (var i = 0; i <= this.options.flakes; i++) {
                this.elementsImage[i] = new Element('span').
                        setStyle({
                    position: 'absolute',
                    top: '0px',
                    cursor: 'default',
                    background: 'transparent'
                }).
                        update(this.options.image[this.random(this.options.image.length)]);
            }
        if (this.options.text.length !== 0)
            for (var i = 0; i <= this.options.flakes; i++) {
                this.elementsText[i] = new Element('span').
                        setStyle({
                    position: 'absolute',
                    top: '0px',
                    cursor: 'default',
                    background: 'transparent'
                }).
                        update(this.options.text[this.random(this.options.text.length)]);
            }
        document.observe('dom:loaded', this.onDomLoad.bindAsEventListener(this));
    },
    onDomLoad: function() {
        var viewport = document.viewport.getDimensions();
		var viewWidth;
		var viewHeight;
		if(this.options.effectsize.max_width > 0)
			viewWidth = this.options.effectsize.max_width;
		else
			viewWidth = viewport.width;
			
		if(this.options.effectsize.max_height > 0)
			viewHeight = this.options.effectsize.max_height;
		else	
			viewHeight = document.body.clientHeight;
		
        this.container = new Element('div', {
            'id': 'EventSnowflakes'
        });
        this.container.setStyle({
            oveflow: 'hidden'
        });
        (document.getElementsByTagName('body')[0]).appendChild(this.container);
        this.elementsImage.each(function(item) {
            this.container.appendChild(item);
            if (this.options.imagesize.max === this.options.imagesize.min) {
                item.size = this.options.imagesize.max;
            }
            else
                item.size = (this.random(this.options.imagesize.max, this.options.imagesize.min));
            item.rotate = (this.random(this.options.rotate.max, this.options.rotate.min));
            Object.extend(item, {
                cords: 0,
                across: (Math.random() * 15),
                horizontal: (0.03 + Math.random() / 10),
                sink: (this.options.speed * 0.5),
                posx: (this.random(viewWidth - item.size)),
                posy: (this.random(viewHeight - item.size))
                // posy: (this.random(100))
            });
            item.setStyle({
                left: item.posx + 'px',
                top: item.posy + 'px',
                zIndex: '1000',
                '-moz-transform': 'rotate(' + item.rotate + 'deg)',
                '-webkit-transform': 'rotate(' + item.rotate + 'deg)',
                '-o-transform': 'rotate(' + item.rotate + 'deg)',
                '-ms-transform': 'rotate(' + item.rotate + 'deg)'

            });
            item.down('img').setStyle({
                width: item.size + 'px',
//                maxHeight: item.size + 'px'
            });
        },
                this);
        this.elementsText.each(function(item) {
            this.container.appendChild(item);
            item.size = (this.random(this.options.textsize.max, this.options.textsize.min));
            Object.extend(item, {
                cords: 0,
                across: (Math.random() * 15),
                horizontal: (0.03 + Math.random() / 10),
                sink: (this.options.speed * 0.5),
                posx: (this.random(viewWidth - item.size)),
                posy: (this.random(viewHeight - item.size))
            });
            item.setStyle({
                fontSize: item.size + 'px',
                color: this.options.color[this.random(this.options.color.length)],
                left: item.posx + 'px',
                top: item.posy + 'px',
                zIndex: '1000'
            });
        },
                this);
        this.start();
    },
    move: function() {
        var viewport = document.viewport.getDimensions();
		var viewWidth;
		var viewHeight;
		if(this.options.effectsize.max_width > 0)
			viewWidth = this.options.effectsize.max_width;
		else
			viewWidth = viewport.width;
			
		if(this.options.effectsize.max_height > 0)
			viewHeight = this.options.effectsize.max_height;
		else	
			viewHeight = document.body.clientHeight;
        this.elementsImage.each(function(item) {
            item.cords += item.horizontal;
            item.posy += item.sink;
            item.setStyle({
                top: item.posy + 'px',
                left: (item.posx + item.across * Math.sin(item.cords)) + 'px'
            });						
            if (item.posy >= viewHeight - item.size / 2 || parseInt(item.getStyle('left')) > (viewWidth - 3 * item.across)) {
                item.posx = this.random(viewWidth - item.size);
                item.posy = -item.size;
            }
			// if(item.posx > 300)
				// item.posx = this.random(300,10);
			// if(item.posy > 100){
				// item.posy = -item.size;				
			// }
        },
                this);
        this.elementsText.each(function(item) {
            item.cords += item.horizontal;
            item.posy += item.sink;
            item.setStyle({
                top: item.posy + 'px',
                left: (item.posx + item.across * Math.sin(item.cords)) + 'px'
            });
            if (item.posy >= viewHeight - item.size / 2 || parseInt(item.getStyle('left')) > (viewWidth - 3 * item.across)) {
                item.posx = this.random(viewWidth - item.size);
                item.posy = -item.size;
            }
        },
                this);
    },
    random: function(max, min) {
        if (!min) {
            return Math.floor(Math.random() * max);
        }
        return Math.floor((Math.random() * (max - min + 1)) + min);
    },
            start: function() {
        this.pe = new PeriodicalExecuter(this.move.bindAsEventListener(this), 0.05);
    },
    stop: function() {
        this.pe.stop();
    }
});