/**********************************************************************************************
* JsCountdown Timer script by Praveen Lobo (http://PraveenLobo.com/techblog/javascript-JsCountdown-timer/)
* This notice MUST stay intact(in both JS file and SCRIPT tag) for legal use.
* http://praveenlobo.com/blog/disclaimer/
**********************************************************************************************/
var JsCountdown = Class.create();
JsCountdown.prototype = {
    initialize: function(fromDate, toDate, id, daysText, textColor){
		this.fromDate = new Date(fromDate);
		this.endDate = new Date(toDate);
		this.countainer = document.getElementById(id);
		this.daysText = daysText;
		this.textColor = textColor;
		this.numOfDays = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];
		this.borrowed = 0, this.years = 0, this.months = 0, this.days = 0;
		this.hours = 0, this.minutes = 0, this.seconds = 0;
		this.updateNumOfDays();
		this.updateCounter();
	},

	updateNumOfDays: function(){
		var dateNow = this.fromDate;
		var currYear = dateNow.getFullYear();
		if ( (currYear % 4 == 0 && currYear % 100 != 0 ) || currYear % 400 == 0 ) {
			this.numOfDays[1] = 29;
		}
		var self = this;
		setTimeout(function(){self.updateNumOfDays();}, (new Date((currYear+1), 1, 2) - dateNow));
	},

	datePartDiff: function(then, now, MAX){
		var diff = now - then - this.borrowed;
		this.borrowed = 0;
		if ( diff > -1 ) return diff;
		this.borrowed = 1;
		return (MAX + diff);
	},

	calculate: function(){
		var futureDate = this.endDate;
		var currDate = this.fromDate;
		var dateDiff = new Date(this.endDate-this.fromDate);
		var secs = Math.floor(dateDiff.valueOf()/1000);
		this.seconds = (Math.floor(secs/1))%60;
		this.minutes = (Math.floor(secs/60))%60;
		this.hours = (Math.floor(secs/3600))%24;
		if (secs >= 86400) {
			this.days = (Math.floor(secs/86400))%100000;
		} else {
			this.days = 0;
		}
		var newSecs = this.fromDate.getSeconds() + 1;
		this.fromDate.setSeconds(newSecs);
	},

	addLeadingZero: function(value){
		return value < 10 ? ("0" + value) : value;
	},

	formatTime: function(){
		this.seconds = this.addLeadingZero(this.seconds);
		this.minutes = this.addLeadingZero(this.minutes);
		this.hours = this.addLeadingZero(this.hours);
	},

	updateCounter: function(){
		this.calculate();
		this.formatTime();
		this.countainer.innerHTML = "<div class=\"js-countdown" + (this.days > 0? "-days" : "") + "\" style=\"color:" + this.textColor + "!important;\">" + (this.days > 0? "<strong>" + this.days + "</strong> " + this.daysText : "") + " <strong>" + this.hours + "</strong>:<strong>" + this.minutes + "</strong>:<strong>" + this.seconds + "</strong></div>";
		if ( this.endDate > this.fromDate ) {
			var self = this;
			setTimeout(function(){self.updateCounter();}, 1000);
		}
	}
};