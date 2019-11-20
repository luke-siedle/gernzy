<?php

namespace Lab19\Cart\Testing;

use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Lab19\Cart\Testing\Seeds\UsersSeeder;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use MakesGraphQLRequests;
    use RefreshDatabase;
    use DatabaseMigrations;

    protected $sessionToken = null;

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app->useEnvironmentPath(__DIR__ . '/..');
        $app->bootstrapWith([LoadEnvironmentVariables::class]);
        parent::getEnvironmentSetUp($app);
    }

    protected function getPackageProviders($app)
    {
        // Setup required packages
        return [
            'Lab19\\Cart\\CartServiceProvider',
        ];
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate', ['--database' => 'testbench'])->run();
        $this->loadLaravelMigrations(['--database' => 'testbench']);
        $this->withFactories(dirname(__DIR__) . '/database/factories');
        $this->seed(UsersSeeder::class);
        $this->withoutExceptionHandling();
    }

    public function graphQLWithSession(String $query)
    {
        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        if (!$this->sessionToken) {
            $response = $this->graphQL('
                    mutation {
                        createSession {
                            token
                        }
                    }
                ');

            $result = $response->decodeResponseJson();
            $this->sessionToken = $result['data']['createSession']['token'];
        }

        return $this->postGraphQL(['query' => $query], [
            'HTTP_Authorization' => 'Bearer ' . $this->sessionToken,
        ]);
    }

    public function graphQLCreateAccountWithSession($email = 'test@test.com', $password = 'password', $token = null)
    {

        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        $response = $this->postGraphQL(['query' => '
                mutation {
                    createAccount(input: {
                        email:"' . $email . '",
                        password: "' . $password . '",
                        name: "Luke"
                        }) {
                        token
                        user {
                            name
                            email
                            id
                        }
                    }
                }
            ', ], [
            'HTTP_Authorization' => 'Bearer ' . ($token ?? $this->sessionToken),
        ]);

        return $response;
    }

    /**
     * Send a multipart form request to GraphQL.
     *
     * This is used for file uploads conforming to the specification:
     * https://github.com/jaydenseric/graphql-multipart-request-spec
     *
     * @param  mixed[]  $parameters
     * @param  mixed[]  $files
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    protected function multipartGraphQLWithSession(array $parameters, array $files): TestResponse
    {
        return $this->call(
            'POST',
            $this->graphQLEndpointUrl(),
            $parameters,
            [],
            $files,
            $this->transformHeadersToServerVars([
                'Content-Type' => 'multipart/form-data',
                'Authorization' => 'Bearer ' . $this->sessionToken,
            ])
        );
    }
}
