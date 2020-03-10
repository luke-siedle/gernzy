import { Products } from '../products';
import { GraphqlService } from '../graphqlService';

// __mocks__/jquery.js
jest.mock('jquery');

// Test single product.
test('query for single product', () => {
    let graphQlService = new GraphqlService();
    let prods = new Products(graphQlService);
    expect.assertions(2);
    return prods.getProduct(1).then(data => {
        expect(data).toBeObject();
        expect(data.data.product).toContainAllKeys([
            'id',
            'title',
            'status',
            'published',
            'short_description',
            'price_cents',
            'price_currency',
        ]);
    });
});

// Test all products.
test('query for all products', () => {
    let graphQlService = new GraphqlService();
    let prods = new Products(graphQlService);
    expect.assertions(2);
    return prods.getAllProducts().then(data => {
        expect(data).toBeObject();
        expect(data.data.products.data[0]).toContainAllKeys([
            'id',
            'published',
            'short_description',
            'status',
            'title',
        ]);
    });
});

test('query for all products with DOM', () => {
    // Set up our document body
    document.body.innerHTML = '<div class="products-container"></div>';

    let graphQlService = new GraphqlService();
    let prods = new Products(graphQlService);
    expect.assertions(3);
    return prods.getAllProducts().then(data => {
        expect(data).toBeObject();

        expect(data.data.products.data[0]).toContainAllKeys([
            'id',
            'published',
            'short_description',
            'status',
            'title',
        ]);

        var productTitles = document.body.getElementsByClassName('product-title');

        // Check some title has content
        expect(productTitles[0].textContent).toEqual('Hello World');
    });
});
