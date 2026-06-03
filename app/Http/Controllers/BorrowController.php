<?php

namespace App\Http\Controllers;

use App\Models\BorrowRecord;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BorrowController extends Controller
{
    /**
     * Show borrow record list with search + sweet alert.
     */
    public function index(Request $request)
    {
        $query = BorrowRecord::with(['book', 'user']); // eager load

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->whereHas('book', function($sub) use ($search) {
                        $sub->where('title', 'like', "%{$search}%")
                            ->orWhere('isbn', 'like', "%{$search}%");
                    })
                  ->orWhereHas('user', function($sub) use ($search) {
                        $sub->where('full_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                  ->orWhere('borrow_status', 'like', "%{$search}%");
            });
        }

        $records = $query->with(['book.authors', 'user'])
            ->latest('br_id')
            ->get();

        // SweetAlert delete confirmation
     
        // session(['alert.delete' => json_encode($deleteConfig)]);

        return view('borrow-records.index', compact('records'));
    }


    public function create()
    {
        $books = Book::all();
        $users = User::query()->get();

        return view('borrow-records.create', compact('books', 'users'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'user_id'        => 'required|exists:users,user_id',
            'book_id'        => 'required|exists:books,book_id',
            'quantity'       => 'required|integer|min:1',
            'check_out_date' => 'required|date',
        ]);

        $book = Book::findOrFail($request->book_id);

        if ($book->available_stock < $request->quantity) {
            return redirect()->back()
                ->with('error', 'Not enough books in stock.')
                ->withInput();
        }

        DB::transaction(function () use ($request, $book) {
            $book->available_stock -= $request->quantity;
            $book->save();

            $checkOutDate = Carbon::parse($request->check_out_date);
            $checkInDate  = $checkOutDate->copy()->addDays(14);

            BorrowRecord::create([
                'user_id'        => $request->user_id,
                'book_id'        => $request->book_id,
                'quantity'       => $request->quantity,
                'borrow_status'  => 'checked_out',
                'check_out_date' => $checkOutDate,
                'check_in_date'  => $checkInDate,
                'penalty'        => 0,
            ]);
        });

        return redirect()->route('borrow-records.index')
                         ->with('success', 'Borrow record created successfully!');
    }


    /**
     * Edit borrow record.
     */
   public function returnBook($id)
{
    $record = BorrowRecord::findOrFail($id);

    DB::transaction(function () use ($record) {
        $book = Book::find($record->book_id);

        if ($book) {
            $book->available_stock += $record->quantity;
            $book->save();
        }

        $record->check_in_date = now();
        $record->borrow_status = 'checked_in';
        $record->save();
    });

    return redirect()->back()->with('success', 'សៀវភៅត្រូវបានសងវិញរួចរាល់!');
}

public function extendBook($id)
{
    $record = BorrowRecord::findOrFail($id);

    // extend check_in_date + 7 days
    $record->check_in_date = \Carbon\Carbon::parse($record->check_in_date)->addDays(7);
    $record->save();

    return redirect()->back()->with('success', 'បានបន្តអានសៀវភៅនេះរួចរាល់!');
}

}
