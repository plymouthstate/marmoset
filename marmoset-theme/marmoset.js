var marm = {
	meta_filters_default: {
		counter: 0,
		stakeholders: [],
		members: [],
		status: []
	},

	meta_filters: null,

	// a list of cached user capabilities
	user_cap: {
		edit_posts: false
	},

	reset_meta_filters: function() {
		marm.meta_filters = marm.meta_filters_default;
	},

	init: function() {
		this.meta_filters = this.meta_filters_default;
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

	meta_contents: function( meta, member ) {
		return $($('.project .meta .' + meta + ' a[href$=' + member + ']').get(0)).html(); 
	},

	meta_data: function( meta, member, calc ) {
		var meta_data = {
			id: meta + '_' + member,
			concat: 'as a',
			count: 0,
			member: member,
			meta: meta,
			readable_meta: meta,
			or: ''
		};

		meta_data.style_id = 'selected-' + meta_data.id;

		if( calc ) {
			if( meta_data.meta.substr( -1 ) == 's' ) {
				meta_data.singular_meta = meta.substr( 0, meta.length - 1 );
			}//end if

			meta_data.count = $('.' + meta_data.id).length;

			if( $('#project-filter li').length > 0 ) {
				meta_data.or = 'or ';
			}//end if

			if( meta == 'stakeholder' ) {
				meta_data.readable_meta = 'stakeholder';
			} else if( meta == 'queue' || meta == 'status' || meta == 'complexity' ) {
				meta_data.concat = 'as its';
			}
		}//end if

		return meta_data;
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

		$('body').toggleClass( 'focus-meta', marm.meta_filters.counter > 0 );
	},

	toggle_meta_filter: function( meta, member, meta_contents, calc_project_count ) {
		meta_contents = meta_contents || marm.meta_contents( meta, member );

		var meta_data = marm.meta_data( meta, member ),
			$style = $('#' + meta_data.style_id),
			filter_index = $.inArray( meta_data.member, marm.meta_filters[meta_data.meta] );

		if( $style.length == 0 ) {
			var theCss = '.focus-meta .' + meta_data.meta + '_' + meta_data.member +
				'{display: block !important; opacity: 1}';

			theCss = theCss + ' #project-filter .' + meta_data.meta + ' .' + meta_data.member + ' a ' +
				'{color: black; background-color: red;}';

			// add in disabled state; will be enabled by toggle_filter_style
			var $style = $('<style/>')
				.attr('id', meta_data.style_id)
				.addClass('filter')
				.attr('type', 'text/css')
				.data('contents', theCss)
				.data('disabled', true)
				.text( theCss );

			$style.appendTo('head');
		}
		
		if( $style.data('disabled') == true ) {
			marm.meta_filters.counter += 1;
			marm.meta_filters[meta_data.meta].push(meta_data.member);
			marm.toggle_filter_style( $style );
		} else {
			marm.meta_filters.counter -= 1;
			marm.meta_filters[meta_data.meta].splice(filter_index, 1);
			marm.toggle_filter_style( $style );
		}//end else

		if( calc_project_count ) {
			marm.count_projects();
		}//end if

		marm.update_hash();
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

	update_hash: function() {
		var hash = {};


		if( marm.meta_filters.stakeholders.length  ) {
			hash.stakeholders = marm.meta_filters.stakeholders.join(',');
		}

		if( marm.meta_filters.members.length  ) {
			hash.members = marm.meta_filters.members.join(',');
		}

		if( marm.meta_filters.status.length  ) {
			hash.status = marm.meta_filters.status.join(',');
		}

		hash = $.param( hash );
		hash = decodeURIComponent( hash ); // "%2C" -> "," and others

		document.location.hash = hash;
	},

	update_numbers: function( $list ) {
		$list.children('li').each(function(i) {
			$(this).find('.item-number').html( (i + 1) + '.' );
		});
	}
};

marm.init();

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
		meta_contents = $(this).html(),
		href = $(this).attr('href'),
		meta = $li.parents('li').attr('class');

	marm.toggle_meta_filter( meta, member, meta_contents, true );
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

});

})(jQuery);
