<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use DB;
use Illuminate\Http\Request;

class BookController extends Controller
{
    // public function bookList(Request $request)
    // {
    //     $query = Book::with(['category', 'author']); // eager load relations

    //     if ($request->filled('q')) {
    //         $search = $request->q;
    //         $query->where(function ($q) use ($search) {
    //             $q->where('title', 'like', "%{$search}%")
    //               ->orWhere('isbn', 'like', "%{$search}%")
    //               ->orWhereHas('author', function ($q2) use ($search) {
    //                   $q2->where('name', 'like', "%{$search}%");
    //               })
    //               ->orWhereHas('category', function ($q3) use ($search) {
    //                   $q3->where('name', 'like', "%{$search}%");
    //               });
    //         });
    //     }

    //     $books = $query->paginate(8)->withQueryString();

    //     // Delete confirmation config
    //     $deleteConfig = [
    //         'title' => 'Are you sure to delete this book?',
    //         'html' => '<div style="text-align: left;">
    //                     <p style="margin-bottom: 10px; text-align: center;">You are about to delete the selected book</p>
    //                 </div>',
    //         'icon' => 'warning',
    //         'showCancelButton' => true,
    //         'confirmButtonColor' => '#830000ff',
    //         'cancelButtonColor' => '#969696ff',
    //         'confirmButtonText' => 'Yes, Delete!',
    //         'cancelButtonText' => 'Cancel',
    //         'reverseButtons' => true,
    //         'focusCancel' => true
    //     ];

    //     session(['alert.delete' => json_encode($deleteConfig)]);

    //     return view('books.bookList', compact('books'));
    // }

