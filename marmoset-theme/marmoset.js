function Marmoset_Filters() {
	this.counter = 0;

	this.filters = {
		stakeholders: [],
		members: [],
		status: []
	};
};

Marmoset_Filters.prototype.ADD = 1;
Marmoset_Filters.prototype.REMOVE = 2;
Marmoset_Filters.prototype.AUTO = 3;

// Merge updated filters into our current filters.
Marmoset_Filters.prototype.merge = function( updated ) {
	// List to add: things in "updated" that are not in existing
	var add_filters = this.diff( this.filters, updated );

	// List to remove: things in existing that are not in updated
	var remove_filters = this.diff( updated, this.filters );

	var f = this;

	$.each( add_filters, function( meta, values ) {
		$.each( values, function( i, value ) {
			f.filters[meta].push(value);

			marm.toggle_meta_filter( meta, value, true );
		});
	});

	$.each( remove_filters, function( meta, values ) {
		$.each( values, function( i, value ) {
			var index = $.inArray( value, f.filters[meta] );
			f.filters[meta].splice( index, 1 );

			marm.toggle_meta_filter( meta, value, false );
		});
	});
};

// Return a list of filters that are in the right array, but not in the left.
Marmoset_Filters.prototype.diff = function( left, right ) {
	var result = {};

	$.each( right, function( meta, values ) {
		result[meta] = result[meta] || [];

		$.each( values, function( j, value ) {
			var index = $.inArray( value, left[meta] );

			if( index == -1 ) {
				result[meta].push( value );
			}
		});
	});

	return result;
};

Marmoset_Filters.prototype.reset = function() {
	var f = this;
	$.each( this.filters, function( key, value ) {
		f.filters[key] = [];
	});
};

function Marmoset_Hash() {
	this.keypairs = {};
};

Marmoset_Hash.prototype.ADD = 1;
Marmoset_Hash.prototype.REMOVE = 2;
Marmoset_Hash.prototype.AUTO = 3;

Marmoset_Hash.prototype.add = function( meta, value ) {
	this.toggle( meta, value, this.ADD );
};

Marmoset_Hash.prototype.remove = function( meta, value ) {
	this.toggle( meta, value, this.REMOVE );
};

// Add new meta/value pair to the document's hash.
Marmoset_Hash.prototype.toggle = function( meta, value, action ) {
	action = action || this.AUTO;

	// Trim leading # from hash.
	var hash = document.location.hash.substr(1);

	this.keypairs = this.hash2obj( hash );
	var index = $.inArray( value, this.keypairs[meta] );

	this.keypairs[meta] = this.keypairs[meta] || [];

	// No need to add if it's already there.
	if( index > -1 && action == this.ADD ) {
		return;
	}

	// Likewise, don't remove if it's not there.
	if( index == -1 && action == this.REMOVE ) {
		return;
	}
	
	// Figure out what the automatic action is.
	if( action == this.AUTO ) {
		// Already there.
		if( index > -1 ) {
			action = this.REMOVE;
		} else {
			action = this.ADD;
		}
	}

	if( action == this.ADD ) {
		this.keypairs[meta].push( value );
	} else {
		this.keypairs[meta].splice( index, 1 );

		// Don't let zero-length params stick around.
		if( this.keypairs[meta].length == 0 ) {
			delete this.keypairs[meta];
		}
	}

	document.location.hash = this.toString();

	return this.keypairs;
};

Marmoset_Hash.prototype.toString = function() {
	return this.obj2hash( this.keypairs );
};

Marmoset_Hash.prototype.obj2hash = function( obj ) {
	var hash = [];
	
	$.each( obj, function( key, value ) {
		if( value.length > 0 ) {
			hash.push( [key, value.join(',')].join('=') );
		}
	});

	return hash.join('&');
};

Marmoset_Hash.prototype.hash2obj = function( hash ) {
	if( hash == '' ) {
		return {};
	}

	var query_parts = hash.split('&');
	var filters = {};

	$.each( query_parts, function(i, query_part) {
		query_part = query_part.split('=');

		var meta = query_part[0];
		var values = query_part[1];

		if( values == '' ) {
			return;
		}

		if( values.length > 0 ) {
			filters[meta] = values.split(',');
		}
	});

	return filters;
};

