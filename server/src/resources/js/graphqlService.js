import $ from 'jquery';

class GraphqlService {
    constructor() {
        this.userToken = localStorage.getItem('userToken');
    }
    async sendQuery(graphqlQuery, userToken = '') {
        try {
            const data = await $.ajax({
                url: 'http://laravel-gernzy.test/graphql',
                contentType: 'application/json',
                type: 'POST',
                headers: {
                    Authorization: `Bearer ${userToken}`,
                },
                data: JSON.stringify({
                    query: graphqlQuery,
                }),
            });
            return data;
        } catch (err) {
            return err;
        }
    }
}
export { GraphqlService };
