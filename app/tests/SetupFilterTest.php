<?php

class SetupFilterTest extends TestCase {

  public function testRedirectToSetupPageZero()
  {

    $filter = new SetupFilter();
    $response = $filter->filter();
    $this->assertEquals(307, $response->getStatusCode());

  }

}