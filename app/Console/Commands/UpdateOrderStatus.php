<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Log;

class UpdateOrderStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:update_on_expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'If plan expired then updated status active to expired';

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
     * @return int
     */
    public function handle()
    {
        //Log::info('Test');
        $getOrders = Order::where('status','Active')->get();
        if (!empty($getOrders)) {
            foreach ($getOrders as $key => $orders) {

                $orderEndDate = Carbon::parse($orders->end_date)->setTimezone('Asia/Kolkata');
                $today = Carbon::now()->setTimezone('Asia/Kolkata');

                $chkDateDiff = $today->gt($orderEndDate);
                if ($chkDateDiff) {
                    $orders->status = 'Deactivate';
                    $orders->save();
                }
            }
        }
        return true;
    }
}
