<?php

namespace App\Services\SMS;

use Illuminate\Support\Facades\Log;

class IPPanelService
{
    protected string $username;

    protected string $password;

    protected string $wsdl = 'http://ippanel.com/class/sms/wsdlservice/server.php?wsdl';

    public function __construct()
    {
        $this->username = config('services.ippanel.username') ?? 'mock_user';
        $this->password = config('services.ippanel.password') ?? 'mock_pass';
    }

    /**
     * Send SMS using a configured pattern name (e.g., 'otp').
     *
     * @param  string  $patternName  The key defined in services.ippanel.patterns
     * @param  string  $recipient  Phone number
     * @param  array  $values  Associative array of pattern variables
     * @return array
     */
    public function send(string $patternName, string $recipient, array $values)
    {
        $patternCode = config("services.ippanel.patterns.{$patternName}");

        if (!$patternCode) {
            Log::error("IPPanel Service: Pattern name '{$patternName}' not found in configuration.");
            if (app()->isLocal()) {
                $patternCode = 'mock_pattern_' . $patternName;
            } else {
                return ['status' => 'ERROR', 'message' => "Pattern '$patternName' not configured"];
            }
        }

        $originator = config('services.ippanel.originator');

        return $this->sendPattern($patternCode, $originator, $recipient, $values);
    }

    public function sendPattern(string $patternCode, string $originator, string $recipient, array $values)
    {
        // if (app()->isLocal()) {
        //     Log::info("SMS Mock (SOAP): Sending pattern $patternCode to $recipient with values: " . json_encode($values));
        //     return ['status' => 'OK', 'message' => 'Mock sent'];
        // }

        try {
            $url = 'https://ippanel.com/patterns/pattern';

            $to = [$recipient];
            $input_data = $values;

            $queryParams = [
                'username' => $this->username,
                'password' => $this->password,
                'from' => $originator,
                'to' => json_encode($to),
                'input_data' => json_encode($input_data),
                'pattern_code' => $patternCode,
            ];

            $queryString = http_build_query($queryParams);
            // http_build_query automatically urlencodes values, so we don't need manual urlencode(json_encode(...)) if we pass the string.
            // However, the reference did: urlencode(json_encode($input_data)).
            // http_build_query will do: input_data=urlencoded_json_string. This matches.

            $fullUrl = $url . '?' . $queryString;

            $handler = curl_init($fullUrl);
            curl_setopt($handler, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($handler, CURLOPT_POSTFIELDS, $input_data); // The reference sends input_data as POST fields too?
            curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handler, CURLOPT_TIMEOUT, 30);
            curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($handler);
            $httpCode = curl_getinfo($handler, CURLINFO_HTTP_CODE);
            $error = curl_error($handler);
            curl_close($handler);

            if ($error) {
                Log::error('IPPanel cURL Error: ' . $error);

                return ['status' => 'ERROR', 'message' => $error];
            }

            // Result is usually a bulk ID (integer) or error code
            // The reference code checks for 200 OK.

            if ($httpCode === 200) {
                // Check if response is a number (bulk ID)
                if (is_numeric($response)) {
                    return ['status' => 'OK', 'message_id' => $response];
                }
                // Some IPPanel responses might be just the ID, or JSON.
                // Reference implementation decodes JSON. Let's try that.
                $decoded = json_decode($response, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return ['status' => 'OK', 'data' => $decoded];
                }

                // If not JSON, maybe just the ID
                return ['status' => 'OK', 'message_id' => $response];
            } else {
                Log::error('IPPanel HTTP Error ' . $httpCode . ': ' . $response);

                return ['status' => 'ERROR', 'message' => "HTTP $httpCode: $response"];
            }

        } catch (\Exception $e) {
            Log::error('IPPanel Exception: ' . $e->getMessage());

            return ['status' => 'ERROR', 'message' => $e->getMessage()];
        }
    }
}
