<?php
	// Stripe API configuration
    $stripe_keys = get_settings('stripe_keys');
    $values = json_decode($stripe_keys);
    if ($values[0]->testmode == 'on') {
        $public_key = $values[0]->public_key;
        $private_key = $values[0]->secret_key;
    } else {
        $public_key = $values[0]->public_live_key;
        $private_key = $values[0]->secret_live_key;
    }

	define('STRIPE_API_KEY', $private_key);
	define('STRIPE_PUBLISHABLE_KEY', $public_key);
?>

<div id="stripePaymentResponse" class="text-danger"></div>

<!-- Buy button -->
<div id="buynow" style="height: 45px;">
    <button class="stripe-button payment-button float-right" id="stripePayButton"><?php echo get_phrase("pay_with_stripe"); ?></button>
</div>

<script>
var buyBtn = document.getElementById('stripePayButton');
var responseContainer = document.getElementById('stripePaymentResponse');

// Create a Checkout Session with the selected product
var createCheckoutSession = function (stripe) {
    return fetch("<?= site_url('home/stripe_checkout/true'); ?>", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            checkoutSession: 1,
        }),
    }).then(function (result) {
        return result.json();
    });
};

// Handle any errors returned from Checkout
var handleResult = function (result) {
    if (result.error) {
        responseContainer.innerHTML = '<p>'+result.error.message+'</p>';
    }
    buyBtn.disabled = false;
    buyBtn.textContent = 'Buy Now';
};

// Specify Stripe publishable key to initialize Stripe.js
var stripe = Stripe('<?php echo STRIPE_PUBLISHABLE_KEY; ?>');

buyBtn.addEventListener("click", function (evt) {
    buyBtn.disabled = true;
    buyBtn.textContent = '<?php echo get_phrase("please_wait"); ?>...';

    createCheckoutSession().then(function (data) {
        if(data.sessionId){
            stripe.redirectToCheckout({
                sessionId: data.sessionId,
            }).then(handleResult);
        }else{
            handleResult(data);
        }
    });
});
</script>
