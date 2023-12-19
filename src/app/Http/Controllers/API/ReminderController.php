<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReminderDeleteRequest;
use App\Http\Requests\ReminderStoreRequest;
use App\Http\Requests\ReminderUpdateRequest;
use App\Models\Reminder;
use App\Services\API\Reminder\DeleteService;
use App\Services\API\Reminder\FetchByIdService;
use App\Services\API\Reminder\FetchService;
use App\Services\API\Reminder\StoreService;
use App\Services\API\Reminder\UpdateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReminderController extends Controller
{

    public function fetch(Request $request){
        return app(FetchService::class)->handle($request);
    }

    public function store(ReminderStoreRequest $request){
        return app(StoreService::class)->handle($request);
    }

    public function fetchById($id){
        return app(FetchByIdService::class)->handle($id);
    }

    public function update(ReminderUpdateRequest $request){
        return app(UpdateService::class)->handle($request);
    }

    public function delete($id){
        return app(DeleteService::class)->handle($id);
    }

}
