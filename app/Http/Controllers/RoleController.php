<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:role-list', ['only' => ['index']]);
        $this->middleware('permission:role-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:role-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
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

        $roles = Role::withCount('users')->orderBy('id', $order);

        if ($terms !== null && $terms !== '') {
            $keywords = explode(' ', $terms);

            $roles->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('name', 'like', '%' . $keyword . '%');
                }
            });
        }

        if ($limit === 0) {
            $roles = $roles->get();
        } else {
            $roles = $roles->paginate($limit);
        }

        return view('role.index', [
            'roles' => $roles,
            'terms' => $terms,
            'order' => $order,
            'limit' => $limit,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory|View
     */
    public function create()
    {
        $permissions = Permission::get();

        return view('role.create', [
            'permissions' => $permissions,
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
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);

        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permission'));

        return redirect()->route('roles.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Factory|View
     */
    public function show(int $id)
    {
        $role = Role::findOrFail($id);

        $rolePermissions = Permission::join('role_has_permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
            ->where('role_has_permissions.role_id', $id)
            ->get();

        return view('role.show', [
            'role' => $role,
            'rolePermissions' => $rolePermissions,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Factory|View
     */
    public function edit(int $id)
    {
        $role = Role::findOrFail($id);

        if ($role->name == 'Admin') {
            abort(403);
        }

        if ($role->id == Auth::User()->roles[0]->id) {
            abort(403);
        }

        $permissions = Permission::get();
        $rolePermissions = DB::table('role_has_permissions')->where('role_has_permissions.role_id', $id)
            ->pluck('role_has_permissions.permission_id')
            ->all();

        return view('role.edit', [
            'role' => $role,
            'permissions' => $permissions,
            'rolePermissions' => $rolePermissions,
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
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);

        $role = Role::findOrFail($id);

        $path = parse_url($request->headers->get('referer'), PHP_URL_PATH);
        $role_id = explode('/', $path)[3];

        if ($role_id != $role->id) {
            abort(403);
        }

        if (! Auth::User()->hasRole('Admin') && $role->id == Auth::User()->roles[0]->id) {
            abort(403);
        }

        $role->name = $request->input('name');
        $role->save();

        $role->syncPermissions($request->input('permission'));

        return redirect()->route('roles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id)
    {
        $role = DB::table('roles')->where('id', $id);

        if ($role->get()->isEmpty()) {
            abort(404);
        }

        if ($role->get()[0]->name == 'Admin') {
            abort(403);
        }

        if (Auth::User()->roles[0]->name == $role->get()[0]->name) {
            abort(403);
        }

        $role->delete();

        return redirect()->route('roles.index');
    }
}
