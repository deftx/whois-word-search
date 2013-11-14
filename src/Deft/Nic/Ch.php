<?php

namespace Deft\Nic;

/**
 * .CH TLD
 *
 * @package Deft\Nic
 * @author Rob Vella <me@robvella.com>
 */
class Ch extends \Deft\Nic {
    /**
     * URL for CURL below
     */
    const URL = "http://whois.europeregistry.com/whoisengine/request/whoisinfo.php?security_code=null&domain_name=";

    const REGEX = "/^[A-Za-z0-9\-]{3,}\.ch$/";

    /**
     * Whether this TLD uses CURL (auto-init in base class)
     */
    const USE_CURL = true;

    /**
     * Rate limit sleep second increment
     */
    const SLEEP_INC = 10;

    /**
     * How long to sleep when rate limited (incremented below)
     * @var int
     */
    protected $sleep = 0;

    /**
     * Determine if domain registered
     *
     * @param $whois
     * @return bool
     */
    protected function isRegistered($whois)
    {
        return preg_match("/do not have an entry/", $whois) ? false : true;
    }

    /**
     * @param $domain
     * @return mixed|string
     */
    protected function runWhois($domain)
    {
        curl_setopt($this->curl, CURLOPT_URL, self::URL . $domain);

        $whois = curl_exec($this->curl);
        $whois = strip_tags($whois);

        if (preg_match("/exceeded this limit/", $whois)) {
            $whois = $this->rateLimit($domain);
        }

        // Reset sleep increment
        $this->sleep = self::SLEEP_INC;

        return $whois;
    }
}