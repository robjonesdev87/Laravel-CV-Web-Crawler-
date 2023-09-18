<?php

namespace App\Providers;

use App\Events\BroadcastCrawlQueue;
use App\Helpers;
use App\Models\CrawlData;
use Carbon\Carbon;
use Illuminate\Log\Logger;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;


class AppServiceProvider extends ServiceProvider
{
    public string $guid = "";

    /**
     * @return string
     */
    public function getGuid(): string
    {
        return $this->guid;
    }

    /**
     * @param string $guid
     */
    public function setGuid(string $guid): void
    {
        $this->guid = $guid;
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Queue::before(function (JobProcessing $event) {

            $commandName = $event->job->payload()["data"]["commandName"];

            if ( $commandName == "App\Jobs\ProcessWebCrawl" ) {
                $crawlQueue = Helpers::getRedisDataArr('crawl-queue');
                $this->setGuid(unserialize($event->job->payload()["data"]["command"])->data->guid);

                $data = Helpers::getArrObj($this->getGuid(), $crawlQueue, 'guid');
                $data->jobStatus = "In Progress";

                $updatedQueue = Helpers::updateArrObj($this->getGuid(), $crawlQueue, 'guid', $data);
                Helpers::setRedisDataArr('crawl-queue', $updatedQueue);

                $webSocketData = json_decode(Redis::get('crawl-queue'), true);
                event(new BroadcastCrawlQueue($webSocketData));
            }
        });

        Queue::after(function (JobProcessed $event) {

            $commandName = $event->job->payload()["data"]["commandName"];

            if ( $commandName == "App\Jobs\ProcessWebCrawl" ) {
                $crawlQueue = Helpers::getRedisDataArr('crawl-queue');
                $this->setGuid(unserialize($event->job->payload()["data"]["command"])->data->guid);

                $data = Helpers::getArrObj($this->getGuid(), $crawlQueue, 'guid');
                $data->jobStatus = "Crawl Complete";
                $data->crawled = 1;
                $data->crawledOn = Carbon::now();

                $updatedQueue = Helpers::updateArrObj($this->getGuid(), $crawlQueue, 'guid', $data);
                Helpers::setRedisDataArr('crawl-queue', $updatedQueue);

                $webSocketData = json_decode(Redis::get('crawl-queue'), true);
                event(new BroadcastCrawlQueue($webSocketData));
            }
        });

        Queue::failing(function (JobFailed $job) {

            $crawlQueue = Helpers::getRedisDataArr('crawl-queue');
            $data = Helpers::getArrObj($this->getGuid(), $crawlQueue, 'guid');
            $data->jobStatus = "Crawl Failed";
            $data->crawled = 1;
            $data->crawledOn = Carbon::now();

            $updatedQueue = Helpers::updateArrObj($data->guid, $crawlQueue, 'guid', $data);
            Helpers::setRedisDataArr('crawl-queue', $updatedQueue);
            $webSocketData = json_decode(Redis::get('crawl-queue'), true);
            event(new BroadcastCrawlQueue($webSocketData));

        });

    }
}
