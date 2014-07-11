// Simulates PHP's date function
Date.prototype.format=function(format){var returnStr='';var replace=Date.replaceChars;for(var i=0;i<format.length;i++){var curChar=format.charAt(i);if(i-1>=0&&format.charAt(i-1)=="\\"){returnStr+=curChar}else if(replace[curChar]){returnStr+=replace[curChar].call(this)}else if(curChar!="\\"){returnStr+=curChar}}return returnStr};Date.replaceChars={shortMonths:['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],longMonths:['January','February','March','April','May','June','July','August','September','October','November','December'],shortDays:['Sun','Mon','Tue','Wed','Thu','Fri','Sat'],longDays:['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],d:function(){return(this.getDate()<10?'0':'')+this.getDate()},D:function(){return Date.replaceChars.shortDays[this.getDay()]},j:function(){return this.getDate()},l:function(){return Date.replaceChars.longDays[this.getDay()]},N:function(){return this.getDay()+1},S:function(){return(this.getDate()%10==1&&this.getDate()!=11?'st':(this.getDate()%10==2&&this.getDate()!=12?'nd':(this.getDate()%10==3&&this.getDate()!=13?'rd':'th')))},w:function(){return this.getDay()},z:function(){var d=new Date(this.getFullYear(),0,1);return Math.ceil((this-d)/86400000)}, W:function(){var d=new Date(this.getFullYear(),0,1);return Math.ceil((((this-d)/86400000)+d.getDay()+1)/7)},F:function(){return Date.replaceChars.longMonths[this.getMonth()]},m:function(){return(this.getMonth()<9?'0':'')+(this.getMonth()+1)},M:function(){return Date.replaceChars.shortMonths[this.getMonth()]},n:function(){return this.getMonth()+1},t:function(){var d=new Date();return new Date(d.getFullYear(),d.getMonth(),0).getDate()},L:function(){var year=this.getFullYear();return(year%400==0||(year%100!=0&&year%4==0))},o:function(){var d=new Date(this.valueOf());d.setDate(d.getDate()-((this.getDay()+6)%7)+3);return d.getFullYear()},Y:function(){return this.getFullYear()},y:function(){return(''+this.getFullYear()).substr(2)},a:function(){return this.getHours()<12?'am':'pm'},A:function(){return this.getHours()<12?'AM':'PM'},B:function(){return Math.floor((((this.getUTCHours()+1)%24)+this.getUTCMinutes()/60+this.getUTCSeconds()/ 3600) * 1000/24)}, g:function(){return this.getHours()%12||12},G:function(){return this.getHours()},h:function(){return((this.getHours()%12||12)<10?'0':'')+(this.getHours()%12||12)},H:function(){return(this.getHours()<10?'0':'')+this.getHours()},i:function(){return(this.getMinutes()<10?'0':'')+this.getMinutes()},s:function(){return(this.getSeconds()<10?'0':'')+this.getSeconds()},u:function(){var m=this.getMilliseconds();return(m<10?'00':(m<100?'0':''))+m},e:function(){return"Not Yet Supported"},I:function(){return"Not Yet Supported"},O:function(){return(-this.getTimezoneOffset()<0?'-':'+')+(Math.abs(this.getTimezoneOffset()/60)<10?'0':'')+(Math.abs(this.getTimezoneOffset()/60))+'00'},P:function(){return(-this.getTimezoneOffset()<0?'-':'+')+(Math.abs(this.getTimezoneOffset()/60)<10?'0':'')+(Math.abs(this.getTimezoneOffset()/60))+':00'},T:function(){var m=this.getMonth();this.setMonth(0);var result=this.toTimeString().replace(/^.+ \(?([^\)]+)\)?$/,'$1');this.setMonth(m);return result},Z:function(){return-this.getTimezoneOffset()*60},c:function(){return this.format("Y-m-d\\TH:i:sP")},r:function(){return this.toString()},U:function(){return this.getTime()/1000}};

jQuery('.nts-ajax-container form').live( 'submit', function(event) {
	/* stop form from submitting normally */
	event.preventDefault(); 
	/* get some values from elements on the page: */
	var thisForm = jQuery( this );
	var thisFormData = thisForm.serialize();
	thisFormData += '&nts-view-mode=ajax';

	var targetUrl = thisForm.attr( 'action' );
	var resultDiv = thisForm.closest('.nts-ajax-container');

	resultDiv.addClass( 'hc-loading' );

	/* Send the data using post and put the results in a div */
	jQuery.ajax({
		type: "POST",
		url: targetUrl,
		data: thisFormData,
		success: function(msg){
			resultDiv.html( msg );
			ntsCompleteAjaxLoad( resultDiv, false );
			}
		});
	return false;
	});

