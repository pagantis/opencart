<?php

class ControllerExtensionPaymentPmt extends controller
{
    /** @var string */
    const CODE = 'payment_pmt';

    /**
     * ControllerPaymentPmt::index().
     *
     * default route for form load/update
     */
    public function index()
    {
        // Compatibility for 1.4.7
        if (empty($this->session->data['user_token'])) {
            $this->session->data['user_token'] = '';
        }

        // Load language file and settings model
        $this->load->language('extension/payment/pmt');
        $this->load->model('setting/setting');

        // Set page title
        $this->document->setTitle($this->language->get('heading_title'));

        // Set errors if fields not correct
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $pk = $this->request->post['payment_pmt_public_key'];
            $sk = $this->request->post['payment_pmt_secret_key'];
            if ($pk =='' || $sk=='') {
                $data['error_warning'] = $this->language->get('error_pmt_pmt_keys');
            } else {
                $this->model_setting_setting->editSetting('payment_pmt', $this->request->post);
                $data['error_success'] =  $this->language->get('text_success');
            }
        }

        if (isset($this->error['test_customer_code'])) {
            $data['error_test_customer_code'] = $this->error['test_customer_code'];
        } else {
            $data['error_test_customer_code'] = '';
        }

        if (isset($this->error['test_customer_key'])) {
            $data['error_test_customer_key'] = $this->error['test_customer_key'];
        } else {
            $data['error_test_customer_key'] = '';
        }

        // Set breadcrumbs
        $us = $this->session->data['user_token'];
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token='.$us, true)
        );


        $data['breadcrumbs'][] = array(
            'href' => $this->url->link('extension/payment/pmt', 'user_token='.$us, true),
            'text' => $this->language->get('text_payment')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/pmt', 'user_token='.$us, true)
        );

        $data['action'] = $this->url->link('extension/payment/pmt', 'user_token='.$us, true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token='.$us.'&type=payment', true);


        // Load values for fields
        if (isset($this->request->post['payment_pmt_status'])) {
            $data['payment_pmt_status'] = $this->request->post['payment_pmt_status'];
        } else {
            $data['payment_pmt_status'] = $this->config->get('payment_pmt_status');
        }
        $data['checked_yes'] = 'unchecked';
        $data['checked_no'] = 'unchecked';
        if ($data['payment_pmt_status'] === 'yes') {
            $data['checked_yes'] = 'checked';
        } else {
            $data['checked_no'] = 'checked';
        }

        if (isset($this->request->post['payment_pmt_public_key'])) {
            $data['payment_pmt_public_key'] = $this->request->post['payment_pmt_public_key'];
        } else {
            $data['payment_pmt_public_key'] = $this->config->get('payment_pmt_public_key');
        }
        if (isset($this->request->post['payment_pmt_secret_key'])) {
            $data['payment_pmt_secret_key'] = $this->request->post['payment_pmt_secret_key'];
        } else {
            $data['payment_pmt_secret_key'] = $this->config->get('payment_pmt_secret_key');
        }
        if (isset($this->request->post['payment_pmt_simulator'])) {
            $data['payment_pmt_simulator'] = $this->request->post['payment_pmt_simulator'];
        } else {
            $data['payment_pmt_simulator'] = $this->config->get('payment_pmt_simulator');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/payment/pmt', $data));
    }

    /**
     * Install code
     */
    public function install()
    {
        $this->load->model('setting/setting');

        $defaults['payment_pmt_status'] = 'no';
        $defaults['payment_pmt_public_key'] = '';
        $defaults['payment_pmt_secret_key'] = '';
        //$defaults['payment_pmt_simulator'] = 'yes';

        $this->model_setting_setting->editSetting(self::CODE, $defaults);
    }

    /**
     * Uninstall code
     */
    public function uninstall()
    {
        $this->load->model('setting/setting');

        $this->model_setting_setting->deleteSetting(self::CODE);
    }
}
