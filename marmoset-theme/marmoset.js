var marm = {
	meta_filters: [],
	count_projects: function() {
		var i = 0;
		var final_count = 0;
		var filter_classes = '';

		for( i in marm.meta_filters ) {
			filter_classes += (filter_classes ? ',' : '') + '.project.' + marm.meta_filters[i];
		}//end for

		$('#project-filter-total').html( $( filter_classes ).length );
	},
	hide_unfocused: function( state ) {
		if( state === true ) {
			$('body').not('.hide-unfocused').addClass('hide-unfocused');
		} else if( state === false ) {
			$('body').removeClass('hide-unfocused');
		} else {
			$('body').toggleClass('hide-unfocused');
		}//end else
	},
	add_meta_filter: function( meta, member, meta_contents, calc_project_count ) {
		meta_contents = meta_contents || marm.meta_contents( meta, member );
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

			if( meta == 'stakehold' ) {
				meta_data.readable_meta = 'stakeholder';
			} else if( meta == 'queue' || meta == 'status' || meta == 'complexity' ) {
				meta_data.concat = 'as its';
			}
		}//end if

		return meta_data;
	},
	toggle_meta_filter: function( meta, member, meta_contents, calc_project_count ) {
		meta_contents = meta_contents || marm.meta_contents( meta, member );

		var focus_class = 'focus-meta';

		var $filter = $('#project-filter ul');

		var meta_data = marm.meta_data( meta, member );

		var filter_index = $.inArray( meta_data.id, marm.meta_filters );

		if( !~filter_index ) {
			marm.meta_filters.push(meta_data.id);

			$('head').append('<style id="' + meta_data.style_id + '" class="selected">.focus-meta .' + meta_data.meta + '_'+ meta_data.member +'{display: block !important; opacity:1;}</style>');

			$filter.append('<li id="filter-' + meta_data.style_id +'" class="' + meta_data.meta +'">(<span class="filter_count">' + meta_data.count +'</span>) ' + meta_data.or + '<a href="' + member +'">' + meta_contents + '</a> '+meta_data.concat+' '+ meta_data.readable_meta +'</li>').closest('div').show();
		} else {
			marm.meta_filters.splice(filter_index, 1);

			$('#' + meta_data.style_id).remove();
			$('#filter-' + meta_data.style_id).remove();

			if( $('#project-filter li').length == 0 ) {
				$filter.closest('div').hide();
				marm.hide_unfocused( false );
			}//end if
		}//end else

		if( $('style.selected').length ) {
			$('body').not('.' + focus_class).addClass( focus_class );
		} else {
			$('body').removeClass( focus_class );
		}//end else

		if( calc_project_count ) {
			marm.count_projects();
		}//end if
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
	clear: function() {
		var $el = $(this).closest('.complexity');
		marm.complexity.toggle( $el, 0, true );
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
			$.post( admin_ajax , params , function(data){});
		}
	}
};

(function($) {

$.root = $(document);

$('.projects').sortable({
	connectWith: '.projects',
	cursor: 'default',
	opacity: 0.4,
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
$.root.delegate('html', 'click', function() { marm.toggle_select(); });
$.root.delegate('.project .details', 'click', function(e) { e.stopPropagation(); });
$.root.delegate('.project .permalink a', 'click', function(e) { e.stopPropagation(); });

/**
 * Complexity Behaviors
 *
 * events are namespaced as marm_complexity
 */
$.root.delegate('.project .complexity .complexity-reset', 'click.marm_complexity', marm.complexity.reset); 
$.root.delegate('.project .complexity .complexity-clear', 'click.marm_complexity', marm.complexity.clear);
$.root.delegate('.project .complexity ul', 'hover.marm_complexity', marm.complexity.cancel);
$.root.delegate('.project .complexity', 'mouseleave.marm_complexity', marm.complexity.cancel);
$.root.delegate('.project .complexity .indicator', 'mouseover.marm_complexity', marm.complexity.over);
$.root.delegate('.project .complexity', 'click.marm_complexity', function(e) { 
	e.stopPropagation();
	marm.complexity.set( $(this) );
});

$.root.delegate('#toggle-unfocused', 'click', function(e) {
	e.preventDefault();
	marm.hide_unfocused();
});

$.root.delegate('.project .meta li a, #project-filter ul a', 'click', function(e) {
	e.preventDefault();

	var meta = $(this).closest('li').attr('class');
	var meta_contents = $(this).html();
	var href = $(this).attr('href');
	var member = /[a-z_]+$/.exec( href );

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

$.root.delegate( '.submit-project .stakeholders a:last', 'click.submit-project.stakeholders', function( e ) {
	e.preventDefault();
	$(e.currentTarget).closest('div').prevAll('div.hidden:first').removeClass('hidden').end().remove();
	$.root.undelegate( e.handleObj.selector, e.handleObj.origType );

	var $theCopyRow, $theA;

	$.root.delegate( '.submit-project .stakeholders a:last', 'click.submit-project.stakeholders', function( e ) {
		e.preventDefault();

		if( ! $theA ) {
			$theA = $(e.currentTarget);
			$theCopyRow = $theA.siblings().find('input').attr('value', '').end();
		}

		var $newCopyRow = $theCopyRow.clone();
		$newCopyRow.insertBefore( $theA );
	});
});

})(jQuery);

