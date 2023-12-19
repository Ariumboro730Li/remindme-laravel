<?php

namespace App\Services\API\Reminder;

use App\Http\Requests\ReminderUpdateRequest;
use App\Repositories\API\Reminders\ReminderRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UpdateService
{

    protected ReminderUpdateRequest $request;
    protected ReminderRepository $reminderRepository;

    public function __construct(ReminderUpdateRequest $request, ReminderRepository $reminderRepository)
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
                $data = $this->request->except('id');
                $id = $this->request->id;
                $user_id = auth()->user()->id;
                DB::transaction(function () use ($user_id, $id, $data, &$reminder, &$returnReminder) {
                    $reminder = $this->reminderRepository->updateById($user_id, $id, $data);
                    if ($reminder) {
                        $returnReminder = $reminder->only("id", "title", "description", "remind_at", "event_at");
                    } else {
                        $returnReminder = [];
                    }
                });
                if ($returnReminder) {
                    return $this->returnSuccess($returnReminder);
                } else {
                    return $this->returnEmpty();
                }
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
     * @return \Illuminate\Http\JsonResponse
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
