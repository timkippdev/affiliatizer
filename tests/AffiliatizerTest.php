<?php

use TimKippDev\Affiliatizer\Affiliatizer;
use PHPUnit\Framework\TestCase;

class AffiliatizerTest extends TestCase {

    public function test_testAffiliatizeVariations()
    {
        $aff = $this->getTestAffiliatizer();
        $this->assertAffiliateUrl($aff, 'https://test-not-affed.com', 'https://test-not-affed.com');
        $this->assertAffiliateUrl($aff, 'https://test-append-path.com', 'https://test-append-path.com/affiliate/test');
        $this->assertAffiliateUrl($aff, 'https://test-append-path.com?param=test', 'https://test-append-path.com/affiliate/test?param=test');
        $this->assertAffiliateUrl($aff, 'https://test-append-path.com/somewhere?param=test', 'https://test-append-path.com/somewhere/affiliate/test?param=test');
        $this->assertAffiliateUrl($aff, 'https://test-append-params.com', 'https://test-append-params.com?aff-tag1=aff-value1&aff-tag2=aff-value2');
        $this->assertAffiliateUrl($aff, 'https://test-redirect.com', 'https://redirect-to-me.com/?url=https%3A%2F%2Ftest-redirect.com');
    }

    private function getTestAffiliatizer()
    {
        return new Affiliatizer([
            'test-append-path.com' => [ 
                'type' => Affiliatizer::AFFILIATIZER_TYPE_APPEND_PATH, // 'append-path'
                'path' => '/affiliate/test'
            ],
            'test-append-params.com' => [ 
                'type' => Affiliatizer::AFFILIATIZER_TYPE_APPEND_PARAMS, // 'append-params'
                'params' => [
                    'aff-tag1' => 'aff-value1',
                    'aff-tag2' => 'aff-value2'
                ]
            ],
            'test-redirect.com' => [ 
                'type' => Affiliatizer::AFFILIATIZER_TYPE_REDIRECT, // 'redirect'
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