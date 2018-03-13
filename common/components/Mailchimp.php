<?php

namespace common\components;

class Mailchimp extends \yii\authclient\OAuth2 
{
	public $clientId = '516771125048';
	public $clientSecret = 'e7b334b60a6421c0ca2d57b189e8a54458de1426ea2aa7fe23';

	public $metadata = 'https://login.mailchimp.com/oauth2/metadata';

    /**
     * {@inheritdoc}
     */
    public $authUrl = 'https://login.mailchimp.com/oauth2/authorize';
    /**
     * {@inheritdoc}
     */
    public $tokenUrl = 'https://login.mailchimp.com/oauth2/token';
    /**
     * {@inheritdoc}
     */
    public $apiBaseUrl = 'https://login.mailchimp.com';

    protected function defaultName()
    {
        return 'mailchimp_auth_client';
    }

    protected function defaultTitle()
    {
        return 'MailChimp Auth Client';
    }

    /**
     * Initializes authenticated user attributes.
     * @return array auth user attributes.
     */
    protected function initUserAttributes()
    {
        $accessToken = $this->getAccessToken();

        try {
            $attributes = $this->api('oauth2/metadata', 'GET', [], [
                'Authorization' => 'OAuth ' . $accessToken->getToken()
            ]);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        return $attributes;
    }
}