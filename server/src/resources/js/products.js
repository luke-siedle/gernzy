import productTemplate from './templates/productTemplate';
import $ from 'jquery';
import errorTemplate from './templates/errorTemplate';

class Products {
    constructor(graphqlService, cart) {
        this.graphqlService = graphqlService;
        this.cart = cart;
    }
    getAllProducts() {
        let query = `query {
            products(first:10) {
                data {
                    id
                    title
                    status
                    published
                    short_description
                    price_cents
                    price_currency
                }
                paginatorInfo {
                    total
                    hasMorePages
                    currentPage
                }
            }
        }`;

        let userToken = localStorage.getItem('userToken');

        return this.graphqlService
            .sendQuery(query, userToken)
            .then(re => {
                let productsArray;
                try {
                    productsArray = re.data.products.data;
                } catch (error) {
                    console.log(re);
                    $('.products-container').html(
                        errorTemplate(`There was an error loading products. <br> ${re.errors[0].extensions.reason}`),
                    );
                    return;
                }

                let mapFields = productsArray.map(product => {
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

                $('.add-to-cart').on('click', this.addProductToCart.bind(this));

                return re;
            })
            .catch(error => {
                console.log(error);
            });
    }

    getProduct(id) {
        let query = `query {
            product(id:${id}) {
                    id
                    title
                    status
                    published
                    short_description
                    price_cents
                    price_currency
            }
        }`;

        let userToken = localStorage.getItem('userToken');

        return this.graphqlService.sendQuery(query, userToken);
    }

    addProductToCart(event) {
        let productID = $(event.target).attr('data-id');
        var userToken = localStorage.getItem('userToken');

        let query = ` mutation {
            addToCart(input: {
                    items: [
                        { product_id: ${productID}, quantity: 1 }
                    ]
                }) {
                cart {
                    items {
                        product_id
                        quantity
                    }
                }
            }
        }`;

        this.graphqlService
            .sendQuery(query, userToken)
            .then(re => {
                re.data.addToCart.cart.items.forEach(element => {
                    if (element.product_id == productID) {
                        $(event.target)
                            .parent()
                            .append($(`<span class="uk-badge">${element.quantity}</span>`));
                    }
                });
            })
            .catch(error => {
                console.log(`addProductToCart: ${error}`);
            });
    }
}
export { Products };
