<?php

class ModelExtensionPaymentPmt extends Model
{

    /**
     * @param $address
     *
     * @return array
     */
    public function getMethod($address)
    {
        $method_data = array();
        $this->load->language('extension/payment/pmt');

        if ($this->config->get('pmt_status') === 'yes') {
            $status = true;
        } else {
            $status = false;
        }

        if ($status) {
            $method_data = array(
                'code' => 'pmt',
                'title' => $this->language->get('text_title')
            );
        }

        return $method_data;
    }
}
