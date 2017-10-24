/**
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
(function () {
    document.getElementById("buy-tickets").addEventListener('click', chargeUser);

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function chargeUser(event) {
        event.preventDefault();

        var concertId = $('#concert-id').val();
        var ticketPrice = $('#ticket-price').text().trim().substring(1);
        var quantity = $('#quantity').val();
        var totalPrice = ticketPrice * quantity;
        var concertTitle = $('#concert-title').text().trim();
        var description = quantity + ' tickets for ' + concertTitle;

        var stripe = StripeCheckout.configure({
            key: 'pk_test_qNELjqOYTWiuiNQ4kS2jMNGz',
            image: 'https://stripe.com/img/documentation/checkout/marketplace.png',
            locale: 'auto'
        });
        stripe.open({
            name: 'TicketBeast',
            description: description,
            currency: "usd",
            panelLabel: 'Pay ',
            allowRememberMe: false,
            amount: totalPrice * 100,
            token: purchaseTickets
        });

        function purchaseTickets(token) {
            $.post('/concerts/' + concertId + '/orders', {
                email: token.email,
                ticket_quantity: quantity,
                payment_token: token.id
            }).done(function (response) {
                console.log(response);
                window.location = '/orders/' + response.confirmation_number;
            }).fail(function (response) {
                console.log(response.responseText);
            });
        }
    }
})();
