var ghostpoolImporter = ghostpoolImporter || {};
(function($){
	
	"use strict";

	var ghostpoolLoader = '<div class="sk-folding-cube"> <div class="sk-cube1 sk-cube"></div> <div class="sk-cube2 sk-cube"></div> <div class="sk-cube4 sk-cube"></div> <div class="sk-cube3 sk-cube"></div> </div>';

	var GhostPoolModal = function() {};
	$.extend( GhostPoolModal.prototype, {
		modal: '',
		closeButton: '<a href="#" class="cd-popup-close img-replace"></a>',
		progressBar: ' <div class="progress"><div class="progress-bar"></div></div>',
		getStructure: function( options ) {

			var out     = '',
				loader  = '',
				classes = 'cd-popup',
				containerClasses = 'cd-popup-container';

			if (options.loader) {
				loader = ghostpoolLoader ;
			}

			if (options.nobg) {
				containerClasses += ' no-bg';
			}
			out += '<div class="' + classes + '" role="alert">' +
				'<div class="' + containerClasses + '">' +
					'<div class="cd-loader">' + loader + '</div>' +
					'<div class="cd-text">' + options.text + '</div>';
			if ( options.buttons ) {
				out += '<ul class="cd-buttons">' +
					'<li><a class="yes-modal" href="#">GO!</a></li>' +
					'<li><a class="no-modal" href="#">Back</a></li>' +
				'</ul>';
			}
			if ( options.progress ) {
				out += this.progressBar;
			}

			if ( options.closeBtn ) {
				out += this.closeButton;
			}
			out += '</div> <!-- cd-popup-container -->' +
				'</div> <!-- cd-popup -->';

			return out;
		},
		open: function( data ) {

			var options = $.extend({
				text: "",
				nobg: false,
				loader: false,
				closeBtn: false,
				progress: false,
				progressData: '',
				buttons: true
			}, data);


			this.modal = $( this.getStructure( options ) );
			this.modal.appendTo('body');

			$('.cd-popup').addClass('is-visible');

			if ( options.progressData ) {
				this.update({ progressData: options.progressData });
			}

			var $this = this;
			$('.yes-modal').on('click', this.modal, function(e) {
				//$this.hide();
				$this.modal.trigger('modal-confirmed');
				e.preventDefault();
				return false;
			});
			$('.no-modal').on('click', this.modal, function(e) {
				$this.close();
				$this.modal.trigger('modal-not-confirmed');
				e.preventDefault();
				return false;
			});

			this.modal.on('click', '.cd-popup-close', function(e) {
				$this.close();
				e.preventDefault();
				return false;
			});

		},

		close: function() {
			this.modal.trigger('modal-closed');
			this.modal.remove();
		},

		hide: function() {
			this.modal.removeClass('is-visible');
		},

		update: function( data ) {
			if (data.hasOwnProperty("text") && data.text != '') {
				this.modal.find('.cd-text').html(data.text);
			}

			if (data.hasOwnProperty("loader")) {
				if (data.loader) {
					this.modal.find('.cd-loader').html(ghostpoolLoader);
				} else {
					this.modal.find('.cd-loader').html('');
				}
			}

			if (data.hasOwnProperty("closeBtn")) {
				if (data.closeBtn) {
					this.modal.find('.cd-popup-close').remove();
					this.modal.find('.cd-popup-container').append(this.closeButton);
				} else {
					this.modal.find('.cd-popup-close').remove();
				}
			}

			if (data.hasOwnProperty("nobg")) {
				if (data.nobg) {
					this.modal.find('.cd-popup-container').addClass('no-bg');
				} else {
					this.modal.find('.cd-popup-container').removeClass('no-bg');
				}
			}

			if (data.hasOwnProperty("progress")) {
				if (data.progress) {
					this.modal.find('.progress').remove();
					this.modal.find('.cd-popup-container').append(this.progressBar);
				} else {
					this.modal.find('.progress').remove();
				}
			}

			if (data.hasOwnProperty("progressData")) {
				if (data.progressData) {
					this.modal.find('.progress').remove();
					this.modal.find('.cd-popup-container').append(this.progressBar);
					this.modal.find('.progress-bar').attr('style', 'width: ' + data.progressData + '%;');
				}
			}
		}
	});

	var importerAction = function() {};
	$.extend( importerAction.prototype, {
		pendingProgress: null,
		updatingProgress: false,
		updatePeriod: 800,
		progressInterval: null,
		failedCount: 0,
		failedMax: 10,
		pendingRequest: null,
		ajaxModal: null,
		importModal: null,
		messageModal: null,
		pid: Math.round( new Date().getTime() + ( Math.random() * 100 ) ),
		data: null,

		init: function( el ) {
			this.pendingProgress = null;
			this.pendingRequest= null;
			this.updatingProgress= false;
			this.progressInterval= null;
			this.failedCount= 0;

			this.ajaxModal = new GhostPoolModal();
			this.importModal = new GhostPoolModal();
			this.messageModal = new GhostPoolModal();
			this.importModal.open( { text: $(el).siblings('.gp-demo-left').html() , buttons: true, nobg: false } );
			var self = this;

			$('.cd-popup .tooltip-me').tooltip({
				position : { my: 'center bottom', at: 'center top-10' }
			});

			self.importModal.modal.on('modal-confirmed', function() {

				var importData = self.importModal.modal.find('input[name]:checked');
				if (! importData.length) {
					self.messageModal.open({text: 'The selection is empty!', buttons: false, closeBtn: true});
					return false;
				}

				var data = {
					action: 'ghostpool_single_import',
					options: importData.serialize(),
					security: $('.gp-import-form').find('[name="ghostpool_import_nonce"]').val(),
					pid: self.pid
				};
				data.options += "&import_demo=" + $(el).val() ;

				self.data = data;

				self.pendingRequest = self.ajaxRequest();

				self.progressInterval = setInterval(function() {self.checkProgress(self);}, self.updatePeriod);

			});
		},

		ajaxRequest: function(e) {
			var self = null;
			if ( e !== undefined ) {
				self = e;
			} else {
				self = this;
			}

			return $.ajax({
				url: ajaxurl,
				method: 'POST',
				data: self.data,
				beforeSend: function( xhr ) {
					//console.log('doing main ajax...');
					self.importModal.close();
					self.ajaxModal.open( { text: "Starting import...", buttons: false, nobg: true, loader: true, progressData: '10' } );
				},
				statusCode: {
					500: function() {
						self.onFail(self);
					}
				},
				error: function() {
					self.onFail(self);
				}
			})
				.done(function( response ) {

					//stop progress loop
					clearInterval(self.progressInterval);

					//abort progress check existing request
					if ( self.pendingProgress !== null ) {
						//console.log('abortin\' progress check');
						self.pendingProgress.abort();
					}

					var dataText = '';

					if ( response.hasOwnProperty('success') ) {

						dataText = 'Import is now complete!!!';
						if (response.hasOwnProperty('data') && response.data.hasOwnProperty('message') ) {
							dataText = response.data.message
						}
						if (response.success == false ) {
							dataText += '<br><small>Debug data: ' + JSON.stringify(response.data.debug) + '</small>';
						}

						self.ajaxModal.update({
							text: dataText,
							loader: false,
							closeBtn: true,
							nobg: false,
							progress: false
						});

					} else {

						dataText = JSON.stringify(response);

						self.ajaxModal.update({
							text: dataText,
							loader: false,
							closeBtn: true,
							nobg: false,
							progress: false
						});
					}

				})
				.fail(function() {
					self.onFail(self);
				});
		},
		onFail: function(self) {

			clearInterval(self.progressInterval);
			if ( self.pendingProgress !== null ) {
				//console.log('abortin');
				self.pendingProgress.abort();
			}

			self.ajaxModal.close();
			self.failedCount ++;
			if (self.failedCount < self.failedMax) {
				self.pendingRequest = self.ajaxRequest();
			} else {
				self.pendingRequest = null;
				self.ajaxModal.open( {text: 'Failed to import all data. Please try again!', loader: false, closeBtn: true, nobg: false, progress: false, buttons: false } );
			}

		},

		checkProgress: function(e) {

			var self = e;
			if(self.updatingProgress === true) return;
			self.data.check_progress = 1;

			self.pendingProgress = $.ajax({
				url: ajaxurl,
				method: 'POST',
				data: self.data,
				beforeSend: function( xhr ) {
					self.updatingProgress = true;
					//console.log('doing progress ajax...');
				},
				error: function() {
					self.updatingProgress = false;
				}
			}).done(function(response) {

				self.updatingProgress = false;
				if (self.pendingRequest == null) {
					return false;
				}
				if (response.hasOwnProperty('data') && response.data.hasOwnProperty('message') && response.data.hasOwnProperty('progress') ) {
					if (response.data.message && response.data.progress) {
						self.ajaxModal.update({progressData: response.data.progress, text: response.data.message});
					}
				}
			}).fail(function() {
				self.updatingProgress = false;
				//clearInterval(self.progressInterval);
			});
		}
	});

	ghostpoolImporter.action = new importerAction();

	$(document).ready(function() {

		var $body = $('body');
		ghostpoolImporter.generalModal = new GhostPoolModal();

		//new ajax functionality
		$('.gp-import-button').on( 'click', function(e) {
			ghostpoolImporter.action.init( this );

			e.preventDefault();
			return false;

		});

		//set as home
		$body.on('click', '.gp-set-as-home', function() {
			var self = $(this);
			$.ajax({
				url: ajaxurl,
				method: 'POST',
				data: {
					action: 'ghostpool_set_as_home', pid: self.data('pid'),
					security: $('.gp-import-form').find('[name="ghostpool_import_nonce"]').val()
				},
				beforeSend: function( xhr ) {
					//console.log('doing progress ajax...');
				}
			}).done(function(response) {
				if ( response.hasOwnProperty('success') ) {
					if (response.hasOwnProperty('data') && response.data.hasOwnProperty('message')) {
						ghostpoolImporter.generalModal.open({text: response.data.message, buttons: false, closeBtn: true});
						self.remove();
					}
				} else {
					ghostpoolImporter.generalModal.open({text: JSON.stringify(response), buttons: false, closeBtn: true});
				}
			});
			return false;
		});

		/* Un-check the Attachments checkbox if no page is checked */
		$body.on("change", "input.check-page", function() {
			var inputContainer = $(this).closest('.gp-demo-checkboxes');
			if( inputContainer.find("input.check-page:checked").length == 0 ) {

				/* Set initial state before un-checking */
				if (inputContainer.find("input.check-attachment").prop('checked') == true) {
					inputContainer.find("input.check-attachment").data('initial-state', 'checked');
				} else {
					inputContainer.find("input.check-attachment").data('initial-state', 'unchecked');
				}

				inputContainer.find("input.check-attachment").prop('disabled', true);
				inputContainer.find("input.check-attachment").prop('checked', false);

			} else {
				inputContainer.find("input.check-attachment").prop('disabled', false);

				if (inputContainer.find("input.check-page:checked").length == 1 && $(this).is(":checked") && inputContainer.find("input.check-attachment").data('initial-state')) {
					var initialState = inputContainer.find("input.check-attachment").data('initial-state');

					if (initialState == 'checked') {
						inputContainer.find("input.check-attachment").prop('checked', true);
					}
				}
			}
		});

		$body.on("change", '.gp-demo-checkboxes input[type=checkbox]',function() {
			var $isChecked = false;
			$(this).closest('.gp-demo-checkboxes').find('input[type=checkbox]').each(function (index, element) {
				if( $(this).is(":checked")) {
					$isChecked = true;
				}
			});

			if ($isChecked === false) {
				$(this).closest(".theme-id-container").find('.gp-import-button').prop('disabled', true);
			} else {
				$(this).closest(".theme-id-container").find('.gp-import-button').prop('disabled', false);
			}
			
			
		});
	});

})(jQuery);