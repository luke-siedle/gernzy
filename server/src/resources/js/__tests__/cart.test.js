import { Products } from '../products';
import { GraphqlService } from '../graphqlService';
import { Cart } from '../cart';

// __mocks__/jquery.js
jest.mock('jquery');

test('MJfc34Icn query for all products in cart', () => {
    // Set up our document body
    document.body.innerHTML = '<div class="cart-products"></div>';

    let graphQlService = new GraphqlService();
    let prods = new Products(graphQlService);
    let cart = new Cart(prods, graphQlService);

    expect.assertions(2);
    return cart.viewProductsInCart().then(data => {
        expect(data).toBeObject();
        expect(data.data.me.cart.items[0]).toContainAllKeys(['product_id', 'quantity']);
    });
});

test('Jfi934fho populateUIWithProducts() function cart test', () => {
    // Set up our document body
    document.body.innerHTML = '<div class="cart-products"></div>';

    let graphQlService = new GraphqlService();
    let prods = new Products(graphQlService);
    let cart = new Cart(prods, graphQlService);

    cart.populateUIWithProducts([
        {
            id: 1,
            title: 'shoes',
            status: 'IN_STOCK',
            published: 1,
            short_description: 'Et autem libero ducimus dolorem explicabo ratione.',
        },
        {
            id: 2,
            title: 'Blah blah blah',
            status: 'IN_STOCK',
            published: 1,
            short_description: 'Et autem libero ducimus dolorem explicabo ratione.',
        },
    ]);

    var productTitles = document.getElementsByClassName('product-title');
    expect(productTitles[0].textContent).toEqual('shoes');
});
