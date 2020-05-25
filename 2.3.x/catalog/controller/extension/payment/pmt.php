<?php
class ControllerExtensionPaymentPmt extends Controller
{
  private $error;

  public function index()
  {
    $this->load->language('extension/payment/pmt');

    $data['text_testmode'] = $this->language->get('text_testmode');
    // Set up confirm/back button text
    $data['button_confirm'] = $this->language->get('button_confirm');
    $data['button_back'] = $this->language->get('button_back');

    // Load model for checkout page
    $this->load->model('checkout/order');

    // Load order into memory
    $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

    //populate data to invoke form
    $data['full_name'] = $order_info['payment_firstname'].' '.$order_info['payment_lastname'];

    $currency = $order_info['currency_code'];
    $data['currency_code'] = $order_info['currency_code'];
    //we only support EUR
    $data['currency_code'] = 'EUR';

    //discount
    if ($this->config->get('pmt_discount')){
      $data['discount'] = 'true';
    }else{
      $data['discount'] = 'false';
    }

    $data['mobile_phone'] = $order_info['telephone'];
    $data['amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false) * 100;
    $data['total'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false) * 100;

    if ($this->config->get('pmt_test')) {
      $data['customer_code'] = $this->config->get('pmt_test_customer_code');
      $data['customer_key'] = $this->config->get('pmt_test_customer_key');
    } else {
      $data['customer_code'] = $this->config->get('pmt_real_customer_code');
      $data['customer_key'] = $this->config->get('pmt_real_customer_key');
    }
    // Other params for payment page
    $data['email'] = $order_info['email'];

    // Encrypt order id for verification
    //$this->load->library('encryption');

    //$encryption = new Encryption($this->config->get('config_encryption'));
    //$enc = $encryption->encrypt($this->session->data['order_id']);
    //for security reasons we encrypt the order id
    $data['order_id'] = $this->session->data['order_id'];
    //encrypted version
    //$data['order_id'] =$enc;
    $route = $_GET['route'];

    // Set success/fail urls
    $data['redirector_success'] = $this->url->link('checkout/success');
    //$data['redirector_success'] = urlencode($enc);
    $data['redirector_failure'] = $this->url->link('checkout/failure');
    $data['cancelled_url'] = $this->url->link('checkout/checkout');

    //adresss

    $data['street'] = $order_info['payment_address_1'].' '.$order_info['payment_address_2'];
    $data['city'] = $order_info['payment_city'];
    $data['province'] = $order_info['payment_zone'];
    $data['citycode'] = $order_info['payment_postcode'];

    $data['sstreet'] = $order_info['shipping_address_1'].' '.$order_info['shipping_address_2'];
    $data['scity'] = $order_info['shipping_city'];
    $data['sprovince'] = $order_info['shipping_zone'];
    $data['scitycode'] = $order_info['shipping_postcode'];

    //locale
    $data['locale'] = strtolower($order_info['language_code']);
    $data['purchase_country'] = strtoupper($order_info['payment_iso_code_2']);

    // we have a list of supported localtes, if locale is not in the list we degault to en.

    $available_locales = array('ca','en','es','eu','fr','gl','it','pl','ru');
    if (!in_array($data['locale'], $available_locales)) {
      $data['locale'] = 'en';
    }
    //shipping price
    $i = 0;

    if (isset($this->session->data['shipping_method'])){
      $shipping = $this->session->data['shipping_method'];

      if (!empty($shipping)) {
        $data['products'][$i]['description'] = $shipping['title'];
        $data['products'][$i]['quantity'] = 1;
        $data['products'][$i]['amount'] = $shipping['cost'];
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
      $data['products'][$i]['description'] ="Taxes";
      $description[]="Taxes";
      $data['products'][$i]['quantity'] = 1;
      $data['products'][$i]['amount'] = $tax_price;
      ++$i;
    }

    //product description
    $products = $this->cart->getProducts();
    $description="";
    foreach ($products as $key => $item) {
      $data['products'][$i]['description'] = $item['name'] . " ( ".$item['quantity'].") ";
      $description[]=$item['name'] . " ( ".$item['quantity'].") ";
      $data['products'][$i]['quantity'] = $item['quantity'];
      $data['products'][$i]['amount'] = $item['price'] * $item['quantity'];
      ++$i;
    }

    $data['description']=implode(",",$description);

    //dynamic callback
    $data['callback'] = $this->url->link('extension/payment/pmt/callback');

    $dataToEncode = $data['customer_key'].$data['customer_code'].$data['order_id'].$data['total'].$data['currency_code'].$data['redirector_success'].$data['redirector_failure'].$data['callback'].$data['discount'].$data['cancelled_url'];
    $data['signature'] = sha1($dataToEncode);
    //form url
    $data['action'] = 'https://pmt.pagantis.com/v1/installments';


    return $this->load->view('extension/payment/pmt', $data);
  }

  public function failure()
  {
    $this->language->load('payment/pmt');

    $this->document->setTitle($this->language->get('heading_fail'));
    $this->session->data['error'] = $this->language->get('heading_fail');
    $this->response->redirect($this->url->link('checkout/failure'));
  }

  public function success()
  {
    $this->response->redirect($this->url->link('checkout/success'));
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
    $signatureCheckSha512 = hash('sha512', $this->key.$temp['account_id'].$temp['api_version'].$temp['event'].$temp['data']['id']);
    if ($signature_check != $temp['signature'] && $signatureCheckSha512 != $temp['signature'] ){
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
      $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('config_order_status_id'), 'Order ID: '.$order_id,true);

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
