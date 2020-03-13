import _defineProperty from '@babel/runtime/helpers/defineProperty';
import $ from 'jquery';
import _classCallCheck from '@babel/runtime/helpers/classCallCheck';
import _createClass from '@babel/runtime/helpers/createClass';
import _regeneratorRuntime from '@babel/runtime/regenerator';
import _asyncToGenerator from '@babel/runtime/helpers/asyncToGenerator';

var productTemplate = function productTemplate(_ref) {
    var title = _ref.title,
        short_description = _ref.short_description,
        id = _ref.id,
        buttonText = _ref.buttonText,
        price_cents = _ref.price_cents,
        price_currency = _ref.price_currency,
        quantity = _ref.quantity;
    return '\n<div>\n    <div class="uk-card uk-card-default uk-margin-left uk-margin-top">\n        <div class="uk-card-header">\n            <div class="uk-grid-small uk-flex-middle" uk-grid>\n                <div class="uk-width-auto">\n                    <//img class="uk-border-circle" width="40" height="40" src="images/avatar.jpg">\n                    <span uk-icon="icon: camera"></span>\n                </div>\n                <div class="uk-width-expand">\n                    <h3 class="uk-card-title uk-margin-remove-bottom product-title" id="product-title-'
        .concat(id, '">')
        .concat(
            title,
            '</h3>\n                    <p class="uk-text-meta uk-margin-remove-top"><time datetime="2016-04-01T19:00">April 01, 2016</time></p>\n                </div>\n            </div>\n        </div>\n        <div class="uk-card-body">\n            <p class="short-description">',
        )
        .concat(
            short_description,
            '</p>\n            <hr class="uk-divider-small">\n            <p class="product-price">',
        )
        .concat(price_cents, ' ')
        .concat(
            price_currency,
            '</p> \n            <hr class="uk-divider-small">\n            <p class="product-quantity"><span class="uk-label">quantity</span> ',
        )
        .concat(
            quantity,
            '</p>\n        </div>\n        <div class="uk-card-footer">\n            <a  href="#" class="uk-button uk-button-text add-to-cart" data-id="',
        )
        .concat(id, '">')
        .concat(buttonText, '</a>\n        </div>\n    </div>\n</div>\n');
};

var errorTemplate$1 = function errorTemplate(message) {
    return '\n<div class="uk-alert-danger" uk-alert>\n    <a class="uk-alert-close" uk-close></a>\n    <p>'.concat(
        message,
        '</p>\n</div>\n',
    );
};

