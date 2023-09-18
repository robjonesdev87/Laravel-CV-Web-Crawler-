<?php

namespace App\Http\Controllers;

use App\Events\BroadcastCrawlQueue;
use App\Helpers;
use App\Jobs\ProcessWebCrawl;
use App\Jobs\SendCrawlCompleteEmail;
use App\Models\CrawlData;
use Carbon\Carbon;
use Illuminate\Broadcasting\Broadcasters\PusherBroadcaster;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Queue\Queue;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redis;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Str;
use Pusher\Pusher;
use stdClass;

class CrawlController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function validateURL(Request $request) {
        $this->processCrawl($request["url"]);
    }

    public function processCrawl($url)
    {
        $data = new StdClass();
        $data->guid = Str::uuid()->toString();
        $data->url = $url;
        $data->jobStatus = "Queued";
        $data->html = "";
        $data->crawled = false;
        $data->crawledOn = null;

        $crawlQueue = Helpers::getRedisDataArr('crawl-queue');
        $crawlQueue[] = $data;
        Helpers::setRedisDataArr('crawl-queue',$crawlQueue);

        $webSocketData = json_decode(Redis::get('crawl-queue'),true);
        event(new BroadcastCrawlQueue($webSocketData));
        ProcessWebCrawl::dispatch($data);
    }

    public function getQueueData(): mixed
    {
        return json_decode(Redis::get('crawl-queue'),true);
    }

}
