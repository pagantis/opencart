<?php
class ControllerExtensionPaymentPmt extends Controller
{
    private $error;
    private $version = '3.0';

    const ORDER_STATUS = 2;

    /**
     * @return mixed
     */
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
        $data['customer_full_name'] = $order_info['firstname'].' '.$order_info['lastname'];
        $data['member_since'] = '';
        $data['amount_orders'] = 0;
        $data['num_orders'] = 0;
        $data['sign_up_date'] = '';
        $data['amount_refunded'] = 0;
        $data['num_full_refunds'] = 0;
        if ($order_info['customer_id'] > 0) {
            $customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE
              customer_id = " . (int)$order_info['customer_id']);
            $data['member_since'] = substr($customer_query->row['date_added'], 0, 10);
            $order_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order WHERE
              order_status_id in (2,3,5,11,12,13,15,16)
              and customer_id = " . (int)$order_info['customer_id']);
        } else {
            $customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE
                email = '".$order_info['email']."'");
            if ($customer_query->num_rows > 0) {
                $data['member_since'] = substr($customer_query->row['date_added'], 0, 10);
            }
            $order_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order WHERE
              order_status_id in (2,3,5,11,12,13,15,16)
              and email = '" . $order_info['email']."'");
        }
        $data['num_orders'] = $order_query->num_rows;
        foreach ($order_query->rows as $prev_order) {
            if (in_array($prev_order['order_status_id'], array(11, 12, 13, 16))) {
                $data['amount_refunded'] += $prev_order['total'];
                $data['num_full_refunds'] += 1;
            }
            $data['amount_orders'] += $prev_order['total'];
        }

        $currency = $order_info['currency_code'];
        $data['currency_code'] = $order_info['currency_code'];
        //we only support EUR
        $data['currency_code'] = 'EUR';

