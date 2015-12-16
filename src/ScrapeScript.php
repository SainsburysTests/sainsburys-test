<?php

namespace App;

require "../vendor/autoload.php";

$page = 'http://hiring-tests.s3-website-eu-west-1.amazonaws.com/2015_Developer_Scrape/5_products.html';

$client = new \Goutte\Client();

$scraper = new \App\Scraper($client);

$scraper->setXpath('.product');

print_r(json_encode($scraper->scrapePage($page), JSON_PRETTY_PRINT));

print "\n";