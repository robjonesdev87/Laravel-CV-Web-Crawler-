<?php

namespace App\Jobs;

use App\Events\BroadcastCrawlQueue;
use App\Events\NewCrawl;
use App\Helpers;
use DOMDocument;
use DOMXPath;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\CrawlData;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Throwable;

class ProcessWebCrawl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public object $data;

    /**
     * @return object
     */
    public function getData(): object
    {
        return $this->data;
    }

    /**
     * @param object $data
     */
    public function setData(object $data): void
    {
        $this->data = $data;
    }

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->setData($data);
        libxml_use_internal_errors(true);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $crawlQueue = Helpers::getRedisDataArr('crawl-queue');
        $data = $this->getData();

        try {
            //Http/Html
            $httpClient = new Client();
            $response = Http::get($data->url);
            $htmlString = (string) $response->getBody();
            $doc = new DOMDocument();
            $doc->loadHTML($htmlString);
            $xpath = new DOMXPath($doc);

            //$data->html = $htmlString;

        } catch (Throwable $e) {
            report($e);
            $this->job->fail($e);
            exit;
        }

        //$data->jobStatus = "Crawl Complete";
        //$data->crawled = 1;
        //$data->crawledOn = Carbon::now();

        $updatedQueue = Helpers::updateArrObj($data->guid, $crawlQueue, 'guid', $data);
        Helpers::setRedisDataArr('crawl-queue', $updatedQueue);

        sleep(1);

        $webSocketData = json_decode(Redis::get('crawl-queue'),true);
        event(new BroadcastCrawlQueue($webSocketData));
    }

}