    public function bookList(Request $request)
    {
        $query = Book::with(['category', 'authors']); // eager load authors and category

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('isbn', 'like', "%{$search}%")
                ->orWhereHas('authors', function($sub) use ($search) {
                    $sub->where('author_name', 'like', "%{$search}%");
                })
                ->orWhereHas('category', function($sub) use ($search) {
                    $sub->where('category_name', 'like', "%{$search}%");
                });
            });
        }

        $books = $query->paginate(10)->withQueryString();

        // SweetAlert delete confirmation configuration
        $deleteConfig = [
            'title' => 'តើអ្នកប្រាកដថាចង់លុបសៀវភៅនេះមែនទេ?',
            'html' => '<div style="text-align: left;">
                        <p style="margin-bottom: 10px; text-align: center;">អ្នកកំពុងត្រៀមលុបសៀវភៅខាងក្រោម</p>
                    </div>',
            'icon' => 'warning',
            'showCancelButton' => true,
            'confirmButtonColor' => '#830000ff',
            'cancelButtonColor' => '#969696ff',
            'confirmButtonText' => 'បាទ/ចាស, លុប!',
            'cancelButtonText' => 'បោះបង់',
            'reverseButtons' => true,
            'focusCancel' => true
        ];
        

        session(['alert.delete' => json_encode($deleteConfig)]);

        return view('books.bookList', compact('books'));
    }


    public function create(){
        $categories = Category::all();
        $authors = Author::all();

        return view('books.create', compact('categories', 'authors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books,isbn',
            'category_id' => 'required|integer|exists:categories,category_id',
            'author_name' => 'required|string',
            'published_year' => 'required|digits:4|integer|min:1901|max:2155',
            'available_stock' => 'required|integer|min:0',
        ], [
            'title.required' => 'សូមបញ្ចូលចំណងជើងសៀវភៅ',
            'title.string' => 'ចំណងជើងត្រូវតែជាអក្សរ',
            'title.max' => 'ចំណងជើងត្រូវតែតិចជាង 255 តួអក្សរ',
        
            'isbn.required' => 'សូមបញ្ចូលលេខ ISBN',
            'isbn.string' => 'លេខ ISBN ត្រូវតែជាអក្សរ',
            'isbn.unique' => 'លេខ ISBN នេះមានរួចហើយ',
        
            'category_id.required' => 'សូមជ្រើសប្រភេទសៀវភៅ',
            'category_id.integer' => 'ប្រភេទត្រូវតែជាចំនួន',
            'category_id.exists' => 'ប្រភេទសៀវភៅមិនមាននៅក្នុងប្រព័ន្ធ',
        
            'author_name.required' => 'សូមបញ្ចូលឈ្មោះអ្នកនិពន្ធ',
            'author_name.string' => 'ឈ្មោះអ្នកនិពន្ធត្រូវតែជាអក្សរ',
        
            'published_year.required' => 'សូមបញ្ចូលឆ្នាំបោះពុម្ព',
            'published_year.digits' => 'ឆ្នាំបោះពុម្ពត្រូវតែមាន 4 តួ',
            'published_year.integer' => 'ឆ្នាំបោះពុម្ពត្រូវតែជាចំនួន',
            'published_year.min' => 'ឆ្នាំបោះពុម្ពត្រូវតែចាប់ពីឆ្នាំ 1901',
            'published_year.max' => 'ឆ្នាំបោះពុម្ពត្រូវតែតិចជាងឆ្នាំ 2155',
        
            'available_stock.required' => 'សូមបញ្ចូលស្តុកសៀវភៅ',
            'available_stock.integer' => 'ស្តុកត្រូវតែជាចំនួនគត់',
            'available_stock.min' => 'ស្តុកត្រូវតែធំជាង ឬស្មើ 0',
        ]);
        

        // 1️⃣ Clean & split author names
        $authorsInput = $request->author_name; // e.g., "Josh Kim & Malita Roush"
        $cleaned = str_replace(['&', '.', '/', ','], '', $authorsInput);
        $authorParts = preg_split('/\s+/', trim($cleaned), -1, PREG_SPLIT_NO_EMPTY);

        // 2️⃣ Loop through and find/create authors
        $authorIds = [];
        for ($i = 0; $i < count($authorParts); $i += 2) {
            // Merge first & last name
            $fullName = $authorParts[$i] . ' ' . ($authorParts[$i+1] ?? '');
            
            // Check if author exists
            $author = Author::firstOrCreate(
                ['author_name' => $fullName]
            );
            $authorIds[] = $author->author_id;
        }

        // 3️⃣ Create the book
        $book = Book::create([
            'title' => $request->title,
            'isbn' => $request->isbn,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'published_year' => $request->published_year,
            'available_stock' => $request->available_stock,
        ]);

        // 4️⃣ Attach authors via pivot table
        $book->authors()->attach($authorIds);

        return redirect()->route('books.bookList')->with('alert.config', json_encode([
            'title' => 'សៀវភៅបានបង្កើត!',
            'text' => "សៀវភៅ '{$book->title}' ត្រូវបានបង្កើតដោយជោគជ័យ",
            'icon' => 'success',
            'confirmButtonText' => 'យល់ព្រម'
        ]));
    }


    public function edit($book_id)
    {
        $book = Book::with(['authors', 'category'])->findOrFail($book_id);
        $categories = Category::all();

        return view('books.edit', compact('book', 'categories'));
    }



    public function update(Request $request, $book_id)
    {
        $request->validate([
            'available_stock' => 'required|integer|min:0',
        ], [
            'available_stock.required' => 'សូមបញ្ចូលស្តុកសៀវភៅ',
            'available_stock.integer' => 'ស្តុកត្រូវតែជាចំនួនគត់',
            'available_stock.min' => 'ស្តុកត្រូវតែធំជាង ឬស្មើ 0',
        ]);

        $book = Book::findOrFail($book_id);
        $book->update([
            'available_stock' => $request->available_stock,
        ]);

        return redirect()->route('books.bookList')->with('alert.config', json_encode([
            'title' => 'សៀវភៅបានកែសម្រួល!',
            'text' => "សៀវភៅ '{$book->title}' ត្រូវបានកែដោយជោគជ័យ",
            'icon' => 'success',
            'confirmButtonText' => 'យល់ព្រម'
        ]));
    }


    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();

        // Trigger SweetAlert success after deletion
        return redirect()->route('books.bookList')
            ->with('alert.config', json_encode([
                'title' => 'សៀវភៅបានលុបចោល!',
                'text' => "សៀវភៅ '{$book->title}' បានលុបចោលដោយជោគជ័យ",
                'icon' => 'success',
                'confirmButtonText' => 'OK',
            ]));
    }

    public function search(Request $request)
    {
        $search = $request->q;
        $books = Book::where('title', 'like', "%{$search}%")
                       ->orWhere('isbn', 'like', "%{$search}%")
                       ->get();

        $response = [];
        foreach($books as $book){
            $response[] = [
                "id" => $book->book_id,
                "text" => $book->title
            ];
        }

        return response()->json($response);
    }
}
