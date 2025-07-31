/**
 * RISE CSV Upload functionality
 * 
 * @since 1.2
 */
(function($) {
	'use strict';

	$(document).ready(function() {
		$('#rise-csv-upload-form').on('submit', function(e) {
			e.preventDefault();
			
			const fileInput = $('#csv_file')[0];
			const submitBtn = $('#rise-csv-upload-btn');
			const spinner = $('#rise-csv-upload-spinner');
			const resultDiv = $('#rise-csv-upload-result');
			
			// Clear previous results
			resultDiv.html('');
			
			// Check if file is selected
			if (!fileInput.files || fileInput.files.length === 0) {
				showMessage('error', 'Please select a CSV file to upload.');
				return;
			}
			
			const file = fileInput.files[0];
			
			// Validate file type
			if (file.type !== 'text/csv' && !file.name.toLowerCase().endsWith('.csv')) {
				showMessage('error', 'Please select a valid CSV file.');
				return;
			}
			
			// Validate file size (check against WordPress max upload size)
			const maxSize = parseInt($('input[name="csv_file"]').siblings('.description').text().match(/\d+/)) || 2;
			if (file.size > maxSize * 1024 * 1024) {
				showMessage('error', 'File size exceeds the maximum allowed size.');
				return;
			}
			
			// Show confirmation dialog
			if (!confirm(rise_csv_upload.strings.confirm)) {
				return;
			}
			
			// Prepare form data
			const formData = new FormData();
			formData.append('action', 'rise_csv_upload');
			formData.append('csv_file', file);
			formData.append('nonce', rise_csv_upload.nonce);
			
			// Update UI - show loading state
			submitBtn.prop('disabled', true).text(rise_csv_upload.strings.uploading);
			spinner.show();
			
			// Send AJAX request
			$.ajax({
				url: rise_csv_upload.ajax_url,
				type: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				dataType: 'json',
				success: function(response) {
					if (response.success) {
						showMessage('success', response.message);
						
						// Show import details if available
						if (response.data) {
							let details = '<div style="margin-top: 10px;"><strong>Import Details:</strong><ul>';
							if (response.data.departments_created > 0) {
								details += '<li>Departments created: ' + response.data.departments_created + '</li>';
							}
							if (response.data.positions_created > 0) {
								details += '<li>Positions created: ' + response.data.positions_created + '</li>';
							}
							if (response.data.skills_created > 0) {
								details += '<li>Skills created: ' + response.data.skills_created + '</li>';
							}
							details += '</ul></div>';
							resultDiv.append(details);
						}
						
						// Reset form
						$('#rise-csv-upload-form')[0].reset();
						
					} else {
						showMessage('error', response.message || rise_csv_upload.strings.error);
					}
				},
				error: function(xhr, textStatus, errorThrown) {
					console.error('AJAX Error:', textStatus, errorThrown);
					showMessage('error', rise_csv_upload.strings.error + ' (Status: ' + textStatus + ')');
				},
				complete: function() {
					// Reset UI state
					submitBtn.prop('disabled', false).text('Upload and Import CSV');
					spinner.hide();
				}
			});
		});
		
		/**
		 * Display a message to the user
		 * 
		 * @param {string} type - 'success' or 'error'
		 * @param {string} message - The message to display
		 */
		function showMessage(type, message) {
			const resultDiv = $('#rise-csv-upload-result');
			const cssClass = type === 'success' ? 'notice-success' : 'notice-error';
			
			const messageHtml = '<div class="notice ' + cssClass + ' is-dismissible" style="padding: 10px; margin: 10px 0;">' +
				'<p>' + escapeHtml(message) + '</p>' +
				'<button type="button" class="notice-dismiss">' +
					'<span class="screen-reader-text">Dismiss this notice.</span>' +
				'</button>' +
				'</div>';
			
			resultDiv.html(messageHtml);
			
			// Add dismiss functionality
			resultDiv.find('.notice-dismiss').on('click', function() {
				$(this).parent().fadeOut();
			});
		}
		
		/**
		 * Escape HTML to prevent XSS
		 * 
		 * @param {string} text - The text to escape
		 * @returns {string} The escaped text
		 */
		function escapeHtml(text) {
			const map = {
				'&': '&amp;',
				'<': '&lt;',
				'>': '&gt;',
				'"': '&quot;',
				"'": '&#039;'
			};
			return text.replace(/[&<>"']/g, function(m) { return map[m]; });
		}
	});

})(jQuery);