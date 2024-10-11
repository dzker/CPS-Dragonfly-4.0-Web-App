<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Models\User;
use App\Models\Item;
use App\Models\Mission;
use Hash;

class AuthController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function index()
    {

        if(Auth::check()){
            return redirect("dashboard");
        }

        return view('auth.login');
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function registration()
    {
        if(Auth::check()){
            return redirect("dashboard");
        }

        return view('auth.registration');
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function postLogin(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('name', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->intended('dashboard')->withSuccess('You have successfully login');
        }

        return redirect("login")->with('invalid','You have entered invalid credentials');
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function postRegistration(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $data = $request->all();
        $check = $this->create($data);


        return redirect("login")->with('registered','You have successfully registered');
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function dashboard()
    {
        if (Auth::check()) {
            try {
                $items = Item::whereIn('status', ['Available', 'Unresolved', 'Lost'])->get();

                // Fetch mission statistics with cumulative count
                $missionStats = Mission::selectRaw('DATE(start_time) as date')
                    ->whereNotNull('start_time')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get()
                    ->map(function ($mission, $key) use (&$cumulativeCount) {
                        $cumulativeCount = isset($cumulativeCount) ? $cumulativeCount + 1 : 1;
                        return [
                            'date' => $mission->date,
                            'count' => $cumulativeCount
                        ];
                    });

                // Fetch item statistics
                $itemStats = Item::selectRaw('status, count(*) as count')
                    ->groupBy('status')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'status' => $item->status,
                            'count' => $item->count
                        ];
                    });

                // Fetch mission duration data
                $missionDurations = Mission::select('mission_id', 'start_time', 'end_time')
                    ->whereNotNull('start_time')
                    ->whereNotNull('end_time')
                    ->orderBy('start_time')
                    ->get()
                    ->map(function ($mission) {
                        $duration = strtotime($mission->end_time) - strtotime($mission->start_time);
                        return [
                            'mission_id' => $mission->mission_id,
                            'start_time' => $mission->start_time,
                            'duration' => $duration / 3600 // Duration in hours
                        ];
                    });

                return view('dashboard', [
                    'items' => $items,
                    'missionStats' => $missionStats,
                    'itemStats' => $itemStats,
                    'missionDurations' => $missionDurations
                ]);
            } catch (\Exception $e) {
                \Log::error('Error in dashboard method: ' . $e->getMessage());
                return redirect("login")->with('error', 'An error occurred while loading the dashboard. Please try again.');
            }
        }
        return redirect("login")->with('noaccess', 'Please login to access that page');
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function create(array $data)
    {
      return User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password'])
      ]);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function logout() {
        Session::flush();
        Auth::logout();

        return Redirect('login');
    }
}