var Products = /*#__PURE__*/ (function() {
    function Products(graphqlService, cart) {
        _classCallCheck(this, Products);

        this.graphqlService = graphqlService;
        this.cart = cart;
    }

    _createClass(Products, [
        {
            key: 'getAllProducts',
            value: function getAllProducts() {
                var _this = this;

                var query =
                    'query {\n            products(first:10) {\n                data {\n                    id\n                    title\n                    status\n                    published\n                    short_description\n                    price_cents\n                    price_currency\n                }\n                paginatorInfo {\n                    total\n                    hasMorePages\n                    currentPage\n                }\n            }\n        }';
                var userToken = localStorage.getItem('userToken');
                return this.graphqlService
                    .sendQuery(query, userToken)
                    .then(function(re) {
                        var productsArray;

                        try {
                            productsArray = re.data.products.data;
                        } catch (error) {
                            console.log(re);
                            $('.products-container').html(
                                errorTemplate$1(
                                    'There was an error loading products. <br> '.concat(re.errors[0].extensions.reason),
                                ),
                            );
                            return;
                        }

                        var mapFields = productsArray.map(function(product) {
                            var currency = localStorage.getItem('currency');

                            if (!currency) {
                                currency = product.price_currency;
                            }

                            return {
                                title: product.title,
                                price_cents: product.price_cents / 100,
                                price_currency: currency,
                                short_description: product.short_description,
                                id: product.id,
                                quantity: 1,
                                buttonText: 'Add to cart',
                            };
                        });
                        $('.products-container').html(mapFields.map(productTemplate).join(''));
                        $('.add-to-cart').on('click', _this.addProductToCart.bind(_this));
                        return re;
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            },
        },
        {
            key: 'getProduct',
            value: function getProduct(id) {
                var query = 'query {\n            product(id:'.concat(
                    id,
                    ') {\n                    id\n                    title\n                    status\n                    published\n                    short_description\n                    price_cents\n                    price_currency\n            }\n        }',
                );
                var userToken = localStorage.getItem('userToken');
                return this.graphqlService.sendQuery(query, userToken);
            },
        },
        {
            key: 'addProductToCart',
            value: function addProductToCart(event) {
                var productID = $(event.target).attr('data-id');
                var userToken = localStorage.getItem('userToken');
                var query = ' mutation {\n            addToCart(input: {\n                    items: [\n                        { product_id: '.concat(
                    productID,
                    ', quantity: 1 }\n                    ]\n                }) {\n                cart {\n                    items {\n                        product_id\n                        quantity\n                    }\n                }\n            }\n        }',
                );
                this.graphqlService
                    .sendQuery(query, userToken)
                    .then(function(re) {
                        re.data.addToCart.cart.items.forEach(function(element) {
                            if (element.product_id == productID) {
                                $(event.target)
                                    .parent()
                                    .append($('<span class="uk-badge">'.concat(element.quantity, '</span>')));
                            }
                        });
                    })
                    .catch(function(error) {
                        console.log('addProductToCart: '.concat(error));
                    });
            },
        },
    ]);

    return Products;
})();

function ownKeys(object, enumerableOnly) {
    var keys = Object.keys(object);
    if (Object.getOwnPropertySymbols) {
        var symbols = Object.getOwnPropertySymbols(object);
        if (enumerableOnly)
            symbols = symbols.filter(function(sym) {
                return Object.getOwnPropertyDescriptor(object, sym).enumerable;
            });
        keys.push.apply(keys, symbols);
    }
    return keys;
}

function _objectSpread(target) {
    for (var i = 1; i < arguments.length; i++) {
        var source = arguments[i] != null ? arguments[i] : {};
        if (i % 2) {
            ownKeys(Object(source), true).forEach(function(key) {
                _defineProperty(target, key, source[key]);
            });
        } else if (Object.getOwnPropertyDescriptors) {
            Object.defineProperties(target, Object.getOwnPropertyDescriptors(source));
        } else {
            ownKeys(Object(source)).forEach(function(key) {
                Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key));
            });
        }
    }
    return target;
}

