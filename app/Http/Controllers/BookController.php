<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Image;
use Illuminate\Http\Request;
use App\Http\Resources\BookResource;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Book::query();

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        $books = $query->with('images')
            ->latest()
            ->paginate(10);

        return BookResource::collection($books);
    }




    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $book=Book::create([
            'title'=>$request->title,
            'description'=>$request->description,
            'author_id' => $request->author_id ?: Auth::id()
        ]);
        $images = [];
    if ($request->hasFile('images')) {
      foreach ($request->file('images') as $image) {
           $images[] = [
               'path' => $this->uploadPhoto($image, "book"),
                'imageable_id'=>$book->id,
                'imageable_type'=>Book::class,
            ];
       }
}
Image::insert($images);

return response()->json([
    'success'=>true,
]);


}

    /**
     * Display the specified resource.
     */
    public function show( $id)
    {
        $book=Book::findOrFail($id);

        return new BookResource($book)->load('images');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $book=Book::findOrFail($id);
        $book->update([
            'title'=>$request->title,
            'description'=>$request->description,

        ]);
        if($request->hasFile('images')){
            foreach($book->images as $image){
                $this->deletePhoto($image->path);
                $image->delete();
            }
        $images=[];

        foreach($request->file('images')as $image)
        {
            $images[]=[
                'path'=>$this->uploadPhoto($image,'book'),
                'imageable_id'=>$book->id,
                'imageable_type'=>Book::class,
            ];
        }
        Image::insert($images);

        return response()->json([
            'success'=>true,
            'book'=>new BookResource($book),
        ]);
    }}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $book=Book::findOrFail($id);
        foreach($book->images as $image){
            $this->deletePhoto($image->path);
            $image->delete();
        }
        $book->delete();
    }
}