var marm = {
	history_options: {
		unescape: ','
	},

	// MIGRATED
	meta_filters_default: {
		counter: 0,
		stakeholders: [],
		members: [],
		status: []
	},

	// MIGRATED
	meta_filters: null,

	// a list of cached user capabilities
	user_cap: {
		edit_posts: false
	},

	// MIGRATED
	reset_meta_filters: function() {
		marm.meta_filters = marm.meta_filters_default;
	},

	// MIGRATED
	init: function() {
		this.meta_filters = this.meta_filters_default;
	},

	history_changed: function( hash ) {
		var new_filters = marm.hash.hash2obj( hash );
		marm.filters.merge( new_filters );
	},

	count_projects: function() {
		var count_found = 0;

		var filter_classes = [].concat(
			$.map( marm.meta_filters.stakeholders, function(n){ return '.stakeholders_' + n; } ),
			$.map( marm.meta_filters.members, function(n){ return '.members_' + n; } ),
			$.map( marm.meta_filters.status, function(n){ return '.status_' + n; } )
		);

		if( filter_classes.length > 0 ) {
			filter_classes = '.project.' + filter_classes.join(', .project');
			count_found = $( filter_classes ).length;
		}

		$('#project-filter-total').html( count_found );
	},

	hide_unfocused: function( state ) {
		// never hide when there are no focused items
		if( marm.meta_filters.counter == 0 ) {
			state = false;
		}

		if( state === true ) {
			$('body').not('.hide-unfocused').addClass('hide-unfocused');
		} else if( state === false ) {
			$('body').removeClass('hide-unfocused');
		} else {
			$('body').toggleClass('hide-unfocused');
		}//end else
	},

	meta_id: function( meta, member ) {
		return [meta, member].join('_');
	},

	meta_style_id: function( meta, member ) {
		return 'selected-' + marm.meta_id( meta, member );
	},

	clear_filters: function() {
		marm.reset_meta_filters();

		$('style.filter').each( function(i,e) { marm.toggle_filter_style(e,false); } );
		$('body').toggleClass( 'focus-meta', marm.meta_filters.counter > 0 );

		marm.count_projects();
		marm.update_hash();
	},

	toggle_filter_style: function( elem, enable ) {
		var $style = $(elem);

		if( typeof enable == 'undefined' ) {
			enable = $style.data('disabled');
		}

		if( enable ) {
			$style.data('disabled', false).text( $style.data('contents') );
		} else {
			$style.data('disabled', true).empty();

			if( $('#project-filter li').length == 0 ) {
				marm.hide_unfocused( false );
			}//end if
		}

		// body.focus-meta if there are active filters
		$('body').toggleClass( 'focus-meta', marm.meta_filters.counter > 0 );
	},

	get_or_create_style: function( meta, member ) {
		var $style = $('#' + marm.meta_style_id(meta, member) );

		if( $style.length == 0 ) {
			var theCss = '.focus-meta .' + marm.meta_id(meta, member) +
				'{display: block !important; opacity: 1}';

			theCss = theCss + ' #project-filter .' + meta + ' .' + member + ' a ' +
				'{color: black; background-color: red;}';

			// add in disabled state; will be enabled by toggle_filter_style
			var $style = $('<style/>')
				.attr('id', marm.meta_style_id(meta, member) )
				.addClass('filter')
				.attr('type', 'text/css')
				.data('contents', theCss)
				.data('disabled', true)
				.text( theCss );

			$style.appendTo('head');
		}

		return $style;
	},

	toggle_meta_filter: function( meta, member, toggleOn ) {
		var filter_index = $.inArray( member, marm.meta_filters[meta] ),
			$style = marm.get_or_create_style( meta, member ),
			isCurrentlyDisabled = $style.filterDisabled();

		if( typeof toggleOn == 'undefined' ) {
			toggleOn = isCurrentlyDisabled;
		}

		// If these do not match, we already have the requested state.
		if( toggleOn != isCurrentlyDisabled ) {
			return;
		}

		if( toggleOn ) {
			marm.meta_filters.counter += 1;
			marm.meta_filters[meta].push(member);
			marm.toggle_filter_style( $style, toggleOn );
		} else {
			marm.meta_filters.counter -= 1;
			marm.meta_filters[meta].splice(filter_index, 1);
			marm.toggle_filter_style( $style, toggleOn );
		}//end else

		marm.count_projects();
	},

	toggle_select: function( $o ) {
		// was an object passed in?
		if( $o ) {
			// yes.  That means either the project was clicked a second time or
			// a new one was clicked
			if( $o.hasClass('expanded') ) {
				// project was clicked a second time, remove focus from body
				$('body').removeClass('focused');
			} else {
				// a new project was clicked.  If body isn't focused, focus it
				$('body').not('.focused').addClass('focused');
			}

			// remove expanded class from projects that weren't the
			// one being clicked.
			$('.expanded').not($o).removeClass('expanded');

			// add or remove expanded from the clicked project
			$o.toggleClass('expanded');
		} else {
			// if we get here, no projects were clicked and we want
			// to remove all expanded classes and body.focused
			$('.project.expanded').removeClass('expanded');
			$('body').removeClass('focused');
		}//end else
	},

	update_numbers: function( $list ) {
		$list.children('li').each(function(i) {
			$(this).find('.item-number').html( (i + 1) + '.' );
		});
	}
};

