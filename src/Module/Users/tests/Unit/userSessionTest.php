<?php
    use Lab19\Cart\Testing\TestCase;

    class TestUserSession extends TestCase
    {
        public function testUserCanInitiateAndResumeSession(): void
        {
            /** @var \Illuminate\Foundation\Testing\TestResponse $response */
            $response = $this->graphQL('
                mutation {
                    createSession {
                        token
                    }
                }
            ');

            $response->assertDontSee('errors');

            $response->assertJsonStructure([
                'data' => [ 'createSession' => ['token'] ]
            ]);

            $start = $response->decodeResponseJson();

            $this->assertNotNull( $start['data']['createSession']['token'] );

            $response = $this->graphQL('
                mutation {
                    createSession {
                        token
                    }
                }
            ');

            $resume = $response->decodeResponseJson();

            $this->assertNotNull( $resume['data']['createSession']['token'] );

            $this->assertSame(
                $start['data']['createSession']['token'],
                $resume['data']['createSession']['token']
            );
        }
    }
