#/bin/sh
# Generates shared schema from server .graphql for use in any graphql related unit tests
PARENT_DIR="$(dirname `pwd`)"
SCHEMA_DIR=$PARENT_DIR/server/src/GraphQL/schema
OUTPUT_DIR=$PARENT_DIR/shared/js/schema
for f in $SCHEMA_DIR/*.graphql; do
	CONTENTS=$(cat $f)
	CONTENTS=${CONTENTS//\\\\/\\}
	CONTENTS=${CONTENTS//\`/}
  	echo "export default \` $CONTENTS \` " > "$OUTPUT_DIR/${f##*/}.js"
done