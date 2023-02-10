<?php
	/**
	 * Plugin Name: Bulk Add Taxonomy Terms
	 * Description: Allows bulk addition of taxonomy terms via a settings page.
	 * Version: 1.0
	 * Author: ChatGPT
	 */

	add_action( 'admin_menu', function () {
		add_options_page( 'Bulk Add Taxonomy Terms', 'Bulk Add Taxonomy Terms', 'manage_options', 'bulk-add-taxonomy-terms', 'bulk_add_taxonomy_terms_page' );
	} );

	function bulk_add_taxonomy_terms_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'You do not have sufficient permissions to access this page.' );
		}
		if ( isset( $_POST['terms'] ) && isset( $_POST['taxonomy'] ) && check_admin_referer( 'bulk_add_taxonomy_terms' ) ) {
			$terms    = explode( "\n", sanitize_textarea_field( $_POST['terms'] ) );
			$taxonomy = sanitize_text_field( $_POST['taxonomy'] );
			foreach ( $terms as $term ) {
				wp_insert_term( trim( $term ), $taxonomy );
			}
			echo '<div class="notice notice-success is-dismissible">Terms added successfully!</div>';
		}
	?>
    <div class="wrap">
        <h1>Bulk Add Taxonomy Terms</h1>
        <form method="post">
            <?php wp_nonce_field( 'bulk_add_taxonomy_terms' );?>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="terms">Terms</label>
                        </th>
                        <td>
                            <textarea id="terms" name="terms" rows="10" cols="50"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="taxonomy">Taxonomy</label>
                        </th>
                        <td>
                            <select id="taxonomy" name="taxonomy">
                                <?php foreach ( get_taxonomies() as $taxonomy_name ): ?>
                                    <option value="<?php echo esc_attr( $taxonomy_name ); ?>"><?php echo esc_html( $taxonomy_name ); ?></option>
                                <?php endforeach;?>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p class="submit">
                <input type="submit" class="button button-primary" value="Add Terms">
            </p>
        </form>
    </div>
    <?php
    }