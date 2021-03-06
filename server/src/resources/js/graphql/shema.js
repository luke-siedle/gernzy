var mainSchema = `
    #A datetime string with format Y-m-d H:i:s, e.g. 2018-01-01 13:00:00.
    #scalar DateTime @scalar(class: "Nuwave\Lighthouse\Schema\Types\Scalars\DateTime")

    #A date string with format Y-m-d, e.g. 2011-05-23.
    #scalar Date @scalar(class: "Nuwave\Lighthouse\Schema\Types\Scalars\Date")

    #Can be used as an argument to upload files using https://github.com/jaydenseric/graphql-multipart-request-spec
    #scalar Upload @scalar(class: "Nuwave\Lighthouse\Schema\Types\Scalars\Upload")

    scalar DateTime
    
    scalar Date

    scalar Upload

    type Query {
        sample: String
    }

    extend type Query {
        users: [User!]!
            @paginate(type: "paginator", model: "Lab19\Cart\Models\User")
            @can(ability: "view", model: "Lab19\Cart\Models\User", policy: "Lab19\Cart\Policies\UserPolicy")

        user(id: ID @eq): User
            @find(model: "Lab19\Cart\Models\User")
            @can(
                ability: "view"
                find: "id"
                model: "Lab19\Cart\Models\User"
                policy: "Lab19\Cart\Policies\UserPolicy"
            )

        orders: [Order!]! @paginate(type: "paginator", model: "Lab19\Cart\Models\Order")

        order_items: [OrderItem!]! @paginate(type: "paginator", model: "Lab19\Cart\Models\OrderItem")
        order_item(id: ID @eq): OrderItem @find(model: "Lab19\Cart\Models\OrderItem")

        shopConfig: [String] @field(resolver: "Lab19\Cart\GraphQL\Queries\ShopConfig@enabledCurrencies")
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
        product_id: Int! #note this supposed to be ID! but when mock server returns ID! it is a string and then can't use that string afterwards to query product
        quantity: Int!
    }

    type OrderItem {
        id: ID!
        order_id: ID!
    }

    type Mutation {
        createSession: Session @field(resolver: "Lab19\Cart\GraphQL\Mutations\CreateSession@create")
        setSession(input: SetSessionInput!): SetSessionPayload
            @field(resolver: "Lab19\Cart\GraphQL\Mutations\SetSession@set")
        setSessionCurrency(input: SetSessionCurrency!): SetSessionCurrencyPayload
            @field(resolver: "Lab19\Cart\GraphQL\Mutations\SetSession@setCurrency")
        setSessionGeoLocation: setSessionGeoLocationPayload
            @field(resolver: "Lab19\Cart\GraphQL\Mutations\SetSession@setGeoLocation")
        createAccount(input: CreateAccountInput! @spread): CreateAccountPayload
            @field(resolver: "Lab19\Cart\GraphQL\Mutations\CreateAccount@create")
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

`;

export default mainSchema;
