<?php
/**
 * Created by PhpStorm.
 * User: mercury
 * Date: 12/17/15
 * Time: 12:41 AM
 */

namespace App;


use Doctrine\Instantiator\Exception\InvalidArgumentException;

abstract class AbstractScraper implements ScraperInterface
{
    protected $client = null;
    protected $crawler = null;
    protected $products;
    protected $aggregates = ['total', 'max'];
    protected $results = [];
    protected $xpath;

    /*
     * TODO Would be nice to have client as dependency injection
     */
    public function __construct(\Goutte\Client $client)
    {
        $this->client = $client;
    }

    public function scrapePage($page, $aggregates = ['total'])
    {
        // Loading the root page
        if($this->crawler == NULL)
            $this->getPage($page);

        // Filtering down to the part of interest
        $this->products = $this->crawler->filter($this->xpath);

        // Processing each product and putting it in the $results variable
        $this->products->each($this->processNode());

        // Calculate total, extensible to include average, min, max etc..
        try {
            $this->applyAggregates($aggregates, 'price');
        } catch (\Exception $e)
        {
            echo "Exception:  {$e->getMessage()}\n";
        }

        // Return the results
        return $this->results;
    }

    abstract public function processNode();

    /**
     * Add any aggregate or operation on any member / leverage php array functions
     * @param $aggregates
     * @param $var
     */
    public function applyAggregates($aggregates, $var)
    {
        $agr = $this->getAggregates();

        if(!isset($this->results['results']))
            throw new InvalidArgumentException('Results array not populated. Check that the processNode methods works correctly');

        $prices = array_column($this->results['results'], $var);

        foreach($aggregates as $operation)
        {
            $this->results[$operation] = $agr[$operation]($prices);
        }

    }

    public function toString()
    {
        return json_encode($this->results);
    }

    /**
     * Sets crawler from page
     * @param $page
     * @return Symfony\Component\DomCrawler\Crawler
     */
    private function getPage($page)
    {
        $this->crawler =  $this->client->request('GET', $page);
    }

    /**
     * @param $crawler
     */
    public function setCrawler($crawler)
    {
        $this->crawler = $crawler;
    }

    /**
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @return array
     */
    public function getAggregates()
    {
        $this->aggregates = [
            'total' => function (array $arr){ return array_sum($arr); },
            'max'   => function(array $arr){ return max($arr); } // example
        ];

        return $this->aggregates;
    }

    /**
     * @param array $customAgr
     */
    public function setAggregates(array $customAgr = [])
    {
        $this->aggregates = array_merge($this->aggregates, $customAgr) ;
    }

    public function setXpath($xpath)
    {
        $this->xpath = $xpath;
    }
}