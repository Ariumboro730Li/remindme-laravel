<?php

namespace App\Services\API\Reminder;

class FetchByIdService extends FetchService
{
    public function handle()
    {
        $reminder = $this->reminderRepository->fetchById(
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
            'data' => $reminders
        ]);
    }

    /**
     * Return empty reminders
     *
     * @@return \Illuminate\Http\JsonResponse
     */
    private function returnEmpty()
    {
        return response()->json([
            'ok' => false,
            'err' => 'ERR_NOT_FOUND',
            'msg' => 'resource is not found'
        ], 404);
    }
}
