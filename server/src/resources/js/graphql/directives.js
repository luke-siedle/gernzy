// Sets definitions so our graphql is not invalid
var directives = `
  directive @paginate(
      type: String,
      model: String,
      scopes: String,
      builder: String
  ) on FIELD_DEFINITION
  directive @can(
      type: Boolean,
      ability: String
      model: String
      policy: String
      find: String
  ) on FIELD_DEFINITION
  directive @find(
      model: String
  ) on FIELD_DEFINITION
  directive @field(
      resolver: String
  ) on FIELD_DEFINITION
  directive @gate(
      ability: String
      sessionOnly: Boolean
  ) on FIELD_DEFINITION
  directive @hasOne on FIELD_DEFINITION
  directive @belongsTo on FIELD_DEFINITION
  directive @spread on ARGUMENT_DEFINITION
  directive @eq on ARGUMENT_DEFINITION
  directive @rules(
      apply: String
  ) on INPUT_FIELD_DEFINITION
  directive @gernzyConvertCurrency on FIELD_DEFINITION
  directive @hasMany(
    type: String
  ) on FIELD_DEFINITION
  directive @all(
    model: String
  ) on FIELD_DEFINITION
`;

export default directives;
