import { Checkout } from '../checkout';
import { GraphqlService } from '../graphqlService';

// __mocks__/jquery.js
jest.mock('jquery');

test('MJfc34Icn checkout test', () => {
    // Set up our document body
    let graphQlService = new GraphqlService();
    let checkout = new Checkout(graphQlService);

    let values = {
        name: 'Joe Fox',
        email: 'examplemail@gmail.com',
        telephone: '0249586904',
        mobile: '0249586904',
        shipping_address_line_1: '5 Some Road',
        shipping_address_line_2: '',
        shipping_address_state: 'Kwazulu-Natal',
        shipping_address_postcode: '7925',
        shipping_address_country: 'ZA',
        use_shipping_for_billing: true,
        billing_address_line_1: '5 Some Road',
        billing_address_line_2: '',
        billing_address_state: 'Kwazulu-Natal',
        billing_address_postcode: '3626',
        billing_address_country: 'ZA',
        payment_method: 'VISA',
        notes: 'Some sort of notes',
        agree_to_terms: true,
    };

    expect.assertions(1);
    return checkout.sendOfCheckoutInfo(values).then(data => {
        expect(data).toBeObject();
    });
});
