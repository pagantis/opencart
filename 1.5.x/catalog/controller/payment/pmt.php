<?php

class ControllerPaymentPmt extends Controller
{
    private $error;

    public function index()
    {
        //version 1.5
        $this->language->load('payment/pmt');

        //version 2.0
        //$this->load->language('payment/pmt');

        $this->data['text_testmode'] = $this->language->get('text_testmode');
        // Set up confirm/back button text
        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['button_back'] = $this->language->get('button_back');

        // Load model for checkout page
        $this->load->model('checkout/order');

        // Load order into memory
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

				//populate data to invoke form
        $this->data['full_name'] = $order_info['payment_firstname'].' '.$order_info['payment_lastname'];

        $currency = $order_info['currency_code'];
        $this->data['currency_code'] = $order_info['currency_code'];
        //we only support EUR
        $this->data['currency_code'] = 'EUR';

        //discount
        if ($this->config->get('pmt_discount')){
          $this->data['discount'] = 'true';
        }else{
          $this->data['discount'] = 'false';
        }
        $this->data['phone'] = $order_info['telephone'];
        $this->data['amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false) * 100;
        $this->data['total'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false) * 100;

        if ($this->config->get('pmt_test')) {
            $this->data['customer_code'] = $this->config->get('pmt_test_customer_code');
            $this->data['customer_key'] = $this->config->get('pmt_test_customer_key');
        } else {
            $this->data['customer_code'] = $this->config->get('pmt_real_customer_code');
            $this->data['customer_key'] = $this->config->get('pmt_real_customer_key');
        }
                // Other params for payment page
                $this->data['email'] = $order_info['email'];

                // Encrypt order id for verification
                //$this->load->library('encryption');

                //$encryption = new Encryption($this->config->get('config_encryption'));
                //$enc = $encryption->encrypt($this->session->data['order_id']);
                //for security reasons we encrypt the order id
                $this->data['order_id'] = $this->session->data['order_id'];
                //encrypted version
                //$this->data['order_id'] =$enc;
                $route = $_GET['route'];

                // Set success/fail urls
                $this->data['redirector_success'] = HTTPS_SERVER.'index.php?route=payment/pmt/success&';
                //$this->data['redirector_success'] = urlencode($enc);
                $this->data['redirector_failure'] = HTTPS_SERVER.'index.php?route=payment/pmt/failure&';

                //adresss

                $this->data['street'] = $order_info['payment_address_1'].' '.$order_info['payment_address_2'];
        $this->data['city'] = $order_info['payment_city'];
        $this->data['province'] = $order_info['payment_zone'];
        $this->data['citycode'] = $order_info['payment_postcode'];

                //locale
                $this->data['locale'] = strtolower($order_info['language_code']);

                // we have a list of supported localtes, if locale is not in the list we degault to en.

                $available_locales = array('ca','en','es','eu','fr','gl','it','pl','ru');
        if (!in_array($this->data['locale'], $available_locales)) {
            $this->data['locale'] = 'en';
        }

                //shipping price
                $i = 0;

        if (isset($this->session->data['shipping_method'])){
          $shipping = $this->session->data['shipping_method'];

          if (!empty($shipping)) {
              $this->data['products'][$i]['description'] = $shipping['title'];
              $this->data['products'][$i]['quantity'] = 1;
              $this->data['products'][$i]['amount'] = $shipping['cost'];
              ++$i;
          }
        }

          //taxes
          	$taxes = $this->cart->getTaxes();
            $tax_price=0;
            foreach ($taxes as $t => $price){
                $tax_price+=$price;
            }

            if ($tax_price > 0){
              $this->data['products'][$i]['description'] ="Taxes";
              $description[]="Taxes";
              $this->data['products'][$i]['quantity'] = 1;
              $this->data['products'][$i]['amount'] = $tax_price;
              ++$i;
            }

                //product description
                $products = $this->cart->getProducts();
                $description="";
        foreach ($products as $key => $item) {
            $this->data['products'][$i]['description'] = $item['name'] . " ( ".$item['quantity'].") ";
            $description[]=$item['name'] . " ( ".$item['quantity'].") ";
            $this->data['products'][$i]['quantity'] = $item['quantity'];
            $this->data['products'][$i]['amount'] = $item['total'] * $item['quantity'];
            ++$i;
        }
        $this->data['description']=implode(",",$description);


                //dynamic callback
                $this->data['callback'] = HTTPS_SERVER.'index.php?route=payment/pmt/callback';


        $dataToEncode = $this->data['customer_key'].$this->data['customer_code'].$this->data['order_id'].$this->data['total'].$this->data['currency_code'].$this->data['redirector_success'].$this->data['redirector_failure'].$this->data['callback'].$this->data['discount'];

        $this->data['signature'] = sha1($dataToEncode);
                //form url
                $this->data['action'] = 'https://pmt.pagantis.com/v1/installments';

                // Render page template
                $this->id = 'payment';

        // version 2.0
        //$this->data['header'] = $this->load->controller('common/header');
        //$this->data['column_left'] = $this->load->controller('common/column_left');
        //$this->data['footer'] = $this->load->controller('common/footer');

        //version 1.5
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/pmt.tpl')) {
    			$this->template = $this->config->get('config_template') . '/template/payment/pmt.tpl';
    		} else {
    			$this->template = 'default/template/payment/pmt.tpl';
    		}

        //version 2.0
        /*
        if (file_exists(DIR_TEMPLATE.$this->config->get('config_template').'/template/payment/pmt.tpl')) {
            return $this->load->view($this->config->get('config_template').'/template/payment/pmt.tpl', $data);
        } else {
            return $this->load->view('default/template/payment/pmt.tpl', $data);
        }
        */
       //version 1.5
       $this->render();
    }


