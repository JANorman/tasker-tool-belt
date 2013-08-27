<?php

/**
 * A wrapper for traintimes.org.uk
 */
class TrainTimesController extends ApiController {

	/**
	 * Returns the train times to and from given stations and point in time
	 * 
	 * @param  string $from Departing station
	 * @param  string $to   Arrival station
	 * @param  string $when Time today, or 'now'
	 * @return string       JSON
	 */
	public function getTrainTimes($from, $to, $when) {
		$trains = new TrainTimesModel;

		$data = $trains->from($from)->to($to)->at($when)->get();

		print_r ($data);
		die();
		$response = array();
		return $this->formatResponse($response);
	}

}