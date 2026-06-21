<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Exports\UsersExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

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

    public function exportExcel()
    {
        return Excel::download(new UsersExport, 'laporan-users-' . now()->format('Ymd-His') . '.xlsx');
    }

    public function exportPdf()
    {
        $users = User::with('roles')->latest()->get();
        $headings = ['ID', 'Name', 'Email', 'Roles', 'Created At'];
        $data = $users->map(fn($u) => [
            $u->id, $u->name, $u->email,
            $u->roles->pluck('name')->join(', ') ?: '-',
            $u->created_at?->format('Y-m-d') ?? '-',
        ]);

        $pdf = Pdf::loadView('reports.pdf', [
            'title' => 'Laporan Data Users',
            'headings' => $headings,
            'data' => $data,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-users-' . now()->format('Ymd-His') . '.pdf');
    }
}
