<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BorrowRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CheckinController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('checkins.index');
    }

//    public function returnBook()
//    {
//        return view('checkins.return-book');
//    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'isbn' => 'required|exists:books,isbn',
            'qty' => 'required|integer|min:1'
        ]);

        // Find the book by ISBN
        $book = Book::where('isbn', $request->isbn)->first();
        if (!$book) {
            return back()->with('error', 'សៀវភៅមិនត្រូវបានរកឃើញ។');
        }

        // Find active borrow record
        $borrowRecord = BorrowRecord::where('user_id', $request->user_id)
            ->where('book_id', $book->book_id)
            ->where('borrow_status', 'checked_out')
            ->first();

        if (!$borrowRecord) {
            return back()->with('error', 'មិនមានការខ្ចីសៀវភៅនេះទេ។');
        }

        if ($borrowRecord->quantity < $request->qty) {
            return back()->with('error', 'ចំនួនសៀវភៅដែលត្រឡប់មកវិញច្រើនជាងចំនួនដែលបានខ្ចី។');
        }

        // Calculate fee if overdue
        $fee = 0.00;
        $today = Carbon::today();
        $dueDate = Carbon::parse($borrowRecord->check_out_date)->addDays(14);

        if ($today->gt($dueDate)) {
            $daysLate = $today->diffInDays($dueDate);
            $fee = $daysLate * 1000; // 1000 រៀល per day
        }

        // Update borrow record
        $borrowRecord->update([
            'check_in_date' => now(),
            'borrow_status' => 'checked_in',
            'penalty' => $fee
        ]);

        // Update book stock
        $book->increment('available_stock', $request->qty);

        return redirect()->route('checkins.index')
            ->with('success', 'សៀវភៅត្រូវបានត្រឡប់មកវិញដោយជោគជ័យ។' .
            ($fee > 0 ? ' ផាកពិន័យ: ' . $fee . ' រៀល' : ''));
    }

    public function checkStatus(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'isbn' => 'required'
        ]);

        $book = Book::where('isbn', $request->isbn)->first();
        if (!$book) {
            return response()->json(['status' => 'error', 'message' => 'សៀវភៅមិនត្រូវបានរកឃើញ។']);
        }

        $borrowRecord = BorrowRecord::where('user_id', $request->user_id)
            ->where('book_id', $book->book_id)
            ->where('borrow_status', 'checked_out')
            ->first();

        if (!$borrowRecord) {
            return response()->json(['status' => 'error', 'message' => 'មិនមានការខ្ចីសៀវភៅនេះទេ។']);
        }

        $dueDate = Carbon::parse($borrowRecord->check_out_date)->addDays(14);
        $isOverdue = Carbon::today()->gt($dueDate);

        return response()->json([
            'status' => 'success',
            'data' => [
                'borrowRecord' => $borrowRecord,
                'isOverdue' => $isOverdue,
                'dueDate' => $dueDate->format('Y-m-d'),
                'daysOverdue' => $isOverdue ? Carbon::today()->diffInDays($dueDate) : 0
            ]
        ]);
    }
}
