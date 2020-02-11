import { Products } from './products';
import { User } from './user';
import { Cart } from './cart';
import { Checkout } from './checkout';
import { GraphqlService } from './graphqlService';

// jQuery ajax spinner
var $loading = $('#loadingDiv').hide();
$(document)
    .ajaxStart(function() {
        $loading.show();
    })
    .ajaxStop(function() {
        $loading.hide();
    });

let pathname = window.location.pathname; // Returns path only (/path/example.html)
let graphQlService = new GraphqlService();

// Session object in localStorage if it doesn't already exist, and verify
let userObj = new User(graphQlService);
if (!userObj.checkIfTokenInLocalStorage()) {
    userObj.createSession();
} else {
    userObj.checkTokenExistsInDatabase().then(re => {
        try {
            if (re.errors[0].debugMessage == 'Cannot return null for non-nullable field Session.id.') {
                // Recreate session object
                userObj.createSession();
            }
        } catch (error) {
            // No error exist
        }
    });
}

// Load all products on the home page
let productObj = new Products(graphQlService);
if (pathname.includes('shop')) {
    productObj.getAllProducts();
}

// Load all products on the cart page
let cart = new Cart(productObj, graphQlService);
if (pathname.includes('cart')) {
    cart.viewProductsInCart();
}

// Load all products on the cart page
let checkout = new Checkout(graphQlService);
if (pathname.includes('checkout')) {
    checkout.checkout();
}
