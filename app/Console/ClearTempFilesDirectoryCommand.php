<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ClearTempFilesDirectoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tempdir:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear temporary files directory';

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
        Storage::disk('s3_public')->deleteDirectory('temp');

        $this->info('Temporary directory has been successfully deleted.');
    }
}
