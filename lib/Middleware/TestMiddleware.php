<?php

namespace OCA\TestApp\Middleware;

use OCP\AppFramework\Middleware;

class TestMiddleware extends Middleware {
	public function __construct() {
	}

	public function beforeOutput($controller, $methodName, $output) {
		if (get_class($controller) == "OCA\Dashboard\Controller\DashboardController" && $methodName == "index") {
			// I want to do stuff here
			return $output;
		}
		return $output;
	}
}