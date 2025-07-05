<?php

namespace Classes;

class GoogleAnalyticsEvent
{
    private const GA_COOKIE_NAME = '_ga';
    private const CLIENT_ID_MIN = 1000000000;
    private const CLIENT_ID_MAX = 9999999999;
    private const ENGAGEMENT_TIME_MS = 100;

    private string $analyticsUrl = 'https://region1.google-analytics.com/mp/collect';
    private string $apiSecret = '';
    private string $measurementId = '';

    private string $clientId;
    private string $sessionId;

    public function __construct()
    {
        $this->setClientId();
        $this->setSessionId();
    }

    /**
     * Extract or generate the Google Analytics client ID.
     *
     * The client ID is a unique identifier that Google Analytics generates for
     * a browser when a user visits your website. The client ID is generated
     * randomly, and its primary purpose is to distinguish between new and
     * returning visitors. It is a required field for event data. Client ID
     * example: 1234567890.1234567890
     *
     * The client ID is found in the _ga cookie from the sixth character.
     * _ga cookie example: GA1.1.1234567890.1234567890
     *
     * If there is no client ID (e.g. cookies are blocked or the visitor is a
     * bot) then we have to generate one. This will allow events to be sent but
     * they will appear to come from different users each time.
     */
    protected function setClientId(): void
    {
        $this->clientId = !empty($_COOKIE[self::GA_COOKIE_NAME])
            ? substr($_COOKIE[self::GA_COOKIE_NAME], 6)
            : sprintf('%d.%d', rand(self::CLIENT_ID_MIN, self::CLIENT_ID_MAX), time());
    }

    /**
     * Extract or generate the session ID.
     *
     * The session ID is automatically generated as the timestamp in seconds
     * when a session starts. It is a required field for event data. Session ID
     * example: 1234567890
     *
     * The session ID is found in a Google Analytics cookie whose name is
     * derived from your measurement ID (which is a unique identifier for a data
     * stream within a Google Analytics 4 property), e.g. _ga_ABCDE12FGH
     *
     * The cookie contents look like GS2.1.s1234567890$f27$g0$t1234567890$p57$k0$n0
     * with the session ID from the seventh character.
     *
     * If there is no session ID (e.g. cookies are blocked or the visitor is a
     * bot) then we have to generate one. This will allow events to be sent but
     * they will not be tracked to the a valid session.
     */
    protected function setSessionId(): void
    {
        $cookieName = 'COOKIE._ga_' . substr($this->measurementId, 2);

        $this->sessionId = !empty($_COOKIE[$cookieName]) ? substr($_COOKIE[$cookieName], 7, 10) : (string) time();
    }

    /**
     * Build the full GA4 event data payload.
     *
     * Take the event name and unique parameters and combine them with other
     * standard required event elements to form the complete array.
     *
     * A default engagement time is required, and the IP of the visitor is
     * provided.
     *
     * @param string $name   Name of the GA4 event.
     * @param array  $params Custom event parameters.
     *
     * @return string JSON-encoded payload.
     */
    protected function buildPostData(string $name, array $params): string
    {
        $params['session_id'] = $this->sessionId;
        $params['engagement_time_msec'] = self::ENGAGEMENT_TIME_MS;

        $payload = [
            'client_id'   => $this->clientId,
            'ip_override' => $_SERVER['REMOTE_ADDR'],
            'events'      => [
                [
                    'name'   => $name,
                    'params' => $params,
                ],
            ],
        ];

        return json_encode($payload, JSON_THROW_ON_ERROR);
    }

    /**
     * Send the GA4 event to the Measurement Protocol endpoint.
     *
     * Use cURL to send the event to the Google Analytics endpoint as a POST
     * request with a JSON payload.
     *
     * @param string $name   Name of the event.
     * @param array  $params Custom event parameters.
     */
    public function sendEvent(string $name, array $params): void
    {
        try {
            $postData = $this->buildPostData($name, $params);
            $url = sprintf(
                '%s?api_secret=%s&measurement_id=%s',
                $this->analyticsUrl,
                urlencode($this->apiSecret),
                urlencode($this->measurementId)
            );

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_POST            => true,
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_HTTPHEADER      => ['Content-Type: application/json'],
                CURLOPT_POSTFIELDS      => $postData,
                CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                throw new \RuntimeException('cURL error: ' . curl_error($ch));
            }

            if ($httpCode !== 204) {
                throw new \RuntimeException("GA4 request failed. HTTP $httpCode: $response");
            }

            curl_close($ch);
        } catch (\Throwable $e) {
            error_log('GoogleAnalytics error: ' . $e->getMessage());
        }
    }
}
