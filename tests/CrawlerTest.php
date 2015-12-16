<?php

namespace App\Tests;

use \Symfony\Component\DomCrawler\Crawler;

class CrawlerTest extends \PHPUnit_Framework_TestCase
{

    public $htmlPage = 'tests/testPageAllProducts.html';
    public $crawler;

    public function testScraping()
    {
        $this->assertFileExists($this->htmlPage);
        $html = file_get_contents($this->htmlPage);

	// Crawler need root url, however the initial page is loaded from $htmlPage variable
	$this->crawler = new Crawler('','http://hiring-tests.s3-website-eu-west-1.amazonaws.com');

	// Load html
        $this->crawler->addHtmlContent($html);

        $client = new \Goutte\Client();

        $scraper = new \App\Scraper($client);

	$scraper->setXpath('.product');

	// Set crawler directly
	$scraper->setCrawler($this->crawler);

	// Page is null as we bypass this by setting the crawler on the above line
        $results = $scraper->scrapePage("");

        $this->assertEquals(json_encode($this->_getExpectedResults()), json_encode($results));

    }

    private function _getExpectedResults(){
      return array (
            'results' =>
                array (
                    0 =>
                        array (
                            'name' => "Sainsbury's Apricot Ripe & Ready x5",
                            'price' => '3.50',
                            'description' => 'Apricots',
                            'size' => '37kb',
                        ),
                    1 =>
                        array (
                            'name' => "Sainsbury's Avocado Ripe & Ready XL Loose 300g",
                            'price' => '1.50',
                            'description' => 'Avocados',
                            'size' => '37kb',
                        ),
                    2 =>
                        array (
                            'name' => "Sainsbury's Avocado, Ripe & Ready x2",
                            'price' => '1.80',
                            'description' => 'Avocados',
                            'size' => '42kb',
                        ),
                    3 =>
                        array (
                            'name' => "Sainsbury's Avocados, Ripe & Ready x4",
                            'price' => '3.20',
                            'description' => 'Avocados',
                            'size' => '37kb',
                        ),
                    4 =>
                        array (
                            'name' => "Sainsbury's Conference Pears, Ripe & Ready x4 (minimum)",
                            'price' => '1.50',
                            'description' => 'Conference',
                            'size' => '37kb',
                        ),
                ),
            'total' => 11.5,
        );
    }
}
