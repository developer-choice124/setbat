<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library(array('ion_auth', 'form_validation'));
        $this->load->helper(array('url', 'language'));
        $this->load->model('Setting_model');
        $this->load->model('MyModel');
        $this->settings = $this->Setting_model->get_setting();
        if (!empty($this->settings->timezone)) {
            date_default_timezone_set($this->settings->timezone);
        } else {
            date_default_timezone_set("Asia/Kolkata");
        }
    }
    public function index() {
        echo 'Welcome to Astrology Tv App';
    }

    public function payByPaypal() {
        $params = $_REQUEST;
        $uid = $params['user_id'];
        $pid = $params['package_id'];
        $cart = $this->Common_model->get_single_query("select * from package_cart where user_id = $uid and package_id = $pid");
        $cData = $this->Common_model->get_single_query("select * from users where id = $uid");
        $product_info = 'Package subscription for Astrology Tv App';
        $amount = round($cart->amount);
        
        // Set variables for paypal form
        $returnURL = base_url().'Payment/paypalSuccess';
        $cancelURL = base_url().'Payment/paypalCancel';
        $notifyURL = base_url().'Payment/paypalIpn';
        
        // Add fields to paypal form
        $this->paypal_lib->add_field('return', $returnURL);
        $this->paypal_lib->add_field('cancel_return', $cancelURL);
        $this->paypal_lib->add_field('notify_url', $notifyURL);
        $this->paypal_lib->add_field('item_name', $product_info);
        $this->paypal_lib->add_field('custom', $uid);
        $this->paypal_lib->add_field('item_number',  $cart->transaction_id);
        $this->paypal_lib->add_field('amount',  $amount);
        
        // Render paypal form
        $this->paypal_lib->paypal_auto_form();
    }

    public function paypalSuccess()
    {
        $response = $_REQUEST;
        $txnid = $response['item_number'];
        $cart = $this->Common_model->get_single_query("select * from package_cart where transaction_id = '$txnid'");
        if($cart) {
            $uid = $cart->user_id;
            $pid = $cart->package_id;
            $amount = $response['payment_gross'];
            $tdate = date("Y-m-d H:i:s", strtotime($response['payment_date']));
            $status = $response['payment_status'];
            $paypalTxnid = $response['txn_id'];
            $data = array(
                    'txnid'             => $txnid,
                    'package_id'        => $pid,
                    'amount'            => $amount,
                    'user_id'           => $uid,
                    'transaction_date'  => $tdate,
                    'status'            => $status,
                    'payu_or_paypal'    => 2,
                    'paypal_txnid'      => $paypalTxnid
                );
            $this->Crud_model->insert_record('transaction', $data);
            $package = $this->Common_model->get_single_query("select * from package where id = $pid");
            $expiryType = $package->type;
            $expiration = $package->expiration;
            if($expiryType == 'year') {
               $expiry_date = date("Y-m-d H:i:s", strtotime("+".$expiration." years"));
            }
            elseif ($expiryType == 'month') {
                $expiry_date = date("Y-m-d H:i:s", strtotime("+".$expiration." months"));
            }
            else {
                $expiry_date = date("Y-m-d H:i:s", strtotime("+".$expiration." days"));
            }
            $udata = array(
                'package_id'        => $pid,
                'expiry_date'       => $expiry_date,
                'premium_account'   => 'yes'
            );
            $this->Crud_model->edit_record('users',$uid,$udata);
            $this->Crud_model->delete_record('package_cart', $cart->id);
            $template['title'] = 'Astrology Tv App';
            $template['page'] = "payment/pay_confirm";
            $template['page_title'] = "Astrology Tv | Thank You";
            $this->load->view('payment/pay_success',$template);
        }
    }

    public function paypalCancel()
    {
        $response = $_REQUEST;
        $response1 = $this->input->get();
        print_r($response);
        print_r($response1);
    }

    public function paypalIpn()
    {
        $response = $_REQUEST;
        $response1 = $this->input->get();
        print_r($response);
        print_r($response1);
    }

    public function payNow() {
        $params = $_REQUEST;
        $uid = $params['user_id'];
        $pid = $params['package_id'];
        $cart = $this->Common_model->get_single_query("select * from package_cart where user_id = $uid and package_id = $pid");
        $cData = $this->Common_model->get_single_query("select * from users where id = $uid");
        $product_info = 'Package subscription for Astrology Tv App';
        $customer_name = $cData->full_name;
        $customer_email = $cData->email;
        $customer_mobile = $cData->phone;
        $amount = round($cart->amount);
        
        //payumoney details
    
        $MERCHANT_KEY = "oFzeGu"; //change  merchant with yours(gtKFFx)(oFzeGu)
        $SALT = "5JzCigC7";  //change salt with yours(eCwWELxi)(5JzCigC7)

        $txnid = $cart->transaction_id;
        //optional udf values 
        $udf1 = '';
        $udf2 = '';
        $udf3 = '';
        $udf4 = '';
        $udf5 = '';
        
        $hash_string = $MERCHANT_KEY."|".$txnid."|".$amount."|".$product_info."|".$customer_name."|".$customer_email."|||||||||||".$SALT;
        $hash = strtolower(hash('sha512', $hash_string));       
        $success = base_url() . 'Payment/payuSuccess?user_id='.$uid.'&package_id='.$pid.'&transaction_id='.$txnid; 
        $fail = base_url() . 'Payment/payuFail';
        $cancel = base_url() . 'Payment/payuCancel';
        $payu = array(
            'mkey' => $MERCHANT_KEY,
            'tid' => $txnid,
            'hash' => $hash,
            'amount' => $amount,         
            'name' => $customer_name,
            'pinfo' => $product_info,
            'mailid' => $customer_email,
            'phoneno' => $customer_mobile,
            'action' => "https://secure.payu.in/_payment", //for live change action  https://secure.payu.in for test https://test.payu.in/_payment
            'sucess' => $success,
            'failure' => $fail,
            'cancel' => $cancel            
        );
        $template['payu'] = $payu;
        $template['title'] = 'Astrology Tv App';
        $template['page'] = "payment/pay_confirm";
        $template['page_title'] = "Astrology Tv | Pay Now";
        $this->load->view('payment/pay_confirm', $template);
    }
    public function payuSuccess() {
        $response = $_REQUEST;
        if($response['status'] == 'success') {
            $txnid = $response['txnid'];
            $cart = $this->Common_model->get_single_query("select * from package_cart where transaction_id = '$txnid'");
            if($cart) {
                $uid = $cart->user_id;
                $pid = $cart->package_id;
                $amount = $response['amount'];
                $tdate = $response['addedon'];
                $status = $response['status'];
                $data = array(
                        'txnid'             => $txnid,
                        'package_id'        => $pid,
                        'amount'            => $amount,
                        'user_id'           => $uid,
                        'transaction_date'  => $tdate,
                        'status'            => $status
                    );
                $this->Crud_model->insert_record('transaction', $data);
                $package = $this->Common_model->get_single_query("select * from package where id = $pid");
                $expiryType = $package->type;
                $expiration = $package->expiration;
                if($expiryType == 'year') {
                   $expiry_date = date("Y-m-d H:i:s", strtotime("+".$expiration." years"));
                }
                elseif ($expiryType == 'month') {
                    $expiry_date = date("Y-m-d H:i:s", strtotime("+".$expiration." months"));
                }
                else {
                    $expiry_date = date("Y-m-d H:i:s", strtotime("+".$expiration." days"));
                }
                $udata = array(
                    'package_id'        => $pid,
                    'expiry_date'       => $expiry_date,
                    'premium_account'   => 'yes'
                );
                $this->Crud_model->edit_record('users',$uid,$udata);
                $this->Crud_model->delete_record('package_cart', $cart->id);
            } else {
                
            }
            
            $template['title'] = 'Astrology Tv App';
            $template['page'] = "payment/pay_confirm";
            $template['page_title'] = "Astrology Tv | Thank You";
            $this->load->view('payment/pay_success',$template);
        }
    }
    public function payuFail() {
        $response = $_REQUEST;
        $txnid = $response['txnid'];
        $data = array('status' => 202, 'message' => 'payment failed');
        echo json_encode($data);
        //return show_error($reponse.'<br/> <a href="'.base_url('Payment').'">Return to Home</a>');
    }
    public function payuCancel() {
        $response = $_REQUEST;
        return show_error($reponse.'<br/> <a href="'.base_url('Payment').'">Return to Home</a>');
    }
}