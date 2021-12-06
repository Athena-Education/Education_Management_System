<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
        /*cache control*/
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
    }

    // VALIDATE STRIPE PAYMENT
    public function stripe_payment($user_id = "", $session_id = "", $is_instructor_payout = false) {
        if(!$is_instructor_payout) {
            $stripe_keys = get_settings('stripe_keys');
            $values = json_decode($stripe_keys);
            if ($values[0]->testmode == 'on') {
                $public_key = $values[0]->public_key;
                $secret_key = $values[0]->secret_key;
            } else {
                $public_key = $values[0]->public_live_key;
                $secret_key = $values[0]->secret_live_key;
            }
        }else{
            $instructor_data = $this->db->get_where('users', array('id' => $user_id))->row_array();
            $stripe_keys = json_decode($instructor_data['stripe_keys'], true);
            $public_key = $stripe_keys[0]['public_live_key'];
            $secret_key = $stripe_keys[0]['secret_live_key'];
        }

        // Stripe API configuration
        define('STRIPE_API_KEY', $secret_key);
        define('STRIPE_PUBLISHABLE_KEY', $public_key);

        $status_msg = '';
        $transaction_id = '';
        $paid_amount = '';
        $paid_currency = '';
        $payment_status = '';

        // Check whether stripe checkout session is not empty
        if($session_id != ""){
            //$session_id = $_GET['session_id'];

            // Include Stripe PHP library
            require_once APPPATH.'libraries/Stripe/init.php';

            // Set API key
            \Stripe\Stripe::setApiKey(STRIPE_API_KEY);

            // Fetch the Checkout Session to display the JSON result on the success page
            try {
                $checkout_session = \Stripe\Checkout\Session::retrieve($session_id);
            }catch(Exception $e) {
                $api_error = $e->getMessage();
            }

            if(empty($api_error) && $checkout_session){
                // Retrieve the details of a PaymentIntent
                try {
                    $intent = \Stripe\PaymentIntent::retrieve($checkout_session->payment_intent);
                } catch (\Stripe\Exception\ApiErrorException $e) {
                    $api_error = $e->getMessage();
                }

                // Retrieves the details of customer
                try {
                    // Create the PaymentIntent
                    $customer = \Stripe\Customer::retrieve($checkout_session->customer);
                } catch (\Stripe\Exception\ApiErrorException $e) {
                    $api_error = $e->getMessage();
                }

                if(empty($api_error) && $intent){
                    // Check whether the charge is successful
                    if($intent->status == 'succeeded'){
                        // Customer details
                        $name = $customer->name;
                        $email = $customer->email;

                        // Transaction details
                        $transaction_id = $intent->id;
                        $paid_amount = ($intent->amount/100);
                        $paid_currency = $intent->currency;
                        $payment_status = $intent->status;

                        // If the order is successful
                        if($payment_status == 'succeeded'){
                            $status_msg = get_phrase("Your_Payment_has_been_Successful");
                        }else{
                            $status_msg = get_phrase("Your_Payment_has_failed");
                        }
                    }else{
                        $status_msg = get_phrase("Transaction_has_been_failed");;
                    }
                }else{
                    $status_msg = get_phrase("Unable_to_fetch_the_transaction_details"). ' ' .$api_error;
                }

                $status_msg = 'success';
            }else{
                $status_msg = get_phrase("Transaction_has_been_failed").' '.$api_error;
            }
        }else{
            $status_msg = get_phrase("Invalid_Request");
        }

        $response['status_msg'] = $status_msg;
        $response['transaction_id'] = $transaction_id;
        $response['paid_amount'] = $paid_amount;
        $response['paid_currency'] = $paid_currency;
        $response['payment_status'] = $payment_status;
        $response['stripe_session_id'] = $session_id;
        $response['payment_method'] = 'stripe';

        return $response;
    }

    // VALIDATE PAYPAL PAYMENT AFTER PAYING
    public function paypal_payment($paymentID = "", $paymentToken = "", $payerID = "", $paypalClientID = "", $paypalSecret = "") {
      $paypal_keys = get_settings('paypal');
      $paypal_data = json_decode($paypal_keys);

      $paypalEnv       = $paypal_data[0]->mode; // Or 'production'
      if ($paypal_data[0]->mode == 'sandbox') {
          $paypalURL       = 'https://api.sandbox.paypal.com/v1/';
      } else {
          $paypalURL       = 'https://api.paypal.com/v1/';
      }

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $paypalURL.'oauth2/token');
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_USERPWD, $paypalClientID.":".$paypalSecret);
      curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
      $response = curl_exec($ch);
      curl_close($ch);

      if(empty($response)){
          return false;
      }else{
          $jsonData = json_decode($response);
          $curl = curl_init($paypalURL.'payments/payment/'.$paymentID);
          curl_setopt($curl, CURLOPT_POST, false);
          curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($curl, CURLOPT_HEADER, false);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($curl, CURLOPT_HTTPHEADER, array(
              'Authorization: Bearer ' . $jsonData->access_token,
              'Accept: application/json',
              'Content-Type: application/xml'
          ));
          $response = curl_exec($curl);
          curl_close($curl);

          // Transaction data
          $result = json_decode($response);

          // CHECK IF THE PAYMENT STATE IS APPROVED OR NOT
          if($result && $result->state == 'approved'){
              return true;
          }else{
              return false;
          }
      }
    }
}
