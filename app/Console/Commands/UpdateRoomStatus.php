<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Room;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Log;

class UpdateRoomStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'room:update_on_expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'If room end date expired then updated status active to deactive';

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
        
        $getRooms = Room::where('status','Active')->get();
        if (!empty($getRooms)) {
            foreach ($getRooms as $key => $rooms) {

                $roomEndDate = Carbon::parse($rooms->date_to)->shiftTimezone('Asia/Kolkata');
                
                $test = Carbon::parse($rooms->date_to);
                $today = Carbon::now()->setTimezone('Asia/Kolkata');
                $chkDateDiff = $today->gte($roomEndDate);
                if ($chkDateDiff) {
                    $rooms->status = 'Deactive';
                    $rooms->save();
                }
            }
        }
        return true;
    }
}
