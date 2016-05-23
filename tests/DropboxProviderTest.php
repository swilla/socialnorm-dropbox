<?php

use Mockery as M;
use SocialNorm\Dropbox\DropboxProvider;
use SocialNorm\Request;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Client as HttpClient;

class DropboxProviderTest extends TestCase
{
    private function getStubbedHttpClient($fixtures = [])
    {
        $mock = new MockHandler($this->createResponses($fixtures));
        $handler = HandlerStack::create($mock);
        return new HttpClient(['handler' => $handler]);
    }

    private function createResponses($fixtures)
    {
        $responses = [];
        foreach ($fixtures as $fixture) {
            $response = require $fixture;
            $responses[] = new Response($response['status'], $response['headers'], $response['body']);
        }

        return $responses;
    }

    /** @test */
    public function it_can_retrieve_a_normalized_user()
    {
        $client = $this->getStubbedHttpClient([
            __DIR__ . '/_fixtures/dropbox_accesstoken.php',
            __DIR__ . '/_fixtures/dropbox_user.php',
        ]);

        $provider = new GoogleProvider([
            'client_id' => 'abcdefgh',
            'client_secret' => '12345678',
            'redirect_uri' => 'http://example.com/login',
        ], $client, new Request(['code' => 'abc123']));

        $user = $provider->getUser();

        $this->assertEquals('1308380', $user->id);
        $this->assertEquals('billy@williams.com', $user->nickname);
        $this->assertEquals('Billy Williams', $user->display_name);
        $this->assertEquals('billy@williams.com', $user->email);
        $this->assertEquals(null, $user->avatar);
        $this->assertEquals('XLCGq5EmuDkGGGGGGUUIOL4JlCVr_p-7001P7fa2zX3KYbQAEOQm7NDK9rqgw', $user->access_token);
    }

    /**
     * @test
     * @expectedException SocialNorm\Exceptions\ApplicationRejectedException
     */
    public function it_fails_to_retrieve_a_user_when_the_authorization_code_is_omitted()
    {
        $client = $this->getStubbedHttpClient([
            __DIR__ . '/_fixtures/dropbox_accesstoken.php',
            __DIR__ . '/_fixtures/dropbox_user.php',
        ]);

        $provider = new GoogleProvider([
            'client_id' => 'abcdefgh',
            'client_secret' => '12345678',
            'redirect_uri' => 'http://example.com/login',
        ], $client, new Request([]));

        $user = $provider->getUser();
    }
}
