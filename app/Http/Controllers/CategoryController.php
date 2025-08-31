<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function CategoryCreate(Request $request)
    {
        //  dd($request->all());
         $user_id =$request->header('id');
        // $user_id = $request->user_id; //body teke user_id nibe


        Category::create([
            'name' => $request->name,
            'user_id' => $user_id ,
        //    $user_id = $request->input('user_id'),//body teke user_id nibe

        ]);

        // return response()->json([
        //     'status' => "success",
        //     'message' => "Category Created Successfully",
        // ]);

        $data = ['message' => 'Category created successfully', 'status' => 'true', 'error' => ''];
        return redirect('/CategoryPage')->with($data);
    }
    //end CategoryCreate

    public function CategoryList(Request $request)
    {
        $user_id =$request->header('id');
        $categories = Category::where('user_id', $user_id)->get();

        return $categories;
    }
    //end CategoryList

    public function CategoryById(Request $request)
    {
        $user_id =$request->header('id');
        $category = Category::where('user_id', $user_id)->where('id', $request->id)->first();

        return $category;
    }
    //end CategoryById

    public function CategoryUpdate(Request $request)
    {
         $user_id =$request->header('id');

         Category::where('id', $request->input('id'))->where('user_id', $user_id)->update([
            'name' => $request->name,
         ]);

        //  return response()->json([
        //      'status' => "success",
        //      'message' => "Category Updated Successfully",
        //  ]);

        $data = ['message' => 'Category updated successfully', 'status' => 'true', 'error' => ''];
        return redirect('/CategoryPage')->with($data);
    }
//end method
    public function CategoryDelete(Request $request, $id)
    {
        $user_id = $request->header('id');
        $category = Category::where('id', $id)->where('user_id', $user_id)->delete();

        // return response()->json([
        //     'status' => "success",
        //     'message' => "Category Deleted Successfully",
        // ]);

        $data = ['message' => 'Category deleted successfully', 'status' => 'true', 'error' => ''];
        return redirect('/CategoryPage')->with($data);
    }
    //end method

    public function CategoryPage(Request $request)
    {
        $user_id = $request->header('id');
        $categories = Category::where('user_id', $user_id)->latest()->get();

        return Inertia::render('CategoryPage', ['categories' => $categories]);

    }//end method

    public function CategorySavePage(Request $request)
    {
        $category_id = $request->query('id');

        $user_id = $request->header('id');

       $category = Category::where('id', $category_id)->where('user_id', $user_id)->first();
        return Inertia::render('CategorySavePage', ['category' => $category]);
    }//end method
}
