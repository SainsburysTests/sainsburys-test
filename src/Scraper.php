<?php

/*
 * This file is being submitted as part of the Sainsbury Software Engineering Test
 *
 * This class gets, scrapes a Sainsburys store products page and retrieves :
 *  - name, description, price and page weight
 *
 * Most of it can be reused, the parts susceptible to change have been put in the
 * processNode() method.
 *
 * Operations on data retrieved, such as price, can be user-defined (total, sum, avg, min, max etc...)
 *
 * The class has dependency on the Symfony Crawler and the Goutte Client
 *
 * (c) Mehdi Souihed <mehdi.souihed@gmail.com>
 */


namespace App;

use \Goutte\Client;
use App\ScraperInterface;

class Scraper extends AbstractScraper {
    /**
     * The core processing logic (not reusable)
     * @return \Closure
     *
     */
    public function processNode(){

        return function ($node){

            $link  = $node->filter('h3 > a')->link();
            $name  = $node->filter('h3 > a')->text();
            $price = $node->filter('.pricePerUnit')->text();

            // We need to load the next page to get size and description
            $nextPage = $this->client->click($link);

            $description = $nextPage->filter('.productText')->first()->text();
            $html        = $nextPage->html();
            $size        = round(mb_strlen($html, '8bit') / 1024) . "kb";

            $this->results['results'][] = [
                'name' => c($name),
                'price' => getPrice($price),
                'description' => c($description),
                'size' => $size
            ];
        };
    }


}