marm.init();
marm.filters = new Marmoset_Filters();
marm.hash = new Marmoset_Hash();

/**
 * Marmoset Complexity object
 */
marm.complexity = {
	cancel: function() {
		var $el = $(this);

		if( !$el.is('.complexity') ) {
			$el = $el.closest('.complexity');
		}//end if

		var complexity = $el.data('complexity');

		marm.complexity.toggle( $el, complexity );
	},
	over: function() {
		var $el = $(this);

		var complexity = 0;
		for(; complexity < 6; complexity++ ) {
			if( $el.is('.indicator-' + complexity) ) {
				break;
			}//end if
		}//end for

		marm.complexity.toggle( $el.closest('.complexity'), complexity );
	},
	reset: function() {
		var $el = $(this).closest('.complexity');
		var complexity = $el.data('complexity-original');

		marm.complexity.toggle( $el, complexity, true );
	},
	set: function( $el ) {
		var complexity = $el.data('temp-complexity');

		marm.complexity.toggle( $el, complexity, true );
	},
	toggle: function( $el, complexity, finalize_complexity ) {
		for( var i = 0; i < 6; i++ ) {
			$el.removeClass('complexity-' + i);
		}//end for

		$el.addClass('complexity-' + complexity);
		$el.data('temp-complexity', complexity);

		if( finalize_complexity === true ) {
			$el.data('complexity', complexity);
			var params = { 
				'action': 'save_complexity',  
				'marm-complexity': $el.data('complexity'), 
				'project-id': $el.closest('li').data('postid'), 
			};
			$.post( admin_ajax, params );
			var params = { 
				'action': 'display_complexity',  
				'marm-complexity': $el.data('complexity'), 
			};
			$.post( admin_ajax, params, function(description) {
				$el.parents('div').next('div').find( '.complexity' ).text( 'Project Complexity: '+description ); 
			});
		}
	}
};

