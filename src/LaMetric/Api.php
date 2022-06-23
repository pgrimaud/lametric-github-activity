<?php

declare(strict_types=1);

namespace LaMetric;

use LaMetric\Response\Frame;
use LaMetric\Response\FrameCollection;
use Github\Client;

class Api
{
    private const MAX_PIXEL = 8;

    public function __construct(private Client $githubClient)
    {
    }

    public function fetchData(array $parameters = []): FrameCollection
    {
        $query = <<<'QUERY'
            query userInfo($login: String!, $dateFrom: DateTime!, $dateTo: DateTime!) {
                user(login: $login) {
                  contributionsCollection(from: $dateFrom, to: $dateTo) {
                      contributionCalendar {
                      weeks {
                        contributionDays {
                          contributionCount
                        }
                      }
                    }
                  }
                }
            }
        QUERY;

        $dateFrom = new \DateTime('-36 days');
        $dateTo = new \DateTime();

        // @phpstan-ignore-next-line
        $result = $this->githubClient->api('graphql')->execute($query, [
            'login' => $parameters['username'],
            'dateFrom' => $dateFrom->format('Y-m-d\TH:i:sp'),
            'dateTo' => $dateTo->format('Y-m-d\TH:i:sp')
        ]);

        return $this->mapData($result['data']['user']['contributionsCollection']['contributionCalendar']['weeks']);
    }


    private function mapData(array $data = []): FrameCollection
    {
        $activity = [];
        $weightedActivity = [];

        $max = 0;
        $min = $data[0]['contributionDays'][0]['contributionCount'];

        foreach ($data as $week) {
            foreach ($week['contributionDays'] as $day) {
                if ($day['contributionCount'] > $max) {
                    $max = $day['contributionCount'];
                }

                if ($day['contributionCount'] < $min) {
                    $min = $day['contributionCount'];
                }

                $activity[] = $day['contributionCount'];
            }
        }

        $difference = $max - $min;

        foreach ($activity as $item) {
            $weightedItem = self::MAX_PIXEL - 1;
            for ($i = self::MAX_PIXEL; $i > 0; $i--) {
                if ($item < $difference / $i) {
                    $weightedItem = self::MAX_PIXEL - $i;
                    break;
                }
            }
            $weightedActivity[] = $weightedItem;
        }

        $frameCollection = new FrameCollection();

        $frame = new Frame();
        $frame->setChartData($weightedActivity);

        $frameCollection->addFrame($frame);

        return $frameCollection;
    }
}
