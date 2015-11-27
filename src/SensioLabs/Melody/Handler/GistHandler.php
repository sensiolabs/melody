<?php

namespace SensioLabs\Melody\Handler;

use SensioLabs\Melody\Composer\Composer;
use SensioLabs\Melody\Exception\AuthenticationRequiredException;
use SensioLabs\Melody\Exception\InvalidCredentialsException;
use SensioLabs\Melody\Handler\Github\Gist;
use SensioLabs\Melody\Resource\Resource;
use SensioLabs\Melody\Resource\Metadata;
use SensioLabs\Melody\Security\AuthenticationStorage;

/**
 * Class GistHandler.
 *
 * @author Charles Sarrazin <charles@sarraz.in>
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 * @author Jérémy Derussé <jeremy@derusse.com>
 */
class GistHandler implements ResourceHandlerInterface, AuthenticableHandlerInterface
{
    private $authenticationStorage;

    public function __construct(AuthenticationStorage $authenticationStorage)
    {
        $this->authenticationStorage = $authenticationStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($uri)
    {
        return 0 !== preg_match(Gist::URI_PATTERN, $uri);
    }

    /**
     * {@inheritdoc}
     */
    public function createResource($filename)
    {
        $gist = new Gist($filename, $this->getOAuthToken());
        $data = $gist->get();
        $content = $data['content'];
        $status = $data['status'];

        if (200 !== $status) {
            if (in_array($status, array(401, 403))) {
                throw new AuthenticationRequiredException($this, $content['message']);
            }

            $message = 'There is an issue with your gist URL: ';
            if (array_key_exists('message', $content)) {
                throw new \InvalidArgumentException($message.$content['message']);
            }

            throw new \InvalidArgumentException($message.'Expected 200 status, got '.$status);
        }

        $files = $content['files'];

        // Throw an error if the gist contains multiple files
        if (1 !== count($files)) {
            throw new \InvalidArgumentException('The gist should contain a single file');
        }

        // Fetch the only element in the array
        $file = current($files);
        $metadata = new Metadata(
            $content['id'],
            $content['owner']['login'],
            new \DateTime($content['created_at']),
            new \DateTime($content['updated_at']),
            count($content['history']),
            $content['html_url']
        );

        return new Resource($file['content'], $metadata);
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredCredentials()
    {
        return array(
            'username' => self::CREDENTIALS_NORMAL,
            'password' => self::CREDENTIALS_SECRET,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(array $credentials)
    {
        if (empty($credentials['username']) || empty($credentials['password'])) {
            throw new InvalidCredentialsException('You should provide non-empty "username" and "password" information.');
        }

        $handle = curl_init();

        $payload = json_encode(array(
            'scopes' => array('public_repo'),
            'note' => sprintf('Melody on %s %s', gethostname(), date('Y-m-d Hi')),
        ));

        curl_setopt_array($handle, array(
            CURLOPT_URL => 'https://api.github.com/authorizations',
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => array(
                'Accept: application/vnd.github.v3+json',
                'User-Agent: Melody-Script',
                'Content-Type: application/json',
                'Content-Length: '.strlen($payload),
            ),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_USERPWD => sprintf('%s:%s', $credentials['username'], $credentials['password']),
        ));

        if ($http_proxy = filter_input(INPUT_ENV, 'HTTPS_PROXY', FILTER_SANITIZE_URL)) {
            curl_setopt($handle, CURLOPT_PROXY, $http_proxy);
        }

        $content = curl_exec($handle);
        curl_close($handle);

        $response = json_decode($content, true);

        if (!isset($response['token'])) {
            throw new InvalidCredentialsException(isset($response['message']) ? $response['message'] : 'Unable to get token.');
        }

        return array('token' => $response['token']);
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return 'gist';
    }

    /**
     * Try to retrieve a token from user config or composer's auth.json file.
     *
     * @return null|string
     */
    private function getOAuthToken()
    {
        $authData = $this->authenticationStorage->get($this->getKey());
        if (isset($authData['token'])) {
            return $authData['token'];
        }

        if (file_exists($path = Composer::getComposerHomeDir().'/auth.json')) {
            $authJson = json_decode(file_get_contents($path), true);
            if (isset($authJson['github-oauth']['github.com'])) {
                return $authJson['github-oauth']['github.com'];
            }
        }
    }
}
