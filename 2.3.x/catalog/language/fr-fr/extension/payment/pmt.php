<?php
// Text
$_['text_title'] = 'Financez avec Pagantis 

<div class="customSimulator"></div>
<script>

    options = {
        //IMPORTANT: Set your public key
        "publicKey": "%s",
        "selector": ".customSimulator",
        "type": pgSDK.simulator.types.PRODUCT_PAGE,
        "skin": pgSDK.simulator.skins.BLUE,
        "itemAmount": "%f",
        "itemQuantity": "1",
        "locale": "fr",
        "country": "fr",
    }
    pgSDK.simulator.init(options);
    ';
$_['text_testmode'] = 'PAYMENT GATEWAY IS IN TEST MODE - NO CHARGE WILL BE MADE';
$_['terms'] = '';
$_['heading_fail'] = 'Transaction Failed!';
$_['message_fail'] = '<p>There was a problem with your payment details.</p><p><strong>Your card has not been charged</strong></p><p>Please click continue to try again</p>';
