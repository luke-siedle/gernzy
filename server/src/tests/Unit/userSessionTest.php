<?php
    use Gernzy\Server\Testing\TestCase;

    /**
     * @group Session
     */
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

            $token = $start['data']['createSession']['token'];

            $this->assertNotNull( $token );

            $response = $this->postGraphQL(['query' => '
                {
                    me {
                        session {
                            token
                        }
                    }
                }
            '], [
                'HTTP_Authorization' => 'Bearer ' . $token
            ]);

            $resume = $response->decodeResponseJson();

            $this->assertNotNull( $resume['data']['me']['session']['token'] );

            $this->assertSame(
                $token,
                $resume['data']['me']['session']['token']
            );
        }
    }
