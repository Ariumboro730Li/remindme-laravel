<?php

namespace App\Services\API\Reminder;

use App\Repositories\API\Reminders\ReminderRepository;
use ErrorException;
use Illuminate\Http\Request;

class FetchService
{
    protected Request $request;
    protected ReminderRepository $reminderRepository;

    public function __construct(Request $request, ReminderRepository $reminderRepository)
    {
        $this->request = $request;
        $this->reminderRepository = $reminderRepository;
        $this->limit = $request->limit ?? 10;
    }

    /**
     * Fetch reminders
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function handle()
    {
        try {
            $reminders = $this->reminderRepository->fetch(auth()->user()->id, $this->limit);

            if ($reminders->isEmpty()) {
                return $this->returnEmpty();
            }

            return $this->returnExist($reminders);
        } catch (\Throwable $th) {
            throw new ErrorException($th->getMessage());
        }
    }

    /**
     * Return exist reminder
     *
     * @param [type] $reminders
     * @return \Illuminate\Http\JsonResponse
     */
    private function returnExist($reminders){
        return response()->json([
            'ok' => true,
            'data' => [
                'reminders' => $reminders,
                'limit' => $this->limit
            ]
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
