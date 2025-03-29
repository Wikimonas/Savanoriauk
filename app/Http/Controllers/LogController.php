<?php
namespace App\Http\Controllers;

use App\Models\ActionLog;  // Assuming you have a Log model
use App\Models\User;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        // Retrieve the username from the query string (if available)
        $name = $request->input('name');
        $user = User::where('name', $name)->first();
        if ($name && !$user) {
            // Handle the case where the user is not found
            return redirect()->route('logs.index')->with('error', 'User not found');
        }
        $user_id = $user ? $user->id : null;

        $logs = ActionLog::query()
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('user_id', $user_id); // Assuming the ActionLog has a 'user_id' column
            })
            ->paginate(10);  // Paginate the results if needed

        // Return the view with the filtered logs
        return view('dashboard', compact('logs'));
    }
}
