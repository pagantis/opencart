<?php
// Text
$_['method_title'] = 'Financiación Instantánea

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
        "locale": "es",
        "country": "es",
    }
    pgSDK.simulator.init(options);
</script>';