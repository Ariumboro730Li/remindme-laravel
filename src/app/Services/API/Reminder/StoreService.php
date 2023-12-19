<?php

namespace App\Services\API\Reminder;

use App\Http\Requests\ReminderStoreRequest;
use App\Repositories\API\Reminders\ReminderRepository;
use ErrorException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class StoreService
{
    protected ReminderStoreRequest $request;
    protected ReminderRepository $reminderRepository;

    public function __construct(ReminderStoreRequest $request, ReminderRepository $reminderRepository)
    {
        $this->request = $request;
        $this->reminderRepository = $reminderRepository;
    }

    /**
     * Execute the job.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(){
        try {
            if($this->validateData()) {
                $data = $this->request->all();
                $data['user_id'] = auth()->user()->id;
                DB::transaction(function () use ($data, &$reminder, &$returnReminder) {
                    $reminder = $this->reminderRepository->store($data);
                    $returnReminder = $reminder->only("id", "title", "description", "remind_at", "event_at");
                });
                return $this->returnSuccess($returnReminder);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'ok' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Return success response
     *
     * @param array $reminder
     * @return void
     */
    private function returnSuccess(array $reminder){
        return response()->json([
            'ok' => true,
            'data' => [
                'reminder' => $reminder
            ]
        ]);
    }

    /**
     * Validate data
     *
     * @return boolean
     */
    private function validateData(){
        $validator = Validator::make($this->request->all(), $this->request->rules());
        if ($validator->fails()) {
            throw new ErrorException($validator->errors()->first());
        }

        return true;
    }
}
