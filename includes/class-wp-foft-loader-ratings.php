<?php
/**
 * Plugin review class.
 * Prompts users to give a review of the plugin on WordPress.org after a period of usage.
 *
 * Based on code by Ryan Hellyer, Heavily based on code by Rhys Wynne
 * https://geek.hellyer.kiwi/tools/plugin-reviews/
 * https://winwar.co.uk/2014/10/ask-wordpress-plugin-reviews-week/
 *
 * @package WP FOFT Loader/Includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Sorry, you are not allowed to access this page directly.' );
}

if ( ! class_exists( 'WP_FOFT_Loader_Ratings' ) ) :
	/**
	 * Prompt user to review plugin.
	 */
	class WP_FOFT_Loader_Ratings {

		/**
		 * Plugin slug.
		 *
		 * @var string $slug The plugin slug.
		 */
		private $slug;

		/**
		 * Plugin name.
		 *
		 * @var string $name The plugin name.
		 */
		private $name;

		/**
		 * Plugin time limit.
		 *
		 * @var int $time_limit The time limit at which notice is shown.
		 */
		private $time_limit;

		/**
		 * Variables.
		 *
		 * @var boolean $nobug_option Whether to dismiss the notice permanently.
		 */
		public $nobug_option;

		/**
		 * Fire the constructor up :)
		 *
		 * @param object $args Constructor arguments.
		 */
		public function __construct( $args ) {

			$this->slug = $args['slug'];
			$this->name = $args['name'];
			if ( isset( $args['time_limit'] ) ) {
				$this->time_limit = $args['time_limit'];
			} else {
				$this->time_limit = WEEK_IN_SECONDS;
			}

			$this->nobug_option = WPFL_BASE . 'no_bug';

			// Loading main functionality.
			add_action( 'admin_init', array( $this, 'check_installation_date' ) );
			add_action( 'admin_init', array( $this, 'set_no_bug' ), 5 );
		}

		/**
		 * Seconds to words.
		 *
		 * @param int $seconds Timeout in seconds.
		 */
		public function seconds_to_words( $seconds ) {

			$arr = array();
			// Get the years.
			$years = (int) ( intval( $seconds ) / YEAR_IN_SECONDS ) % 100;
			if ( $years > 1 ) { /* translators: this is part of a sentence that reads "You’ve been using the $plugin_name plugin for $number years", where $number is the number of years since the plugin was activated. */
				return sprintf( wp_kses( __( '%s years', 'wp-foft-loader' ), $arr ), $years );
			} elseif ( $years > 0 ) { /* translators: this is part of a sentence that reads "You’ve been using the $plugin_name plugin for a year". */
				return wp_kses( __( 'a year', 'wp-foft-loader' ), $arr );
			}

			// Get the weeks.
			$weeks = (int) ( intval( $seconds ) / WEEK_IN_SECONDS ) % 52;
			if ( $weeks > 1 ) { /* translators: this is part of a sentence that reads "You’ve been using the $plugin_name plugin for $number weeks", where $number is the number of weeks since the plugin was activated. */
				return sprintf( wp_kses( __( '%s weeks', 'wp-foft-loader' ), $arr ), $weeks );
			} elseif ( $weeks > 0 ) { /* translators: this is part of a sentence that reads "You’ve been using the $plugin_name plugin for a week". */
				return wp_kses( __( 'a week', 'wp-foft-loader' ), $arr );
			}

			// Get the days.
			$days = (int) ( intval( $seconds ) / DAY_IN_SECONDS ) % 7;
			if ( $days > 1 ) { /* translators: this is part of a sentence that reads "You’ve been using the $plugin_name plugin for $number days", where $number is the number of days since the plugin was activated. */
				return sprintf( wp_kses( __( '%s days', 'wp-foft-loader' ), $arr ), $days );
			} elseif ( $days > 0 ) { /* translators: this is part of a sentence that reads "You’ve been using the $plugin_name plugin for a day". */
				return wp_kses( __( 'a day', 'wp-foft-loader' ), $arr );
			}

			// Get the hours.
			$hours = (int) ( intval( $seconds ) / HOUR_IN_SECONDS ) % 24;
			if ( $hours > 1 ) { /* translators: this is part of a sentence that reads "You’ve been using the $plugin_name plugin for $number hours", where $number is the number of hours since the plugin was activated. */
				return sprintf( wp_kses( __( '%s hours', 'wp-foft-loader' ), $arr ), $hours );
			} elseif ( $hours > 0 ) { /* translators: this is part of a sentence that reads "You’ve been using the $plugin_name plugin for an hour". */
				return wp_kses( __( 'an hour', 'wp-foft-loader' ), $arr );
			}

			// Get the minutes.
			$minutes = (int) ( intval( $seconds ) / MINUTE_IN_SECONDS ) % 60;
			if ( $minutes > 1 ) { /* translators: this is part of a sentence that reads "You’ve been using the $plugin_name plugin for $number minutes", where $number is the number of minutes since the plugin was activated. */
				return sprintf( wp_kses( __( '%s minutes', 'wp-foft-loader' ), $arr ), $minutes );
			} elseif ( $minutes > 0 ) { /* translators: this is part of a sentence that reads "You’ve been using the $plugin_name plugin for a minute". */
				return __( 'a minute', 'wp-foft-loader' );
			}

			// Get the seconds.
			$seconds = (int) intval( $seconds ) % 60;
			if ( $seconds > 1 ) { /* translators: this is part of a sentence that reads "You’ve been using the $plugin_name plugin for $number seconds", where $number is the number of seconds since the plugin was activated. */
				return sprintf( wp_kses( __( '%s seconds', 'wp-foft-loader' ), $arr ), $seconds );
			} elseif ( $seconds > 0 ) { /* translators: this is part of a sentence that reads "You’ve been using the $plugin_name plugin for a second". */
				return __( 'a second', 'wp-foft-loader' );
			}
			return; // phpcs:ignore
		}

		/**
		 * Check date on admin initiation and add to admin notice if it was more than the time limit.
		 */
		public function check_installation_date() {

			if ( true !== get_site_option( $this->nobug_option ) ) {

				// If installation date is not set, then add it.
				$install_date = get_site_option( WPFL_BASE . 'activation-date' );
				if ( '' === $install_date ) {
					add_site_option( WPFL_BASE . 'activation-date', time() );
				}

				// If difference between install date and now is greater than time limit, then display notice.
				if ( ( time() - $install_date ) > $this->time_limit ) {
					add_action( 'admin_notices', array( $this, 'display_admin_notice' ) );
				}
			}

		}

		/**
		 * Display Admin Notice, asking for a review.
		 */
		public function display_admin_notice() {

			global $pagenow;
			$arr = array(); // For wp_kses.
			if ( ! isset( $_GET['page'] ) ) { // phpcs:ignore
				return;
			} elseif ( ( 'options-general.php' === $pagenow ) && ( 'wp-foft-loader' === $_GET['page'] ) ) { // phpcs:ignore

				$no_bug_url = wp_nonce_url( admin_url( 'options-general.php?page=' . $this->slug . '&' . $this->nobug_option . '=true' ), 'review-nonce' );
				$time       = $this->seconds_to_words( time() - get_site_option( WPFL_BASE . 'activation-date' ) );

				echo '
	<div class=" ratings notice notice-success is-dismissible">
		<p><span class="dashicons dashicons-star-filled wp-admin-lite-blue"></span> ' . sprintf(
					wp_kses( /* translators: the placeholders represent the plugin name and the period of time since the plugin was activated, respectively. */
						__( 'Hi there! You’ve been using the <strong>%1$s</strong> plugin for %2$s. Please leave us a 5-star review with your feedback! It helps others to find <strong>%1$s</strong> and makes the world a better place where <strong>puppies and kittens abound, ice cream never melts</strong>, and <strong>web fonts load quickly and smoothly</strong>!', 'wp-foft-loader' ),
						array(
							'strong' => array(),
						)
					),
					wp_kses( $this->name, $arr ),
					wp_kses( $time, $arr )
				) . '
			<br /><br />
			<a class="button button-primary" href="' . esc_url( 'https://wordpress.org/support/view/plugin-reviews/' . $this->slug . '#postform' ) . '" target="_blank">' . esc_html__( 'Leave a Review', 'wp-foft-loader' ) . '</a>
			<a onclick="location.href=\'' . esc_url( $no_bug_url ) . '\';" class="button button-secondary" href="' . esc_url( $no_bug_url ) . '">' . esc_html__( 'I’ve already done it!', 'wp-foft-loader' ) . '</a>
			<a href="' . esc_url( $no_bug_url ) . '">' . esc_html__( 'No thanks.', 'wp-foft-loader' ) . '</a>
		</p>
	</div>';
			}
		}

		/**
		 * Set the plugin to no longer bug users if user asks not to be.
		 */
		public function set_no_bug() {

			// Bail out if not on correct page.
			if (
			! isset( $_GET['_wpnonce'] )
			||
			(
				! wp_verify_nonce( $_GET['_wpnonce'], 'review-nonce' ) //phpcs:ignore
				||
				! is_admin()
				||
				! isset( $_GET[ $this->nobug_option ] )
				||
				! current_user_can( 'manage_options' )
			)
			) {
				return;
			}

			add_site_option( $this->nobug_option, true );

		}

	}
endif;

new WP_FOFT_Loader_Ratings(
	array(
		'slug'       => 'wp-foft-loader', // The plugin slug.
		'name'       => 'WP FOFT Loader', // The plugin name.
		'time_limit' => 1209600,          // The time limit at which notice is shown (2 weeks).
	)
);
