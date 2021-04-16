<?php

namespace OCA\TestApp\AppInfo;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCA\TestApp\Middleware\TestMiddleware;

class Application extends App implements IBootstrap {
	public function __construct() {
		parent::__construct('testapp');
	}

	public function register(IRegistrationContext $context): void {
		// I have tried putting everything here, but I cannot make this work.
	}

	public function boot(IBootContext $context): void {
		$serverContainer = $context->getServerContainer();
	}
}
