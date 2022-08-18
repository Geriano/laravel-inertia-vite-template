<?php

namespace App\Http\Controllers;

use App\Http\Requests\DataTableRequest;
use App\Models\Login;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityController extends Controller
{
    /**
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        return Inertia::render('Superuser/Activity/Login');
    }

    /**
     * @param \App\Http\Requests\DataTableRequest $request
     * @return \Illuminate\Http\Response
     */
    public function logins(DataTableRequest $request)
    {
        $request->validated();

        return Login::join('users', 'login_activities.user_id', '=', 'users.id')
                    ->where(function (Builder $query) use ($request) {
                        $search = '%' . $request->search . '%';

                        $query->where('users.name', 'like', $search)
                                ->orWhere('users.username', 'like', $search)
                                ->orWhere('login_activities.ip_address', 'like', $search)
                                ->orWhere('login_activities.browser', 'like', $search)
                                ->orWhere('login_activities.platform', 'like', $search)
                                ->orWhere('login_activities.created_at', 'like', $search);
                    })
                    ->when(!$request->user()->hasRole('superuser'), function (Builder $query) use ($request) {
                        $query->where('users.id', $request->user()->id);
                    })
                    ->select(['users.*', 'login_activities.*'])
                    ->orderBy($request->input('order.key') ?: 'login_activities.created_at', $request->input('order.dir') ?: 'desc')
                    ->paginate($request->per_page ?: 10);
    }
}
