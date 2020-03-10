import { Products } from './products';
import { Cart } from './cart';
import { Checkout } from './checkout';
import { GraphqlService } from './graphqlService';
import { SessionService } from './session';

// jQuery ajax spinner
var $loading = $('#loadingDiv').hide();
$(document)
    .ajaxStart(function() {
        $loading.show();
    })
    .ajaxStop(function() {
        $loading.hide();
    });

let pathname = window.location.pathname;
let graphQlService = new GraphqlService();
let sessionService = new SessionService(graphQlService);
let productObj = new Products(graphQlService);
let cart = new Cart(productObj, graphQlService);
let checkout = new Checkout(graphQlService, cart);

// Session setup
sessionService.setupUser();
sessionService.setUpShopConfig();
sessionService.setUpSessionData();
// sessionService.setUpGeoLocation();

if (pathname.includes('shop')) {
    productObj.getAllProducts();
}

if (pathname.includes('cart')) {
    cart.viewProductsInCart();
}

if (pathname.includes('checkout')) {
    checkout.getBasketTotal();
    checkout.displayLineItems();
    checkout.checkout();
}
