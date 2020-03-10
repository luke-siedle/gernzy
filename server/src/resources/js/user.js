class User {
    constructor(graphqlService) {
        this.graphqlService = graphqlService;
    }
    createSession() {
        let query = `mutation {
            createSession {
                token
            }
        }`;

        return this.graphqlService
            .sendQuery(query)
            .then(re => {
                let token = re.data.createSession.token;
                this.addSessionTokenToLocalStorage(token);
                return re;
            })
            .catch(error => {
                console.log(error);
            });
    }

    addSessionTokenToLocalStorage(token) {
        localStorage.setItem('userToken', token);
    }

    checkIfTokenInLocalStorage() {
        let userTokenLocalStorage = localStorage.getItem('userToken');
        // Check if token not already in local storage, and if not add to localStorage
        if (userTokenLocalStorage) {
            return true;
        } else {
            return false;
        }
    }

    checkTokenExistsInDatabase() {
        let userTokenLocalStorage = localStorage.getItem('userToken');

        let query = ` {
            me {
                session {
                    id
                    token
                }
            }
        }`;

        return this.graphqlService.sendQuery(query, userTokenLocalStorage);
    }
}
export { User };
