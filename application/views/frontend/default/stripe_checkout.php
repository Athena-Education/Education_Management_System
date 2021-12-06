<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Stripe | <?php echo get_settings('system_name');?></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo base_url('assets/payment/css/stripe.css');?>"
            rel="stylesheet">
        <link name="favicon" type="image/x-icon" href="<?php echo base_url('uploads/system/'.get_frontend_settings('favicon'));?>" rel="shortcut icon" />
    </head>
    <body>
<!--required for getting the stripe token-->
        <?php
            $stripe_keys = get_settings('stripe_keys');
            $values = json_decode($stripe_keys);
            if ($values[0]->testmode == 'on') {
                $public_key = $values[0]->public_key;
                $private_key = $values[0]->secret_key;
            } else {
                $public_key = $values[0]->public_live_key;
                $private_key = $values[0]->secret_live_key;
            }
        ?>

        <script>
            var stripe_key = '<?php echo $public_key;?>';
        </script>

<!--required for getting the stripe token-->

        <!-- <img src="<?php echo base_url('uploads/system/'.get_frontend_settings('light_logo')); ?>" width="15%;" style="opacity: 0.05;" id="application-logo"> -->

            <div id="loader_modal" style="position: fixed; display: none; width: 100%; height: 100%; top: 0; left: 0; right: 0; bottom: 0; background-color: #42477077; z-index: 1000; color: #fff; text-align: center; padding-top: 100px;">Please wait....</div>

            <form method="post"
              <?php if($payment_request == 'true'): ?>
                action="<?php echo site_url('home/stripe_payment/' . $user_details['id'].'/'.$amount_to_pay.'/true');?>"
              <?php else: ?>
                action="<?php echo site_url('home/stripe_payment/' . $user_details['id'].'/'.$amount_to_pay);?>"
              <?php endif; ?>
            >
              <label>
                  <div id="card-element" class="field is-empty"></div>
                  <span><span><?php echo site_phrase('credit_or_debit_card');?></span></span>
              </label>
              <button type="submit" id = "stripe_pay_button">
                  <?php echo site_phrase('pay');?> <?php echo $amount_to_pay.' '.get_settings('stripe_currency');?>
              </button>
              <div class="outcome">
                  <div class="error" role="alert"></div>
                  <div class="success">
                  Success! Your Stripe token is <span class="token"></span>
                  </div>
              </div>
              <div class="package-details">
                  <strong><?php echo site_phrase('student_name');?> | <?php echo $user_details['first_name'].' '.$user_details['last_name'];?></strong> <br>
              </div>
              <input type="hidden" name="stripeToken" value="">
          </form>
        <!-- <img src="https://stripe.com/img/about/logos/logos/blue.png" width="25%;" style="opacity: 0.05;" id="payment-gateway-logo"> -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://js.stripe.com/v3/"></script>
        <script src="<?php echo base_url('assets/payment/js/stripe.js');?>"></script>

        <script type="text/javascript">
            get_stripe_currency('<?php echo get_settings('stripe_currency'); ?>');

            $('#stripe_pay_button').click(function() {
              $('#loader_modal').show();
            });
        </script>
    </body>
</html>
