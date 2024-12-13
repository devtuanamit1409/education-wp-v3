<?php
/**
 * Authorize.net payment gateway class.
 *
 * @author   ThimPress
 * @package  LearnPress/Authorizenet/Classes
 * @version  4.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Gateway_Authorizenet' ) ) {
	/**
	 * Class LP_Gateway_Authorizenet.
	 */
	class LP_Gateway_Authorizenet extends LP_Gateway_Abstract {
		/**
		 * @var LP_Order $order
		 */
		public $order;

		/**
		 * LP_Gateway_Authorizenet constructor.
		 */
		public function __construct() {
			$this->id                 = 'authorizenet';
			$this->method_title       = 'Authorize.net';
			$this->method_description = esc_html__( 'Make a payment with Authorize.net payment methods.', 'learnpress-authorizenet-payment' );
			$this->icon               = apply_filters( 'learn-press/authorizenet-icon', '' );
			$this->title              = 'Authorize.net';
			$this->description        = esc_html__( 'Make a payment with Authorize.net payment methods.', 'learnpress-authorizenet-payment' );

			/*if ( did_action( 'learn-press/authorizenet-add-on/loaded' ) ) {
				return;
			}*/

			//add_action( 'authorizenet_checkout_order_processed', array( $this, 'checkout_order_processed' ), 10, 2 );

			// check payment gateway enable
			add_filter( 'learn-press/payment-gateway/' . $this->id . '/available', array(
				$this,
				'authorizenet_available'
			), 10, 2 );

			//do_action( 'learn-press/authorizenet-add-on/loaded' );

			parent::__construct();
		}

		/**
		 * Check gateway available.
		 *
		 * @return bool
		 */
		public function authorizenet_available(): bool {
			return LearnPress::instance()->settings()->get( "{$this->id}.enable" ) == 'yes' &&
			       LearnPress::instance()->settings()->get( "{$this->id}.transaction_key" ) &&
			       LearnPress::instance()->settings()->get( "{$this->id}.login_id" );
		}

		/**
		 * Admin payment settings.
		 *
		 * @return array
		 */
		public function get_settings() {
			return apply_filters(
				'learn-press/gateway-payment/authorizenet/settings',
				array(
					array(
						'type' => 'title',
					),
					array(
						'title'   => esc_html__( 'Enable', 'learnpress-authorizenet-payment' ),
						'id'      => '[enable]',
						'default' => 'no',
						'type'    => 'checkbox',
					),
					array(
						'title'   => esc_html__( 'Login ID', 'learnpress-authorizenet-payment' ),
						'id'      => '[login_id]',
						'default' => '',
						'type'    => 'text',
					),
					array(
						'title'   => esc_html__( 'Transaction Key', 'learnpress-authorizenet-payment' ),
						'id'      => '[transaction_key]',
						'default' => '',
						'type'    => 'text',
					),
					array(
						'title'   => esc_html__( 'Test mode', 'learnpress-authorizenet-payment' ),
						'id'      => '[test_mode]',
						'default' => 'no',
						'type'    => 'checkbox',
					),
					array(
						'title'   => esc_html__( 'Secure post', 'learnpress-authorizenet-payment' ),
						'id'      => '[secure_post]',
						'default' => 'no',
						'type'    => 'checkbox',
					),
					array(
						'type' => 'sectionend',
					),
				)
			);
		}

		/**
		 * Payment form.
		 */
		public function get_payment_form() {
			ob_start();
			//$template = learn_press_locate_template( 'form.php', learn_press_template_path() . 'addons/authorizenet-payment/', LP_ADDON_AUTHORIZENET_TEMPLATE );
			LP_Addon_Authorizenet_Payment_Preload::$addon->get_template( 'form.php' );

			//include $template;

			return ob_get_clean();
		}

		/**
		 * @param $order_id
		 * @param $posted
		 *
		 * @deprecated 4.0.1
		 */
		public function checkout_order_processed( $order_id, $posted ) {
			_deprecated_function( __METHOD__, '4.0.1' );
			$lp_order_id = LearnPress::instance()->session->get( 'order_awaiting_payment' );

			if ( $lp_order_id ) {
				$map_keys = array(
					'_order_currency'       => '_order_currency',
					'_user_id'              => '_customer_user',
					'_order_subtotal'       => '_order_total',
					'_order_total'          => '_order_total',
					'_payment_method_id'    => '_payment_method',
					'_payment_method_title' => '_payment_method_title',
				);

				foreach ( $map_keys as $k => $v ) {
					update_post_meta( $lp_order_id, $k, get_post_meta( $order_id, $v, true ) );
				}

				update_post_meta( $order_id, '_learn_press_order_id', $lp_order_id );
			}
		}

		/**
		 * Process the payment and return the result.
		 *
		 * @param int $order_id
		 *
		 * @return array
		 * @throws Exception
		 */
		public function process_payment( $order_id ) {
			$result = [
				'result'   => 'fail',
				'redirect' => site_url(),
				'message'  => esc_html__( 'Payment fail!', 'learnpress-authorizenet-payment' ),
			];

			require_once LP_ADDON_AUTHORIZENET_PATH . '/inc/libraries/class-lp-authorizenet-AIM.php';

			$lp_order = learn_press_get_order( $order_id );
			if ( ! $lp_order ) {
				throw new Exception( __( 'LP Order not found', 'learnpress-authorizenet-payment' ) );
			}

			$amount = $lp_order->get_total();
			//$amount = $lp_order->get_data( 'order_total', 0 );
			if ( $amount < 0 ) {
				throw new Exception( __( 'Total price small than zero!', 'learnpress-authorizenet-payment' ) );
			}

			$payment_setting = LearnPress::instance()->settings()->get( 'learn_press_authorizenet' );
			$card_num        = $_POST['learn-press-authorizenet-payment']['cardnum'];
			$exp_date        = $_POST['learn-press-authorizenet-payment']['expmonth'] . substr( $_POST['learn-press-authorizenet-payment']['expyear'], 2 );
			$card_code       = $_POST['learn-press-authorizenet-payment']['cardcvv'];
			$api_login_id    = $payment_setting['login_id'];
			$transaction_key = $payment_setting['transaction_key'];
			$test_mode       = $payment_setting['test_mode'];
			$sale            = new LearnPressAuthorizeNetAIM( $api_login_id, $transaction_key );

			if ( $test_mode !== 'yes' ) {
				$sale->setSandbox( false );
			} else {
				$sale->setSandbox( true );
			}

			$sale->setFields(
				array(
					'amount'    => $amount,
					'card_num'  => $card_num,
					'exp_date'  => $exp_date,
					'card_code' => $card_code,
				)
			);

			$response = $sale->authorizeAndCapture();
			if ( $response->approved ) {
				$lp_order->add_note( sprintf( "%s payment completed with Transaction Id of '%s'", $lp_order->get_title(), $response->transaction_id ) );
				$lp_order->payment_complete( $response->transaction_id );

				update_post_meta( $lp_order->get_id(), '_lp_transaction_id', $response->transaction_id );

				//$page_id            = LearnPress::instance()->settings()->get( 'learn_press_profile_page_id' );
				//$profile_order_slug = LearnPress::instance()->settings()->get( 'learn_press_profile_endpoints[profile-orders]' );
				//$user               = wp_get_current_user();
				$url = $this->get_return_url( $lp_order );

				$result = [
					'result'   => 'success',
					'redirect' => $url,
					'message'  => esc_html__( 'Payment success!', 'learnpress-authorizenet-payment' ),
				];
			} else {
				throw new Exception( $response->response_reason_text ?? $response->error_message );
			}

			return $result;
		}

		/**
		 * Complete order.
		 *
		 * @deprecated 4.0.1
		 */
		public function order_complete() {
			_deprecated_function( __METHOD__, '4.0.1' );
			if ( $this->order->status == 'completed' ) {
				return;
			}

			$this->order->payment_complete();
			LearnPress::instance()->cart->empty_cart();

			$this->order->add_note( sprintf( "%s payment completed with Transaction Id of '%s'", $this->title, $this->charge->id ) );

			LearnPress::instance()->session->order_awaiting_payment = null;
		}
	}
}
