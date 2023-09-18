<?php

namespace App\Models;

use App\Helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CrawlData extends Model
{
    public string $jobId;
    public string $url;
    public string $html;
    public string $jobStatus;
    public bool $crawled;
    public bool $queued;
    public Carbon $queuedAt;
    public Carbon $crawledAt;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'crawl_data';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['jobId','url','html','jobStatus','crawled','queued','queuedAt','crawledAt'];


}
