<!DOCTYPE html>
<html lang="en">
<head>
    <title>Paypal | <?php echo get_settings('system_name');?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo base_url('assets/payment/css/stripe.css');?>"
    rel="stylesheet">
    <link name="favicon" type="image/x-icon" href="<?php echo base_url('uploads/system/'.get_frontend_settings('favicon'));?>" rel="shortcut icon" />
</head>
<body>

    <?php
        $paypal_keys = get_settings('paypal');
        $paypal = json_decode($paypal_keys);
    ?>
    <!--required for getting the stripe token-->

    <img src="<?php echo base_url('uploads/system/'.get_frontend_settings('light_logo')); ?>" width="15%;"
    style="opacity: 0.05;">

    <div class="package-details">
        <strong><?php echo get_phrase('instructor');?> | <?php echo $instructor_name;?></strong> <br>
        <strong><?php echo get_phrase('payout_status');?> | <?php echo get_phrase('pending');?></strong> <br>
        <strong><?php echo get_phrase('payment_due');?> | <?php echo $amount_to_pay;?></strong> <br>
        <div id="paypal-button" style="margin-top: 20px;"></div><br>
    </div>

    <img src="https://www.paypalobjects.com/webstatic/i/logo/rebrand/ppcom-white.svg" width="25%;"
    style="opacity: 0.05;">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://www.paypalobjects.com/api/checkout.js"></script>

    <script>
    paypal.Button.render({
        env: '<?php echo $paypal[0]->mode;?>', // 'sandbox' or 'production'
        style: {
            label: 'paypal',
            size:  'medium',    // small | medium | large | responsive
            shape: 'rect',     // pill | rect
            color: 'blue',     // gold | blue | silver | black
            tagline: false
        },
        client: {
            production: '<?php echo $production_client_id;?>',
            sandbox:    '<?php echo $production_client_id;?>'
        },

        commit: true, // Show a 'Pay Now' button

        payment: function(data, actions) {
            return actions.payment.create({
                payment: {
                    transactions: [
                        {
                            amount: { total: '<?php echo $amount_to_pay;?>', currency: '<?php echo get_settings('paypal_currency'); ?>' }
                        }
                    ]
                }
            });
        },

        onAuthorize: function(data, actions) {
            // executes the payment
            return actions.payment.execute().then(function() {
                var redirectUrl = '<?php echo site_url('admin/paypal_payment/'.$payout_id);?>'+'/'+data.paymentID+'/'+data.paymentToken+'/'+data.payerID;
                window.location = redirectUrl;
            });
        }

    }, '#paypal-button');
</script>

</body>
</html>
