<?php

class TestPrinterDriver {
  public function enque($a, $b) {
    return true;
  }
}

class TestCase extends Illuminate\Foundation\Testing\TestCase {

	/**
	 * Creates the application.
	 *
	 * @return Symfony\Component\HttpKernel\HttpKernelInterface
	 */
	public function createApplication()
	{
		$unitTesting = true;

		$testEnvironment = 'testing';

		return require __DIR__.'/../../bootstrap/start.php';
	}

  public function setUp() {
    parent::setUp();

    $this->app['printer.driver'] = $this->app->share(function($app) {
      return new TestPrinterDriver();
    });

    Config::set('app.mar_path', '/tmp/halon');
  }

  public function tearDown() {
    parent::tearDown();
    \Mockery::close();
  }
}
