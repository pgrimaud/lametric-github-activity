<?php

declare(strict_types=1);

namespace LaMetric;

use LaMetric\Response\Frame;
use LaMetric\Response\FrameCollection;
use Github\Client;

class Api
{
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

        $dateFrom = new \DateTime('-38 days');
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

        foreach ($data as $week) {
            foreach ($week['contributionDays'] as $day) {
                $activity[] = $day['contributionCount'];
            }
        }

        $frameCollection = new FrameCollection();

        $frame = new Frame();
        $frame->setChartData($activity);

        $frameCollection->addFrame($frame);

        return $frameCollection;
    }
}
