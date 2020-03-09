import $ from 'jquery';
import { User } from './user';

class SessionService {
    constructor(graphqlService) {
        this.graphqlService = graphqlService;
    }

    setUpSessionData() {
        var userToken = localStorage.getItem('userToken');

        let query = `{
            me {
                session {
                    data
                }
            }
        }`;

        return this.graphqlService.sendQuery(query, userToken).then(re => {
            localStorage.setItem('sessionData', re.data.me.session.data);

            if (re.data.me.session.data[1]) {
                localStorage.setItem('currency', re.data.me.session.data[1]);
            }
        });
    }

    setUpShopConfig() {
        var userToken = localStorage.getItem('userToken');

        let query = `
            query {
                shopConfig {
                    enabled_currencies
                    default_currency
                }
            }
        `;

        return this.graphqlService.sendQuery(query, userToken).then(re => {
            re.data.shopConfig.enabled_currencies.forEach(element => {
                $('#available-currencies').append(
                    `<li><a href='#' class='available-currency' data-currency="${element}">${element}</a></li>`,
                );
            });

            $('.available-currency').on('click', this.changeUserCurrency.bind(this));

            localStorage.setItem('default_currency', re.data.shopConfig.default_currency);
        });
    }

    setUpGeoLocation() {
        var userToken = localStorage.getItem('userToken');

        let query = `
            mutation {
                setSessionGeoLocation {
                    geolocation_record
                }
            }
        `;

        return this.graphqlService.sendQuery(query, userToken).then(re => {
            let error = re.errors[0].debugMessage;
            if (!error) {
                localStorage.setItem('setSessionGeoLocation', re.data.setSessionGeoLocation.geolocation_record);
            } else {
                // handle error
                console.log(error);
            }
        });
    }

    changeUserCurrency(event) {
        var userToken = localStorage.getItem('userToken');
        let currrency = $(event.target).attr('data-currency');

        let query = `
            mutation {
                setSessionCurrency(input: {
                    currency: "${currrency}"
                }){
                    currency
                }
            }
        `;

        return this.graphqlService.sendQuery(query, userToken).then(re => {
            try {
                // See if there is an error
                let error = re.errors[0].debugMessage;
                console.log(error);
            } catch {
                localStorage.setItem('currency', re.data.setSessionCurrency.currency);

                // A lot of state will need to change with the currency changing, and for now an easy fix is just to force
                // a reload
                location.reload(true);
            }
        });
    }

    setupUser() {
        // Session object in localStorage if it doesn't already exist, and verify
        let userObj = new User(this.graphqlService);
        if (!userObj.checkIfTokenInLocalStorage()) {
            userObj.createSession();
        } else {
            userObj.checkTokenExistsInDatabase().then(re => {
                try {
                    if (re.errors[0].debugMessage == 'Cannot return null for non-nullable field Session.id.') {
                        // Recreate session object
                        userObj.createSession();
                    }
                } catch (error) {
                    // No error exist
                }
            });
        }
    }
}
export { SessionService };
