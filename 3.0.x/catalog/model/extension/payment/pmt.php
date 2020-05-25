<?php

class ModelExtensionPaymentPmt extends Model
{

    /**
     * @param $address
     * @param $total
     *
     * @return array
     */
    public function getMethod($address, $total)
    {
        $this->load->language('extension/payment/pmt');

        if ($this->config->get('payment_pmt_status') === 'yes') {
            $status = true;
        } else {
            $status = false;
        }

        $publicKey = $this->config->get('payment_pmt_public_key');
        $financia = sprintf($this->language->get('method_title'), $publicKey, $total);
        $method_data = array();
        if ($status) {
            $method_data = array(
                'code' => 'pmt',
                'title' => $financia,
                'sort_order' => 1
            );
        }
        return $method_data;
    }
}
