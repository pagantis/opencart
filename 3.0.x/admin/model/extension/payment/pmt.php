<?php

class ModelExtensionPaymentPmt extends Model
{
    const CODE = 'payment_pmt';

    public function install()
    {
        $defaults['payment_pmt_status'] = 'no';
        $defaults['payment_pmt_public_key'] = '';
        $defaults['payment_pmt_secret_key'] = '';
        //$defaults['payment_pmt_simulator'] = 'yes';

        $this->model_setting_setting->editSetting(self::CODE, $defaults);
    }

    public function uninstall()
    {
        $this->model_setting_setting->deleteSetting(self::CODE);
    }

    public function getMethod($address)
    {
        $method_data = array();
        $this->load->language('extension/payment/pmt');

        if ($this->config->get('pmt_status') === 'yes') {
            $status = true;
        } else {
            $status = false;
        }

        //original message
        $financia = $this->language->get('text_title');

        if ($status) {
            $method_data = array(
                'code' => 'pmt',
                'title' => 'blablabla',
                'terms' => 'blabla'
            );
        }

        return $method_data;
    }
}
