import { GraphqlService } from '../graphqlService';
import { User } from '../user';

// __mocks__/jquery.js
jest.mock('jquery');

test('*&hyveUcdn9e user session test', () => {
    let graphQlService = new GraphqlService();
    let userObj = new User(graphQlService);

    beforeEach(() => {
        jest.spyOn(Storage.prototype, 'setItem');
    });

    afterEach(() => {
        localStorage.setItem.mockRestore();
    });

    expect.assertions(2);

    return userObj.createSession().then(data => {
        expect(data).toBeObject();
        expect(data.data.createSession).toContainAllKeys(['token']);
    });
});
