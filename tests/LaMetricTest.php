<?php

declare(strict_types=1);

namespace LaMetric\tests;

use LaMetric\Response;
use PHPUnit\Framework\TestCase;

class LaMetricTest extends TestCase
{
    public function testInvalidProcess(): void
    {
        $response = new Response();
        $json     = $response->printError();

        $jsonFixtures = file_get_contents(__DIR__ . '/fixtures/error.json');

        $this->assertSame($jsonFixtures, $json);
    }
}
