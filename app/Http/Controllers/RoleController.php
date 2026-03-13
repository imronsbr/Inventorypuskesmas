<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response|JsonResponse
    {
        $query = Role::query();
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Sorting
        $sortBy = $request->get('sortBy', 'name');
        $sortDir = $request->get('sortDir', 'asc');
        $query->orderBy($sortBy, $sortDir);
        
        // Pagination
        $perPage = $request->get('perPage', 10);
        $roles = $query->paginate($perPage)->withQueryString();
        
        // If API request, return JSON
        if ($request->wantsJson() && !$request->has('inertia')) {
            return response()->json([
                'data' => $roles->items(),
                'meta' => [
                    'current_page' => $roles->currentPage(),
                    'last_page' => $roles->lastPage(),
                    'per_page' => $roles->perPage(),
                    'total' => $roles->total(),
                ]
            ]);
        }
        
        // Return Inertia response for web
        return Inertia::render('Role/Index', [
            'roles' => $roles,
            'filters' => [
                'search' => $request->search,
                'sortBy' => $sortBy,
                'sortDir' => $sortDir,
                'perPage' => $perPage,
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Role/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:roles,name',
            ]);

            $role = Role::create($validated);

            // If API request, return JSON
            if ($request->wantsJson() && !$request->has('inertia')) {
                return response()->json([
                    'message' => 'Role berhasil dibuat',
                    'data' => $role
                ], 201);
            }

            return redirect()->route('roles.index')
                ->with('success', 'Role berhasil dibuat');

        } catch (ValidationException $e) {
            if ($request->wantsJson() && !$request->has('inertia')) {
                return response()->json([
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Role $role): Response|JsonResponse
    {
        // If API request, return JSON
        if ($request->wantsJson() && !$request->has('inertia')) {
            return response()->json([
                'data' => $role
            ]);
        }

        return Inertia::render('Role/Show', [
            'role' => $role
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role): Response
    {
        return Inertia::render('Role/Edit', [
            'role' => $role
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role): RedirectResponse|JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            ]);

            $role->update($validated);

            // If API request, return JSON
            if ($request->wantsJson() && !$request->has('inertia')) {
                return response()->json([
                    'message' => 'Role berhasil diperbarui',
                    'data' => $role
                ]);
            }

            return redirect()->route('roles.index')
                ->with('success', 'Role berhasil diperbarui');

        } catch (ValidationException $e) {
            if ($request->wantsJson() && !$request->has('inertia')) {
                return response()->json([
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Role $role): RedirectResponse|JsonResponse
    {
        try {
            // Check if role is being used by any users
            if ($role->users()->count() > 0) {
                $message = 'Role tidak dapat dihapus karena masih digunakan oleh pengguna';
                
                if ($request->wantsJson() && !$request->has('inertia')) {
                    return response()->json([
                        'message' => $message
                    ], 422);
                }
                
                return redirect()->route('roles.index')
                    ->with('error', $message);
            }

            $role->delete();

            // If API request, return JSON
            if ($request->wantsJson() && !$request->has('inertia')) {
                return response()->json([
                    'message' => 'Role berhasil dihapus'
                ]);
            }

            return redirect()->route('roles.index')
                ->with('success', 'Role berhasil dihapus');

        } catch (\Exception $e) {
            $message = 'Terjadi kesalahan saat menghapus role';
            
            if ($request->wantsJson() && !$request->has('inertia')) {
                return response()->json([
                    'message' => $message
                ], 500);
            }
            
            return redirect()->route('roles.index')
                ->with('error', $message);
        }
    }
}
