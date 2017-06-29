<?php
// Text
$_['text_title'] = 'Finaciación con Paga+Tarde <div class="PmtSimulator" data-pmt-num-quota="4" data-pmt-style="neutral" data-pmt-type="3" data-pmt-discount="%d" data-pmt-amount="%f" data-pmt-expanded="no"></div>
    <script type ="text/javascript" src ="https://cdn.pagamastarde.com/pmt-simulator/3/js/pmt-simulator.min.js">
    </script>
    <script>
     jQuery( document ).ready(function() {
       setTimeout(function(){ pmtSimulator.simulator_app.load_jquery();  }, 1500);
    });
    pmtClient.events.send('checkout', { basketAmount: "%f" });
    </script>';
$_['text_testmode'] = 'PAYMENT GATEWAY IS IN TEST MODE - NO CHARGE WILL BE MADE';
$_['terms'] = '';
$_['heading_fail'] = 'Transaction Failed!';
$_['message_fail'] = '<p>There was a problem with your payment details.</p><p><strong>Your card has not been charged</strong></p><p>Please click continue to try again</p>';
