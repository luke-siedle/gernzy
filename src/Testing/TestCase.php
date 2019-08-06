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
            $this->seed(UsersSeeder::class);
            $this->withoutExceptionHandling();
        }
    }

