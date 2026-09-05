<?php
// whois.php — pure utility, no includes, no DB side-effects

/**
 * Normalize an IP: trim, take first if comma-separated, strip IPv6-mapped prefix.
 */
function normalize_ip($ip) {
    $ip = urldecode(trim((string)$ip));
    if (strpos($ip, ',') !== false) {
        $parts = array_map('trim', explode(',', $ip));
        $ip = $parts[0];
    }
    // Strip IPv6-mapped IPv4 prefix if present, e.g. ::ffff:1.2.3.4
    $ip = preg_replace('/^::ffff:/i', '', $ip);
    return $ip;
}

/**
 * Return IP whois/geo info as an assoc array or an error string.
 * Uses https://ipwho.is/ (free, HTTPS, no key).
 */
function get_ipwhois_data($ip) {
    $ip = normalize_ip($ip);

    if ($ip === '' || !filter_var($ip, FILTER_VALIDATE_IP)) {
        return "Invalid IP address.";
    }

    // Private/reserved ranges: don't call external API
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
        return [
            'IP'        => $ip,
            'Type'      => strpos($ip, ':') !== false ? 'IPv6 (private/reserved)' : 'IPv4 (private/reserved)',
            'Note'      => 'Private/reserved address; external whois skipped.',
        ];
    }

    $url = "https://ipwho.is/" . rawurlencode($ip);

    // Prefer cURL if available
    $response = false;
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT        => 4,
            CURLOPT_USERAGENT      => 'MHM-Whois/1.0',
        ]);
        $response = curl_exec($ch);
        if ($response === false) {
            $err = curl_error($ch);
            curl_close($ch);
            return "Error contacting whois service: $err";
        }
        curl_close($ch);
    } else {
        // Fallback to file_get_contents with short timeout
        $ctx = stream_context_create([
            'http' => ['timeout' => 4, 'header' => "User-Agent: MHM-Whois/1.0\r\n"],
            'https'=> ['timeout' => 4, 'header' => "User-Agent: MHM-Whois/1.0\r\n"],
        ]);
        $response = @file_get_contents($url, false, $ctx);
        if ($response === false) {
            return "Error contacting whois service.";
        }
    }

    $data = json_decode($response, true);
    if (!is_array($data) || empty($data['success'])) {
        // ipwho.is includes a "message" on failure
        $msg = is_array($data) && isset($data['message']) ? $data['message'] : 'Unknown error';
        return "Whois lookup failed: " . $msg;
    }

    // Map interesting fields; add others as you like
    return [
        'IP'         => $data['ip']        ?? $ip,
        'Type'       => $data['type']      ?? '',
        'Continent'  => $data['continent'] ?? '',
        'Country'    => $data['country']   ?? '',
        'CountryCode'=> $data['country_code'] ?? '',
        'Region'     => $data['region']    ?? '',
        'City'       => $data['city']      ?? '',
        'Latitude'   => $data['latitude']  ?? '',
        'Longitude'  => $data['longitude'] ?? '',
        'ASN'        => $data['connection']['asn']  ?? '',
        'ORG'        => $data['connection']['org']  ?? '',
        'ISP'        => $data['connection']['isp']  ?? '',
        'Timezone'   => $data['timezone']['id']     ?? '',
    ];
}
