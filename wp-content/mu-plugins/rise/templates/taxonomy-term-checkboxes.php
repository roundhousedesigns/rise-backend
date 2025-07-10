<?php
	/**
	 * Template for the taxonomy term checkboxes.
	 *
	 * @package RISE
	 * @subpackage Templates
	 *
	 * @since 1.2
	 */

	$name           = $template_args['name'];
	$taxonomy       = $template_args['taxonomy'];
	$selected_terms = $template_args['selected_terms'];
	$all_terms      = $template_args['all_terms'];
?>

<h2><?php \__( $name, 'rise' ); ?></h2>
<table class="form-table">
	<tr>
		<th><label><?php \__( 'Select ' . $name, 'rise' ); ?></label></th>
		<td>
			<?php foreach ( $all_terms as $term ): ?>
				<label>
					<input type="checkbox" name="<?php \printf( '%s', \esc_attr( $taxonomy ) ); ?>[]" value="<?php \printf( '%s', \esc_attr( $term->term_id ) ); ?>"<?php \checked( in_array( $term->term_id, $selected_terms, true ), true ); ?>>
					<?php \printf( '%s', \esc_html( $term->name ) ); ?>
				</label>
				<br>
			<?php endforeach; ?>
		</td>
	</tr>
</table>
