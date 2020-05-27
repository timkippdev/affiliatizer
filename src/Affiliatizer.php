<?php

namespace TimKippDev\Affiliatizer;

class Affiliatizer {

    const AFFILIATIZER_TYPE_REPLACEMENT = 'replace';
    const AFFILIATIZER_TYPE_REDIRECT = 'redirect';

    private static $AFFILIATE_DATA_TYPE = 'type';
    private static $AFFILIATE_DATA_DESTINATION = 'destination';
    private static $AFFILIATE_DATA_REPLACERS = 'replacers';

    // holds data that maps a given domain with its affiliate data replacement values
    private $affiliateDataMap = [];

    /**
     * @param array $affiliateDataMap
     * 
     * @example 
     * [
     *     'replace.com' => [ 'type' => 'replace', 'replacers' => [ 'subtag' => 'timkipp' ] ],
     *     'redirect.com' => [ 'type' => 'redirect', 'destination' => 'https://sendmehere.com?url=<URL>' ]
     * ]
     */
    public function __construct(array $affiliateDataMap)
    {
        $this->validateAffiliateDataMap($affiliateDataMap);
        $this->affiliateDataMap = $affiliateDataMap;
    }

    /**
     * takes a given url and adds affiliate onto to the query parameters based on the configured
     * affiliateDataMap values defined in the initial class constructor
     * 
     * @param string $url
     * @return string
     */
    public function affiliatizeUrl($originalUrl)
    {
        $urlParts = parse_url($originalUrl);

        // if url cannot for parsed, return original url
        if (is_null($urlParts) || !$urlParts)
        {
            return $originalUrl;
        }

        $host = str_ireplace('www.', '', strtolower($urlParts['host']));

        // get affiliate info for host
        $affiliateInfo = $this->getAffiliateInfoForHost($host);

        if (is_null($affiliateInfo))
        {
            return $originalUrl;
        }

        $queryParams = [];

        // parse existing url query string into query params array
        if (array_key_exists('query', $urlParts))
        {
            parse_str($urlParts['query'], $queryParams);
        }

        $finalUrl = $originalUrl;

        switch ($affiliateInfo['type'])
        {
            case self::AFFILIATIZER_TYPE_REPLACEMENT:
                // add affiliate info to query parameters
                $replacers = $affiliateInfo[self::$AFFILIATE_DATA_REPLACERS];
                foreach ($replacers as $key => $value)
                {
                    $queryParams[$key] = $value;
                }
                $finalUrl = $this->buildUrlWithQueryParams($urlParts, $queryParams);
            break;
            case self::AFFILIATIZER_TYPE_REDIRECT:
                $finalUrl = $this->buildRedirectUrl($originalUrl, $affiliateInfo['destination']);
                break;
        }

        return $finalUrl;
    }

    /**
     * builds redirect url by injecting the original url into the destination url
     * 
     * @param string $originalUrl
     * @param string $destinationUrl
     * @return string
     */
    private function buildRedirectUrl($originalUrl, $destinationUrl)
    {
        // replace <URL> in the destination url with the url encoded original url
        return str_replace('<URL>', urlencode($originalUrl), $destinationUrl);
    }

    /**
     * builds a url with the original url parts and the query parameters
     * 
     * @param array $urlParts
     * @param array $queryParams
     * @return string
     */
    private function buildUrlWithQueryParams(array $urlParts, array $queryParams)
    {
        // rebuild the url from parts
        $url = $urlParts['scheme'] . '://' . $urlParts['host'] . $urlParts['path'];

        // append the query parameters back onto the url
        if (count($queryParams) > 0)
        {
            $urlParts['query'] = http_build_query($queryParams);
            $url .= '?' . $urlParts['query'];
        }

        return $url;
    }

    /**
     * returns the affiliate into for the given host
     * 
     * @param string $host
     * @return array|null
     */
    private function getAffiliateInfoForHost($host)
    {
        // check for exact host match
        if (array_key_exists($host, $this->affiliateDataMap))
        {
            return $this->affiliateDataMap[$host];
        }

        // check for partial host match to handle scenarios like subdomains
        foreach ($this->affiliateDataMap as $domain => $data)
        {
            if (stripos($host, $domain) !== false)
            {
                return $this->affiliateDataMap[$domain];
            }
        }

        return null;
    }

    /**
     * validate the affiliateDataMap to ensure all domain values are valid and all data values are present
     * 
     * @param array $affiliateDataMap
     * @throws \Exception
     */
    private function validateAffiliateDataMap(array $affiliateDataMap)
    {
        foreach ($affiliateDataMap as $domain => $data)
        {
            if (!is_string($domain) || trim($domain) == '')
            {
                throw new \Exception('invalid affiliate domain key: ' . $domain);
            }

            if (!is_array($data))
            {
                throw new \Exception('affiliate data must be an array for domain: ' . $domain);
            }

            if (!array_key_exists(self::$AFFILIATE_DATA_TYPE, $data))
            {
                throw new \Exception('affiliate data array must contain a key named "' . self::$AFFILIATE_DATA_TYPE . '" for domain: ' . $domain);
            }

            $type = $data[self::$AFFILIATE_DATA_TYPE];

            if ($type == self::AFFILIATIZER_TYPE_REPLACEMENT && (!array_key_exists(self::$AFFILIATE_DATA_REPLACERS, $data) || !is_array($data[self::$AFFILIATE_DATA_REPLACERS])))
            {
                throw new \Exception('affiliate data array must contain a key named "' . self::$AFFILIATE_DATA_REPLACERS . '" and an array of data for domain: ' . $domain);
            }
            else if ($type == self::AFFILIATIZER_TYPE_REDIRECT && !array_key_exists(self::$AFFILIATE_DATA_DESTINATION, $data))
            {
                throw new \Exception('affiliate data array must contain a key named "' . self::$AFFILIATE_DATA_DESTINATION . '" for domain: ' . $domain);
            }
        }
    }

}