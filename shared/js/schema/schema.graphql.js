export default `
"A datetime string with format Y-m-d H:i:s, e.g. 2018-01-01 13:00:00."
scalar DateTime #@scalar(class: "Nuwave\Lighthouse\Schema\Types\Scalars\DateTime")

"A date string with format Y-m-d, e.g. 2011-05-23."
scalar Date #@scalar(class: "Nuwave\Lighthouse\Schema\Types\Scalars\Date")

"Can be used as an argument to upload files using https://github.com/jaydenseric/graphql-multipart-request-spec"
scalar Upload #@scalar(class: "Nuwave\Lighthouse\Schema\Types\Scalars\Upload")

type Query {
    users: [User!]!
        @paginate(type: "paginator", model: "Gernzy\Server\Models\User")
        @can(ability: "view", model: "Gernzy\Server\Models\User", policy: "Gernzy\Server\Policies\UserPolicy")

    user(id: ID @eq): User
        @find(model: "Gernzy\Server\Models\User")
        @can(
            ability: "view"
            find: "id"
            model: "Gernzy\Server\Models\User"
            policy: "Gernzy\Server\Policies\UserPolicy"
        )

    orders: [Order!]! @paginate(type: "paginator", model: "Gernzy\Server\Models\Order")

    order_items: [OrderItem!]! @paginate(type: "paginator", model: "Gernzy\Server\Models\OrderItem")
    order_item(id: ID @eq): OrderItem @find(model: "Gernzy\Server\Models\OrderItem")

    shopConfig: [String] @field(resolver: "Gernzy\Server\GraphQL\Queries\ShopConfig@enabledCurrencies")
}

type User {
    id: ID!
    name: String!
    email: String!
    cart: Cart
    session: Session
    created_at: DateTime!
    updated_at: DateTime!
}

type Order {
    id: ID!
    email: String!
    name: String!
    currency_id: Int!
    cart_id: ID!
    is_admin_order: Boolean!
    cart: Cart @hasOne
}

type Cart {
    id: ID!
    order_id: ID
    item_count: Int!
    order: Cart @belongsTo
    items: [CartItem!]
    products: Int
}

type CartItem {
    product_id: ID!
    quantity: Int!
}

type OrderItem {
    id: ID!
    order_id: ID!
}

type Mutation {
    createSession: Session @field(resolver: "Gernzy\Server\GraphQL\Mutations\CreateSession@create")
    setSession(input: SetSessionInput!): SetSessionPayload
        @field(resolver: "Gernzy\Server\GraphQL\Mutations\SetSession@set")
    setSessionCurrency(input: SetSessionCurrency!): SetSessionCurrencyPayload
        @field(resolver: "Gernzy\Server\GraphQL\Mutations\SetSession@setCurrency")
    setSessionGeoLocation: setSessionGeoLocationPayload
        @field(resolver: "Gernzy\Server\GraphQL\Mutations\SetSession@setGeoLocation")
    createAccount(input: CreateAccountInput! @spread): CreateAccountPayload
        @field(resolver: "Gernzy\Server\GraphQL\Mutations\CreateAccount@create")
}

type setSessionGeoLocationPayload {
    geolocation_record: String
}

type Session {
    id: ID!
    token: String!
    cart_id: ID
}

input CreateOrderInput {
    cart: CreateCartRelation
}

input CreateCartRelation {
    connect: ID
    create: CreateCartInput
    update: UpdateCartInput
}

input CreateCartInput {
    item_count: Int
}

input UpdateCartInput {
    id: ID
    item_count: Int
}

#import user.graphql
#import products.graphql
#import orders.graphql
#import tags.graphql 
`
