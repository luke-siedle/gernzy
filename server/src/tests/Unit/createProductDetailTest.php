<?php

use Faker\Factory as Faker;
use Gernzy\Server\Testing\TestCase;
use Illuminate\Http\UploadedFile;

/**
 * @group Products
 */
class TestCreateProductDetailTest extends TestCase
{
    protected $createProductDetailedMutation = '
            mutation {
                createProduct(input: {
                    title: "1x Cappuccino",
                    short_description: "A Cappuccino is a espresso-based coffee drink originating from Italy.",
                    long_description: "A Cappuccino is an espresso-based coffee drink that originated in Italy, and is traditionally prepared with steamed milk foam (microfoam). \n\nThis description can support newlines too!"
                }){
                    short_description
                    long_description
                }
            }
        ';


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
                    }
                }
            ');
        $result = $response->decodeResponseJson();

        // Set the global session token to use for the test
        $this->sessionToken = $result['data']['logIn']['token'];
    }

    public function logoutUser($token = null)
    {
        $response = $this->postGraphQL(['query' => '
                mutation {
                    logOut {
                        success
                    }
                }
            '], [
            'HTTP_Authorization' => 'Bearer ' . $token
        ]);

        $response->assertDontSee('errors');

        $logOut = $response->decodeResponseJson();

        $this->assertTrue($logOut['data']['logOut']['success']);

        $response->assertJsonStructure([
            'data' => [
                'logOut' => ['success']
            ]
        ]);

        return $response;
    }

    public function createProduct()
    {
        return $this->graphQLWithSession('
                mutation {
                    createProduct(input:{
                        title: "1x Cappuccino"
                        }) {
                        id
                        title
                    }
                }
            ');
    }

    public function testAdminUserCanCreateProductWithDetailedFields(): void
    {
        $response = $this->graphQLWithSession($this->createProductDetailedMutation);
        $response->assertDontSee('errors');
        $result = $response->decodeResponseJson();
        $this->assertStringStartsWith("A Cappuccino", $result['data']['createProduct']['short_description']);
        $this->assertStringStartsWith("A Cappuccino", $result['data']['createProduct']['long_description']);
    }

    public function testAdminUserCanUpdateProductWithDetailedFields(): void
    {
        $product = $this->createProduct()->decodeResponseJson();
        $id = $product['data']['createProduct']['id'];
        $response = $this->graphQLWithSession('
                mutation {
                    updateProduct(id: ' . $id . ', input: {
                        short_description: "A Cappuccino is a espresso-based coffee drink originating from Italy.",
                        long_description: "A Cappuccino is an espresso-based coffee drink that originated in Italy, and is traditionally prepared with steamed milk foam (microfoam). \n\nThis description can support newlines too!"
                    }){
                        short_description
                        long_description
                    }
                }
            ');

        $response->assertDontSee('errors');
        $result = $response->decodeResponseJson();
        $this->assertStringStartsWith("A Cappuccino", $result['data']['updateProduct']['short_description']);
        $this->assertStringStartsWith("A Cappuccino", $result['data']['updateProduct']['long_description']);
    }

    public function testAdminUserCanCreateProductWithADefaultPrice(): void
    {
        $response = $this->graphQLWithSession('
                mutation {
                    createProduct(input: {
                        title: "1x Cappuccino",
                        price_cents: 200,
                        price_currency: "EUR"
                    }){
                        id
                        price_cents
                        price_currency
                    }
                }
            ');

        $response->assertDontSee('errors');

        $result = $response->decodeResponseJson();

        $this->assertEquals($result['data']['createProduct']['price_cents'], 200);
        $this->assertEquals($result['data']['createProduct']['price_currency'], "EUR");
    }

    public function testAdminUserCanUpdateProductWithADefaultPrice(): void
    {
        $product = $this->createProduct()->decodeResponseJson();
        $response = $this->graphQLWithSession('
                mutation {
                    updateProduct(id: ' . $product['data']['createProduct']['id'] . 'input: {
                        title: "1x Cappuccino",
                        price_cents: 200,
                        price_currency: "EUR"
                    }){
                        id
                        price_cents
                        price_currency
                    }
                }
            ');

        $response->assertDontSee('errors');

        $result = $response->decodeResponseJson();

        $this->assertEquals($result['data']['updateProduct']['price_cents'], 200);
        $this->assertEquals($result['data']['updateProduct']['price_currency'], "EUR");
    }

    /**
     * @group ProductAttributes
     */
    public function testAdminUserCanCreateProductWithArbitraryMeta(): void
    {
        $response = $this->graphQLWithSession('
                mutation {
                    createProduct(input: {
                        title: "1x Cappuccino",
                        price_cents: 200,
                        price_currency: "EUR",
                        meta: [{
                            key: "bean",
                            value: "Light roast"
                        },{
                            key: "bean",
                            value: "Medium roast"
                        },{
                            key: "bean",
                            value: "Dark roast"
                        }]
                    }){
                        id
                        price_cents
                        price_currency
                        meta {
                            key
                            value
                        }
                    }
                }
            ');

        $response->assertDontSee('errors');
        $result = $response->decodeResponseJson();

        $this->assertEquals($result['data']['createProduct']['meta'][0]['value'], "Light roast");
    }

    /**
     * @group ProductMeta
     */
    public function testAdminUserCanUpdateProductWithArbitraryMeta(): void
    {
        $product = $this->createProduct()->decodeResponseJson();
        $id = $product['data']['createProduct']['id'];

        $mutation = '
                mutation {
                    updateProduct(id: ' . $id . ', input: {
                        meta: [{
                            key: "bean",
                            value: "Medium roast"
                        }]
                    }){
                        meta {
                            key
                            value
                        }
                    }
                }
            ';

        // Run this twice, so we can be sure the meta is removed
        $this->graphQLWithSession($mutation);
        $response = $this->graphQLWithSession($mutation);

        $response->assertDontSee('errors');
        $result = $response->decodeResponseJson();

        $this->assertCount(1, $result['data']['updateProduct']['meta']);
        $this->assertEquals($result['data']['updateProduct']['meta'][0]['value'], "Medium roast");
    }

    /**
     * @group ProductMeta
     */
    public function testAdminUserCanCreateProductWithDetailedPricing(): void
    {
        $response = $this->graphQLWithSession('
                mutation {
                    createProduct(input: {
                        title: "1x Cappuccino",
                        price_cents: 200,
                        price_currency: "EUR",
                        prices: [{
                            currency: "GBP",
                            value: 300
                        },{
                            currency: "USD",
                            value: 250
                        }]
                    }){
                        prices {
                            currency
                            value
                        }
                    }
                }
            ');

        $response->assertDontSee('errors');
        $result = $response->decodeResponseJson();

        $this->assertCount(2, $result['data']['createProduct']['prices']);
        $this->assertEquals($result['data']['createProduct']['prices'][0]['currency'], "GBP");
    }

    /**
     * @group ProductAttributes
     */
    public function testAdminUserCanUpdateProductWithDetailedPricing(): void
    {
        $mutation = '
                mutation {
                    createProduct(input: {
                        title: "1x Cappuccino",
                        price_cents: 200,
                        price_currency: "EUR",
                        prices: [{
                            currency: "GBP",
                            value: 300
                        }]
                    }){
                        prices {
                            currency
                            value
                        }
                    }
                }
            ';

        // Run this twice, so we can be sure that price data isn't being merged
        $this->graphQLWithSession($mutation);
        $response = $this->graphQLWithSession($mutation);

        $response->assertDontSee('errors');
        $result = $response->decodeResponseJson();

        $this->assertCount(1, $result['data']['createProduct']['prices']);
        $this->assertEquals($result['data']['createProduct']['prices'][0]['currency'], "GBP");
    }

    /**
     * @group ProductVariants
     */
    public function testAdminUserCanCreateProductVariant(): void
    {
        $product = $this->createProduct()->decodeResponseJson();
        $id = $product['data']['createProduct']['id'];
        $response = $this->graphQLWithSession('
                mutation {
                    createProductVariant( id: ' . $id . ', input: {
                        title: "1x Cappuccino (Small)",
                        price_cents: 200,
                        price_currency: "EUR",
                        sizes: [{
                            size: "Small"
                        }]
                    }) {
                        id
                        parent_id
                        sizes {
                            size
                        }
                    }
                }
            ');

        $response->assertDontSee('errors');
        $response = $this->graphQLWithSession('
                {
                    product(id:' . $id . '){
                        variants {
                            id
                            sizes {
                                size
                            }
                        }
                    }
                }
            ');

        $result = $response->decodeResponseJson();
        $response->assertDontSee('errors');
        $this->assertEquals($result['data']['product']['variants'][0]['sizes'][0]['size'], "Small");
    }

    /**
     * @group ProductCategory
     */
    public function testAdminUserCanCreateCategoryOnProductWithOrWithoutCategoryExisting(): void
    {
        $product = $this->createProduct()->decodeResponseJson();
        $id = $product['data']['createProduct']['id'];
        $response = $this->graphQLWithSession('
                mutation {
                    createProduct(input: {
                        title: "1x Cappuccino",
                        categories: [{
                            title: "Coffee"
                        }]
                    }){
                        categories {
                            id
                            title
                        }
                    }
                }
            ');

        $response->assertDontSee('errors');
        $response->assertJsonStructure([
            'data' => [
                'createProduct' => [
                    'categories' => [['id', 'title']]
                ]
            ]
        ]);

        $json = $response->decodeResponseJson();

        $response = $this->graphQLWithSession('
                mutation {
                    createProduct(input: {
                        title: "1x Cappuccino",
                        categories: [{
                            id: ' . $json['data']['createProduct']['categories'][0]['id'] .  '
                        }]
                    }){
                        categories {
                            id
                            title
                        }
                    }
                }
            ');

        $response->assertDontSee('errors');
        $response->assertJsonStructure([
            'data' => [
                'createProduct' => [
                    'categories' => [['id', 'title']]
                ]
            ]
        ]);
    }

    /**
     * @group ProductDimensions
     */
    public function testAdminUserCanSetDimensionsAndWeightOfProduct(): void
    {
        $response = $this->graphQLWithSession('
                mutation {
                    createProduct(input: {
                        title: "1x Cappuccino",
                        dimensions: {
                            length: 20,
                            width: 10,
                            height: 5,
                            unit: "cm"
                        },
                        weight: {
                            weight: 0.25,
                            unit: "kg"
                        }
                    }){
                        dimensions {
                            width
                            length
                            height
                        }
                        weight {
                            weight
                        }
                    }
                }
            ');

        $response->assertDontSee('errors');
        $response->assertJsonStructure([
            'data' => [
                'createProduct' => [
                    'dimensions' => ['length', 'width', 'height'],
                    'weight' => ['weight']
                ]
            ]
        ]);

        $result = $response->decodeResponseJson();

        $this->assertEquals($result['data']['createProduct']['dimensions']['width'], 10);
    }

    /**
     * @group ProductDimensions
     */
    public function testAdminUserCanUpdateDimensionsAndWeightOfProduct(): void
    {
        $product = $this->createProduct()->decodeResponseJson();
        $id = $product['data']['createProduct']['id'];
        $response = $this->graphQLWithSession('
                mutation {
                    updateProduct(id: ' . $id . ', input: {
                        title: "1x Cappuccino",
                        dimensions: {
                            length: 25,
                            width: 12,
                            height: 5,
                            unit: "cm"
                        },
                        weight: {
                            weight: 0.33,
                            unit: "kg"
                        }
                    }){
                        dimensions {
                            width
                            length
                            height
                        }
                        weight {
                            weight
                        }
                    }
                }
            ');

        $response->assertDontSee('errors');
        $response->assertJsonStructure([
            'data' => [
                'updateProduct' => [
                    'dimensions' => ['length', 'width', 'height'],
                    'weight' => ['weight']
                ]
            ]
        ]);

        $result = $response->decodeResponseJson();

        $this->assertEquals($result['data']['updateProduct']['dimensions']['width'], 12);
    }

    /**
     * @group ProductImage
     */
    public function testAdminUserCanCreateImagesOnProduct(): void
    {
        $product = $this->createProduct()->decodeResponseJson();
        $productId = $product['data']['createProduct']['id'];

        $json = [
            "query" => '
                    mutation($file: Upload!){
                        addImage(input: { file: $file }){
                            id
                            url
                            name
                            type
                        }
                    }
                ',
            "variables" => [
                "file" => null
            ]
        ];

        $operations = json_encode($json);

        $response = $this->multipartGraphQLWithSession(
            [
                "operations" => $operations,
                "map" => '{ "0": ["variables.file"] }'
            ],
            [
                '0' => UploadedFile::fake()->create('image.jpg', 500),
            ]
        );

        $response->assertDontSee('errors');

        $json = $response->decodeResponseJson();
        $imageId = $json['data']['addImage']['id'];

        $response->assertJsonStructure([
            'data' => [
                'addImage' => [
                    'url',
                    'type',
                    'name'
                ]
            ]
        ]);

        $response = $this->graphQLWithSession('
                mutation {
                    addProductImages(product_id: ' . $productId . ', images: [' . $imageId . ']){
                        product {
                            id
                            images {
                                id
                                url
                            }
                        }
                    }
                }
            ');

        $response->assertDontSee('errors');

        $response->assertJsonStructure([
            'data' => ['addProductImages' => [
                'product' => ['id', 'images' => [
                    ['id', 'url']
                ]]
            ]]
        ]);
    }

    /**
     * @group ProductTag
     */
    public function testAdminUserCanCreateTagOnProduct(): void
    {
        $product = $this->createProduct()->decodeResponseJson();
        
        $this->assertDatabaseHas('gernzy_products', [
            'title' => $product['data']['createProduct']['title'],
        ]);

        $productId = $product['data']['createProduct']['id'];

        $response = $this->graphQLWithSession('
                mutation {
                    createTag(input: {
                        name:"' . Faker::create()->word() . '"
                        }) {
                        id
                        name
                    }
                }
            ');

        $response->assertDontSee('errors');
        $json = $response->decodeResponseJson();
        $tagId = $json['data']['createTag']['id'];


        $response->assertJsonStructure([
            'data' => [
                'createTag' => [
                    'id', 'name'
                ]
            ]
        ]);

        $response = $this->graphQLWithSession('
                mutation {
                    addProductTags(product_id: ' . $productId . ', tags: [' . $tagId . ']){
                        product {
                            id
                            tags {
                                id
                                name
                            }
                        }
                    }
                }
            ');

        $response->assertDontSee('errors');

        $response->assertJsonStructure([
            'data' => ['addProductTags' => [
                'product' => ['id', 'tags' => [
                    ['id', 'name']
                ]]
            ]]
        ]);
    }

    /**
     * @group ProductTags
     */
    public function testAdminUserCanCreateManyTagsOnProduct(): void
    {
        $product = $this->createProduct()->decodeResponseJson();

        $this->assertDatabaseHas('gernzy_products', [
            'title' => $product['data']['createProduct']['title'],
        ]);

        $productId = $product['data']['createProduct']['id'];
        $tags = [];

        for ($i = 0; $i < 20; $i++) {
            $response = $this->graphQLWithSession('
                mutation {
                    createTag(input:{
                        name:"' . Faker::create()->word() . '"
                        }) {
                        id
                        name
                    }
                }
            ');

            $response->assertDontSee('errors');
            $json = $response->decodeResponseJson();
            $tagId = $json['data']['createTag']['id'];
            array_push($tags, $tagId);
        }


        $response->assertJsonStructure([
            'data' => [
                'createTag' => [
                    'id', 'name'
                ]
            ]
        ]);

        $response = $this->graphQLWithSession('
                mutation {
                    addProductTags(product_id: ' . $productId . ', tags: [' . implode(", ", $tags) . ']){
                        product {
                            id
                            tags {
                                id
                                name
                            }
                        }
                    }
                }
            ');

        $response->assertDontSee('errors');

        $response->assertJsonStructure([
            'data' => ['addProductTags' => [
                'product' => ['id', 'tags' => [
                    ['id', 'name']
                ]]
            ]]
        ]);
    }

    /**
     * @group ProductTags
     */
    public function testAdminUserCanNotCreateManyTagsOnProductIfGuest(): void
    {
        $product = $this->createProduct()->decodeResponseJson();

        $productId = $product['data']['createProduct']['id'];
        $tags = [];

        for ($i = 0; $i < 20; $i++) {
            $response = $this->graphQLWithSession('
                mutation {
                    createTag(input:{
                        name:"' . Faker::create()->word() . '"
                        }) {
                        id
                        name
                    }
                }
            ');

            $response->assertDontSee('errors');
            $json = $response->decodeResponseJson();
            $tagId = $json['data']['createTag']['id'];
            array_push($tags, $tagId);
        }


        $response->assertJsonStructure([
            'data' => [
                'createTag' => [
                    'id', 'name'
                ]
            ]
        ]);

        // Make sure the user now becomes a guest by loggin them out
        $this->logoutUser($this->sessionToken);

        $response = $this->graphQLWithSession('
                mutation {
                    addProductTags(product_id: ' . $productId . ', tags: [' . implode(", ", $tags) . ']){
                        product {
                            id
                            tags {
                                id
                                name
                            }
                        }
                    }
                }
            ');

        $start = $response->decodeResponseJson();
        
        // TODO: Access the response code somehow to compare
        $errors = $start['errors'][0]['message'];
    
        $this->assertEquals($errors, 'You are not authorized to access addProductTags');
    }

    /**
     * @group ProductImage
     */
    public function testAdminUserCanSetFeaturedImageOnProduct(): void
    {
        $product = $this->createProduct()->decodeResponseJson();
        $productId = $product['data']['createProduct']['id'];

        $json = [
            "query" => '
                    mutation($file: Upload!){
                        addImage(input: { file: $file }){
                            id
                        }
                    }
                ',
            "variables" => [
                "file" => null
            ]
        ];

        $response = $this->multipartGraphQLWithSession(
            [
                "operations" => json_encode($json),
                "map" => '{ "0": ["variables.file"] }'
            ],
            [
                '0' => UploadedFile::fake()->create('image.jpg', 500),
            ]
        );

        $json = $response->decodeResponseJson();
        $imageId = $json['data']['addImage']['id'];

        for ($i = 0; $i < 5; $i++) {
            $response = $this->graphQLWithSession('
                    mutation {
                        setProductFeaturedImage(product_id: ' . $productId . ', image_id: ' . $imageId . '){
                            product {
                                id
                                featured_image {
                                    id
                                    url
                                }
                            }
                        }
                    }
                ');
        }

        $response->assertDontSee('errors');

        $response->assertJsonStructure([
            'data' => [
                'setProductFeaturedImage' => [
                    'product' => ['id', 'featured_image' => ['id', 'url']]
                ]
            ]
        ]);
    }
}
