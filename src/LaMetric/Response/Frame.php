<?php

declare(strict_types=1);

namespace LaMetric\Response;

class Frame
{
    private array $chartData = [];

    public function getChartData(): array
    {
        return $this->chartData;
    }

    public function setChartData(array $chartData): void
    {
        $this->chartData = $chartData;
    }
}
