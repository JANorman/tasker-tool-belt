<?php

class ApiController extends BaseController {

	public function formatResponse(array $content) {
		return Response::json($content);
	}

}