export default `
extend type Query {
    products(input: ProductsQueryInput): [Product!]!
        @gernzyConvertCurrency
        @paginate(
            type: "paginator"
            model: "Gernzy\Server\Models\Product"
            scopes: ["published", "inStock"]
            builder: "Gernzy\Server\GraphQL\Builders\ProductsBuilder@search"
        )

    product(id: ID @eq): Product @find(model: "Gernzy\Server\Models\Product")

    productsByCategories(input: ProductsCategoriesInput): [Product!]!
        @paginate(
            type: "paginator"
            model: "Gernzy\Server\Models\Product"
            scopes: ["published", "inStock"]
            builder: "Gernzy\Server\GraphQL\Builders\ProductsBuilder@byCategory"
        )

    productsByTag(tag: Int!): [Product!]!
        @paginate(type: "paginator", builder: "Gernzy\Server\GraphQL\Builders\ProductsBuilder@productsbytag")

    productsByTags(tags: [Int!]): [Product!]!
        @paginate(type: "paginator", builder: "Gernzy\Server\GraphQL\Builders\ProductsBuilder@productsByTags")
}

input ProductsQueryInput {
    keyword: String
    attributes: [ProductsQueryInputAttributes!]
}

input ProductsQueryInputAttributes {
    name: String
    value: String
}

input ProductsCategoriesInput {
    ids: [Int!]
    titles: [String!]
}

type Product {
    id: ID!
    parent_id: ID
    title: String!
    status: String!
    published: Int!
    price_cents: Int
    price_currency: String
    short_description: String
    long_description: String
    created_at: DateTime!
    updated_at: DateTime!
    meta: [ProductAttribute!]
    prices: [ProductPrice!]
    sizes: [ProductSize!]
    variants: [Product!]
    categories: [ProductCategory!]
    dimensions: ProductDimensions
    weight: ProductWeight
    images: [Image!]
    featured_image: Image
    tags: [Tag!] @hasMany
    fixedPrices: [ProductFixedPrice!] @hasMany
}

type ProductFixedPrice {
    id: ID!
    country_code: String
    price: Float
}

type ProductDimensions {
    length: Float
    width: Float
    height: Float
    unit: String
}

type ProductWeight {
    weight: Float
    unit: String
}

type ProductCategory {
    id: Int!
    title: String!
}

extend type Mutation {
    createProduct(input: CreateProductInput!): Product
        @field(resolver: "Gernzy\Server\GraphQL\Mutations\Product@create")
        @can(ability: "create", model: "Gernzy\Server\Models\Product", policy: "Gernzy\Server\Policies\ProductPolicy")
    updateProduct(id: ID!, input: UpdateProductInput!): Product
        @field(resolver: "Gernzy\Server\GraphQL\Mutations\Product@update")
        @can(ability: "update", model: "Gernzy\Server\Models\Product", policy: "Gernzy\Server\Policies\ProductPolicy")
    deleteProduct(id: ID!): DeleteResult
        @field(resolver: "Gernzy\Server\GraphQL\Mutations\Product@delete")
        @can(ability: "delete", model: "Gernzy\Server\Models\Product", policy: "Gernzy\Server\Policies\ProductPolicy")
}

input CreateProductInput {
    title: String!
    price_cents: Int
    price_currency: String
    short_description: String
    long_description: String
    meta: [ProductAttributeInput!]
    prices: [ProductPriceInput!]
    sizes: [ProductSizeInput!]
    categories: [CategoryInput!]
    dimensions: ProductDimensionsInput
    weight: ProductWeightInput
    fixprices: [PricingInput]
}

input PricingInput {
    currency: String!
    price_cents: Float
}

input ProductDimensionsInput {
    length: Float
    width: Float
    height: Float
    unit: String
}

input ProductWeightInput {
    weight: Float
    unit: String
}

input CategoryInput {
    title: String
    id: ID
}

input UpdateProductInput {
    title: String
    price_cents: Int
    price_currency: String
    short_description: String
    long_description: String
    meta: [ProductAttributeInput!]
    prices: [ProductPriceInput!]
    sizes: [ProductSizeInput!]
    categories: [CategoryInput!]
    dimensions: ProductDimensionsInput
    weight: ProductWeightInput
}

input ProductAttributeInput {
    key: String!
    value: String!
}

input ProductPriceInput {
    currency: String!
    value: Int!
}

input ProductSizeInput {
    size: String!
}

type DeleteResult {
    success: Boolean!
}

type ProductAttribute {
    id: ID!
    group: String!
    key: String!
    value: String!
    created_at: DateTime
    updated_at: DateTime
}

type ProductPrice {
    currency: String!
    value: Int!
}

type ProductSize {
    size: String!
}

extend type Mutation {
    createProductVariant(id: ID!, input: CreateProductInput!): Product
        @field(resolver: "Gernzy\Server\GraphQL\Mutations\Product@createVariant")
        @can(ability: "create", model: "Gernzy\Server\Models\Product", policy: "Gernzy\Server\Policies\ProductPolicy")
    addImage(input: AddProductImage!): Image
        @field(resolver: "Gernzy\Server\GraphQL\Mutations\Image@create")
        @can(ability: "create", model: "Gernzy\Server\Models\Product", policy: "Gernzy\Server\Policies\ProductPolicy")
    addProductImages(product_id: ID!, images: [ID!]): AddProductImagesPayload!
        @field(resolver: "Gernzy\Server\GraphQL\Mutations\Product@attachImages")
        @can(ability: "create", model: "Gernzy\Server\Models\Product", policy: "Gernzy\Server\Policies\ProductPolicy")
    setProductFeaturedImage(product_id: ID!, image_id: ID!): SetProductFeaturedImagePayload!
        @field(resolver: "Gernzy\Server\GraphQL\Mutations\Product@setFeaturedImage")
        @can(ability: "create", model: "Gernzy\Server\Models\Product", policy: "Gernzy\Server\Policies\ProductPolicy")
    addProductTags(product_id: ID!, tags: [ID!]): AddProductTagsPayload!
        @field(resolver: "Gernzy\Server\GraphQL\Mutations\Product@attachTags")
        @can(ability: "create", model: "Gernzy\Server\Models\Tag", policy: "Gernzy\Server\Policies\TagPolicy")
}

input AddProductImage {
    file: Upload!
    gallery: ID
}

type Image {
    id: ID!
    url: String!
    type: String!
    name: String!
}

type AddProductImagesPayload {
    product: Product!
    images: [Image!]
}

type AddProductTagsPayload {
    product: Product!
    tags: [Tag!]
}

type SetProductFeaturedImagePayload {
    product: Product!
} 
`
