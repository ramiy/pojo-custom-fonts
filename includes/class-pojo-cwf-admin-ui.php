<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class Pojo_CWF_Admin_UI {

	protected $_capability = 'edit_theme_options';
	protected $_page_id = 'pojo-cwf';

	public function get_setting_page_link( $message_id = '' ) {
		$link_args = array(
			'page' => $this->_page_id,
		);

		if ( ! empty( $message_id ) )
			$link_args['message'] = $message_id;

		return add_query_arg( $link_args, admin_url( 'admin.php' ) );
	}

	public function get_remove_font_link( $font_id ) {
		return add_query_arg(
			array(
				'action' => 'pcwf_remove_font',
				'font_id' => $font_id,
				'_nonce' => wp_create_nonce( 'pcwf-remove-font-' . $font_id ),
			),
			admin_url( 'admin-ajax.php' )
		);
	}

	public function ajax_pcwf_remove_font() {
		if ( ! isset( $_GET['font_id'] ) || ! check_ajax_referer( 'pcwf-remove-sidebar-' . $_GET['font_id'], '_nonce', false ) || ! current_user_can( $this->_capability ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'pojo-cwf' ) );
		}

		Pojo_CWF_Main::instance()->db->remove_font( $_GET['font_id'] );

		wp_redirect( $this->get_setting_page_link() );
		die();
	}

	public function manager_actions() {
		if ( empty( $_POST['pwcf_action'] ) )
			return;

		switch ( $_POST['pwcf_action'] ) {
			case 'add_font' :
				if ( ! check_ajax_referer( 'pcwf-add-font', '_nonce', false ) || ! current_user_can( $this->_capability ) ) {
					wp_die( __( 'You do not have sufficient permissions to access this page.', 'pojo-cwf' ) );
				}

				$return = Pojo_CWF_Main::instance()->db->update_font(
					array(
						'name' => $_POST['name'],
					)
				);

				if ( is_wp_error( $return ) ) {
					wp_die( $return->get_error_message() );
				}

				wp_redirect( $this->get_setting_page_link() );
				die;

			case 'update_font' :
				if ( ! isset( $_POST['font_id'] ) || ! check_ajax_referer( 'pcwf-update-font-' . $_POST['font_id'], '_nonce', false ) || ! current_user_can( $this->_capability ) ) {
					wp_die( __( 'You do not have sufficient permissions to access this page.', 'pojo-cwf' ) );
				}

				$return = Pojo_CWF_Main::instance()->db->update_font(
					array(
						'name' => $_POST['name'],
					),
					$_POST['font_id']
				);

				if ( is_wp_error( $return ) ) {
					wp_die( $return->get_error_message() );
				}

				wp_redirect( $this->get_setting_page_link() );
				die;
		}
	}

	public function register_menu() {
		add_submenu_page(
			'pojo-home',
			__( 'Custom Web Fonts', 'pojo-cwf' ),
			__( 'Custom Web Fonts', 'pojo-cwf' ),
			$this->_capability,
			'pojo-cwf',
			array( &$this, 'display_page' )
		);
	}


	public function display_page() {
		$fonts = Pojo_CWF_Main::instance()->db->get_fonts();
		?>
		<div class="wrap">

			<div id="icon-themes" class="icon32"></div>
			<h2><?php _e( 'Widgets Area', 'pojo-cwf' ); ?></h2>

			<?php // Add Font ?>
			<div>
				<form action="" method="post">
					<input type="hidden" name="pcwf_action" value="add_font" />
					<input type="hidden" name="_nonce" value="<?php echo wp_create_nonce( 'pcwf-add-font' ) ?>" />

					<div>
						<label>
							<?php _e( 'Name', 'pojo-cwf' ); ?>:
							<input type="text" name="name" />
						</label>
					</div>

					<div>
						<p><button type="submit" class="button"><?php _e( 'Create', 'pojo-cwf' ); ?></button></p>
					</div>
				</form>
			</div>

			<?php // All Fonts ?>
			<div>
				<?php if ( ! empty( $fonts ) ) : ?>
					<?php foreach ( $fonts as $font_id => $font_data ) : ?>
						<form action="" method="post">
							<input type="hidden" name="pcwf_action" value="update_font" />
							<input type="hidden" name="font_id" value="<?php echo esc_attr( $font_id ); ?>" />
							<input type="hidden" name="_nonce" value="<?php echo wp_create_nonce( 'pcwf-update-font-' . $font_id ) ?>" />

							<h3><?php echo $font_data['name']; ?></h3>

							<div>
								<a href="<?php echo $this->get_remove_font_link( $font_id ); ?>"><?php _e( 'Remove', 'pojo-cwf' ); ?></a>
							</div>

							<div>
								<label>
									<?php _e( 'Name', 'pojo-cwf' ); ?>:
									<input type="text" name="name" value="<?php echo esc_attr( $font_data['name'] ); ?>" />
								</label>
							</div>

							<div>
								<p><button type="submit" class="button"><?php _e( 'Update', 'pojo-cwf' ); ?></button></p>
							</div>
						</form>
					<?php endforeach; ?>
				<?php else : ?>
					<p><?php _e( 'No have any fonts.', 'pojo-cwf' ); ?></p>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	public function __construct() {
		$this->manager_actions();

		add_action( 'admin_menu', array( &$this, 'register_menu' ), 200 );

		add_action( 'wp_ajax_pcwf_remove_font', array( &$this, 'ajax_pcwf_remove_font' ) );
	}
	
}