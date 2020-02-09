import { createLocalVue as createVue } from "@vue/test-utils"
import Vuex from "vuex"
import { mockServer } from "graphql-tools"
import { print } from 'graphql/language/printer'
import { createProvider } from '../../src/vue-apollo'

// Import the shared schema, which is generated via script from Laravel .graphql source files
import schemaDefinitions from "../../../../shared/js/schema/common.schema.js"
import schema from "../../../../shared/js/schema/schema.graphql.js"
import schemaOrders from "../../../../shared/js/schema/orders.graphql.js"
import schemaUser from "../../../../shared/js/schema/user.graphql.js"
import schemaProducts from "../../../../shared/js/schema/products.graphql.js"
import schemaTags from "../../../../shared/js/schema/tags.graphql.js"

export const createMockServer = () => {
  return mockServer(schemaDefinitions + schema + schemaOrders + schemaUser + schemaProducts + schemaTags)
}

export const createLocalVue = () => {
  const localVue = createVue()
  localVue.use(Vuex)
  return localVue
}

export const createApolloTestProvider = () => {
  const client = createMockServer()
  const testClient = createProvider({
    testClient: client
  })
  client.mutate = ( obj ) => {
    return client.query(print(obj.mutation), obj.variables);
  }
  // client.query = ( obj ) => {
  //   return client.query(print(obj.query), obj.variables)
  // }
  return testClient
}

// Serializes the initial state so that it can be restored
// as a replacement using store.replaceState and returns
// a method to create the initial state object
export const makeGetInitialState = ( store ) => {
  const initialState = JSON.stringify(store.state);
  return () => JSON.parse(initialState)
}
