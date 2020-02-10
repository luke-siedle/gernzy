@extends('Gernzy\Server::app')

@section('content')
<div class="cart-container">

    <div class="uk-flex  uk-flex-right">
        <div class="uk-card uk-card-default uk-card-body">
            <a id="cart-checkout" href="/checkout" class="uk-button uk-button-primary tm-button-default">Checkout </a>
        </div>
    </div>
    <h1 class="uk-heading-small uk-padding-small">Your cart items</h1>
    <div class="cart-products uk-flex uk-flex-wrap uk-flex-wrap-around"></div>
</div>
@endsection