(function(){
  var cache = {};
 
  this.hc_tmpl = function hc_tmpl(str, data){
    // Figure out if we're getting a template, or if we need to
    // load the template - and be sure to cache the result.

    var fn = !/\W/.test(str) ?
      cache[str] = cache[str] || hc_tmpl(document.getElementById(str).innerHTML) :
      // Generate a reusable function that will serve as a template
      // generator (and which will be cached).
      new Function("obj",
        "var p=[],print=function(){p.push.apply(p,arguments);};" +
       
        // Introduce the data as local variables using with(){}
        "with(obj){p.push('" +
       
        // Convert the template into pure JavaScript
        str
          .replace(/[\r\t\n]/g, " ")
          .split("<%").join("\t")
          .replace(/((^|%>)[^\t]*)'/g, "$1\r")
          .replace(/\t=(.*?)%>/g, "',$1,'")
          .split("\t").join("');")
          .split("%>").join("p.push('")
          .split("\r").join("\\'")
      + "');}return p.join('');");
   
    // Provide some basic currying to the user
    return data ? fn( data ) : fn;
  };
})();

jQuery(document).on( 'click', '.hc-target ul.dropdown-menu', function(e)
{
	e.stopPropagation();
//	e.preventDefault();
});

jQuery(document).on( 'click', '.hc-confirm', function(event)
{
	if( window.confirm("Are you sure?") )
	{
		return true;
	}
	else
	{
		event.preventDefault();
		event.stopPropagation();
		return false;
	}
});

/* load ajax content into flatmodal */
function hc_click_flatmodal_closer( obj )
{
	var myParent = obj.closest( '.hc-flatmodal-parent' );
	var targetDiv = myParent.find('.hc-flatmodal-container');

	myParent.children().show();
	obj.hide();
	targetDiv.hide();
}

/* load ajax content */
function hc_click_ajax_loader( obj ){
	var targetUrl = obj.attr('href');
	if(
		( targetUrl.length > 0 ) &&
		( targetUrl.charAt(targetUrl.length-1) == '#' )
		)
	{
		return false;
	}

/* search in children */
	var myParent = obj.closest( '.hc-ajax-parent' );
	var targetDiv = myParent.find('.hc-ajax-container');
	var scrollInto = obj.hasClass('hc-ajax-scroll') ? true : false;

	if( targetDiv.length )
	{
		var currentUrl = targetDiv.data( 'targetUrl' );
		/* already loaded? then close */
		if( currentUrl == targetUrl )
		{
			targetDiv.data( 'targetUrl', '' );
			targetDiv.html('');
		}
		else
		{
			var highlightTarget = ( targetDiv.is(':visible') && (targetDiv.html().length > 0) );
			if( highlightTarget )
			{
				targetDiv.addClass( 'hc-loading' );
			}
			else
			{
				targetDiv.show();
				myParent.addClass( 'hc-loading' );
			}

			targetDiv.data( 'targetUrl', targetUrl );
			targetDiv.load( targetUrl, function()
			{
				if( highlightTarget )
				{
					targetDiv.removeClass( 'hc-loading' );
				}
				else
				{
					myParent.removeClass( 'hc-loading' );
				}

				if( scrollInto )
				{
					jQuery('html, body').animate(
						{
						scrollTop: targetDiv.offset().top - 20,
						}
					);
				}

				/* get some values from elements on the page: */
				var reloadTargetDiv = obj.closest('.hc-target');
				if( reloadTargetDiv.length > 0 )
				{
					targetDiv.data( 'return-target', reloadTargetDiv );
				}
			});
		}
	}
	else // append after parent
	{
		myParent.addClass( 'hc-loading' );
		jQuery.get( targetUrl, function(data){
			var wrap_with = myParent.data('wrap-ajax-child');
			if( wrap_with )
			{
				data = '<' + wrap_with + '>' + '<span>' + data + '</span>' + '</' + wrap_with + '>';
			}
			myParent.after( data );
			myParent.removeClass( 'hc-loading' );

			myParent[0].scrollIntoView();
			});
	}

	return false;
}

function hc_close_flatmodal( obj )
{
	var myParent = obj.closest( '.hc-flatmodal-parent' );
	if( myParent.length > 0 )
	{
		var targetDiv = myParent.find('.hc-flatmodal-container');
		var scrollInto = true;

		myParent.children(':not(.hc-flatmodal-closer)').show();
		targetDiv.hide();
		myParent.children('.hc-flatmodal-closer').hide();

		if( scrollInto )
		{
			var returnDiv = targetDiv.data('return-target');
			if( returnDiv )
			{
				jQuery('html, body').animate(
					{
					scrollTop: returnDiv.offset().top - 20,
					}
				);
			}
		}
	}
}

function hc_submit_ajax( method, targetUrl, resultDiv, thisFormData )
{
	resultDiv.addClass( 'hc-loading' );

	if( targetUrl == '-referrer-' )	{
		targetUrl = resultDiv.data('targetUrl');
		if( ! targetUrl ){
			resultDiv.removeClass( 'hc-loading' );
			return false;
		}
	}

	jQuery.ajax({
		type: method,
		url: targetUrl,
//		dataType: "json",
		dataType: "text",
		data: thisFormData,
		success: function(data, textStatus){
			resultDiv.removeClass( 'hc-loading' );

			var is_json = true;
			try {
				data = jQuery.parseJSON( data );
			}
			catch( err ){
				is_json = false;
			}

			var is_flatmodal = resultDiv.closest(".hc-flatmodal-container").length;
			var result_in_me = false;
			if( is_flatmodal ){
				result_in_me = true;
			}

			if( is_json ){
				if( data && data.redirect ){
					var parent_redirect = ( (data.parent !== undefined) && data.parent ) ? 1 : 0;
					var full_parent_redirect = ( (data.parent !== undefined) && (data.parent == 2) ) ? 1 : 0;

					if( full_parent_redirect ){
						location.reload();
					}
					/* reload me with another url */
					else if( result_in_me && (! parent_redirect) ){
						var src = resultDiv.data('targetUrl');
						if( ! src )	{
							src = data.redirect;
						}
						src = data.redirect;

						if( data.redirect != '-referrer-'){
							resultDiv.data('targetUrl', data.redirect);	
						}
						hc_submit_ajax( "GET", src, resultDiv, null )
					}
				/* reload target div in main screen */
					else {
						if( is_flatmodal ){
							hc_close_flatmodal( resultDiv );
						}

						var returnDiv = resultDiv.data('return-target');
						if( returnDiv )	{
							var src = returnDiv.data('src');
							returnDiv.addClass( 'hc-loading' );
							returnDiv.load( src, function(){
								returnDiv.removeClass( 'hc-loading' );
							});

							/* also if we have hc-page-status divs */
							jQuery('.hc-page-status').each(
								function(index)
								{
									var thisDiv = jQuery(this);
									var src = thisDiv.data('src');
									thisDiv.addClass( 'hc-loading' );
									thisDiv.load( src, function(){
										thisDiv.removeClass( 'hc-loading' );
									});
								});
						}
						else {
							// reload window
							location.reload();
						}

					// close the parent modal
						if( resultDiv.closest("#hc-modal").length )
						{
							resultDiv.closest("#hc-modal").modal('hide');
						}
					// or itself
						else
						{
	//					if( resultDiv.data('return-target') )
	//						resultDiv.hide();
						}
					}
				}
				else if( data && data.html )
				{
					resultDiv.html( data.html );
				}
				else
				{
					alert( 'Unrecognized JSON from ' + targetUrl );
				}
			}
			else
			{
				resultDiv.html( data );
			}
		}
	})
	.fail( function(jqXHR, textStatus, errorThrown){
		alert( 'Error parsing JSON from ' + targetUrl );
		alert( jqXHR.responseText );
		resultDiv.removeClass( 'hc-loading' );
		})
	.always( function(){
//		resultDiv.removeClass( 'hc-loading' );
		});
}

jQuery(document).on( 'click', 'a.hc-ajax-loader', function(e)
{
	return hc_click_ajax_loader( jQuery(this) );
});

jQuery(document).on( 'click', 'a.hc-flatmodal-loader', function(e)
{
	var obj = jQuery(this);
	var targetUrl = obj.attr('href');
	if(
		( targetUrl.length > 0 ) &&
		( targetUrl.charAt(targetUrl.length-1) == '#' )
		)
	{
		return false;
	}

/* search in children */
	var myParent = obj.closest( '.hc-flatmodal-parent' );
	if( myParent.length > 0 )
	{
		var scrollInto = true;
		var targetDiv = myParent.find('.hc-flatmodal-container');
		var currentUrl = targetDiv.data( 'targetUrl' );

		var markParent = obj.closest('div,li');

		markParent.addClass( 'hc-loading' );
		targetDiv.data( 'targetUrl', targetUrl );

		targetDiv.load( targetUrl, function()
		{
			myParent.children(':not(.hc-flatmodal-closer)').hide();
			myParent.children('.hc-flatmodal-closer').show();
			targetDiv.show();
			markParent.removeClass( 'hc-loading' );

			/* get some values from elements on the page: */
			var reloadTargetDiv = obj.closest('.hc-target');
			if( reloadTargetDiv.length > 0 )
			{
				targetDiv.data( 'return-target', reloadTargetDiv );
			}

			if( scrollInto )
			{
				var closerLink = myParent.find('.hc-flatmodal-closer');
				var animateTo = (closerLink.length > 0) ? closerLink : targetDiv;
				jQuery('html, body').animate(
					{
					scrollTop: animateTo.offset().top - 20,
					}
				);
			}
		});
		return false;
	}
});

jQuery(document).on( 'click', 'a.hc-flatmodal-closer', function(e)
{
	hc_close_flatmodal( jQuery(this) );
	var myParent = jQuery(this).closest( '.hc-flatmodal-parent' );
	if( myParent.length > 0 )
	{
		var targetDiv = myParent.find('.hc-flatmodal-container');
		var returnDiv = targetDiv.data('return-target');
		if( returnDiv )
		{
			var src = returnDiv.data('src');
			returnDiv.addClass( 'hc-loading' );
			returnDiv.load( src, function()
			{
				returnDiv.removeClass( 'hc-loading' );
			});

			/* also if we have hc-page-status divs */
			jQuery('.hc-page-status').each( 
				function(index)
				{
					var thisDiv = jQuery(this);
					var src = thisDiv.data('src');
					thisDiv.addClass( 'hc-loading' );
					thisDiv.load( src, function()
					{
						thisDiv.removeClass( 'hc-loading' );
					});
				});
		}
		return false;
	}
});

jQuery(document).on( 'click', 'a.hc-modal', function(e)
{
	// find my container
	var cont = jQuery('#hc-modal').find('.modal-body');
	if( ! cont.length )
	{
		return true;
	}

	e.preventDefault();
    var url = jQuery(this).attr("href");

// if i'm inside #hc-modal itself?
	if( jQuery(this).closest('#hc-modal').length )
	{
		cont.addClass( 'hc-loading' );
		cont.load( url, function(){
			cont.removeClass( 'hc-loading' );
			});
		return false;
	}

// find nearest hc-target
	var return_target = jQuery(this).closest('.hc-target');
	if( return_target.length > 0 )
	{
		cont.data( 'return-target', return_target );
	}
// return action
	var return_action = jQuery(this).data('return-action');
	cont.data( 'return-action', return_action );

	// load modal
	jQuery('#hc-modal').removeData('modal');
	jQuery('#hc-modal').addClass( 'hc-loading' );
	jQuery('#hc-modal').on( 'loaded', function () {
		jQuery('#hc-modal').removeClass( 'hc-loading' );
		});

	jQuery('#hc-modal').modal({
		remote: url,
		})

	return false;
});

/* submit forms by links */
jQuery(document).on( 'click', 'a.hc-form-submit', function(event)
{
	var thisLink = jQuery( this );
	var thisForm = thisLink.closest('form');
	var myAction = thisLink.prop('hash').substr(1);

	var moreCollect = thisLink.data('collect');
	if( moreCollect )
	{
		var moreAppend = [];
		jQuery("input[name^='" + moreCollect + "']").each( function()
		{
			var appendValue = jQuery(this).val();
			if( 
				( jQuery(this).attr('type') != 'checkbox' )
				|| 
				( jQuery(this).is(':checked') )
				)
			{
				moreAppend.push( appendValue );
			}
		});

		var addInput2 = jQuery("<input>").attr("type", "hidden").attr("name", moreCollect).val( moreAppend.join('-') );
		thisForm.append( addInput2 );
	}

	var addInput = jQuery("<input>").attr("type", "hidden").attr("name", "nts-action").val( myAction );
	thisForm.append( addInput );

	thisForm.submit();
	return false;
});

jQuery(document).on( 'click', 'a.hc-target-reloader', function(event)
{
	var resultDiv = jQuery(this).closest('.hc-target');
	if( resultDiv.length > 0 ){
		var src = resultDiv.data('src');
		resultDiv.addClass( 'hc-loading' );
		resultDiv.load( src, function(){
			resultDiv.removeClass( 'hc-loading' );
		});
	}
});


/*
click ajaxified links within hc-target
the hc-target is being reloaded with its data-src url after success
*/
jQuery(document).on( 'click', '.hc-target a:not(.hc-toggler,.hc-toggle,.hc-collapse-next,.hc-ajax-loader,.hc-flatmodal-loader,.hc-modal,.hc-parent-loader)', function(event)
{
	if( ! jQuery(this).hasClass('hc-target-reloader2') ){
		if( jQuery(this).closest('.hc-ajax-container').length ){
			return false;
		}
	}

	if( event.isPropagationStopped() )
		return false;

	var targetUrl = jQuery(this).attr('href');
	if(
		( targetUrl.length > 0 ) &&
			( 
			(targetUrl.charAt(targetUrl.length-1) == '#') ||
			(targetUrl.charAt(0) == '#')
			)
		)
	{
		return false;
	}

	/* stop form from submitting normally */
	event.preventDefault(); 

	/* get some values from elements on the page: */
	var resultDiv = jQuery(this).closest('.hc-target');
	resultDiv.data( 'return-target', resultDiv );

	hc_submit_ajax( 
		"GET", 
		targetUrl,
		resultDiv,
		null
		);

	return false;
});

/*
click ajaxified links within hc-ajax-container
the hc-ajax-container is being reloaded with the URL of the clicked link
*/
jQuery(document).on( 'click', '.hc-ajax-container a:not(.hc-ajax-loader,.hc-flatmodal-loader,.hc-modal,.hc-parent-loader)', function(event)
{
	var thisLink = jQuery( this );
	var targetUrl = thisLink.attr('href');
	if(
		( targetUrl.length > 0 ) &&
			( 
			(targetUrl.charAt(targetUrl.length-1) == '#') ||
			(targetUrl.charAt(0) == '#')
			)
		)
	{
		return false;
	}

	if( event.isPropagationStopped() )
		return false;

	/* stop form from submitting normally */
	event.preventDefault();

	var resultDiv = thisLink.closest('.hc-ajax-container');

	if(
		( ! thisLink.hasClass('hc-confirm') ) && 
		( ! thisLink.hasClass('hc-action') )
		)
	{
		resultDiv.data( 'targetUrl', targetUrl );
	}

	hc_submit_ajax(
		"GET",
		targetUrl,
		resultDiv,
		null
		);

	return false;
});


/*
post ajaxified forms within hc-container
the hc-target is being reloaded with its data-src url after success
*/
jQuery(document).on( 'submit', '.hc-target form:not(.hc-form-external)', function(event)
{
	if( jQuery(this).closest('.hc-ajax-container').length )
	{
		return false;
	}

	/* stop form from submitting normally */
	event.preventDefault(); 
	/* get some values from elements on the page: */
	var thisForm = jQuery( this );
	var thisFormData = thisForm.serialize();

	var targetUrl = thisForm.attr( 'action' );
	var resultDiv = thisForm.closest('.hc-target');
	resultDiv.data( 'return-target', resultDiv );

	/* Send the data using post and put the results in a div */
	hc_submit_ajax(
		"POST",
		targetUrl,
		resultDiv,
		thisFormData
		);
	return false;
});

jQuery(document).on( 'click', '.hc-action-setter', function(event)
{
	var thisForm = jQuery(this).closest('form');
	var actionFieldName = 'action';
	var actionValue = jQuery(this).attr('name');

	thisForm.find("input[name='" + actionFieldName + "']").each( function(){
		jQuery(this).val( actionValue );
	});
});

jQuery(document).on( 'submit', '.hc-ajax-container form:not(.hc-form-external)', function(event)
{
	/* stop form from submitting normally */
	event.preventDefault(); 
	/* get some values from elements on the page: */
	var thisForm = jQuery( this );
	var thisFormData = thisForm.serialize();

	var targetUrl = thisForm.attr('action');
	var resultDiv = thisForm.closest('.hc-ajax-container');

	/* Send the data using post and put the results in a div */
	hc_submit_ajax(
		"POST",
		targetUrl,
		resultDiv,
		thisFormData
		);
	return false;
});

/*
this displays more info divs for radio choices
*/
jQuery(document).on( 'change', '.hc-radio-more-info', function(event)
{
	jQuery('.hc-radio-info').hide();
	var my_container = jQuery( this ).closest('label');
	var my_info = my_container.find('.hc-radio-info');
	my_info.show();
});

/*
this displays more info divs for radio choices
*/
jQuery(document).on( 'change', '.hc-radio-more-info', function(event)
{
	jQuery('.hc-radio-info').hide();
	var my_container = jQuery( this ).closest('label');
	var my_info = my_container.find('.hc-radio-info');
	my_info.show();
});

/* toggle */
jQuery(document).on('click', '.hc-toggle', function(e)
{
	var this_target_id = jQuery(this).data('target');
	if( this_target_id.length > 0 ){
		this_target = jQuery(this_target_id);
		if( this_target.is(':visible') ){
			this_target.hide();
		}
		else {
			this_target.show();
		}
	}
	return false;
});

/* collapse next */
jQuery(document).on('click', '.hc-collapse-next,[data-toggle=collapse-next]', function(e)
{
	// var this_target = jQuery(this).parents('.collapse-panel').find('.collapse');
	var this_target = jQuery(this).closest('.collapse-panel').children('.collapse');

	if( this_target.is(':visible') ){
		this_target.hide();
	}
	else {
		this_target.show();
	}
//	this_target.collapse('toggle');

	if( jQuery(this).attr('type') != 'checkbox' )
	{
		/* scroll into view */
//		var this_parent = jQuery(this).parents('.collapse-panel');
//		this_parent[0].scrollIntoView();
		return false;
	}
	else
	{
		return true;
	}
});

jQuery(document).on('click', '.dropdown-menu select', function()
{
	return false;
});

jQuery(document).on( 'click', 'a.hc-toggler', function(event)
{
	jQuery('.hc-toggled').toggle();
	return false;
});

jQuery(document).on( 'click', '.hc-all-checker', function(event)
{
	var thisLink = jQuery( this );
	var firstFound = false;
	var whatSet = true;

	var moreCollect = thisLink.data('collect');
	if( moreCollect )
	{
		var myParent = thisLink.closest('.hc-ajax-container');
		if( myParent.length > 0 )
			myParent.first();
		else
			myParent = jQuery('#nts');

		myParent.find("input[name^='" + moreCollect + "']").each( function()
		{
			if( 
				( jQuery(this).attr('type') == 'checkbox' )
				)
			{
				if( ! firstFound )
				{
					whatSet = ! this.checked;
					firstFound = true;
				}
				this.checked = whatSet;
			}
		});
	}

	if(
		( thisLink.prop('tagName').toLowerCase() == 'input' ) &&
		( thisLink.attr('type').toLowerCase() == 'checkbox' )
		)
	{
		return true;
	}
	else
	{
		return false;
	}
});

jQuery(document).ready( function()
{
	jQuery('.hc-radio-more-info:checked').each( function()
	{
		var my_container = jQuery( this ).closest('label');
		var my_info = my_container.find('.hc-radio-info');
		my_info.show();
	});

	/* add icon for external links */
	jQuery('#nts a[target="_blank"]').append( '<i class="fa fa-fw fa-external-link"></i>' );

	/* scroll into view */
	if ( typeof nts_no_scroll !== 'undefined' )
	{
		// no scroll
	}
	else
	{
		document.getElementById("nts").scrollIntoView();	
	}

/*
	jQuery('html, body').animate(
	{
		scrollTop: jQuery("#nts").offset().top - 20,
	}, 0 );
*/

	/* auto dismiss alerts */
	jQuery('#nts .hc-auto-dismiss').delay(4000).slideUp(200, function()
	{
		jQuery('#nts .hc-auto-dismiss .alert').alert('close');
	});

	/* multiselect */
	try
	{
		jQuery('.hc-multiselect').multiselect();
	}
	catch(err)
	{
		//
	}
});

