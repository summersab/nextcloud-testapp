<?php
/**
 * @copyright Copyright (c) 2021, Andrew Summers
 *
 * @author Andrew Summers
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\TestApp\Middleware;

use OCP\AppFramework\Middleware;
use OCP\AppFramework\Http\Response;
use OC;

class TestMiddleware extends Middleware {
	public function __construct() {
	}

	public function beforeOutput($controller, $methodName, $output){
		if (OC::$server->getUserSession()->isLoggedIn()) {
			$json = json_decode($output, 1);

			if (is_array($json)) {
				// Manipulate the JSON payload
				//$output = json_encode($json);
				return $output;
			}

			// This SHOULD only match HTML. SHOULD.
			if ($output != strip_tags($output)) {
				// Manipulate the DOM
				return $output;
			}
		}
		// Not logged in
		else {
			$json = json_decode($output, 1);

			if (is_array($json)) {
				// Manipulate the JSON payload
				//$output = json_encode($json);
				return $output;
			}

			// This SHOULD only match HTML. SHOULD.
			if ($output != strip_tags($output)) {
				// Manipulate the DOM
				return $output;
			}
		}

		return $output;
	}

	public function beforeController($controller, $methodName) {
		// Just a dummy breakpoint
		$i = 1;
	}
	public function afterController($controller, $methodName, Response $response): Response {
		return $response;
	}

}