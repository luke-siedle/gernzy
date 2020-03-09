import productTemplate from './templates/productTemplate';
import errorTemplate from './templates/errorTemplate';
import $ from 'jquery';

class Cart {
    constructor(productObj, graphqlService) {
        this.productObj = productObj;
        this.graphqlService = graphqlService;
    }
    viewProductsInCart() {
        var userToken = localStorage.getItem('userToken');

        let query = `{
            me {
                cart {
                    items {
                        product_id
                        quantity
                    }
                }
            }
        }`;

        return this.graphqlService
            .sendQuery(query, userToken)
            .then(re => {
                let items = re.data.me.cart.items;

                if (items && items.length > 0) {
                    this.lookupProductsInCart(re.data.me.cart.items);
                } else {
                    $('.cart-products').html(errorTemplate('No products in cart.'));

                    // Disable checkout as there are no products in the cart
                    $('#cart-checkout').addClass('uk-disabled');
                }

                return re;
            })
            .catch(error => {
                console.log(`viewProductsInCart: ${error}`);
            });
    }

    async lookupProductsInCart(products) {
        return await Promise.all(
            products.map(async product => {
                // Merging quantity into the product object to use later
                const queriedProduct = await this.productObj.getProduct(product.product_id);
                let quantityObje = { quantity: product.quantity };
                let mergedObj = { ...queriedProduct.data.product, ...quantityObje };

                return mergedObj;
            }),
        )
            .then(re => {
                this.populateUIWithProducts(re);
                return re;
            })
            .catch(error => {
                console.log(error);
            });
    }

    populateUIWithProducts(products) {
        let mapFields = products.map(product => {
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
    }
}
export { Cart };
