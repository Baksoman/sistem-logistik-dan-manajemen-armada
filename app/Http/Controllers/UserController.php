<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;

class UserController extends Controller
{
    public function __construct(protected UserService $userService) {}

    public function index()
    {
        $users = $this->userService->getPaginatedUsers(10);
        return view('users.index', compact('users'));
    }

    public function store(StoreUserRequest $request)
    {
        $this->userService->createUser($request->validated());
        return back()->with('success', 'User created successfully.');
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->userService->updateUser($user, $request->validated());
        return back()->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        try {
            $this->userService->deleteUser($user);
            return back()->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
