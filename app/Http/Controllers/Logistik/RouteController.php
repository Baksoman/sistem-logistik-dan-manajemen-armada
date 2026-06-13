<?php

namespace App\Http\Controllers\Logistik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RouteService;
use App\Services\RoutingService;
use App\Models\Route;

class RouteController extends Controller
{
    public function __construct(
        protected RouteService $routeService,
        protected RoutingService $routingService
    ) {}

    public function index()
    {
        $routes = $this->routeService->getPaginatedRoutes(10);
        return view('logistik.routes.index', compact('routes'));
    }

    public function create()
    {
        return view('logistik.routes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'route_code' => 'required|string|unique:routes,route_code|max:255',
            'route_type' => 'required|in:land,sea,combined',
            'origin_name' => 'required|string|max:255',
            'destination_name' => 'required|string|max:255',
            'waypoints' => 'required|json'
        ]);

        $waypoints = json_decode($validated['waypoints'], true);

        if (!is_array($waypoints) || count($waypoints) < 2) {
            return back()->with('error', 'Minimal diperlukan origin dan destination koordinat.');
        }

        try {
            $this->routeService->createRoute($validated, $waypoints);
            return redirect()->route('routes.index')->with('success', 'Rute berhasil dibuat dan dihitung.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $route = $this->routeService->getRouteById($id);
        return view('logistik.routes.show', compact('route'));
    }

    public function destroy(Route $route)
    {
        try {
            $this->routeService->deleteRoute($route);
            return redirect()->route('routes.index')->with('success', 'Rute berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // Ajax endpoint to test calculation without saving
    public function calculatePreview(Request $request)
    {
        $request->validate([
            'route_type' => 'required|in:land,sea,combined',
            'waypoints' => 'required|array|min:2'
        ]);

        try {
            if ($request->route_type === 'land') {
                $result = $this->routingService->calculateLandRoute($request->waypoints);
            } elseif ($request->route_type === 'sea') {
                $result = $this->routingService->calculateSeaRoute($request->waypoints[0], end($request->waypoints));
            } else {
                $result = $this->routingService->calculateCombinedRoute($request->waypoints);
            }
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
