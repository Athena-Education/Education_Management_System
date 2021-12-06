<?php
	// Stripe API configuration
	define('STRIPE_API_KEY', $stripe_keys[0]['secret_live_key']);
	define('STRIPE_PUBLISHABLE_KEY', $stripe_keys[0]['public_live_key']);
?>

<div id="stripePaymentResponse-<?php echo $pending_payout['id']; ?>" class="text-danger"></div>

<!-- Buy button -->
<div id="buynow-<?php echo $pending_payout['id']; ?>" style="height: 45px;">
    <button class="stripe-button btn btn-outline-info btn-sm btn-rounded" id="stripePayButton-<?php echo $pending_payout['id']; ?>"><?php echo get_phrase("pay_with_stripe"); ?></button>
</div>

<!--Stripe API-->
<script src="https://js.stripe.com/v3/"></script>
<script>
var buyBtn = document.getElementById("stripePayButton-<?php echo $pending_payout['id']; ?>");
var responseContainer = document.getElementById("stripePaymentResponse-<?php echo $pending_payout['id']; ?>");

// Create a Checkout Session with the selected product
var createCheckoutSession = function (stripe) {
    return fetch("<?= site_url('admin/stripe_checkout_for_instructor_revenue/'.$pending_payout['id']); ?>", {
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
