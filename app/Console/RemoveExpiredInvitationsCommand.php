<?php

namespace App\Console\Commands;

use App\Invitation;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RemoveExpiredInvitationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invitation:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes all expired invitations from table';

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
     * @throws \Exception
     */
    public function handle()
    {
        Invitation::where('expires_at', '<=', Carbon::now())->delete();

        $this->info('Expired invitations are successfully removed.');
    }
}
