<?php

use Gernzy\Server\Testing\TestCase;

/**
 * @group Orders
 */
class TestAdminCreateOrdersTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $response = $this->graphQL('
            mutation {
                logIn(input:{
                    email:"admin@example.com",
                    password: "password"
                    }) {
                    token
                    user {
                        name
                        email
                        id
                    }
                }
            }
        ');
        $result = $response->decodeResponseJson();

        // Set the global session token to use for the test
        $this->sessionToken = $result['data']['logIn']['token'];
    }

    public function createOrder(){
        return $this->graphQLWithSession('
            mutation {
                createOrder(input:{
                    name: "Luke",
                    email: "onbehalfof@example.com",
                    telephone: "",
                    mobile: "",
                    billing_address: {
                        line_1: "1 London Way",
                        line_2: "",
                        state: "London",
                        postcode: "SW1A 1AA",
                        country: "UK"
                    },
                    shipping_address: {
                        line_1: "1 London Way",
                        line_2: "",
                        state: "London",
                        postcode: "SW1A 1AA",
                        country: "UK"
                    },
                    use_shipping_for_billing: true,
                    payment_method: "",
                    agree_to_terms: true,
                    notes: ""
                    }) {
                    id
                    email
                    is_admin_order
                }
            }
        ');
    }

    public function testAdminUserCanCreateOrder(){
        $response = $this->createOrder();
        $json = $response->decodeResponseJson();

        $response->assertDontSee('errors');
        $response->assertJsonStructure([
            'data' => [
                'createOrder' => [
                    'id', 'email', 'is_admin_order'
                ]
            ]
        ]);

        $this->assertEquals($json['data']['createOrder']['is_admin_order'], 1);
        $this->assertEquals($json['data']['createOrder']['email'], "onbehalfof@example.com");
    }

    public function testAdminUserCanUpdateOrder(){
        $response = $this->createOrder();
        $json = $response->decodeResponseJson();
        $response = $this->graphQLWithSession('
            mutation {
                updateOrder(id: "' . $json['data']['createOrder']['id'] . '", input:{
                    email: "luke@example.com"
                }){
                    id
                    email
                }
            }
        ');

        $response->assertDontSee('errors');
        $json = $response->decodeResponseJson();
        $this->assertEquals($json['data']['updateOrder']['email'], "luke@example.com");
    }

    public function testAdminUserCanSetItemsOnOrder(): void
    {
        $json = $this->createOrder()->decodeResponseJson();
        $response = $this->graphQLWithSession('
            mutation {
                setOrderItems(id: ' . $json['data']['createOrder']['id'] . ', input: [
                    {
                        product_id: 1,
                        quantity: 1
                    },{
                        product_id: 2,
                        quantity: 1
                    }
                ]){
                    cart {
                        items {
                            product_id
                            quantity
                        }
                    }
                }
            }
        ');

        $response->assertDontSee('errors');

        $response->assertJsonStructure([
            'data' => [
                'setOrderItems' => [
                    'cart' => [
                        'items' => [['product_id', 'quantity']]
                    ]
                ]
            ]
        ]);
    }

    public function testAdminUserCanDeleteOrder(){
        $response = $this->createOrder();
        $json = $response->decodeResponseJson();
        $response = $this->graphQLWithSession('
            mutation {
                deleteOrder(id: "' . $json['data']['createOrder']['id'] . '"){
                    success
                }
            }
        ');

        $response->assertDontSee('errors');
        $json = $response->decodeResponseJson();
        $this->assertEquals($json['data']['deleteOrder']['success'], true);
    }
}
