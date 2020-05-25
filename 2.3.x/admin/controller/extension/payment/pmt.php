<?php

class ControllerExtensionPaymentPmt extends controller
{
    /**
     * ControllerPaymentPmt::index().
     *
     * default route for form load/update
     */
    public function index()
    {
        // Compatibility for 1.4.7
        if (empty($this->session->data['token'])) {
            $this->session->data['token'] = '';
        }

        // Load language file and settings model
        $this->load->language('extension/payment/pmt');
        $this->load->model('setting/setting');

        // Set page title
        $this->document->setTitle($this->language->get('heading_title'));

        $this->model_setting_event->addEvent(
            'extension_pmt',
            'catalog/controller/checkout/checkout/before',
            'extension/payment/pmt/eventLoadCheckoutJs'
        );

        // Process settings if form submitted
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            $this->model_setting_setting->editSetting('pmt', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/extension', 'token='.$this->session->data['token']. '&type=payment', true));
        }

        // Load language texts
        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['test_customer_code'] = $this->language->get('test_customer_code');
        $data['test_customer_key'] = $this->language->get('test_customer_key');
        $data['real_customer_code'] = $this->language->get('real_customer_code');
        $data['real_customer_key'] = $this->language->get('real_customer_key');
        $data['entry_test'] = $this->language->get('entry_test');
        $data['entry_discount'] = $this->language->get('entry_discount');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['entry_order_status'] = $this->language->get('entry_order_status');
        $data['tip'] = $this->language->get('tip');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['tab_general'] = $this->language->get('tab_general');

        // Set errors if fields not correct
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
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

        if (isset($this->error['real_customer_code'])) {
            $data['error_real_customer_code'] = $this->error['real_customer_code'];
        } else {
            $data['error_real_customer_code'] = '';
        }

        if (isset($this->error['real_customer_key'])) {
            $data['error_real_customer_key'] = $this->error['real_customer_key'];
        } else {
            $data['error_real_customer_key'] = '';
        }

        // Set breadcrumbs
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
                               'text' => $this->language->get('text_home'),
                               'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
                       );


        $data['breadcrumbs'][] = array(
               'href' => $this->url->link('extension/payment/pmt', 'token=' . $this->session->data['token'], true),
               'text' => $this->language->get('text_payment')
        );

        $data['breadcrumbs'][] = array(
                               'text' => $this->language->get('heading_title'),
                               'href' => $this->url->link('extension/payment/pmt', 'token=' . $this->session->data['token'], true)
                       );

        $data['action'] = $this->url->link('extension/payment/pmt', 'token=' . $this->session->data['token'], true);

        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true);


        // Load values for fields
        if (isset($this->request->post['pmt_test_customer_code'])) {
            $data['pmt_test_customer_code'] = $this->request->post['pmt_test_customer_code'];
        } else {
            $data['pmt_test_customer_code'] = $this->config->get('pmt_test_customer_code');
        }
        if (isset($this->request->post['pmt_test_customer_key'])) {
            $data['pmt_test_customer_key'] = $this->request->post['pmt_test_customer_key'];
        } else {
            $data['pmt_test_customer_key'] = $this->config->get('pmt_test_customer_key');
        }
        if (isset($this->request->post['pmt_real_customer_code'])) {
            $data['pmt_real_customer_code'] = $this->request->post['pmt_real_customer_code'];
        } else {
            $data['pmt_real_customer_code'] = $this->config->get('pmt_real_customer_code');
        }
        if (isset($this->request->post['pmt_real_customer_key'])) {
            $data['pmt_real_customer_key'] = $this->request->post['pmt_real_customer_key'];
        } else {
            $data['pmt_real_customer_key'] = $this->config->get('pmt_real_customer_key');
        }
        if (isset($this->request->post['pmt_test'])) {
            $data['pmt_test'] = $this->request->post['pmt_test'];
        } else {
            $data['pmt_test'] = $this->config->get('pmt_test');
        }
        if (isset($this->request->post['pmt_discount'])) {
            $data['pmt_discount'] = $this->request->post['pmt_discount'];
        } else {
            $data['pmt_discount'] = $this->config->get('pmt_discount');
        }
        if (isset($this->request->post['pmt_status'])) {
            $data['pmt_status'] = $this->request->post['pmt_status'];
        } else {
            $data['pmt_status'] = $this->config->get('pmt_status');
        }
        if (isset($this->request->post['pmt_sort_order'])) {
            $data['pmt_sort_order'] = $this->request->post['pmt_sort_order'];
        } else {
            $data['pmt_sort_order'] = $this->config->get('pmt_sort_order');
        }

        // Render template
        //v1.5.x version
        //$this->template = 'payment/pmt.tpl';
        //v2.0 version
        //v2.0
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/payment/pmt', $data));
    }

    /**
     * ControllerPaymentPmt::validate().
     *
     * Validation code for form
     */
    private function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/payment/pmt')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (empty($this->request->post['pmt_test_customer_code'])) {
            $this->error['pmt_test_customer_code'] = $this->language->get('error_pmt_test_customer_code');
        }

        if (empty($this->request->post['pmt_test_customer_key'])) {
            $this->error['pmt_test_customer_key'] = $this->language->get('pmt_test_customer_key');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}
