<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Gestion des comptes. En phase 7 : création et gestion des caissières
 * par le gestionnaire (policy UserPolicy). Le Super Admin (phase 8)
 * gère en plus gestionnaires et rôles.
 */
class UserController extends Controller
{
    public function index()
    {
        // Le gestionnaire ne voit que les caissières ; le Super Admin
        // voit tout le monde.
        $users = auth()->user()->isSuperAdmin()
            ? User::with('roles')->orderBy('name')->get()
            : User::with('roles')->whereHas('roles', fn ($query) => $query->where('name', Roles::CASHIER))->orderBy('name')->get();

        return view('admin.users.index', ['users' => $users]);
    }

    public function create()
    {
        return view('admin.users.form', ['user' => new User]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in($this->assignableRoles())],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        $user->assignRole($validated['role']);

        return redirect()->route('admin.users.index')->with('status', __('site.admin.users.created'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        return view('admin.users.form', ['user' => $user]);
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users')->ignore($user)],
            'password' => ['nullable', 'string', 'min:8'],
            'is_active' => ['boolean'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'] ? Hash::make($validated['password']) : $user->password,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.users.index')->with('status', __('site.admin.users.updated'));
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();

        return back()->with('status', __('site.admin.users.deleted'));
    }

    /**
     * Rôles que l'utilisateur courant peut attribuer : le gestionnaire
     * ne crée que des caissières (spécification §6.2).
     *
     * @return array<int, string>
     */
    private function assignableRoles(): array
    {
        return auth()->user()->isSuperAdmin()
            ? [Roles::CASHIER, Roles::MANAGER, Roles::SUPER_ADMIN]
            : [Roles::CASHIER];
    }
}
