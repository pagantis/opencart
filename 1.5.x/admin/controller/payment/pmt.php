<?php

class ControllerPaymentPmt extends controller
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
        $this->load->language('payment/pmt');
        $this->load->model('setting/setting');

        // Set page title
        $this->document->setTitle($this->language->get('heading_title'));

        // Process settings if form submitted
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            $this->load->model('setting/setting');
            $this->model_setting_setting->editSetting('pmt', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            //v1.5
            $this->redirect(HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token']);
            //v2.0
            //$this->response->redirect($this->url->link('extension/payment', 'token='.$this->session->data['token'], 'SSL'));
        }

        // Load language texts
        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        $this->data['text_all_zones'] = $this->language->get('text_all_zones');
        $this->data['text_yes'] = $this->language->get('text_yes');
        $this->data['text_no'] = $this->language->get('text_no');
        $this->data['text_edit'] = $this->language->get('text_edit');
        $this->data['test_customer_code'] = $this->language->get('test_customer_code');
        $this->data['test_customer_key'] = $this->language->get('test_customer_key');
        $this->data['real_customer_code'] = $this->language->get('real_customer_code');
        $this->data['real_customer_key'] = $this->language->get('real_customer_key');
        $this->data['entry_test'] = $this->language->get('entry_test');
        $this->data['entry_discount'] = $this->language->get('entry_discount');
        $this->data['entry_status'] = $this->language->get('entry_status');
        $this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
        $this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $this->data['entry_order_status'] = $this->language->get('entry_order_status');
        $this->data['tip'] = $this->language->get('tip');
        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_cancel'] = $this->language->get('button_cancel');
        $this->data['tab_general'] = $this->language->get('tab_general');

        // Set errors if fields not correct
        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->error['test_customer_code'])) {
            $this->data['error_test_customer_code'] = $this->error['test_customer_code'];
        } else {
            $this->data['error_test_customer_code'] = '';
        }

        if (isset($this->error['test_customer_key'])) {
            $this->data['error_test_customer_key'] = $this->error['test_customer_key'];
        } else {
            $this->data['error_test_customer_key'] = '';
        }

        if (isset($this->error['real_customer_code'])) {
            $this->data['error_real_customer_code'] = $this->error['real_customer_code'];
        } else {
            $this->data['error_real_customer_code'] = '';
        }

        if (isset($this->error['real_customer_key'])) {
            $this->data['error_real_customer_key'] = $this->error['real_customer_key'];
        } else {
            $this->data['error_real_customer_key'] = '';
        }

        // Set breadcrumbs
        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
               'href' => HTTPS_SERVER.'index.php?route=common/home&token='.$this->session->data['token'],
               'text' => $this->language->get('text_home'),
              'separator' => false,
        );

        $this->data['breadcrumbs'][] = array(
               'href' => HTTPS_SERVER.'index.php?route=extension/payment&token='.$this->session->data['token'],
               'text' => $this->language->get('text_payment'),
              'separator' => ' :: ',
        );

        $this->data['breadcrumbs'][] = array(
               'href' => HTTPS_SERVER.'index.php?route=payment/pmt&token='.$this->session->data['token'],
               'text' => $this->language->get('heading_title'),
              'separator' => ' :: ',
        );

        // Set save/cancel button urls
        $this->data['action'] = HTTPS_SERVER.'index.php?route=payment/pmt&token='.$this->session->data['token'];

        $this->data['cancel'] = HTTPS_SERVER.'index.php?route=extension/payment&token='.$this->session->data['token'];

        // Load values for fields
        if (isset($this->request->post['pmt_test_customer_code'])) {
            $this->data['pmt_test_customer_code'] = $this->request->post['pmt_test_customer_code'];
        } else {
            $this->data['pmt_test_customer_code'] = $this->config->get('pmt_test_customer_code');
        }
        if (isset($this->request->post['pmt_test_customer_key'])) {
            $this->data['pmt_test_customer_key'] = $this->request->post['pmt_test_customer_key'];
        } else {
            $this->data['pmt_test_customer_key'] = $this->config->get('pmt_test_customer_key');
        }
        if (isset($this->request->post['pmt_real_customer_code'])) {
            $this->data['pmt_real_customer_code'] = $this->request->post['pmt_real_customer_code'];
        } else {
            $this->data['pmt_real_customer_code'] = $this->config->get('pmt_real_customer_code');
        }
        if (isset($this->request->post['pmt_real_customer_key'])) {
            $this->data['pmt_real_customer_key'] = $this->request->post['pmt_real_customer_key'];
        } else {
            $this->data['pmt_real_customer_key'] = $this->config->get('pmt_real_customer_key');
        }
        if (isset($this->request->post['pmt_test'])) {
            $this->data['pmt_test'] = $this->request->post['pmt_test'];
        } else {
            $this->data['pmt_test'] = $this->config->get('pmt_test');
        }
        if (isset($this->request->post['pmt_discount'])) {
            $this->data['pmt_discount'] = $this->request->post['pmt_discount'];
        } else {
            $this->data['pmt_discount'] = $this->config->get('pmt_discount');
        }
        if (isset($this->request->post['pmt_status'])) {
            $this->data['pmt_status'] = $this->request->post['pmt_status'];
        } else {
            $this->data['pmt_status'] = $this->config->get('pmt_status');
        }
        if (isset($this->request->post['pmt_sort_order'])) {
            $this->data['pmt_sort_order'] = $this->request->post['pmt_sort_order'];
        } else {
            $this->data['pmt_sort_order'] = $this->config->get('pmt_sort_order');
        }

        // Render template
        //v1.5.x version
        $this->template = 'payment/pmt.tpl';
            $this->children = array(
                'common/header',
                'common/footer'
            );

        $this->response->setOutput($this->render());
        //v2.0 version
        //v2.0
        //$this->data['header'] = $this->load->controller('common/header');
        //$this->data['column_left'] = $this->load->controller('common/column_left');
        //$this->data['footer'] = $this->load->controller('common/footer');
        //$this->response->setOutput($this->load->view('payment/pmt.tpl', $this->data));
    }

    /**
     * ControllerPaymentPmt::validate().
     *
     * Validation code for form
     */
    private function validate()
    {
        if (!$this->user->hasPermission('modify', 'payment/pmt')) {
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
