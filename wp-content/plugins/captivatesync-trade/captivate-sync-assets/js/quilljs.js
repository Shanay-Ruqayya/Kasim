/**
 * QuilJs
 * Used to generate our QuilJs powered shownotes section.
 */

jQuery(document).ready(function($){

	/**
	 * Episode show notes quill
	 */
	var publish_episode_screens = ['toplevel_page_cfm-hosting-publish-episode', 'admin_page_cfm-hosting-publish-episode', 'captivate-sync_page_cfm-hosting-publish-episode'];

	const BlockEmbed = Quill.import('blots/block/embed');
	class SnippetEmbed extends BlockEmbed {
		static create(value) {
			const node = super.create(value);
			node.setAttribute('contenteditable', 'true');
			node.innerHTML = this.transformValue(value);
			return node;
		}
		static transformValue(value) {
			let handleArr = value.split('\n')
			handleArr = handleArr.map(e => e.replace(/^[\s]+/, '').replace(/[\s]+$/, ''));
			return handleArr.join('');
		}
		static value(node) {
			return node.innerHTML;
		}
	}
	SnippetEmbed.blotName = 'SnippetEmbed';
	SnippetEmbed.className = 'cfm-ql-snippet';
	SnippetEmbed.tagName = 'div';
	Quill.register(SnippetEmbed, true);

	var quill = '',
		quill_container = '#cfm-field-wpeditor';

	if ( $( quill_container ).length ) {

		quill = new Quill(
			quill_container,
			{
				modules: {
					toolbar: '#quilljs-toolbar'
				},
				placeholder: 'Insert text here ...',
				theme: 'snow'
			}
		);

		var form = document.querySelector( '#cfm-form-publish-episode' );

		form.onsubmit = function() {
			var ql_editor = $(quill_container),
				ql_html = ql_editor.find('.ql-editor').html();

			// Populate hidden form on submit.
			var ql_post_content = document.querySelector( 'textarea[name=post_content]' );
			ql_post_content.value = ql_html;
		};

		quill.on(
			'text-change',
			function(delta, source) {
				var ql_editor = $(quill_container),
					ql_html = ql_editor.find('.ql-editor').html();

				if ( ql_html != '' && ql_html != '<p><br></p>' ) {
					$( '#cfm-field-wpeditor' ).removeClass( 'cfm-field-error' );
					$( '.cfm-show-description .ql-toolbar.ql-snow' ).removeClass( 'cfm-field-error' );
					$( '#shownotes-error' ).remove();
				}

				// LOCALSTORAGE - save custom localstorage.
				if( $.inArray( cfmsync.CFMH_CURRENT_SCREEN, publish_episode_screens) !== -1) {
					localStorage.setItem(cfmsync.CFMH_SHOWID + '_shownotes_local', JSON.stringify(quill.getContents()));
					localStorage.setItem(cfmsync.CFMH_SHOWID + '_shownotes_local_html', ql_html);
				}
			}
		);

		$('span#cfm-snippets .ql-picker-options').html('');

		// LOCALSTORAGE - populate custom localstorage.
		if( $.inArray( cfmsync.CFMH_CURRENT_SCREEN, publish_episode_screens) !== -1) {
			const shownotes_local = localStorage.getItem(cfmsync.CFMH_SHOWID + '_shownotes_local');
			quill.setContents(JSON.parse(shownotes_local));
		}

		// Insert snippet to editor.
		$( document ).on(
			'click',
			'#cfm-snippets .ql-picker-item',
			function(e) {
				var data_val = $(this).data('value'),
					selection = quill.selection.savedRange.index;

				if ('' != data_val || 'undefined' != data_val ) {

					function quillGetHTML(inputDelta) {
					    var tempCont = document.createElement("div");
					    (new Quill(tempCont)).setContents(inputDelta);
					    return tempCont.getElementsByClassName("ql-editor")[0].innerHTML;
					}

					quill.insertEmbed( selection, 'SnippetEmbed', quillGetHTML(data_val) );
					$(".cfm-ql-snippet").contents().unwrap();
				}

				$('span#cfm-snippets').removeClass('ql-expanded');
			}
		);

	}

	/**
	 * Prevent form submission on manage snippets toolbar click
	 */
	$( document ).on('click', '#cfm-manage-snippets', function(e) {
		e.preventDefault();
	});

	/**
	 * Load snippets on ql dropdown click
	 */
	$( document ).on(
		'click',
		'span#cfm-snippets',
		function(e) {

			$.ajax(
				{
					url: cfmsync.ajaxurl,
					type: 'post',
					data: {
						action: 'ql-load-snippets',
						show_id: cfmsync.CFMH_SHOWID,
						_nonce: cfmsync.ajaxnonce,
					},
					dataType: 'json',
					beforeSend: function( response ) {
						$('span#cfm-snippets .ql-picker-options').html('<div id="snippets-preloader" class="mt-2 mb-2"><div class="d-flex justify-content-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div></div>');
					},
					success: function( response ) {
						if ( 'error' == response.output ) {
							alert( "Something went wrong. Please contact support." );
						} else {
							$('select#cfm-snippets').html(response.ql_snippets);
							$('span#cfm-snippets .ql-picker-options').html(response.ql_snippets2);
						}
					}
				}
			);
		}
	);

	/**
	 * Snippet quill
	 */
	var snippet_quill = '',
		quill_snippet_container = '#cfm-snippet-ql-editor';

	if ( $( quill_snippet_container ).length ){

		var toolbarOptions = [
			['bold', 'italic', 'underline', 'strike'],        // toggled buttons
			['blockquote'],
			[{ 'size': ['small', false, 'large', 'huge'] }],
			[{ 'header': 1 }, { 'header': 2 }],
			[{ 'list': 'ordered'}, { 'list': 'bullet' }],
			[{ 'align': [] }],
			['link'],
			['clean']
		];

		snippet_quill = new Quill(
			quill_snippet_container,
			{
				modules: {
					toolbar: toolbarOptions
				},
				placeholder: 'Insert text here ...',
				theme: 'snow'
			}
		);

		snippet_quill.on(
			'text-change',
			function(delta, source) {
				var ql_editor = $(quill_snippet_container),
				ql_html = ql_editor.find('.ql-editor').html();

				$( '#snippet_content' ).html(html);

				if ( ql_html != '' && ql_html != '<p><br></p>' ) {
					$( '#cfm-snippet-ql-editor' ).removeClass( 'cfm-field-error' );
					$( '.cfm-snippet-create .ql-toolbar.ql-snow' ).removeClass( 'cfm-field-error' );
					$( '#snippet_content-error' ).remove();
				}
			}
		);

	}

	/**
	 * Checks quill content
	 */
	function isQuillEmpty(quill) {
	  if ((quill.getContents()['ops'] || []).length !== 1) { return false }
	  return quill.getText().trim().length === 0
	}

	/**
	 * Load snippets
	 */
	$.fn.loadSnippets = function() {
	    $.ajax(
			{
				url: cfmsync.ajaxurl,
				type: 'post',
				data: {
					action: 'manage-snippets',
					show_id: cfmsync.CFMH_SHOWID,
					_nonce: cfmsync.ajaxnonce,
				},
				beforeSend: function( response ) {
					$('#cfm-snippet-list').html('<div id="snippets-preloader"><div class="d-flex justify-content-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div></div>');
				},
				success: function( response ) {
					$('#cfm-snippet-list').html(response);
				}
			}
		);
   	};

	$('#cfm-snippets-modal').on('show.bs.modal', function (e) {
		$(document).loadSnippets();
	});

	/**
	 * Clear snippet fields
	 */
	$.fn.clearSnippetFields = function() {
		$( '#snippet_id' ).val('');
		$( '#snippet_title' ).val('');
		$( '#cfm-snippet-ql-editor .ql-editor' ).html('');
		$( '#snippet_content' ).html('');
		$( '#cfm-snippet-save' ).prop('disabled', false);
		$( '#cfm-snippet-save' ).html('Save');
		$( '#snippet_title-error' ).remove();
		$( '#snippet_content-error' ).remove();

		$( 'input[name=snippet_title]' ).removeClass( 'cfm-field-error' );
		$( '#cfm-snippet-ql-editor' ).removeClass( 'cfm-field-error' );
		$( '.cfm-snippet-create .ql-toolbar.ql-snow' ).removeClass( 'cfm-field-error' );
	};

	/**
	 * Show snippet create
	 */
	$.fn.showSnippetCreate = function() {
		$( '#cfm-snippet-list' ).hide();
		$( '#cfm-snippet-create' ).fadeIn();
		$( '#cfm-modal-footer-edit' ).fadeIn();
		$( '#cfm-modal-footer-default' ).hide();
	};

	/**
	 * Show snippet list
	 */
	$.fn.showSnippetList = function() {
		$( '#cfm-snippet-list' ).fadeIn();
		$( '#cfm-snippet-create' ).hide();
		$( '#cfm-modal-footer-edit' ).hide();
		$( '#cfm-modal-footer-default' ).fadeIn();
	};

	$( document ).on(
		'keyup',
		'input[name=snippet_title]',
		function(e) {
			$( '#snippet_title-error' ).remove();
			$( 'input[name=snippet_title]' ).removeClass( 'cfm-field-error' );
		}
	);

	/**
	 * Create/edit snippet button
	 */
	$( document ).on(
		'click',
		'.cfm-snippet-edit',
		function(e) {
			var snippet_id = $(this).data('id'),
				snippet_title = '',
				snippet_content = '';

			if ( snippet_id !== '' ) {
				snippet_id = $(this).data('id');
				snippet_title = $(this).attr('data-title');
				snippet_content = $(this).attr('data-content');
			}

			$( '#snippet_id' ).val(snippet_id);
			$( '#snippet_title' ).val(snippet_title);

			if ( '' == snippet_content ) {
				$( '#snippet_content' ).html('');
				$( '#cfm-snippet-ql-editor .ql-editor' ).html('');
			}
			else {
				snippet_quill.setContents(JSON.parse(snippet_content));
			}

			$(document).showSnippetCreate();
			e.preventDefault();
		}
	);

	/**
	 * Cancel snippet
	 */
	$( document ).on(
		'click',
		'#cfm-snippet-cancel',
		function(e) {
			$(document).clearSnippetFields();
			$(document).showSnippetList();
			e.preventDefault();
		}
	);

	/**
	 * Create/edit snippet
	 */
	$( document ).on(
		'click',
		'#cfm-snippet-save',
		function(e) {

			e.preventDefault();

			var snippet_id = $( 'input[name=snippet_id]' ).val(),
				snippet_title = $( 'input[name=snippet_title]' ).val(),
				snippet_content = JSON.stringify(snippet_quill.getContents()),
				snippet_textarea = $( 'textarea[name=snippet_content]' ).html(),
				errors = 0;

			if ( snippet_title == '' ) {
				$( 'input[name=snippet_title]' ).addClass( 'cfm-field-error' );
				if ( ! $( '#snippet_title-error' ).length ) {
					$( '<div id="snippet_title-error" class="cfm-field-error-text">You need a title for your snippet.</div>' ).insertAfter( 'input[name=snippet_title]' );
				}
				errors += 1;
			}

			if ( isQuillEmpty( snippet_quill ) ) {

				$( '#cfm-snippet-ql-editor' ).addClass( 'cfm-field-error' );
				$( '.cfm-snippet-create .ql-toolbar.ql-snow' ).addClass( 'cfm-field-error' );
				if ( ! $( '#snippet_content-error' ).length ) {
					$( '<div id="snippet_content-error" class="cfm-field-error-text">Please enter a reusable snippet.</div>' ).insertAfter( '#cfm-snippet-ql-editor' );
				}
				errors += 1;
			}

			if ( errors == 0 ) {
				$.ajax(
					{
						url: cfmsync.ajaxurl,
						type: 'post',
						data: {
							action: 'edit-snippet',
							show_id: cfmsync.CFMH_SHOWID,
							snippet_id: snippet_id,
							snippet_title: snippet_title,
							snippet_content: snippet_content,
							snippet_textarea: snippet_textarea,
							_nonce: cfmsync.ajaxnonce,
						},
						beforeSend: function( response ) {
							$( '#cfm-snippet-save' ).prop('disabled', true);
							$( '#cfm-snippet-save' ).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
						},
						success: function( response ) {
							$( '#cfm-snippet-save' ).prop('disabled', false);
							$( '#cfm-snippet-save' ).html('Save');

							if ( 'error' == response ) {
								alert( "Something went wrong. Please contact support." );
							} else if ( 'req_fields' == response ) {
								alert( "Please fill in the required fields." );
							} else if ( 'max_snippets_reached' == response ) {
								alert( "You've reached the maximum snippets allowed." );
							} else {
								$(document).loadSnippets();
								$(document).clearSnippetFields();
								$(document).showSnippetList();
							}
						}
					}
				);
			}

			e.preventDefault();

		}
	);

	/**
	 * Delete snippet
	 */
	$( document ).on(
		'click',
		'#cfm-snippet-list .cfm-snippet-delete',
		function(e) {

			e.preventDefault();

			var snippet_id = $(this).data('id'),
				_nonce = $(this).data('nonce'),
				delete_snippet = $('#cfm-snippet-list #snippet-' + snippet_id);

			if ( confirm( "Are you sure you want to delete this snippet? This snippet will be deleted on Captivate too." ) ) {
				$.ajax(
					{
						url: cfmsync.ajaxurl,
						type: 'post',
						data: {
							action: 'delete-snippet',
							snippet_id: snippet_id,
							show_id: cfmsync.CFMH_SHOWID,
							_nonce: _nonce,
						},
						beforeSend: function( response ) {
							delete_snippet.css({
								"background-color": "#ff3333"
							}, 500);

						},
						success: function( response ) {
							if ( 'error' == response ) {
								alert( "Something went wrong. Please contact support." );
							} else {
								delete_snippet.fadeOut(500, function() {
									delete_snippet.remove();

									$(document).loadSnippets();
								});
							}
						}
					}
				);
			}

			e.preventDefault();

		}
	);

	/**
	 * Snippet modal close
	 */
	$("#cfm-snippets-modal").on("hidden.bs.modal", function () {
		$(document).clearSnippetFields();
		$(document).showSnippetList();
	});

});
