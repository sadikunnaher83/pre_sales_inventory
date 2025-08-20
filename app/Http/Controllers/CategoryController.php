<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

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

        return response()->json([
            'status' => "success",
            'message' => "Category Created Successfully",
        ]);
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

         return response()->json([
             'status' => "success",
             'message' => "Category Updated Successfully",
         ]);
    }
//end method
    public function CategoryDelete(Request $request, $id)
    {
        $user_id = $request->header('id');
        $category = Category::where('id', $id)->where('user_id', $user_id)->delete();

        return response()->json([
            'status' => "success",
            'message' => "Category Deleted Successfully",
        ]);
    }
    //end method
}
