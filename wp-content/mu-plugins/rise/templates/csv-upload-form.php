<?php
	/**
	 * Template for the CSV upload form.
	 *
	 * @package RISE
	 * @subpackage Templates
	 *
	 * @since 1.2
	 */

	$existing_departments = $template_args['existing_departments'];
?>

<div class="rise-csv-upload-section">
	<h3><?php esc_html_e( 'CSV File Format', 'rise' ); ?></h3>
	<p><?php esc_html_e( 'Upload a CSV file to import second-level positions and skills data. The CSV should follow this format:', 'rise' ); ?></p>
	<ul>
		<li><?php esc_html_e( 'First column: Position names (e.g., "Director of Education", "Finance Manager")', 'rise' ); ?></li>
		<li><?php esc_html_e( 'Subsequent columns: Skills associated with each position', 'rise' ); ?></li>
		<li><?php esc_html_e( 'All positions will be created under the selected first-level department', 'rise' ); ?></li>
	</ul>

	<form id="rise-csv-upload-form" enctype="multipart/form-data">
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="parent_department"><?php esc_html_e( 'First-Level Department', 'rise' ); ?></label>
				</th>
				<td>
					<select id="parent_department" name="parent_department" required>
						<option value=""><?php esc_html_e( 'Select a department...', 'rise' ); ?></option>
						<?php if ( !empty( $existing_departments ) && !is_wp_error( $existing_departments ) ): ?>
							<?php foreach ( $existing_departments as $department ): ?>
								<option value="<?php echo esc_attr( $department->term_id ); ?>">
									<?php echo esc_html( $department->name ); ?>
								</option>
							<?php endforeach; ?>
						<?php endif; ?>
						<option value="0"><?php esc_html_e( 'Create new department...', 'rise' ); ?></option>
					</select>
					<p class="description">
						<?php esc_html_e( 'Select an existing first-level department or create a new one.', 'rise' ); ?>
					</p>
				</td>
			</tr>
			<tr id="new_department_row" style="display: none;">
				<th scope="row">
					<label for="new_department_name"><?php esc_html_e( 'New Department Name', 'rise' ); ?></label>
				</th>
				<td>
					<input type="text" id="new_department_name" name="new_department_name" class="regular-text" />
					<p class="description">
						<?php esc_html_e( 'Enter the name for the new first-level department.', 'rise' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="csv_file"><?php esc_html_e( 'CSV File', 'rise' ); ?></label>
				</th>
				<td>
					<input type="file" id="csv_file" name="csv_file" accept=".csv" required />
					<p class="description">
						<?php esc_html_e( 'Select a CSV file to upload. Maximum file size: ', 'rise' ); ?>
						<?php echo esc_html( size_format( wp_max_upload_size() ) ); ?>
					</p>
				</td>
			</tr>
		</table>

		<p class="submit">
			<button type="submit" class="button button-primary" id="rise-csv-upload-btn">
				<?php esc_html_e( 'Upload and Import CSV', 'rise' ); ?>
			</button>
			<span class="spinner" id="rise-csv-upload-spinner" style="display: none;"></span>
		</p>
	</form>

	<div id="rise-csv-upload-result" style="margin-top: 15px;"></div>
</div> 