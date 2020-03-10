const jQuery = require('jquery/dist/jquery.js');
import { makeExecutableSchema, addMockFunctionsToSchema } from 'graphql-tools';
import { graphql } from 'graphql';
// The schemas
import mainSchema from '../../../../../shared/js/schema/schema.graphql';
import productSchema from '../../../../../shared/js/schema/products.graphql';
import userSchema from '../../../../../shared/js/schema/user.graphql';
import ordersSchema from '../../../../../shared/js/schema/orders.graphql';
import tagSchema from '../../../../../shared/js/schema/tags.graphql';
import schemaDefinitions from '../../../../../shared/js/schema/common.schema';

const { parse } = require('graphql');

// Fill this in with the schema string
const schemaString = schemaDefinitions + mainSchema + userSchema + productSchema + tagSchema + ordersSchema;

// This is for testing mutations by only using a temporary simple schema on the checkout test. (works)
// const schemaString = temp;

// Make a GraphQL schema with no resolvers
const schema = makeExecutableSchema({
    typeDefs: schemaString,
});

// Add mocks, modifies schema in place
addMockFunctionsToSchema({ schema });

jQuery.ajax = settings => {
    return new Promise((resolve, reject) => {
        let query = JSON.parse(settings.data);
        query = query.query;

        const object = parse(query);
        let name = object.definitions[0].selectionSet.selections[0].name.value;

        // The grahpql-tools mock server doesnt understand light house pagination fields, so as
        // a temporary work around I check if the products query is run and alter it to remove
        // lighthouse specific syntax. Alternative could be to loop through 'const object = parse(query);'
        // and pick up fields queried for products.
        if (name == 'products') {
            query = `query {
                products {
                        id
                        title
                        status
                        published
                        short_description
                }
            }`;
        }

        process.nextTick(() => {
            graphql(schema, query).then(result => {
                if (name == 'products') {
                    // Return the expected data structure.. again this because of @paginate from lighthouse-php
                    let newOb = { data: { products: { data: result.data.products } } };
                    resolve(newOb);
                }
                resolve(result);
            });
        });
    });
};

export default jQuery;
