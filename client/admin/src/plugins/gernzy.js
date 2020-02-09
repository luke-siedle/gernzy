export default function( GraphQLServer ){

  const App = this;
  const graphql = {
    query: function( query, params ){
       return GraphQLServer.query( query, params );
    }
  }

  return {
    install: function (Vue, options) {
      Vue.prototype.$gernzy = {
        graphql: graphql
      }
    }
  }
}