(function($) {

$.root = $(document);

marm.user_cap.edit_posts = $('body').hasClass('user-cap-edit_posts');

$('.projects').sortable({
	connectWith: '.projects',
	cursor: 'default',
	opacity: 0.4,
	disabled: !marm.user_cap.edit_posts,
	dropOnEmpty: true,
	placeholder: 'ui-state-highlight',
	stop: function(event, ui){
		// purge any lingering inline styles from dragging...because those are stupid
		ui.item.attr('style', '');
	},
	deactivate: function(event, ui){
		marm.update_numbers(ui.item.closest('.projects'));
	}
});

$.root.delegate('.projects', 'selectstart', function() { return false; });

var proj_queue = $('.project-queue').data('queue');

$.root.delegate('.project', 'click', function(e) { e.stopPropagation(); marm.toggle_select( $(this) ); });
$.root.delegate('html', 'click', function() {
	marm.toggle_select();
	$('#project-filter').hide();
});
$.root.delegate('.project .details', 'click', function(e) { e.stopPropagation(); });
$.root.delegate('.project .permalink a', 'click', function(e) { e.stopPropagation(); });

/**
 * Complexity Behaviors
 *
 * events are namespaced as marm_complexity
 */

if( marm.user_cap.edit_posts ) {
	$.root.delegate('.project .complexity .complexity-reset', 'click.marm_complexity', marm.complexity.reset); 
	$.root.delegate('.project .complexity ul', 'hover.marm_complexity', marm.complexity.cancel);
	$.root.delegate('.project .complexity', 'mouseleave.marm_complexity', marm.complexity.cancel);
	$.root.delegate('.project .complexity .indicator', 'mouseover.marm_complexity', marm.complexity.over);
	$.root.delegate('.project .complexity', 'click.marm_complexity', function(e) { 
		e.stopPropagation();
		marm.complexity.set( $(this) );
	});
}

$.root.delegate('#toggle-unfocused', 'click', function(e) {
	e.preventDefault();
	marm.hide_unfocused();
});

// Don't let clicks in the filter window close that window.
$.root.delegate('#project-filter', 'click', function(e) {
	e.stopPropagation();
});

$.root.delegate('#project-filter ul a', 'click', function(e) {
	e.preventDefault();
	e.stopPropagation();

	var $li = $(this).closest('li');

	var member = $li.attr('class'),
		meta = $li.parents('li').attr('class');

	marm.hash.toggle( meta, member );
});

$.root.delegate('.projects', 'sortstart', function(event, ui) {
	$('.project.expanded').removeClass('expanded');
	$('body').removeClass('focused');
});

$.root.delegate('.projects', 'sortupdate', function(event, ui) {
	var $e = $(ui.item),
		placement,
		target_id = $e.data('postid'),
		other_id = 0,
		proj_status = $e.closest('.project-status').data('status');

	// show the "no projects" text if this list is empty
	$(this).nextAll('p').toggle( $(this).children(':first').length == 0 );

	// don't run twice if project switched statuses
	if( ui.sender != null ) {
		return;
	}

	var $next = $e.nextAll('.project:first');

	if( $next.length == 1 ) {
		placement = 'before';
		other_id = $next.data('postid');
	} else {
		var $prev = $e.prevAll('.project:first');
		
		if( $prev.length == 1 ) {
			placement = 'after';
			other_id = $prev.data('postid');
		} else {
			placement = 'single';
		}
	}

	var args = {
		action: 'project_order',
		target_id: target_id,
		other_id: other_id,
		placement: placement,
		proj_queue: proj_queue,
		proj_status: proj_status
	};

	$.get( admin_ajax, args, function( data, ts ) {
	});
});

$.root.delegate('.submit-project .cancel', 'click.submit-project.cancel', function( e ) {
	e.preventDefault();
	$.colorbox.close();
	$.publish('submit-project-cancel');
});

$.root.delegate('.submit-project .save', 'click.submit-project.save', function( e ) { $.publish('submit-project-save'); } );

$.root.delegate( '.submit-project .stakeholders a:last', 'click.submit-project.stakeholders', function( e ) {
	e.preventDefault();
	$(e.currentTarget).closest('div').prevAll('div.hidden:first').removeClass('hidden').end().remove();
	$.root.undelegate( e.handleObj.selector, e.handleObj.origType );

	$.publish('submit-project-stakeholder-add');

	var $theCopyRow, $theA;

	$.root.delegate( '.submit-project .stakeholders a:last', 'click.submit-project.stakeholders', function( e ) {
		e.preventDefault();

		if( ! $theA ) {
			$theA = $(e.currentTarget);
			$theCopyRow = $theA.siblings().find('input').attr('value', '').end();
		}

		var $newCopyRow = $theCopyRow.clone();
		$newCopyRow.insertBefore( $theA );

		$.publish('submit-project-stakeholder-add');
	});
});

/***************
 * Colorbox Stuff
 ***************/
$(function(){
	$('.submit-proposal').colorbox({
		width: 650,
		title: 'Submit Project',
		inline: true,
		href: '.submit-project'
	});

	$.subscribe('submit-project-stakeholder-add', $.colorbox.resize);
	$.subscribe('submit-project-stakeholder-remove', $.colorbox.resize);
});

/**************
 * Document.ready
 *************/
$(function(){
	$.root.bind('keydown', 'f', function(e) {
		$('#project-filter').toggle();
	});

	$.root.bind('keydown', 'esc', function(e) {
		$('#project-filter').toggle(false);
		marm.toggle_select();
	});

	$.root.bind('keydown', 'h', function(e) {
		marm.hide_unfocused();
	});

	$.root.bind('keydown', 'c', function(e) {
		marm.clear_filters();
	});

	$.history.init( marm.history_changed, marm.history_options );
});

/**************
 * jQuery extensions
 *************/
$.fn.filterDisabled = function() {
	return this.first().data('disabled') || false;
};

})(jQuery);
