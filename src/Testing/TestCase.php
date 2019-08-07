<?php

    namespace Lab19\Cart\Testing;

    use Orchestra\Testbench\TestCase as BaseTestCase;
    use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
    use Illuminate\Foundation\Testing\DatabaseMigrations;
    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Lab19\Cart\Module\Users\Seeds\UsersSeeder;

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
                'driver'   => 'sqlite',
                'database' => ':memory:',
                'prefix'   => '',
            ]);
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
            $this->withFactories(dirname(__DIR__) . '/Module/Users/factories');
            $this->withFactories(dirname(__DIR__) . '/Module/Products/factories');
            $this->seed(UsersSeeder::class);
            $this->withoutExceptionHandling();
        }

        public function graphQLWithSession( String $query ){
            /** @var \Illuminate\Foundation\Testing\TestResponse $response */
            if( !$this->sessionToken ){
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
                'HTTP_Authorization' => 'Bearer ' . $this->sessionToken
            ]);
        }
    }

