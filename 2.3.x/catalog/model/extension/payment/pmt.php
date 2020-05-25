<?php

class ModelExtensionPaymentPmt extends Model
{
    public function getMethod($address, $total)
    {
        $this->load->language('extension/payment/pmt');

        if ($this->config->get('pmt_status')) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = array();

        //discount
        if ($this->config->get('pmt_discount')) {
            $data_bool = 1;
        } else {
            $data_bool = 0;
        }

        $publicKey = $this->config->get('payment_pmt_public_key');
        $financia = sprintf($this->language->get('method_title'), $publicKey, $total);

        //original message
        //$financia = $this->language->get('text_title');
        if ($status) {
            $method_data = array(
                'code' => 'pmt',
                'title' => $financia,
                'terms' => '',
                'sort_order' => $this->config->get('pmt_sort_order'),
              );
        }

        return $method_data;
    }
}
