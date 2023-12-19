<?php

namespace App\Services\API\Reminder;

use App\Http\Requests\ReminderDeleteRequest;
use App\Repositories\API\Reminders\ReminderRepository;

class DeleteService extends FetchByIdService
{

    public function handle()
    {
        $reminder = $this->reminderRepository->deleteById(
            auth()->user()->id,
            $this->request->id
        );
        if($reminder){
            return $this->returnExist($reminder);
        } else {
            return $this->returnEmpty();
        }
    }

    protected function returnExist($reminders){
        return response()->json([
            'ok' => true,
        ]);
    }
}
