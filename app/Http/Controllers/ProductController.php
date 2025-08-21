<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Exception;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function ProductCreate(Request $request)
    {
        try {
            $user_id = $request->header('id');

            $request->validate([
                'name' => 'required',
                'price' => 'required',
                'unit' => 'required',
                'category_id' => 'required',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            ]);

            $data = [
                'name' => $request->name,
                'price' => $request->price,
                'unit' => $request->unit,
                'category_id' => $request->category_id,
                'user_id' => $user_id
            ];

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $filePath = '/uploads/' . $filename;
                $file->move(public_path('uploads'), $filename);
                $data['image'] = $filePath;
            }

            Product::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Product created successfully',
            ]);
        }catch(Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }


    }
    //end method

    public function ProductList(Request $request)
    {

        $user_id = $request->header('id');
        $products = Product::where('user_id', $user_id)->get();
        return $products;
    }
    //end method

    public function ProductById(Request $request)
    {
        try {
            $user_id = $request->header('id');
            // $product = Product::where('user_id', $user_id)->findOrFail($request->id);
             $product = Product::where('id', $request->input('id'))->where('user_id', $user_id)->first();
            return response()->json([
                'status' => 'success',
                'data' => $product,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }//end method

    public function ProductUpdate(Request $request)
    {
        try {
            $user_id = $request->header('id');
            $product = Product::where('id', $request->input('id'))->where('user_id', $user_id)->first();

            if (!$product) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Product not found',
                ]);
            }

            $request->validate([
                'name' => 'required',
                'price' => 'required',
                'unit' => 'required',
                'category_id' => 'required',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            ]);

            $data = [
                'name' => $request->name,
                'price' => $request->price,
                'unit' => $request->unit,
                'category_id' => $request->category_id,
                'user_id' => $user_id
            ];

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $filePath = '/uploads/' . $filename;
                $file->move(public_path('uploads'), $filename);
                $data['image'] = $filePath;
            }

            $product->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Product updated successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }//end method


    public function ProductDelete(Request $request, $id)
{
    try {
        $user_id = $request->header('id');

    $product = Product::where('user_id', $user_id)->findOrFail($id);
    if ($product->image && file_exists(public_path($product->image))) {
        unlink(public_path($product->image));
    }

    $product->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'Product deleted successfully',
    ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'failed',
            'message' => 'something went wrong,Please try again later',
        ]);
    }
}

}
