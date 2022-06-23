<?php

declare(strict_types=1);

namespace LaMetric;

use LaMetric\Response\Frame;
use LaMetric\Response\FrameCollection;

class Response
{
    /**
     * @param string $value
     *
     * @return string
     */
    public function printError(string $value = 'INTERNAL ERROR'): string
    {
        return $this->asJson([
            'frames' => [
                [
                    'index' => 0,
                    'text'  => $value,
                    'icon'  => 'null',
                ],
            ],
        ]);
    }

    /**
     * @param array $data
     *
     * @return string
     */
    private function asJson(array $data = []): string
    {
        return (string) json_encode($data);
    }

    /**
     * @param FrameCollection $frameCollection
     *
     * @return string
     */
    public function printData(FrameCollection $frameCollection): string
    {
        $response = [
            'frames' => [],
        ];

        /** @var Frame $frame */
        foreach ($frameCollection->getFrames() as $key => $frame) {
            $response['frames'][] = [
                'index' => $key,
                'chartData'  => $frame->getChartData(),
            ];
        }

        return $this->asJson($response);
    }
}
