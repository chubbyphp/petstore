<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Tests\IntegrationTestListener;
use PHPUnit\Framework\TestCase;

abstract class AbstractIntegrationTest extends TestCase
{
    /**
     * @var string
     */
    const DEFAULT_INTEGRATION_ENDPOINT = 'http://localhost:%d/index_ci.php';

    const UUID_PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/';

    /**
     * @var resource
     */
    private $curl;

    /**
     * @param string      $method
     * @param string      $resource
     * @param array       $headers
     * @param string|null $body
     *
     * @throws \RuntimeException
     *
     * @return array
     */
    protected function httpRequest(string $method, string $resource, array $headers = [], string $body = null): array
    {
        $curlHeaders = [];
        foreach ($headers as $key => $value) {
            $curlHeaders[] = sprintf('%s: %s', $key, implode(', ', (array) $value));
        }

        if (null === $this->curl) {
            $this->curl = $this->initializeCurl();
        }

        curl_setopt($this->curl, CURLOPT_URL, sprintf($this->getEndpoint().'%s', $resource));
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $curlHeaders);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $body);

        $rawResponse = curl_exec($this->curl);
        if (false === $rawResponse) {
            $info = curl_getinfo($this->curl);
            $error = curl_error($this->curl);
            throw new \RuntimeException('Invalid response from server! '.print_r($info, true).PHP_EOL.$error);
        }

        $headerSize = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);

        $headerRows = $this->getHttpHeaderRows($rawResponse, $headerSize);

        $status = $this->getHttpStatus(array_shift($headerRows));
        $headers = $this->geHttpHeaders($headerRows);

        $body = substr($rawResponse, $headerSize);

        $errorBody = '';
        if (500 === $status['code']) {
            $errorBody = json_encode(json_decode($body, true), JSON_PRETTY_PRINT);
        }

        self::assertNotSame(500, $status['code'], $errorBody);

        if ('' === $body) {
            $body = null;
        }

        return ['status' => $status, 'headers' => $headers, 'body' => $body];
    }

    /**
     * @return resource
     */
    private function initializeCurl()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        return $ch;
    }

    /**
     * @param string $rawResponse
     * @param int    $headerSize
     *
     * @return array
     */
    private function getHttpHeaderRows(string $rawResponse, int $headerSize): array
    {
        $headerRawGroups = explode("\r\n\r\n", trim(substr($rawResponse, 0, $headerSize)));

        return explode("\r\n", end($headerRawGroups));
    }

    /**
     * @param string $statusRow
     *
     * @return array
     */
    private function getHttpStatus(string $statusRow): array
    {
        $matches = [];
        preg_match('#^HTTP/1.\d{1} (\d+) (.+)$#', $statusRow, $matches);

        return [
            'code' => (int) $matches[1],
            'message' => $matches[2],
        ];
    }

    /**
     * @param array $headerRows
     *
     * @return array
     */
    private function geHttpHeaders(array $headerRows): array
    {
        $headers = [];

        foreach ($headerRows as $headerRow) {
            if (false === $pos = strpos($headerRow, ':')) {
                continue;
            }

            $key = strtolower(trim(substr($headerRow, 0, $pos)));
            $value = trim(substr($headerRow, $pos + 1));

            if ('' === $value) {
                continue;
            }

            if (!isset($headers[$key])) {
                $headers[$key] = [];
            }

            $headers[$key][] = $value;
        }

        ksort($headers);

        return $headers;
    }

    /**
     * @return string
     */
    private function getEndpoint(): string
    {
        $integrationEndpoint = getenv(IntegrationTestListener::ENV_INTEGRATION_ENDPOINT);

        if (false !== $integrationEndpoint) {
            return $integrationEndpoint;
        }

        return sprintf(self::DEFAULT_INTEGRATION_ENDPOINT, IntegrationTestListener::PHP_SERVER_PORT);
    }
}
