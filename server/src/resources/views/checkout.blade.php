@extends('lab19\cart::app')

@section('content')
<div class="checkout-container">
    <h1 class="uk-heading-small uk-padding-small">Complete checkout</h1>

    <div class="uk-padding">
        <form id="checkout-form" class="uk-card uk-card-default uk-card-body uk-card-large">
            <fieldset class=" uk-fieldset">

                <legend class="uk-legend">Your details</legend>

                <div class="uk-margin">
                    <input name="name" class="uk-input" type="text" placeholder="Name" required autocomplete="name">
                </div>
                <div class="uk-margin">
                    <input name="email" class="uk-input" type="email" placeholder="Email" required autocomplete="email">
                </div>
                <div class="uk-margin">
                    <input name="telephone" class="uk-input" type="tel" placeholder="Mobile" required autocomplete="tel">
                </div>
                <div class="uk-margin">
                    <input name="mobile" class="uk-input" type="tel" placeholder="Telephone" required autocomplete="tel">
                </div>

                <div class="uk-card uk-card-default uk-card-body">
                    <label class="uk-form-label" for="form-horizontal-text">Shipping Address</label>
                    <div class="uk-margin">
                        <input class="uk-input" name="shipping_address_line_1" id="frmAddressSL1" placeholder="123 Any Street" required autocomplete="shipping address-line1">
                    </div>
                    <div class="uk-margin">
                        <input class="uk-input" name="shipping_address_line_2" id="frmAddressSL2" placeholder="123 Any Street" autocomplete="shipping address-line2">
                    </div>
                    <div class="uk-margin">
                        <input class="uk-input" name="shipping_address_state" id="frmAddressState" placeholder="State" required autocomplete="shipping address-level1">
                    </div>
                    <div class="uk-margin">
                        <input class="uk-input" name="shipping_address_postcode" id="frmAddressPostal" placeholder="Postal code" autocomplete="shipping postal-code">
                    </div>
                    <div class="uk-margin">
                        <input class="uk-input" name="shipping_address_country" id="frmAddressCountry" placeholder="Country" required autocomplete="shipping country">
                    </div>

                    <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                        <label><input id="use_shipping_for_billing" name="use_shipping_for_billing" class="uk-checkbox" type="checkbox">Use shipping for billing address</label>
                    </div>
                </div>

                <div id="billing-info" class="uk-card uk-card-default uk-card-body">
                    <label class="uk-form-label" for="form-horizontal-text">Billing Address</label>
                    <div class="uk-margin">
                        <input class="uk-input" name="billing_address_line_1" id="frmAddressSBillL1" placeholder="123 Any Street" required autocomplete="address-line1">
                    </div>
                    <div class="uk-margin">
                        <input class="uk-input" name="billing_address_line_2" id="frmAddressSBillL2" placeholder="123 Any Street" autocomplete="address-line2">
                    </div>
                    <div class="uk-margin">
                        <input class="uk-input" name="billing_address_state" id="frmAddressStateBill" placeholder="State" required autocomplete="address-level1">
                    </div>
                    <div class="uk-margin">
                        <input class="uk-input" name="billing_address_postcode" id="frmAddressPostalBill" placeholder="Postal code" autocomplete="postal-code">
                    </div>
                    <div class="uk-margin">
                        <input class="uk-input" name="billing_address_country" id="frmAddressCountryBill" placeholder="Country" required autocomplete="country">
                    </div>
                </div>

                <div class="uk-margin">
                    <label class="uk-form-label" for="form-horizontal-text">Payment method</label>
                    <select name="payment_method" class="uk-select">
                        <option>VISA</option>
                        <option>Master Card</option>
                        <option>PayPal</option>
                        <option>Credit Card</option>
                    </select>
                </div>

                <div class="uk-margin">
                    <textarea name="notes" class="uk-textarea" rows="5" placeholder="Notes"></textarea>
                </div>

                <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                    <label><input id="agree_to_terms" name="agree_to_terms" class="uk-checkbox" required type="checkbox">Agree to Terms</label>
                </div>
            </fieldset>

            <button type="submit" class="uk-button uk-button-default">Submit</button>
        </form>
    </div>
    <div class="cart-products"></div>
</div>
@endsection