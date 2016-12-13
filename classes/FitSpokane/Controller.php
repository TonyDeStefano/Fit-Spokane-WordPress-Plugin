<?php

namespace FitSpokane;

use FitSpokane\Stripe;

class Controller {

	const VERSION = '1.0.0';
	const VERSION_CSS = '1.1.1';
	const VERSION_JS = '1.0.0';
	const OPTION_VERSION = 'fit_spokane_version';

	public $attributes;

	/**
	 *
	 */
	public function activate()
	{
		add_option( self::OPTION_VERSION, self::VERSION );
	}

	/**
	 *
	 */
	public function init()
	{
		add_thickbox();
		wp_enqueue_script( 'fit-spokane-stripe-js', 'https://checkout.stripe.com/checkout.js', array( 'jquery' ), (WP_DEBUG) ? time() : self::VERSION_JS, FALSE );
		wp_enqueue_script( 'fit-spokane-js', plugin_dir_url( dirname( __DIR__ )  ) . 'js/fit-spokane.js', array( 'jquery' ), (WP_DEBUG) ? time() : self::VERSION_JS, TRUE );
		wp_enqueue_style( 'fit-spokane-css', plugin_dir_url( dirname( __DIR__ ) ) . 'css/fit-spokane.css', array(), (WP_DEBUG) ? time() : self::VERSION_CSS );
		wp_enqueue_style( 'fit-spokane-bootstrap-css', plugin_dir_url( dirname( __DIR__ ) ) . 'css/bootstrap.css', array(), (WP_DEBUG) ? time() : self::VERSION_CSS );
		wp_enqueue_style( 'fit-spokane-kitchen-rescue-pak-css', plugin_dir_url( dirname( __DIR__ ) ) . 'css/kitchen-rescue-pak.css', array(), (WP_DEBUG) ? time() : self::VERSION_CSS );
	}

