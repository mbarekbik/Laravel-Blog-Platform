<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\RoleNotification;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:user-list', ['only' => ['index']]);
        $this->middleware('permission:user-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Factory|View
     */
    public function index(Request $request)
    {
        if ($request->input('q') !== null) {
            $terms = $request->input('q');
        } else {
            $terms = '';
        }

        if ($request->input('order') !== null) {
            $order = $request->input('order');
        } else {
            $order = 'desc';
        }
        if ($request->input('limit') !== null) {
            $limit = $request->input('limit');
        } else {
            $limit = 20;
        }

        $users = User::orderBy('id', $order);

        if ($terms !== null && $terms !== '') {
            $keywords = explode(' ', $terms);

            $users->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('firstname', 'like', '%' . $keyword . '%')
                        ->orWhere('lastname', 'like', '%' . $keyword . '%')
                        ->orWhere('email', 'like', '%' . $keyword . '%');
                }
            });
        }

        if ($request->input('roles') !== null && $request->input('roles')[0] !== null) {
            $selected_roles_array = $request->input('roles');
            if (is_array($request->input('roles'))) {
                $temp = explode(',', $selected_roles_array[0]);
            } else {
                $temp = explode(',', $selected_roles_array);
            }
            $selected_roles = $selected_roles_array[0];
            $selected_roles_array = $temp;
            $users = $users->whereHas('roles', function ($query) use ($temp) {
                $query->whereIn('id', $temp);
            });
        } else {
            $selected_roles = null;
            $selected_roles_array = null;
        }

        if ($limit === 0) {
            $users = $users->get();
        } else {
            $users = $users->paginate($limit);
        }

        $roles = Role::withCount('users')->get();

        return view('user.index', [
            'users' => $users,
            'roles' => $roles,
            'terms' => $terms,
            'order' => $order,
            'limit' => $limit,
            'selected_roles' => $selected_roles,
            'selected_roles_array' => $selected_roles_array,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory|View
     */
    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();

        return view('user.create', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'roles' => 'required',
        ]);

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        $input['image_path'] = '/images/user.png';

        $user = User::create($input);
        $user->assignRole($request->input('roles'));

        $input['send_mail'] = $request->send_mail == 'on' ? true : false;

        if ($input['send_mail']) {
            $data['subject'] = 'Zostało założone konto na Blogu!';
            $data['user'] = $request->firstname.' '.$request->lastname;
            $data['rola'] = $request->roles;
            $data['login'] = $request->email;
            $data['password'] = $request->password;
            $data['toEmail'] = $data['login'];

            return redirect()->route('mail.send', [
                'data' => $data,
            ]);
        }

        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id)
    {
        // $user = User::find($id);
        // return view('user.show',compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Factory|View
     */
    public function edit(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            abort(403);
        }

        if (! empty($user->roles[0])) {
            if ($user->roles[0]->name == 'Admin' && ! Auth::User()->hasRole('Admin')) {
                abort(403);
            }
        }

        $roles = Role::pluck('name', 'name')->all();

        if (!Auth::User()->hasRole('Admin')) {
            unset($roles['Admin']);
            unset($roles[Auth::User()->roles[0]->name]);
        }

        $userRole = $user->roles->pluck('name')->all();

        if (! $userRole) {
            $userRole = null;
        }

        return view('user.edit', [
            'user' => $user,
            'roles' => $roles,
            'userRole' => $userRole,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id)
    {
        if ($request->profile_update) {
            $this->validate($request, [
                'firstname' => 'required',
                'lastname' => 'required',
            ]);

            if ($id != Auth::id()) {
                abort(403);
            }
        } else {
            $path = parse_url($request->headers->get('referer'), PHP_URL_PATH);
            $user_id = explode('/', $path)[3];

            if ($user_id != $id) {
                abort(403);
            }

            if ($user_id == Auth::id()) {
                abort(403);
            }

            if (! Auth::User()->can('user-edit')) {
                abort(403);
            }

            $this->validate($request, [
                'firstname' => 'required',
                'lastname' => 'required',
                'email' => 'required|email|unique:users,email,'.$id,
                'roles' => 'required',
            ]);
        }

        $input = $request->all();

        if (! empty($input['password'])) {
            if ($request->profile_update) {
                $this->validate($request, [
                    'password' => 'min:8|required_with:password_confirmation|same:password_confirmation',
                    'password_confirmation' => 'min:8',
                ]);

                $input['password'] = Hash::make($input['password']);
            } else {
                $input['password'] = Hash::make($input['password']);
            }
        } else {
            $input = Arr::except($input, ['password']);
        }

        if (! empty($input['image'])) {
            $input['image_path'] = $this->storeImage($request);
        }

        $data = [];

        $user = User::findOrFail($id);

        if (! empty($user->roles[0])) {
            if ($user->roles[0]->name == 'Admin' && ! Auth::User()->hasRole('Admin')) {
                abort(403);
            }
        }

        $oldName = $user->firstname.' '.$user->lastname;

        if (! $request->profile_update) {
            if (! empty($user->roles[0])) {
                $oldRole = $user->roles[0]->name;
            } else {
                $oldRole = null;
            }
        }

        $user->update($input);

        if ($request->roles) {
            DB::table('model_has_roles')->where('model_id', $id)->delete();

            $user->assignRole($request->input('roles'));
        }

        $changes = Arr::except($user->getChanges(), 'updated_at');

        if (! $request->profile_update) {
            if (! empty($user->roles[0])) {
                if ($oldRole != $user->roles[0]->name) {
                    $changes['rola'] = $user->roles[0]->name;
                    $data['rola'] = $user->roles[0]->name;
                    if (Auth::Id() !== $user->id) {
                        $user->notify(new RoleNotification('INFO', 'Przyznano nową role '. $user->roles[0]->name . '.'));
                    }
                }
            }
        }

        if (isset($changes['firstname'])) {
            $data['user'] = $changes['firstname'];
            $data['new_name'] = $data['user'];
        }
        if (isset($changes['lastname'])) {
            if (isset($changes['firstname'])) {
                $data['user'] = $changes['firstname'].' '.$changes['lastname'];
                $data['new_name'] = $data['user'];
            } else {
                $data['user'] = $request->firstname.' '.$changes['lastname'];
                $data['new_name'] = $data['user'];
            }
        } else {
            if (isset($changes['firstname'])) {
                $data['user'] = $changes['firstname'].' '.$request->lastname;
                $data['new_name'] = $data['user'];
            } else {
                $data['user'] = $request->firstname.' '.$request->lastname;
            }
        }

        if (isset($changes['email'])) {
            $data['login'] = $changes['email'];
            $data['toEmail'] = $changes['email'];
        } else {
            $data['toEmail'] = $request->email;
        }
        if (isset($changes['password'])) {
            $data['password'] = $request->password;
        }

        $input['send_mail'] = $request->send_mail == 'on' ? true : false;

        if ($changes) {
            $data['subject'] = 'Zostały wprowadzone zmiany na koncie.';
            $data['user'] = $oldName;
            if ($input['send_mail']) {
                return redirect()->route('mail.send', [
                    'data' => $data,
                ]);
            }
            if ($data['toEmail'] == 'admin@db.com' || ! isset($data['toEmail'])) {
                return redirect()->back();
            }
        } else {
            if ($request->profile_update) {
                return redirect()->back();
            }
        }

        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id)
    {
        if (Auth::id() == $id) {
            abort(403);
        }

        $user = User::findOrFail($id);

        if ($user->hasRole('Admin')) {
            abort(403);
        }

        $user->delete();

        return redirect()->route('users.index');
    }

    public function readNotifications(Request $request)
    {
        $date = Carbon::createFromTimestamp($request->input('date') / 1000);
        Auth::user()->unreadNotifications->where('created_at', '<=', $date)->markAsRead();
        return response()->json('OK');
    }

    public function clearNotifications()
    {
        $notifications = Auth::user()->notifications;

        foreach ($notifications as $notification) {
            $notification->delete();
        }
        return response()->json('OK');
    }


    private function storeImage(Request $request)
    {
        $imageName = str_replace(' ', '-', $request->image->getClientOriginalName());
        $newImageName = uniqid().'-'.$imageName;
        $request->image->move(public_path('images\avatars'), $newImageName);

        return '/images/avatars/'.$newImageName;
    }
}
