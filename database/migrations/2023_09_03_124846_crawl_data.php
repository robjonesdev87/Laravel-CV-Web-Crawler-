<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
        public string $jobId;
        public string $url;
        public string $html;
        public string $jobStatus;
        public bool $crawled;
        public bool $queued;
        public Carbon $queuedAt;
        public Carbon $crawledAt;
     */
    public function up(): void
    {
        Schema::create('crawl_data', function (Blueprint $table) {
            $table->id();
            $table->string('jobId')->default("");
            $table->string('url')->default("");
            $table->string('html')->default("");
            $table->string('jobStatus')->default("");
            $table->integer('crawled')->default(0);
            $table->integer('queued')->default(0);
            $table->dateTime('queuedAt')->nullable();
            $table->dateTime('crawledAt')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crawl_data');
    }
};