	/**
	 * @param $attributes
	 *
	 * @return string
	 */
	public function short_code( $attributes )
	{
		$this->attributes = shortcode_atts( array(
			'program' => '',
			'kitchen_rescue_pak' => ''
		), $attributes );

		ob_start();
		include( dirname( dirname( __DIR__ ) ) . '/includes/shortcode.php');
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	/**
	 * @param $attribute
	 *
	 * @return string
	 */
	public function get_attribute( $attribute )
	{
		if ( is_array( $this->attributes ) && array_key_exists( $attribute, $this->attributes ) )
		{
			return $this->attributes[ $attribute ];
		}

		return '';
	}

	/**
	 * @param array $links
	 *
	 * @return array
	 */
	public function instructions_link( $links )
	{
		$link = '<a href="options-general.php?page=' . plugin_basename( dirname( dirname( __DIR__ ) ) ) . '">' . __( 'Instructions', 'fit-spokane' ) . '</a>';
		$links[] = $link;
		return $links;
	}

	/**
	 *
	 */
	public function instructions_page()
	{
		add_options_page(
			'Fit Spokane ' . __( 'Instructions', 'fit-spokane' ),
			'Fit Spokane',
			'manage_options',
			plugin_basename( dirname( dirname( __DIR__ ) ) ),
			array( $this, 'print_instructions_page')
		);
	}

	/**
	 *
	 */
	public function print_instructions_page()
	{
		include( dirname( dirname( __DIR__ ) ) . '/includes/instructions.php' );
	}

	/**
	 *
	 */
	public function admin_scripts()
	{
		wp_enqueue_media();
		wp_enqueue_script( 'fit-spokane-admin-js', plugin_dir_url( dirname( __DIR__ )  ) . 'js/admin.js', array( 'jquery' ), (WP_DEBUG) ? time() : self::VERSION_JS, TRUE );
		wp_enqueue_style( 'fit-spokane-admin-css', plugin_dir_url( dirname( __DIR__ ) ) . 'css/admin.css', array(), (WP_DEBUG) ? time() : self::VERSION_CSS );
	}

	/**
	 *
	 */
	public function register_settings()
	{
		register_setting( 'fit_spokane_settings', 'fit_spokane_test_secret_key' );
		register_setting( 'fit_spokane_settings', 'fit_spokane_test_pub_key' );
		register_setting( 'fit_spokane_settings', 'fit_spokane_live_secret_key' );
		register_setting( 'fit_spokane_settings', 'fit_spokane_live_pub_key' );
		register_setting( 'fit_spokane_settings', 'fit_spokane_mode' );
		register_setting( 'fit_spokane_settings', 'fit_spokane_company_name' );
		register_setting( 'fit_spokane_settings', 'fit_spokane_suppress_https_warning' );
		register_setting( 'fit_spokane_settings', 'fit_spokane_company_logo' );
		register_setting( 'fit_spokane_settings', 'fit_spokane_mailchimp_api_key' );
		register_setting( 'fit_spokane_settings', 'fit_spokane_mailchimp_api_url' );
		register_setting( 'fit_spokane_settings', 'fit_spokane_mailchimp_list_id' );
	}

	/**
	 * @return string
	 */
	public function getMailChimpApiKey()
	{
		return get_option( 'fit_spokane_mailchimp_api_key', '' );
	}

	/**
	 * @return string
	 */
	public function getMailChimpApiUrl()
	{
		return get_option( 'fit_spokane_mailchimp_api_url', '' );
	}

	/**
	 * @return string
	 */
	public function getMailChimpListId()
	{
		return get_option( 'fit_spokane_mailchimp_list_id', '' );
	}

	/**
	 * @return array
	 */
	public static function getStripeKeys()
	{
		$mode = ( get_option('fit_spokane_mode') == 'live' ) ? 'live' : 'test';

		return array(
			'secret' => get_option( 'fit_spokane_'.$mode.'_secret_key' ),
			'pub' => get_option( 'fit_spokane_'.$mode.'_pub_key' )
		);
	}

	/**
	 *
	 */
	public function form_capture()
	{
		if ( isset( $_POST['fit_spokane_action'] ) )
		{
			if ( wp_verify_nonce( $_POST['_wpnonce'], 'fit_spokane_nonce' ) )
			{
				if ( $_POST['fit_spokane_action'] == 'kitchen_rescue_pak' )
				{
					$data = array(
						'email' => strtolower( trim( $_POST['email'] ) ),
						'status' => 'subscribed',
						'firstname' => trim( $_POST['first'] ),
						'lastname' => trim( $_POST['last'] )
					);

					$member_id = md5( $data['email'] );
					$url = $this->getMailChimpApiUrl() . '/lists/' . $this->getMailChimpListId() . '/members/' . $member_id;

					$json = json_encode( array(
						'email_address' => $data['email'],
						'status' => $data['status'],
						'merge_fields' => array(
							'FNAME' => $data['firstname'],
							'LNAME' => $data['lastname']
						)
					) );

					$ch = curl_init( $url );

					curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
						'Accept: application/vnd.api+json',
						'Content-Type: application/vnd.api+json',
						'Authorization: apikey ' . $this->getMailChimpApiKey()
					) );
					curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
					curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
					curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'PUT' );
					curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE);
					curl_setopt( $ch, CURLOPT_POSTFIELDS, $json );

					$result = curl_exec( $ch );
					$http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
					curl_close( $ch );

					if( $http_code == 200 )
					{
						$referrer = $_POST['_wp_http_referer'];
						$parts = explode( '?', $referrer );
						$referrer = $parts[0];
						header( 'Location: ' . $referrer . '?fit_spokane_action=kitchen_pak_success' );
						exit;
					}
				}
				elseif ( $_POST['fit_spokane_action'] == 'charge' )
				{
					$program = new Program( $_POST['id'] );

					$stripe_keys = self::getStripeKeys();
					Stripe\Stripe::setApiKey( $stripe_keys['secret'] );

					try
					{
						$token = Stripe\Token::retrieve( $_POST['token'] );
						$email =  $token->email;
						$address = $token->card->address_line1;
						$city = $token->card->address_city;
						$state =  $token->card->address_state;
						$zip =  $token->card->address_zip;
						$name = $token->card->name;

						if ( $program->isRecurring() )
						{
							try
							{
								$plan = Stripe\Plan::retrieve( 'program-' . $program->getId() );
							}
							catch ( \Exception $e )
							{
								$plan = Stripe\Plan::create( array (
									'name' => $program->getTitle(),
									'id' => 'program-' . $program->getId(),
									'interval' => 'month',
									'currency' => 'usd',
									'amount' => $program->getPrice() * 100
								) );
							}

							$customer = Stripe\Customer::create(array(
								'email' => $email,
								'description' => $name,
								'source' => $token
							));

							Stripe\Subscription::create(array(
								'customer' => $customer->id,
								'plan' => $plan->id,
							));
						}
						else
						{
							/** @var \Stripe\Charge $charge */
							Stripe\Charge::create( array (
								'amount' => round( $program->getPrice(), 2 ) * 100,
								'currency' => 'usd',
								'source' => $_POST['token'],
								'description' => $program->getTitle()
							) );
						}

						$content = '
							<p>
								<strong>Customer:</strong><br>' . $name . '<br><br>
								<strong>Email:</strong><br>' . $email . '<br><br>
								<strong>Address:</strong><br>' . $address . '<br>' . $city . ', ' . $state . ' ' . $zip . '<br><br>
								<strong>Program:</strong><br>' . $program->getTitle() . '<br><br>
								<strong>Price:</strong><br>$' . number_format( $program->getPrice(), 2 ) . '<br><br>
								<strong>Recurring:</strong><br>' . ( ( $program->isRecurring() && $program->getRecurPeriod() > 1 ) ? 'Every ' . $program->getRecurPeriod() . ' months' : 'No' ) . '
							</p>';

						$post_id = wp_insert_post(
							array(
								'post_title' => $name . ' - ' . $program->getTitle() . ' ($' . number_format( $program->getPrice(), 2) . ')',
								'post_content' => $content,
								'post_status' => 'publish',
								'post_type' => Payment::POST_TYPE
							)
						);

						if ( $program->isRecurring() )
						{
							$end_date = date( 'n/j/Y',  strtotime( '+' . $program->getRecurPeriod() . ' months' ) );
							add_post_meta( $post_id, Payment::POST_META_EXPIRATION, $end_date );
						}

						$referrer = $_POST['_wp_http_referer'];
						$parts = explode( '?', $referrer );
						$page = $parts[0];
						if ( count( $parts ) > 1 )
						{
							unset( $parts[0] );
							$qs = $parts;
						}
						else
						{
							$qs = array();
						}
						if ( ! in_array( 'fit_spokane=success', $qs ) )
						{
							$qs[] = 'fit_spokane=success';
						}

						header( 'Location:' . $page . '?' . implode( '&', $qs ) );
						exit;
					}
					catch ( \Exception $e )
					{
						// card was declined
					}
				}
			}
		}
	}

	/**
	 *
	 */
	public function create_post_type()
	{
		$title = __( 'Program', 'fit-spokane' );
		$plural = __( 'Programs', 'fit-spokane' );

		$labels = array (
			'name' => $plural,
			'singular_name' => $plural,
			'add_new_item' => __( 'Add New', 'fit-spokane' ) . ' ' . $title,
			'edit_item' => __( 'Edit', 'fit-spokane' ) . ' ' . $title,
			'new_item' => __( 'New', 'fit-spokane' ) . ' ' . $title,
			'view_item' => __( 'View', 'fit-spokane' ) . ' ' . $title,
			'search_items' => __( 'Search', 'fit-spokane' ) . ' ' . $plural,
			'not_found' => __( 'No', 'fit-spokane' ) . ' ' . $plural . ' ' . __( 'Found', 'fit-spokane'  )
		);

		$args = array (
			'labels' => $labels,
			'hierarchical' => FALSE,
			'description' => $plural,
			'supports' => array( 'title' ),
			'show_ui' => TRUE,
			'show_in_menu' => 'fit_spokane',
			'show_in_nav_menus' => TRUE,
			'publicly_queryable' => TRUE,
			'exclude_from_search' => FALSE,
			'has_archive' => TRUE,
			'public' => FALSE
		);

		register_post_type( Program::POST_TYPE , $args );

		$title = __( 'Payment', 'fit-spokane' );
		$plural = __( 'Payments', 'fit-spokane' );

		$labels = array (
			'name' => $plural,
			'singular_name' => $plural,
			'add_new_item' => __( 'Add New', 'fit-spokane' ) . ' ' . $title,
			'edit_item' => __( 'Edit', 'fit-spokane' ) . ' ' . $title,
			'new_item' => __( 'New', 'fit-spokane' ) . ' ' . $title,
			'view_item' => __( 'View', 'fit-spokane' ) . ' ' . $title,
			'search_items' => __( 'Search', 'fit-spokane' ) . ' ' . $plural,
			'not_found' => __( 'No', 'fit-spokane' ) . ' ' . $plural . ' ' . __( 'Found', 'fit-spokane'  )
		);

		$args = array (
			'labels' => $labels,
			'hierarchical' => FALSE,
			'description' => $plural,
			'supports' => array( 'title', 'editor' ),
			'show_ui' => TRUE,
			'show_in_menu' => 'fit_spokane',
			'show_in_nav_menus' => TRUE,
			'publicly_queryable' => TRUE,
			'exclude_from_search' => FALSE,
			'has_archive' => TRUE,
			'public' => FALSE
		);

		register_post_type( Payment::POST_TYPE , $args );
	}

	/**
	 *
	 */
	public function admin_menus()
	{
		add_menu_page( 'Fit Spokane', 'Fit Spokane', 'manage_options', 'fit_spokane', array( $this, 'print_instructions_page' ), 'dashicons-heart' );
		add_submenu_page( 'fit_spokane', __( 'Settings', 'fit-spokane' ), __( 'Settings', 'fit-spokane' ), 'manage_options', 'fit_spokane' );
	}

	public function extra_program_meta()
	{
		add_meta_box( 'fit-spokane-program-meta', 'Program Info', array( $this, 'extra_program_fields'), Program::POST_TYPE );
	}

	public function extra_program_fields()
	{
		include( dirname( dirname( __DIR__ ) ) . '/includes/extra-program-fields.php' );
	}

	public function save_program_meta()
	{
		/** @var \WP_Post $post */
		global $post;

		if ( $post )
		{
			if ( $post->post_type == Program::POST_TYPE )
			{
				$program = new Program( $post->ID );
				$program
					->setIsVisible( $_POST['is_visible'] )
					->setIsRecurring( $_POST['is_recurring'] )
					->setPrice( $_POST['price'] )
					->setRecurPeriod( $_POST['recur_period'] )
					->update();
			}
		}
	}

	public function add_new_program_columns( $columns )
	{
		$new = array(
			'is_visible' => 'Status',
			'price' => 'Price',
			'is_recurring' => 'Recurring',
			'short_code' => 'Shortcode'
		);

		unset( $columns['date'] );

		$columns = array_slice( $columns, 0, 2, TRUE ) + $new + array_slice( $columns, 2, NULL, TRUE );
		return $columns;
	}

	public function custom_program_columns( $column )
	{
		/** @var \WP_Post $post */
		global $post;

		$program = new Program( $post->ID );

		switch ( $column )
		{
			case 'is_visible':
				echo ( $program->isVisible() ) ? 'On' : 'Off';
				break;

			case 'price':
				echo '$' . number_format( $program->getPrice(), 2 );
				break;

			case 'is_recurring':
				if ( $program->isRecurring() )
				{
					echo 'Every ' . $program->getRecurPeriod() . ' month' . ( ( $program->getRecurPeriod() != 1 ) ? 's' : '' );
				}
				else
				{
					echo 'No';
				}
				break;

			case 'short_code':
				echo '[fit_spokane program="' . $program->getId() . '"]';
				break;
		}
	}

	public function add_new_payment_columns( $columns )
	{
		$new = array(
			'expires_at' => 'Expiration Date'
		);

		unset( $columns['date'] );

		$columns = array_slice( $columns, 0, 2, TRUE ) + $new + array_slice( $columns, 2, NULL, TRUE );
		return $columns;
	}

	public function custom_payment_columns( $column )
	{
		/** @var \WP_Post $post */
		global $post;

		switch ( $column )
		{
			case 'expires_at':
				echo get_post_meta( $post->ID, Payment::POST_META_EXPIRATION, TRUE );
				break;
		}
	}

	/**
	 * @param array $actions
	 *
	 * @return array
	 */
	public function remove_row_actions( $actions )
	{
		if ( get_post_type() == Program::POST_TYPE )
		{
			unset( $actions['view'] );
		}

		return $actions;
	}

	/**
	 * @param array $messages
	 *
	 * @return array
	 */
	public function custom_post_type_messages( $messages )
	{
		if ( get_post_type() == Program::POST_TYPE )
		{
			$messages['post'][1] = 'Program has been updated! <a href="edit.php?post_type=fit_spokane_program">Back to List</a>';
		}

		return $messages;
	}
}
