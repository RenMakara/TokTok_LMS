<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Barryvdh\DomPDF\Facade\Pdf;
class UserController extends Controller
{

    public function userList(Request $request)
    {
        $query = User::query();

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(10)->withQueryString();

        $deleteConfig = [
            'title' => 'តើអ្នកប្រាកដទេថានឹងលុបសមាជិកនេះ?',
            'html' => '<div style="text-align: left;">
                        <p style="margin-bottom: 10px; text-align: center;">អ្នកកំពុងត្រៀមលុប</p>
                    </div>',
            'icon' => 'warning',
            'showCancelButton' => true,
            'confirmButtonColor' => '#b50404ff',
            'cancelButtonColor' => '#aeaeaeff',
            'confirmButtonText' => 'បាទ/​ចាស, លុប!',
            'cancelButtonText' => 'បោះបង់',
            'reverseButtons' => true,
            'focusCancel' => true
        ];

        session(['alert.delete' => json_encode($deleteConfig)]);

        return view('users.userList', compact('users'));
    }


    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request, User $user)
    {
    
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|min:9',
        ],[
            'phone.min' => 'លេខទូរស័ព្ទត្រូវមានយ៉ាងតិច ៩ ខ្ទង់',
            'email.unique' => 'អ៊ីមែលនេះបានប្រើរួចហើយ',
            'full_name.required' => 'សូមបញ្ចូលឈ្មោះពេញ',
            'email.required' => 'សូមបញ្ចូលអ៊ីមែល',
            'phone.required' => 'សូមបញ្ចូលលេខទូរស័ព្ទ'
        ]);

        User::create([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => 'member'
        ]);
        $userName = $user->full_name ?? $validated['full_name'];

        Alert::success('ជោគជ័យ', ' សមាជិក ' . $userName . ' ត្រូវបានបង្កើតដោយជោគជ័យ!')
             ->persistent(false)
             ->autoClose(2000);
        return redirect()->route('users.userList');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $userName = $user->full_name;
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|min:9'
        ],[
            'phone.min' => 'លេខទូរស័ព្ទត្រូវមានយ៉ាងតិច ៩ ខ្ទង់',
            'email.unique' => 'អ៊ីមែលនេះបានប្រើរួចហើយ',
            'full_name.required' => 'សូមបញ្ចូលឈ្មោះពេញ',
            'email.required' => 'សូមបញ្ចូលអ៊ីមែល',
            'phone.required' => 'សូមបញ្ចូលលេខទូរស័ព្ទ'
        ]);
        $user->update($validated);

        Alert::success('ជោគជ័យ', 'សមាជិក ' . $userName . ' ត្រូវបានកែប្រែដោយជោគជ័យ!')
             ->persistent(false)
             ->autoClose(2000);
        return redirect()->route('users.userList');
    }

     public function destroy(User $user)
    {
        $userName = $user->full_name;
        $user->delete();

        Alert::success('ជោគជ័យ!', "សមាជិក $userName ត្រូវបានលុបរួចរាល់!")
             ->persistent(false)
             ->autoClose(2000);

        return redirect()->route('users.userList');
    }

    //== == == Print to pdf function == == ==

    public function printPreview()
    {
        $users = User::all();
        return view('users.print-preview', compact('users'));
    }

    public function printPdf(Request $request)
    {
        $printOption = $request->input('print_option', 'all');
        $customLimit = $request->input('custom_limit', 10);

        $query = User::query();

        switch ($printOption) {
            case 'custom':
                $users = $query->limit($customLimit)->get();
                break;
            case 'all':
            default:
                $users = $query->get();
                break;
        }

        $pdf = Pdf::loadView('users.pdf', compact('users'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('users-' . $printOption . '.pdf');
    }

    public function search(Request $request)
    {
        $search = $request->q;
        $users = User::where('full_name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%")
                       ->orWhere('user_id', 'like', "%{$search}%")
                       ->get();

        $response = [];
        foreach($users as $user){
            $response[] = [
                "id" => $user->user_id,
                "text" => $user->full_name . ' - ' . $user->email
            ];
        }

        return response()->json($response);
    }
}
