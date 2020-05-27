<?php

use TimKippDev\Affiliatizer\Affiliatizer;
use PHPUnit\Framework\TestCase;

class AffiliatizerTest extends TestCase {

    public function test_testAffiliatizeVariations()
    {
        $aff = $this->getTestAffiliatizer();
        $this->assertAffiliateUrl($aff, 'https://test-not-affed.com', 'https://test-not-affed.com');
        $this->assertAffiliateUrl($aff, 'https://test-replace.com', 'https://test-replace.com?aff-tag=aff-value');
        $this->assertAffiliateUrl($aff, 'https://test-redirect.com', 'https://redirect-to-me.com/?url=https%3A%2F%2Ftest-redirect.com');
    }

    private function getTestAffiliatizer()
    {
        return new Affiliatizer([
            'test-replace.com' => [ 
                'type' => Affiliatizer::AFFILIATIZER_TYPE_REPLACEMENT, 
                'replacers' => [
                    'aff-tag' => 'aff-value'
                ]
            ],
            'test-redirect.com' => [ 
                'type' => Affiliatizer::AFFILIATIZER_TYPE_REDIRECT,
                'destination' => 'https://redirect-to-me.com/?url=<URL>'
            ]
        ]);
    }

    private function assertAffiliateUrl(Affiliatizer $affiliatizer, $originalUrl, $expectedUrl)
    {
        $affiliateUrl = $affiliatizer->affiliatizeUrl($originalUrl);
        $this->assertEquals($expectedUrl, $affiliateUrl);
    }

}