        $data['mobile_phone'] = $order_info['telephone'];
        $data['amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false) * 100;
        $data['total'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false) * 100;

        $data['customer_code'] = $this->config->get('payment_pmt_public_key');
        $data['customer_key'] = $this->config->get('payment_pmt_secret_key');

        // Other params for payment page
        $data['email'] = $order_info['email'];

        $data['order_id'] = $this->session->data['order_id'];
        $route = $_GET['route'];

        // Set success/fail urls
        $data['redirector_success'] = $this->url->link('extension/payment/pmt/success').'&order_id='.$data['order_id'];
        $data['redirector_failure'] = $this->url->link('checkout/failure');
        $data['cancelled_url'] = $this->url->link('checkout/checkout');
        $data['module_version'] = $this->version;
        $data['platform'] = 'opencart '.VERSION;
        //$data['php'] = phpversion();

        //adresss
        $data['street'] = $order_info['payment_address_1'].' '.$order_info['payment_address_2'];
        $data['city'] = $order_info['payment_city'];
        $data['province'] = $order_info['payment_zone'];
        $data['citycode'] = $order_info['payment_postcode'];
        $data['company'] = $order_info['payment_company'];
        $data['sstreet'] = $order_info['shipping_address_1'].' '.$order_info['shipping_address_2'];
        $data['scity'] = $order_info['shipping_city'];
        $data['sprovince'] = $order_info['shipping_zone'];
        $data['scitycode'] = $order_info['shipping_postcode'];
        $data['sfull_name'] = $order_info['shipping_firstname'].' '.$order_info['shipping_lastname'];
        $data['scompany'] = $order_info['shipping_company'];

        //locale
        $data['locale'] = strtolower($order_info['language_code']);
        $data['is_guest'] = $order_info['customer_id'] == 0 ? 'true' : 'false';
        $data['locale'] = substr($data['locale'], 0, 2);

        //shipping price
        $i = 0;
        if (isset($this->session->data['shipping_method'])) {
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
        foreach ($taxes as $t => $price) {
            $tax_price+=$price;
        }

        if ($tax_price > 0) {
            $data['products'][$i]['description'] ="Taxes";
            $description[]="Taxes";
            $data['products'][$i]['quantity'] = 1;
            $data['products'][$i]['amount'] = $tax_price;
            ++$i;
        }

        //product description
        $products = $this->cart->getProducts();

        $description= array();
        foreach ($products as $key => $item) {
            $data['products'][$i]['description'] = $item['name'] . " (".$item['quantity'].") ";
            $description[]=$item['name'] . " ( ".$item['quantity'].") ";
            $data['products'][$i]['quantity'] = $item['quantity'];
            $data['products'][$i]['amount'] = $item['price'] * $item['quantity'];
            $i++;
        }

        $data['description']=implode(",", $description);

        //dynamic callback
        $data['callback'] = $this->url->link('extension/payment/pmt/callback');
        $data['discount'] = 0;

        $dataToEncode = $data['customer_key'].$data['customer_code'].$data['order_id'].$data['total'].$data['currency_code'].$data['redirector_success'].$data['redirector_failure'].$data['callback'].$data['discount'].$data['cancelled_url'];
        $data['signature'] = sha1($dataToEncode);
        //form url
        $data['action'] = 'https://pmt.pagantis.com/v1/installments';

        return $this->load->view('extension/payment/pmt', $data);
    }

    /**
     * Redirection after a failure process
     */
    public function failure()
    {
        $this->language->load('payment/pmt');

        $this->document->setTitle($this->language->get('heading_fail'));
        $this->session->data['error'] = $this->language->get('heading_fail');
        $this->response->redirect($this->url->link('checkout/failure'));
    }

    /**
     * Redirection after a successfull process
     */
    public function success()
    {
        try {
            $redirect = 'checkout/failure';
            $this->load->model('checkout/order');
            $i = 0;
            $found = false;
            do {
                $i++;
                sleep(1);
                $order_info = $this->model_checkout_order->getOrder($_GET['order_id']);
                if ($order_info) {
                    if ($order_info['order_status_id'] >= self::ORDER_STATUS) {
                        $found = true;
                        $redirect = 'checkout/success';
                    }
                }
            } while ($i<5 && !$found);

            $this->response->redirect($this->url->link($redirect));
        } catch (Exception $e) {
            $this->response->redirect($this->url->link('checkout/failure'));
        }
    }

    /**
     * Callback from PMT => ?route=extension/payment/pmt/callback
     */
    public function callback()
    {
        try {
            $json = file_get_contents('php://input');
            $temp = json_decode($json, true);
            $data = $temp['data'];

            $this->key = $this->config->get('payment_pmt_secret_key');

            $signature_check = sha1($this->key.$temp['account_id'].$temp['api_version'].$temp['event'].$temp['data']['id']);
            $signatureCheckSha512 = hash('sha512', $this->key.$temp['account_id'].$temp['api_version'].$temp['event'].$temp['data']['id']);
            if ($signature_check != $temp['signature'] && $signatureCheckSha512 != $temp['signature']) {
                //hack detected - not implemented yet
                die('ERROR - Hack attempt detected');
            }

            $order_id = $data['order_id'];
            $pmt_order_id = $data['id'];
            $event = $temp['event'];
            if ($event == 'charge.created') {
                $this->load->model('checkout/order');
                // Load order, and verify the order has not been processed before, if it has, go to success page
                $order_info = $this->model_checkout_order->getOrder($order_id);
                $order_message = "Order ID=$order_id - PMT order_id=$pmt_order_id";
                $this->model_checkout_order->addOrderHistory($order_id, self::ORDER_STATUS, $order_message, true);

                $order_info = $this->model_checkout_order->getOrder($order_id);
                if ($order_info) {
                    if ($order_info['order_status_id'] == self::ORDER_STATUS) {
                        //RESPONSE OK
                        $response = array (
                            'statusCode' => '200',
                            'result' => 'Order confirmed',
                            'timestamp' => time(),
                            'order_id' => $order_id
                        );
                    } else {
                        //RESPONSE KO
                        $response = array (
                            'statusCode' => '500',
                            'result' => 'Order NOT confirmed',
                            'timestamp' => time(),
                            'order_id' => $order_id
                        );
                    }
                }
            } elseif ($event == 'charge.failed') {
                //do nothing
            }
        } catch (\Exception $e) {
            $response = array (
                'statusCode' => '500',
                'result' => $e->getMessage(),
                'timestamp' => time(),
                'order_id' => $order_id
            );
        }

        $toJson = json_encode($response, JSON_UNESCAPED_SLASHES);
        $status = $response['statusCode'];
        header("HTTP/1.1 ".$status, true, $status);
        header('Content-Type: application/json', true);
        echo ($toJson);
        exit();
    }

    /**
     * Api controller
     */
    public function api()
    {
        $response = array();
        try {
            if ($_GET['secret' ] == $this->config->get('payment_pmt_secret_key')) {
                $where_id = (isset($_GET['order_id'])) ? 'and order_id='.$_GET['order_id'] : null;
                $order_query = $this->db->query("SELECT * FROM ".DB_PREFIX."order where payment_code='pmt' $where_id");

                $data['num_orders'] = $order_query->num_rows;
                foreach ($order_query->rows as $prev_order) {
                    $key = $prev_order['order_id'];
                    $response['message'][$key]['timestamp'] = $prev_order['date_added'];
                    $response['message'][$key]['order_id'] = $key;
                    $response['message'][$key]['content'] = $prev_order;
                }
            } else {
                $response['result'] = 'Error';
            }

            $response = json_encode($response);
            header("HTTP/1.1 200", true, 200);
            header('Content-Type: application/json', true);
            header('Content-Length: '.strlen($response));
            echo($response);
            exit();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}