    public function failure()
    {
        $this->language->load('payment/pmt');

        $this->document->setTitle($this->language->get('heading_fail'));
        $this->session->data['error'] = $this->language->get('heading_fail');
        $this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));

    }

    public function success()
    {
        $this->response->redirect(HTTPS_SERVER.'index.php?route=checkout/success');
    }

    public function callback()
    {
        $json = file_get_contents('php://input');
        $temp = json_decode($json, true);
        $data = $temp['data'];

        // Verify notification
        if ($this->config->get('pmt_test')) {
            $this->key = $this->config->get('pmt_test_customer_key');
        } else {
            $this->key = $this->config->get('pmt_real_customer_key');
        }
        $signature_check = sha1($this->key.$temp['account_id'].$temp['api_version'].$temp['event'].$temp['data']['id']);
        if ($signature_check != $temp['signature'] ){
          //hack detected - not implemented yet
          die('ERROR - Hack attempt detected');
        }

        $order_id = $data['order_id'];
        $event = $temp['event'];

        //we got a new correct sale
        if ($event == 'charge.created') {
            $this->load->model('checkout/order');
                // Load order, and verify the order has not been processed before, if it has, go to success page
                $order_info = $this->model_checkout_order->getOrder($order_id);
                //version 2
                //$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('config_order_status_id'), 'Order ID: '.$order_id,true);

                //version 1.5
                if ($order_info['order_status_id'] == '0') {
                  $this->model_checkout_order->confirm($order_id, $this->config->get('config_order_status_id'), 'Order ID: ' . $order_id);
                }else{
                  $this->model_checkout_order->update($order_id, $this->config->get('config_order_status_id'), 'Order ID: ' .$order_id, FALSE);
                }
            if ($order_info) {
                if ($order_info['order_status_id'] != 0) {
                    $this->response->redirect(HTTPS_SERVER.'index.php?route=checkout/success');
                }
            }


        } elseif ($event == 'charge.failed') {
            //do nothing
        }
    }
}
