# sainsburys-test

PHP Page scraping for Sainsburys test

# Requirements

[Composer](https://getcomposer.org/download/) is required.

Install :

`curl -sS https://getcomposer.org/installer | php`

or use your preferred installation method.

All dependencies are managed through Composer. For this test I have used the Goutte Library, which itself relies on Symfony Crawler, Guzzle and PHPUnit.

# Installation

`git clone https://github.com/gringo-m/sainsburys-test.git`

`composer update `


# Usage

`cd sainsburys-test/bin`

`php ScraperScript.php`

# Test

The code is provided with a unit test.
The strategy chosen is to use a local copy of the target products page that is slightly modified (the products list has been lenghtened) in order to have a different total cost which makes it obvious that the scraper
does rely on a certain structure of the html but doesn't rely on the specific contents of the page.

To test the scraper just type :

`phpunit`

# Structure

The code is composed of :
* A Scraper interface, to define a reusable 'contract' and might allow for dependency injection if we happen to have multiple types of scrapers.
* An abstract Scraper class, that encapsulate the invariable functionality for our kind of page
* A concrete Scraper, that must provide a concrete way of processing the html data
* A Script file, that calls and uses the concrete Scraper class
* A helpers file, for unrelated/reusable small functions to clean strings

# More Information

1. The code has been written with extensibility in mind. As HTML content tends to change often, a separate processNode() method has been extracted from the main scrapePage() method to allow for easy reajusting/readapting. In order to do so the user can implement the AbstractCrawler class and define a processNode() that takes a Symfony Crawler instance and updates the results.

2. Upon noticing that the total sum of product prices is not the only aggregate operation that can be performed on prices (product data in general), an easy and extensible way has been provided for the user to define other aggregate functions such as average price, minimum price, maximum price etc... To do so, add your array of aggregate functions with the setAggregates() method.

# How to improve the code

* Accept command line arguments
* Add reporting (time to fetch the content, etc..)

