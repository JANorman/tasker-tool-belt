<?php

use Guzzle\Http\Client;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Traintimes.org.uk scraper
 */
class TrainTimesModel {

	/**
	 * Base URL for the Train Times page
	 */
	const TRAIN_TIMES_BASE_URL = 'http://traintimes.org.uk/';

	/**
	 * Departing from
	 * @var string
	 */
	protected $station_from = null;

	/**
	 * Arrival station
	 * @var string
	 */
	protected $station_to = null;

	/**
	 * Departure Time
	 * @var string
	 */
	protected $departure_time = null;

	public function __construct() {
		Guzzle\Http\StaticClient::mount();
	}

	/**
	 * Sets the departure station
	 * 
	 * @param string $station Departure station
	 */
	public function from($station) {
		$this->station_from = $station;
		return $this;
	}

	/**
	 * Sets the arrival station
	 * 
	 * @param string $station Arrival station
	 */
	public function to($station) {
		$this->station_to = $station;
		return $this;
	}

	public function at($time = 'now') {
		if($time != 'now' && preg_match('/[0-2][0-3]:[0-5][0-9]/', $time) > 0) {
			$this->departure_time = $time;
		}
		return $this;
	}

	public function get() {
		$traintimes = $this->getTrainTimesPage();
		if(!$traintimes) {
			return false;
		}

		die(var_dump($traintimes));
		return $this->extractTrainTimes($traintimes);
	}

	protected function getTrainTimesPage() {
		$url = $this->station_from . '/' . $this->station_to . '/';

		if($this->departure_time !== null) {
			$url .= $this->departure_time;
		}

		$client = new Client(self::TRAIN_TIMES_BASE_URL);
		$request = $client->get($url);
		$response = $request->send();

		if($response->getStatusCode() !== 200) {
			return false;
		}

		return $response->getBody(true);
	}

	protected function extractTrainTimes($page) {
		$crawler = new Crawler();
		$crawler->addHtmlContent($page);

		$train_data = $crawler->filter('ul.results > li');
		// die(print_r($train_data->text()));
		$trains = array();
		$train_data->each(function (Crawler $node, $i) {
			$data = array();
			$times = explode(' - ', $node->filter('strong')->text());
			die(var_dump($times));
			$data['leaving'] = $times[0];
			$data['arriving'] = $times[1];

			$trains[] = $data;
		});
		
		return $trains;
	}
}