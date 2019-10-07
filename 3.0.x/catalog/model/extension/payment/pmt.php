<?php

class ModelExtensionPaymentPmt extends Model
{
    public function getMethod($address, $total)
    {
        $this->load->language('extension/payment/pmt');

        if ($this->config->get('payment_pmt_status') === 'yes') {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            $method_data = array(
                'code' => 'pmt',
                'title' => $this->language->get('method_title'),
                'sort_order' => 1
            );
        }
        return $method_data;
    }
}
