<?php
// Text
$_['method_title'] = 'Financiación Instantánea<script type="text/javascript" src="https://cdn.pagantis.com/js/pg-v2/sdk.js"></script> 

<div class="customSimulator"></div>
<script>
    options = {
        //IMPORTANT: Set your public key
        "publicKey": "%f",
        "selector": ".customSimulator",
        "type": pgSDK.simulator.types.PRODUCT_PAGE,
        "skin": pgSDK.simulator.skins.BLUE,
        "itemAmount": "%f",
        "itemQuantity": "1",
        "locale": "%f",
        "country": "%f",
    }
    pgSDK.simulator.init(options);
</script>';
