export default `
extend type Mutation {
    checkout(input: CheckoutInput!): CheckoutPayload
        @field(resolver: "Gernzy\Server\GraphQL\Mutations\Checkout@checkout")
        @gate(ability: "can-checkout", sessionOnly: true)
}

extend type Query {
    order(id: ID @eq): Order
        @find(model: "Gernzy\Server\Models\Order")
        @can(
            ability: "view"
            find: "id"
            model: "Gernzy\Server\Models\Order"
            policy: "Gernzy\Server\Policies\OrderPolicy"
        )
}

input CheckoutInput {
    name: String! @rules(apply: ["required"])
    email: String! @rules(apply: ["required", "email"])
    mobile: String!
    telephone: String!
    billing_address: BillingAddress! @rules(apply: ["required"])
    shipping_address: ShippingAddress! @rules(apply: ["required"])
    use_shipping_for_billing: Boolean!
    payment_method: String!
    agree_to_terms: Boolean!
    notes: String!
}

input BillingAddress {
    line_1: String! @rules(apply: ["required"])
    line_2: String
    state: String! @rules(apply: ["required"])
    postcode: String
    country: String!
}

input ShippingAddress {
    line_1: String! @rules(apply: ["required"])
    line_2: String
    state: String! @rules(apply: ["required"])
    postcode: String
    country: String! @rules(apply: ["required"])
}

type CheckoutPayload {
    success: Boolean!
    order: Order
}

extend type Mutation {
    createOrder(input: CheckoutInput!): Order
        @field(resolver: "Gernzy\Server\GraphQL\Mutations\Order@create")
        @can(ability: "create", model: "Gernzy\Server\Models\Order", policy: "Gernzy\Server\Policies\OrderPolicy")
    updateOrder(id: ID!, input: UpdateOrderInput!): Order
        @field(resolver: "Gernzy\Server\GraphQL\Mutations\Order@update")
        @can(ability: "update", model: "Gernzy\Server\Models\Order", policy: "Gernzy\Server\Policies\OrderPolicy")
    setOrderItems(id: ID!, input: [SetOrderItemsInput!]): Order
        @field(resolver: "Gernzy\Server\GraphQL\Mutations\Order@setItems")
        @can(ability: "update", model: "Gernzy\Server\Models\Order", policy: "Gernzy\Server\Policies\OrderPolicy")
    deleteOrder(id: ID!): DeleteResult
        @field(resolver: "Gernzy\Server\GraphQL\Mutations\Order@delete")
        @can(ability: "delete", model: "Gernzy\Server\Models\Order", policy: "Gernzy\Server\Policies\OrderPolicy")
}

input UpdateOrderInput {
    name: String
    email: String @rules(apply: ["email"])
    mobile: String
    telephone: String
    billing_address: BillingAddress
    shipping_address: ShippingAddress
    use_shipping_for_billing: Boolean
    payment_method: String
    notes: String
}

input SetOrderItemsInput {
    product_id: ID!
    quantity: Int!
} 
`
