/**
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
(function () {
    document.getElementById("buy-tickets").addEventListener('click', chargeUser);
    function chargeUser () {
        var stripe = Stripe('pk_test_qNELjqOYTWiuiNQ4kS2jMNGz');
        stripe.elements();
        /*stripe.createToken('bank_account', {
            country: 'US',
            currency: 'usd',
            routing_number: '110000000',
            account_number: '000123456789',
            account_holder_name: 'Jenny Rosen',
            account_holder_type: 'individual',
        }).then(function(result) {
            // handle result.error or result.token
        });*/
        console.log('ok');
    }
})();
