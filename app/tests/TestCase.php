<?php

class TestPrinterDriver implements Northwestern\Printer\PrinterDriverInterface {
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
    $this->prepareDatabase();
    $this->prepareConfig();
  }

  public function tearDown() {
    parent::tearDown();
    \Mockery::close();
  }

  /**
   * Migrate the database
   */
  private function prepareDatabase()
  {
    Artisan::call('migrate');
  }

  /**
   * Set printer driver and mar file path
   */
  private function prepareConfig()
  {
    $this->app['printer.driver'] = $this->app->share(function($app) {
      return new TestPrinterDriver();
    });

    Config::set('app.mar_path', '/tmp/halon');
  }
}