/* click ajaxified links  */
jQuery(document).on( 'click', '.nts-ajax-container a:not(.nts-ajax-loader)', function(event)
{
	if( jQuery( this ).hasClass('nts-no-ajax') )
	{
		return true;
	}

	var thisLink = jQuery( this );
	var targetUrl = thisLink.attr('href');

	if( targetUrl.length > 0 )
	{
		if( targetUrl.charAt(0) == '#' )
		{
			return false;
		}

		if( targetUrl.charAt(targetUrl.length-1) != '#' )
		{
			/* stop form from submitting normally */
//			event.preventDefault();

			var thisFormData = 'nts-view-mode=ajax';
			var resultDiv = thisLink.closest('.nts-ajax-container');

			var parentTarget = false;
			if( thisLink.is('.nts-target-parent') ){
				// if there's a parent of parent
				resultDiv = thisLink.closest('.nts-ajax-return');
				if( resultDiv.length <= 0 ){
					parentTarget = true;
					}
				}
			else if( thisLink.is('.nts-target-parent2') ){
				// if there's a parent of parent
				resultDiv = thisLink.closest('.nts-ajax-return').parents('.nts-ajax-return');
				if( resultDiv.length <= 0 ){
					parentTarget = true;
					}
				else {
					resultDiv = resultDiv.first();
					}
				}

			if( parentTarget ){
				return true;
				}
			else {
				resultDiv.addClass( 'hc-loading' );

			/* Send the data using get and put the results in a div */
				jQuery.ajax({
					type: "GET",
					url: targetUrl,
					data: thisFormData,
					success: function(msg){
						resultDiv.html( msg );
						ntsCompleteAjaxLoad( resultDiv, false );
						}
					});
				return false;
				}
			}
	}
	return false;
});

jQuery('a.nts-toggler').live( 'click', function() 
{
	var myParent = jQuery(this).closest('.nts-toggle-container');
	/* toggle all checkboxes within */
	myParent.find('input[type=checkbox]').each( function()
		{
		jQuery(this).prop( 'checked', ! jQuery(this).prop('checked') );
		});
	return false;
});

/* expand existing content */
jQuery('a.nts-sublist-expander').live( 'click', function() {
	var myParent = jQuery(this).closest('li');

// find which one is clicked	
	var myText = jQuery(this).html();
	var myIndex = -1;
	var ii = 0;
	myParent.find('a.nts-sublist-expander').each( function(){
		if( jQuery(this).html() == myText ){
			myIndex = ii;
			}
		ii++;
		});
	if( myIndex < 0 ){
		return false;
		}

// now toggle corresponding sublist
	var ii = 0;
	myParent.children('ul.nts-sublist').each( function(){
		if( ii == myIndex ){
			jQuery(this).toggle();
			}
		else {
			jQuery(this).hide();
			}
		ii++;
		});
	return false;
});

/* load ajax content */
jQuery('a.nts-ajax-loader').live( 'click', function() {
	var targetUrl = jQuery(this).attr('href');
	if( 
		( targetUrl.length > 0 ) &&
		( targetUrl.charAt(targetUrl.length-1) == '#' )
		){
		return false;
		}

	targetUrl += '&nts-view-mode=ajax';
	var myTitle = jQuery(this).html();
	var scrollInto = jQuery(this).hasClass('nts-ajax-scroll') ? true : false;

/* if target container specified */
	var targetPrefix = 'nts-target-';
	var myId = jQuery(this).attr('id');
	if( myId && myId.substr(0, targetPrefix.length) == targetPrefix ){
		var targetId = 'nts-ajax-container-' + myId.substr(targetPrefix.length);
		var targetDiv = jQuery('#' + targetId);
		}
/* else search in children */
	else {
		var myParent = jQuery(this).closest( '.nts-ajax-parent' );
		var targetDiv = myParent.find('.nts-ajax-container').first();
		}

	if( targetDiv.data('targetUrl') == targetUrl )
	{
		targetDiv.html('');
		targetDiv.hide();
		targetDiv.data( 'targetUrl', '' );
	}
	else
	{
		targetDiv.show();
		targetDiv.addClass( 'hc-loading' );
		targetDiv.data( 'targetUrl', targetUrl );
		targetDiv.load( targetUrl, function()
		{
			ntsCompleteAjaxLoad( targetDiv, scrollInto );
		});
	}
	return false;
});

function ntsCompleteAjaxLoad( targetDiv, scrollInto )
{
	targetDiv.removeClass( 'hc-loading' );
	targetDiv.find('.nts-auto-load').each( function()
	{
		var returnDiv = jQuery( this );
		if( returnDiv.hasClass('nts-ajax-container') )
		{
			returnDiv.show();
		}

		var src = returnDiv.data('src');
		src += '&nts-view-mode=ajax';
		returnDiv.addClass( 'hc-loading' );
		returnDiv.load( src, function()
		{
			returnDiv.removeClass( 'hc-loading' );
		});
	});

	/* add icon for external links */
	targetDiv.find('a[target="_blank"]').append( '<i class="fa fa-fw fa-external-link"></i>' );

	if( scrollInto )
	{
		//targetDiv[0].scrollIntoView();
		jQuery('html, body').animate(
			{
			scrollTop: targetDiv.offset().top - 20,
			}
		);
	}
}

jQuery(document).ready( function()
{
	jQuery('.nts-auto-load').each( function()
	{
		var returnDiv = jQuery( this );
		if( returnDiv.hasClass('nts-ajax-container') )
		{
			returnDiv.show();
		}

		var src = returnDiv.data('src');
		src += '&nts-view-mode=ajax';
		returnDiv.addClass( 'hc-loading' );
		returnDiv.load( src, function()
		{
			returnDiv.removeClass( 'hc-loading' );
		});
	});
});

// nasty ie bug
if( !Array.indexOf ){
	Array.prototype.indexOf = function(obj){
		for(var i=0; i<this.length; i++){
			if(this[i]==obj){
				return i;
				}
			}
		return -1;
		}
	}

function ntsZeroFill( number, width ){
	width -= number.toString().length;
	if ( width > 0 ){
		return new Array( width + (/\./.test( number ) ? 2 : 1) ).join( '0' ) + number;
		}
	return number + ""; // always return a string
	}
