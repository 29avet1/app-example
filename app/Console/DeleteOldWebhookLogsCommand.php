<?php

namespace App\Console\Commands;

use App\WebhookLog;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteOldWebhookLogsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webhook_log:delete {--days=14}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $days = $this->option('days');
        $daysBefore = Carbon::now()->subDays($days);

        WebhookLog::where('created_at', '<=', $daysBefore)->delete();

        $this->info('Old webhook logs have been successfully deleted.');
    }
}
