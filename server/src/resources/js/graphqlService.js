import $ from 'jquery';

class GraphqlService {
    constructor(config) {
        this.userToken = localStorage.getItem('userToken');
        this.config = config;
    }
    async sendQuery(graphqlQuery, userToken = '') {
        try {
            const data = await $.ajax({
                url: this.config.apiUrl,
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
