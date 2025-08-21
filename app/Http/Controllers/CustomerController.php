<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Customer;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;

class CustomerController extends Controller
{
    public function CustomerCreate(Request $request)
    {
        try {
            $user_id = $request->header('id');

            $request->validate([
                'name' => 'required',
                'email' => 'required|email',
                'phone' => 'required|numeric',
            ]);

            Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'user_id' => $user_id,
            ]);

            return response()->json([
                'status' => 'true',
                'message' => 'Customer created successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'false',
                'message' => 'something went wrong Please try again later',
            ]);
        }
    }//end method

    public function CustomerList(Request $request)
    {
        $user_id = $request->header('id');

       return Customer::where('user_id', $user_id)->get();

    }//end method

    public function CustomerById(Request $request)
    {
        $user_id = $request->header('id');
        return Customer::where('id', $request->input('id'))->where('user_id', $user_id)->first();
    }//end method

    public function CustomerUpdate(Request $request)
    {
                $user_id = $request->header('id');

                 $request->validate([
                    'name' => 'required',
                    'email' => 'required|email',
                    'phone' => 'required|numeric',
               ]);

               Customer::where('id', $request->input('id'))->where('user_id', $user_id)->update([
                   'name' => $request->input('name'),
                   'email' => $request->input('email'),
                   'phone' => $request->input('phone'),
               ]);

               return response()->json([
                   'status' => 'true',
                   'message' => 'Customer updated successfully',
               ]);

    }//end method

    public function CustomerDelete(Request $request, $id)
    {
        $user_id = $request->header('id');
        Customer::where('id', $id)->where('user_id', $user_id)->delete();

        return response()->json([
            'status' => 'true',
            'message' => 'Customer deleted successfully',
        ]);
    }


}
