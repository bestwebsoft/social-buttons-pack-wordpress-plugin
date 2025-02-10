<?php
/**
 * Pinterest Widget
 *
 * Widget that allows users to add Pinterest Pin, Board and Profile widgets on their site.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Pinterest_Widget for Pinterest Widget
 */
class Pinterest_Widget extends WP_Widget {
	/**
	 * Sets up the widget name etc
	 */
	public function __construct() {
		/* widget actual processes */
		parent::__construct(
			/*id*/
			'pntrst-widget',
			/*name*/
			__( 'Pinterest Widget', 'bws-pinterest' ),
			/* Widget description */
			array(
				'description' => __( 'Widget for adding Pinterest Pin, Board, and Profile widgets', 'bws-pinterest' ), /* description displayed in admin */
			)
		);
	}
	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args     Args gor widget.
	 * @param array $instance Widget options.
	 */
	public function widget( $args, $instance ) {
		global $pntrst_options;
		if ( empty( $pntrst_options ) ) {
			pntrst_register_settings();
		}

		$title            = empty( $instance['pntrst_title'] ) ? '' : apply_filters( 'widget_title', $instance['pntrst_title'], $instance, $this->id_base );
		$widget_type      = empty( $instance['pntrst_widget_type'] ) ? '' : $instance['pntrst_widget_type'];
		$widget_url       = empty( $instance['pntrst_widget_url'] ) ? '' : $instance['pntrst_widget_url'];
		$pin_widget_size  = empty( $instance['pntrst_pin_widget_size'] ) ? '' : $instance['pntrst_pin_widget_size'];
		$widget_width     = empty( $instance['pntrst_widget_width'] ) ? '' : $instance['pntrst_widget_width'];
		$widget_height    = empty( $instance['pntrst_widget_height'] ) ? '' : $instance['pntrst_widget_height'];
		$widget_pin_scale = empty( $instance['pntrst_widget_pin_scale'] ) ? '' : $instance['pntrst_widget_pin_scale'];
		/* before and after widget arguments are defined by themes */
		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] ) . esc_html( $title ) . wp_kses_post( $args['after_title'] );
		}
		?>
		<div class="pntrst-widget">
			<?php if ( 'embedPin' === $widget_type ) { ?>
				<a data-pin-do="<?php echo esc_attr( $widget_type ); ?>" data-pin-width="<?php echo esc_attr( $pin_widget_size ); ?>" href="<?php echo esc_url( $widget_url ); ?>"></a>
			<?php } elseif ( 'embedBoard' === $widget_type ) { ?>
				<a data-pin-do="<?php echo esc_attr( $widget_type ); ?>" data-pin-board-width="<?php echo esc_attr( $widget_width ); ?>" data-pin-scale-height="<?php echo esc_attr( $widget_height ); ?>" data-pin-scale-width="<?php echo esc_attr( $widget_pin_scale ); ?>" href="<?php echo esc_url( $widget_url ); ?>"></a>
			<?php } elseif ( 'embedUser' === $widget_type ) { ?>
				<a data-pin-do="<?php echo esc_attr( $widget_type ); ?>" data-pin-board-width="<?php echo esc_attr( $widget_width ); ?>" data-pin-scale-height="<?php echo esc_attr( $widget_height ); ?>" data-pin-scale-width="<?php echo esc_attr( $widget_pin_scale ); ?>" href="https://www.pinterest.com/<?php echo esc_attr( $pntrst_options['profile_url'] ); ?>"></a>
			<?php } ?>
		</div>
		<?php
		echo wp_kses_post( $args['after_widget'] );
	}
	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance Widget options.
	 */
	public function form( $instance ) {
		global $pntrst_options;
		if ( empty( $pntrst_options ) ) {
			pntrst_register_settings();
		}
		/* outputs the options form on admin */
		$instance         = wp_parse_args(
			(array) $instance,
			array(
				'pntrst_title'            => '',
				'pntrst_widget_type'      => 'embedPin',
				'pntrst_widget_url'       => '',
				'pntrst_pin_widget_size'  => '',
				'pntrst_widget_width'     => '',
				'pntrst_widget_height'    => '',
				'pntrst_widget_pin_scale' => '',
			)
		);
		$pntrst_title     = esc_attr( $instance['pntrst_title'] );
		$widget_type      = esc_attr( $instance['pntrst_widget_type'] );
		$widget_url       = esc_attr( $instance['pntrst_widget_url'] );
		$pin_widget_size  = esc_attr( $instance['pntrst_pin_widget_size'] );
		$widget_width     = esc_attr( $instance['pntrst_widget_width'] );
		$widget_height    = esc_attr( $instance['pntrst_widget_height'] );
		$widget_pin_scale = esc_attr( $instance['pntrst_widget_pin_scale'] );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'pntrst_title' ) ); ?>"><?php esc_html_e( 'Title', 'bws-pinterest' ); ?>:</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'pntrst_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'pntrst_title' ) ); ?>" type="text" value="<?php echo esc_attr( $pntrst_title ); ?>" />
		</p>
		<div>
			<label for="<?php echo esc_attr( $this->get_field_id( 'pntrst_widget_type' ) ); ?>">
				<?php esc_html_e( 'Type', 'bws-pinterest' ); ?>:
				<select id="<?php echo esc_attr( $this->get_field_id( 'pntrst_widget_type' ) ); ?>" class="pntrst-widget-type" name="<?php echo esc_attr( $this->get_field_name( 'pntrst_widget_type' ) ); ?>">
					<option value="embedPin"
					<?php
					if ( 'embedPin' === $widget_type ) {
						echo 'selected="selected"'; }
					?>
					><?php esc_html_e( 'Pin Widget', 'bws-pinterest' ); ?></option>
					<option value="embedBoard"
					<?php
					if ( 'embedBoard' === $widget_type ) {
						echo 'selected="selected"'; }
					?>
					><?php esc_html_e( 'Board Widget', 'bws-pinterest' ); ?></option>
					<option value="embedUser"
					<?php
					if ( 'embedUser' === $widget_type ) {
						echo 'selected="selected"'; }
					?>
					><?php esc_html_e( 'Profile Widget', 'bws-pinterest' ); ?></option>
				</select>
			</label>
			<p class="pntrst-widget-url 
			<?php
			if ( 'embedUser' === $widget_type ) {
				echo 'hidden'; }
			?>
			">
				<label for="<?php echo esc_attr( $this->get_field_id( 'pntrst_widget_url' ) ); ?>"><?php esc_html_e( 'URL', 'bws-pinterest' ); ?>*:</label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'pntrst_widget_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'pntrst_widget_url' ) ); ?>" value="<?php echo esc_url( $widget_url ); ?>" />
				<span class="bws_info">
					<?php esc_html_e( 'Examples', 'bws-pinterest' ); ?>:<br />
					<?php esc_html_e( 'Pin Widget', 'bws-pinterest' ); ?>: https://www.pinterest.com/pin/99360735500167749/<br />
					<?php esc_html_e( 'Board Widget', 'bws-pinterest' ); ?>: https://www.pinterest.com/pinterest/official-news/
				</span><br />
			</p>
			<div class="pntrst-pin-widget-size 
			<?php
			if ( 'embedBoard' === $widget_type || 'embedUser' === $widget_type ) {
				echo 'hidden'; }
			?>
			">
				<label for="<?php echo esc_attr( $this->get_field_id( 'pntrst_pin_widget_size' ) ); ?>"><?php esc_html_e( 'Size', 'bws-pinterest' ); ?>:</label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'pntrst_pin_widget_size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'pntrst_pin_widget_size' ) ); ?>">
					<option value="small"<?php selected( $pin_widget_size, 'small' ); ?>><?php esc_html_e( 'Small', 'bws-pinterest' ); ?></option>
					<option value="medium"<?php selected( $pin_widget_size, 'medium' ); ?>><?php esc_html_e( 'Medium', 'bws-pinterest' ); ?></option>
					<option value="large"<?php selected( $pin_widget_size, 'large' ); ?>><?php esc_html_e( 'Large', 'bws-pinterest' ); ?></option>
				</select>
			</div>
			<p class="pntrst-widget-size 
			<?php
			if ( 'embedPin' === $widget_type ) {
				echo 'hidden'; }
			?>
			">
				<label for="<?php echo esc_attr( $this->get_field_id( 'pntrst_widget_width' ) ); ?>"><?php esc_html_e( 'Width', 'bws-pinterest' ); ?>:</label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'pntrst_widget_width' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'pntrst_widget_width' ) ); ?>" type="number" value="<?php echo esc_attr( $widget_width ); ?>" />
				<span class="bws_info">
					<?php
					printf(
						esc_html__( 'Min width%1s. If the widget width field is empty, the thumbnail width will be auto.', 'bws-pinterest' ),
						': 130px'
					);
					?>
				</span><br />
				<label for="<?php echo esc_attr( $this->get_field_id( 'pntrst_widget_height' ) ); ?>"><?php esc_html_e( 'Height', 'bws-pinterest' ); ?>:</label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'pntrst_widget_height' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'pntrst_widget_height' ) ); ?>" type="number" value="<?php echo esc_attr( $widget_height ); ?>" />
				<span class="bws_info">
					<?php
					printf(
						esc_html__( 'Min height%1s.', 'bws-pinterest' ),
						': 60px'
					);
					?>
				</span><br />
				<label for="<?php echo esc_attr( $this->get_field_id( 'pntrst_widget_pin_scale' ) ); ?>"><?php esc_html_e( 'Thumbnails width', 'bws-pinterest' ); ?>:</label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'pntrst_widget_pin_scale' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'pntrst_widget_pin_scale' ) ); ?>" type="number" value="<?php echo esc_attr( $widget_pin_scale ); ?>" />
				<span class="bws_info">
					<?php
					printf(
						esc_html__( 'Min width%1s. If the widget width field is empty, the thumbnail width will be auto.', 'bws-pinterest' ),
						': 60px'
					);
					?>
				</span>
			</p>
		</div>
		<?php
	}
	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options.
	 * @param array $old_instance The previous options.
	 */
	public function update( $new_instance, $old_instance ) {
		/* processes widget options to be saved */
		$instance = $old_instance;
		/* Fields */
		$instance['pntrst_title'] = sanitize_text_field( $new_instance['pntrst_title'] );
		if ( 'embedPin' === $new_instance['pntrst_widget_type'] || 'embedBoard' === $new_instance['pntrst_widget_type'] || 'embedUser' === $new_instance['pntrst_widget_type'] ) {
			$instance['pntrst_widget_type'] = $new_instance['pntrst_widget_type'];
		}
		/* Check if user save correct url. Else clean url. */
		if ( preg_match( '|^http(s)?://(.*)?pinterest(.*)?$|i', trim( $new_instance['pntrst_widget_url'] ) ) ) {
			$instance['pntrst_widget_url'] = esc_url( trim( $new_instance['pntrst_widget_url'] ) );
		} else {
			$instance['pntrst_widget_url'] = '';
		}
		/* Check if board or profile widget selected and save widget size options/clean pin widget options. Else clean widget size options and save pin widget options */
		if ( 'embedPin' !== $new_instance['pntrst_widget_type'] ) {
			/* clean pin widget size option */
			$instance['pntrst_pin_widget_size'] = '';
			/* save board and profile widget size options */
			if ( empty( $new_instance['pntrst_widget_width'] ) || intval( $new_instance['pntrst_widget_width'] ) < 130 ) {
				$instance['pntrst_widget_width'] = 130;
			} elseif ( intval( $new_instance['pntrst_widget_width'] ) > 2000 ) {
				$instance['pntrst_widget_width'] = 2000;
			} else {
				$instance['pntrst_widget_width'] = intval( $new_instance['pntrst_widget_width'] );
			}
			if ( empty( $new_instance['pntrst_widget_height'] ) || intval( $new_instance['pntrst_widget_height'] ) < 60 ) {
				$instance['pntrst_widget_height'] = 60;
			} elseif ( intval( $new_instance['pntrst_widget_height'] ) > 1500 ) {
				$instance['pntrst_widget_height'] = 1500;
			} else {
				$instance['pntrst_widget_height'] = intval( $new_instance['pntrst_widget_height'] );
			}
			if ( empty( $new_instance['pntrst_widget_pin_scale'] ) || intval( $new_instance['pntrst_widget_pin_scale'] ) < 60 ) {
				$instance['pntrst_widget_pin_scale'] = 60;
			} elseif ( intval( $new_instance['pntrst_widget_pin_scale'] ) > 2000 ) {
				$instance['pntrst_widget_pin_scale'] = 2000;
			} else {
				$instance['pntrst_widget_pin_scale'] = intval( $new_instance['pntrst_widget_pin_scale'] );
			}
		} else {
			/* save pin widget size option */
			if ( 'small' === $new_instance['pntrst_pin_widget_size'] || 'medium' === $new_instance['pntrst_pin_widget_size'] || 'large' === $new_instance['pntrst_pin_widget_size'] ) {
				$instance['pntrst_pin_widget_size'] = $new_instance['pntrst_pin_widget_size'];
			}
			/* clean board and profile widget size options */
			$instance['pntrst_widget_width']     = '';
			$instance['pntrst_widget_height']    = '';
			$instance['pntrst_widget_pin_scale'] = '';
		}
		return $instance;
	}
}