var Cart = /*#__PURE__*/ (function() {
    function Cart(productObj, graphqlService) {
        _classCallCheck(this, Cart);

        this.productObj = productObj;
        this.graphqlService = graphqlService;
    }

    _createClass(Cart, [
        {
            key: 'viewProductsInCart',
            value: function viewProductsInCart() {
                var _this = this;

                var userToken = localStorage.getItem('userToken');
                var query =
                    '{\n            me {\n                cart {\n                    items {\n                        product_id\n                        quantity\n                    }\n                }\n            }\n        }';
                return this.graphqlService
                    .sendQuery(query, userToken)
                    .then(function(re) {
                        var items = re.data.me.cart.items;

                        if (items && items.length > 0) {
                            _this.lookupProductsInCart(re.data.me.cart.items);
                        } else {
                            $('.cart-products').html(errorTemplate$1('No products in cart.')); // Disable checkout as there are no products in the cart

                            $('#cart-checkout').addClass('uk-disabled');
                        }

                        return re;
                    })
                    .catch(function(error) {
                        console.log('viewProductsInCart: '.concat(error));
                    });
            },
        },
        {
            key: 'lookupProductsInCart',
            value: (function() {
                var _lookupProductsInCart = _asyncToGenerator(
                    /*#__PURE__*/ _regeneratorRuntime.mark(function _callee2(products) {
                        var _this2 = this;

                        return _regeneratorRuntime.wrap(function _callee2$(_context2) {
                            while (1) {
                                switch ((_context2.prev = _context2.next)) {
                                    case 0:
                                        _context2.next = 2;
                                        return Promise.all(
                                            products.map(
                                                /*#__PURE__*/ (function() {
                                                    var _ref = _asyncToGenerator(
                                                        /*#__PURE__*/ _regeneratorRuntime.mark(function _callee(
                                                            product,
                                                        ) {
                                                            var queriedProduct, quantityObje, mergedObj;
                                                            return _regeneratorRuntime.wrap(function _callee$(
                                                                _context,
                                                            ) {
                                                                while (1) {
                                                                    switch ((_context.prev = _context.next)) {
                                                                        case 0:
                                                                            _context.next = 2;
                                                                            return _this2.productObj.getProduct(
                                                                                product.product_id,
                                                                            );

                                                                        case 2:
                                                                            queriedProduct = _context.sent;
                                                                            quantityObje = {
                                                                                quantity: product.quantity,
                                                                            };
                                                                            mergedObj = _objectSpread(
                                                                                {},
                                                                                queriedProduct.data.product,
                                                                                {},
                                                                                quantityObje,
                                                                            );
                                                                            return _context.abrupt('return', mergedObj);

                                                                        case 6:
                                                                        case 'end':
                                                                            return _context.stop();
                                                                    }
                                                                }
                                                            },
                                                            _callee);
                                                        }),
                                                    );

                                                    return function(_x2) {
                                                        return _ref.apply(this, arguments);
                                                    };
                                                })(),
                                            ),
                                        )
                                            .then(function(re) {
                                                _this2.populateUIWithProducts(re);

                                                return re;
                                            })
                                            .catch(function(error) {
                                                console.log(error);
                                            });

                                    case 2:
                                        return _context2.abrupt('return', _context2.sent);

                                    case 3:
                                    case 'end':
                                        return _context2.stop();
                                }
                            }
                        }, _callee2);
                    }),
                );

                function lookupProductsInCart(_x) {
                    return _lookupProductsInCart.apply(this, arguments);
                }

                return lookupProductsInCart;
            })(),
        },
        {
            key: 'populateUIWithProducts',
            value: function populateUIWithProducts(products) {
                var mapFields = products.map(function(product) {
                    var currency = localStorage.getItem('currency');

                    if (!currency) {
                        currency = product.price_currency;
                    }

                    return {
                        title: product.title,
                        short_description: product.short_description,
                        id: product.id,
                        price_cents: product.price_cents / 100,
                        price_currency: currency,
                        quantity: product.quantity,
                        buttonText: 'Remove',
                    };
                });
                $('.cart-products').html(mapFields.map(productTemplate).join(''));
            },
        },
    ]);

    return Cart;
})();

var successTemplate = function successTemplate() {
    return '\n<div class="uk-alert-success" uk-alert>\n    <a class="uk-alert-close" uk-close></a>\n    <p>Your checkout has been successful.</p>\n</div>\n';
};

var Checkout = /*#__PURE__*/ (function() {
    function Checkout(graphqlService) {
        var cart = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

        _classCallCheck(this, Checkout);

        this.graphqlService = graphqlService;
        this.cart = cart;
    }

    _createClass(Checkout, [
        {
            key: 'checkout',
            value: function checkout() {
                // This is to keep the object context of and access it's methods
                var self = this;
                $('#checkout-form').submit(function(event) {
                    event.preventDefault(); // get all the inputs into an array.

                    var inputs = $('#checkout-form :input'); // not sure if you wanted this, but I thought I'd add it.
                    // get an associative array of just the values.

                    var values = {};
                    inputs.each(function() {
                        values[this.name] = $(this).val();
                    }); // Checkbox values

                    values['use_shipping_for_billing'] = $('#use_shipping_for_billing').prop('checked');
                    values['agree_to_terms'] = $('#agree_to_terms').prop('checked');
                    self.sendOfCheckoutInfo(values);
                });
            },
        },
        {
            key: 'sendOfCheckoutInfo',
            value: function sendOfCheckoutInfo(values) {
                var userToken = localStorage.getItem('userToken');
                var query = ' mutation {\n            checkout(input: {\n                name: "'
                    .concat(values['name'], '",\n                email: "')
                    .concat(values['email'], '",\n                telephone: "')
                    .concat(values['telephone'], '",\n                mobile: "')
                    .concat(values['mobile'], '",\n                billing_address: {\n                    line_1: "')
                    .concat(values['billing_address_line_1'], '",\n                    line_2: "')
                    .concat(values['billing_address_line_2'], '",\n                    state: "')
                    .concat(values['billing_address_state'], '",\n                    postcode: "')
                    .concat(values['billing_address_postcode'], '",\n                    country: "')
                    .concat(
                        values['billing_address_country'],
                        '"\n                },\n                shipping_address: {\n                    line_1: "',
                    )
                    .concat(values['shipping_address_line_1'], '",\n                    line_2: "')
                    .concat(values['shipping_address_line_2'], '",\n                    state: "')
                    .concat(values['shipping_address_state'], '",\n                    postcode: "')
                    .concat(values['shipping_address_postcode'], '",\n                    country: "')
                    .concat(
                        values['shipping_address_country'],
                        '"\n                },\n                use_shipping_for_billing: ',
                    )
                    .concat(values['use_shipping_for_billing'], ',\n                payment_method: "')
                    .concat(values['payment_method'], '",\n                agree_to_terms: ')
                    .concat(values['agree_to_terms'], ',\n                notes: "')
                    .concat(
                        values['notes'],
                        '"\n            }){\n                order {\n                    id\n                }\n            }\n        }',
                    );
                return this.graphqlService
                    .sendQuery(query, userToken)
                    .then(function(re) {
                        $('.checkout-container').html(successTemplate);
                        return re;
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            },
        },
        {
            key: 'getBasketTotal',
            value: function getBasketTotal() {
                var userToken = localStorage.getItem('userToken');
                var query =
                    '{\n            me {\n                cart {\n                    cart_total\n                }\n            }\n        }';
                return this.graphqlService
                    .sendQuery(query, userToken)
                    .then(function(re) {
                        var currency = localStorage.getItem('currency'); // get the default currency from the shopConfig

                        if (!currency) {
                            currency = localStorage.getItem('default_currency');
                        }

                        $('#checkout-cart-total').html(
                            ''.concat(re.data.me.cart.cart_total / 100, ' ').concat(currency),
                        );
                        return re;
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            },
        },
        {
            key: 'displayLineItems',
            value: function displayLineItems() {
                var _this = this;

                return this.cart.viewProductsInCart().then(function(re) {
                    try {
                        // See if there is an error
                        var error = re.errors[0].debugMessage;
                        console.log(error);
                    } catch (_unused) {
                        var items = re.data.me.cart.items;

                        if (items && items.length > 0) {
                            _this.cart.lookupProductsInCart(items).then(function(re) {
                                var currency = localStorage.getItem('currency'); // get the default currency from the shopConfig

                                if (!currency) {
                                    currency = localStorage.getItem('default_currency');
                                }

                                re.forEach(function(element) {
                                    $('#table-body-line-item').append(
                                        $(
                                            '<tr>\n                                    <td>'
                                                .concat(
                                                    element.title,
                                                    '</td>\n                                    <td>',
                                                )
                                                .concat(
                                                    element.quantity,
                                                    '</td>\n                                    <td>',
                                                )
                                                .concat(element.price_cents / 100, ' ')
                                                .concat(currency, '</td>\n                                </tr>'),
                                        ),
                                    );
                                });
                            });
                        } else {
                            $('.checkout-container').html(errorTemplate('No products in cart.'));
                        }
                    }

                    return re;
                });
            },
        },
    ]);

    return Checkout;
})();

var GraphqlService = /*#__PURE__*/ (function() {
    function GraphqlService(config) {
        _classCallCheck(this, GraphqlService);

        this.userToken = localStorage.getItem('userToken');
        this.config = config;
    }

    _createClass(GraphqlService, [
        {
            key: 'sendQuery',
            value: (function() {
                var _sendQuery = _asyncToGenerator(
                    /*#__PURE__*/ _regeneratorRuntime.mark(function _callee(graphqlQuery) {
                        var userToken,
                            data,
                            _args = arguments;
                        return _regeneratorRuntime.wrap(
                            function _callee$(_context) {
                                while (1) {
                                    switch ((_context.prev = _context.next)) {
                                        case 0:
                                            userToken = _args.length > 1 && _args[1] !== undefined ? _args[1] : '';
                                            _context.prev = 1;
                                            _context.next = 4;
                                            return $.ajax({
                                                url: this.config.apiUrl,
                                                contentType: 'application/json',
                                                type: 'POST',
                                                headers: {
                                                    Authorization: 'Bearer '.concat(userToken),
                                                },
                                                data: JSON.stringify({
                                                    query: graphqlQuery,
                                                }),
                                            });

                                        case 4:
                                            data = _context.sent;
                                            return _context.abrupt('return', data);

                                        case 8:
                                            _context.prev = 8;
                                            _context.t0 = _context['catch'](1);
                                            return _context.abrupt('return', _context.t0);

                                        case 11:
                                        case 'end':
                                            return _context.stop();
                                    }
                                }
                            },
                            _callee,
                            this,
                            [[1, 8]],
                        );
                    }),
                );

                function sendQuery(_x) {
                    return _sendQuery.apply(this, arguments);
                }

                return sendQuery;
            })(),
        },
    ]);

    return GraphqlService;
})();

var User = /*#__PURE__*/ (function() {
    function User(graphqlService) {
        _classCallCheck(this, User);

        this.graphqlService = graphqlService;
    }

    _createClass(User, [
        {
            key: 'createSession',
            value: function createSession() {
                var _this = this;

                var query = 'mutation {\n            createSession {\n                token\n            }\n        }';
                return this.graphqlService
                    .sendQuery(query)
                    .then(function(re) {
                        var token = re.data.createSession.token;

                        _this.addSessionTokenToLocalStorage(token);

                        return re;
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            },
        },
        {
            key: 'addSessionTokenToLocalStorage',
            value: function addSessionTokenToLocalStorage(token) {
                localStorage.setItem('userToken', token);
            },
        },
        {
            key: 'checkIfTokenInLocalStorage',
            value: function checkIfTokenInLocalStorage() {
                var userTokenLocalStorage = localStorage.getItem('userToken'); // Check if token not already in local storage, and if not add to localStorage

                if (userTokenLocalStorage) {
                    return true;
                } else {
                    return false;
                }
            },
        },
        {
            key: 'checkTokenExistsInDatabase',
            value: function checkTokenExistsInDatabase() {
                var userTokenLocalStorage = localStorage.getItem('userToken');
                var query =
                    ' {\n            me {\n                session {\n                    id\n                    token\n                }\n            }\n        }';
                return this.graphqlService.sendQuery(query, userTokenLocalStorage);
            },
        },
    ]);

    return User;
})();

var SessionService = /*#__PURE__*/ (function() {
    function SessionService(graphqlService) {
        _classCallCheck(this, SessionService);

        this.graphqlService = graphqlService;
    }

    _createClass(SessionService, [
        {
            key: 'setUpSessionData',
            value: function setUpSessionData() {
                var userToken = localStorage.getItem('userToken');
                var query =
                    '{\n            me {\n                session {\n                    data\n                }\n            }\n        }';
                return this.graphqlService.sendQuery(query, userToken).then(function(re) {
                    localStorage.setItem('sessionData', re.data.me.session.data);

                    if (re.data.me.session.data[1]) {
                        localStorage.setItem('currency', re.data.me.session.data[1]);
                    }
                });
            },
        },
        {
            key: 'setUpShopConfig',
            value: function setUpShopConfig() {
                var _this = this;

                var userToken = localStorage.getItem('userToken');
                var query =
                    '\n            query {\n                shopConfig {\n                    enabled_currencies\n                    default_currency\n                }\n            }\n        ';
                return this.graphqlService.sendQuery(query, userToken).then(function(re) {
                    re.data.shopConfig.enabled_currencies.forEach(function(element) {
                        $('#available-currencies').append(
                            "<li><a href='#' class='available-currency' data-currency=\""
                                .concat(element, '">')
                                .concat(element, '</a></li>'),
                        );
                    });
                    $('.available-currency').on('click', _this.changeUserCurrency.bind(_this));
                    localStorage.setItem('default_currency', re.data.shopConfig.default_currency);
                });
            },
        },
        {
            key: 'setUpGeoLocation',
            value: function setUpGeoLocation() {
                var userToken = localStorage.getItem('userToken');
                var query =
                    '\n            mutation {\n                setSessionGeoLocation {\n                    geolocation_record\n                }\n            }\n        ';
                return this.graphqlService.sendQuery(query, userToken).then(function(re) {
                    var error = re.errors[0].debugMessage;

                    if (!error) {
                        localStorage.setItem('setSessionGeoLocation', re.data.setSessionGeoLocation.geolocation_record);
                    } else {
                        // handle error
                        console.log(error);
                    }
                });
            },
        },
        {
            key: 'changeUserCurrency',
            value: function changeUserCurrency(event) {
                var userToken = localStorage.getItem('userToken');
                var currrency = $(event.target).attr('data-currency');
                var query = '\n            mutation {\n                setSessionCurrency(input: {\n                    currency: "'.concat(
                    currrency,
                    '"\n                }){\n                    currency\n                }\n            }\n        ',
                );
                return this.graphqlService.sendQuery(query, userToken).then(function(re) {
                    try {
                        // See if there is an error
                        var error = re.errors[0].debugMessage;
                        console.log(error);
                    } catch (_unused) {
                        localStorage.setItem('currency', re.data.setSessionCurrency.currency); // A lot of state will need to change with the currency changing, and for now an easy fix is just to force
                        // a reload

                        location.reload(true);
                    }
                });
            },
        },
        {
            key: 'setupUser',
            value: function setupUser() {
                // Session object in localStorage if it doesn't already exist, and verify
                var userObj = new User(this.graphqlService);

                if (!userObj.checkIfTokenInLocalStorage()) {
                    userObj.createSession();
                } else {
                    userObj.checkTokenExistsInDatabase().then(function(re) {
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
            },
        },
    ]);

    return SessionService;
})();

function ownKeys$1(object, enumerableOnly) {
    var keys = Object.keys(object);
    if (Object.getOwnPropertySymbols) {
        var symbols = Object.getOwnPropertySymbols(object);
        if (enumerableOnly)
            symbols = symbols.filter(function(sym) {
                return Object.getOwnPropertyDescriptor(object, sym).enumerable;
            });
        keys.push.apply(keys, symbols);
    }
    return keys;
}

function _objectSpread$1(target) {
    for (var i = 1; i < arguments.length; i++) {
        var source = arguments[i] != null ? arguments[i] : {};
        if (i % 2) {
            ownKeys$1(Object(source), true).forEach(function(key) {
                _defineProperty(target, key, source[key]);
            });
        } else if (Object.getOwnPropertyDescriptors) {
            Object.defineProperties(target, Object.getOwnPropertyDescriptors(source));
        } else {
            ownKeys$1(Object(source)).forEach(function(key) {
                Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key));
            });
        }
    }
    return target;
}
var gernzy = {
    init: function init() {
        var userConfig = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

        var config = _objectSpread$1(
            {},
            {
                apiUrl: 'http://laravel-gernzy.test/graphql',
            },
            {},
            userConfig,
        ); // jQuery ajax spinner

        var $loading = $('#loadingDiv').hide();
        $(document)
            .ajaxStart(function() {
                $loading.show();
            })
            .ajaxStop(function() {
                $loading.hide();
            });
        var pathname = window.location.pathname;
        var graphQlService = new GraphqlService(config);
        var sessionService = new SessionService(graphQlService);
        var productObj = new Products(graphQlService);
        var cart = new Cart(productObj, graphQlService);
        var checkout = new Checkout(graphQlService, cart); // Session setup

        sessionService.setupUser();
        sessionService.setUpShopConfig();
        sessionService.setUpSessionData(); // sessionService.setUpGeoLocation();

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
    },
};

export default gernzy;
