<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\InvoiceProduct;
use App\Models\Product;
use Exception;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function InvoiceCreate(Request $request)
    {
        DB::beginTransaction();
        try {
            $user_id = $request->header('id');

            $data = [
                'user_id' => $user_id,
                'customer_id' => $request->input('customer_id'),
                'total' => $request->input('total'),
                'discount' => $request->input('discount'),
                'vat' => $request->input('vat'),
                'payable' => $request->input('payable'),
            ];

            $invoice = Invoice::create($data);

            $products = $request->input('products');
            foreach($products as $product){
                $exitUnit = Product::where('id', $product['id'])->select('unit')->first();
                if(!$exitUnit){
                    return response()->json([
                        'status' => 'failed',
                        'message' => "Product with ID {$product['id']} not found",
                    ],404);
                }

                if($exitUnit->unit < $product['unit']){
                    return response()->json([
                        'status' => 'failed',
                        'message' => "only {$exitUnit->unit} Unit available for Product with ID {$product['id']}",
                    ]);
                }
                InvoiceProduct::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $product['id'],
                    'user_id' => $user_id,
                    'quantity' => $product['unit'],
                    'sale_price' => $product['price'],
                ]);

                Product::where('id', $product['id'])->update(['unit' => $exitUnit->unit - $product['unit']]);

            }//end foreach


            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Invoice created successfully',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
        }
    }//End method

    public function InvoiceList(Request $request)
    {
        $user_id = $request->header('id');
        return Invoice::where('user_id', $user_id)->with(['customer'])->get();
    }//end method

    public function InvoiceDetails(Request $request)
    {

        $user_id = $request->header('id');

        $customerDetails = Customer::where('user_id', $user_id)->where('id', $request->input('customer_id'))->first();

        $invoiceDetails = Invoice::where('user_id', $user_id)->where('id', $request->input('invoice_id'))->first();

        $invoiceProducts = InvoiceProduct::where('user_id', $user_id)->where('invoice_id', $request->input('invoice_id'))->with('product')->get();

        return [
            'customer' => $customerDetails,
            'invoice' => $invoiceDetails,
            'products' => $invoiceProducts,
        ];

    }//end method


    public function InvoiceDelete(Request $request, $id)
    {
       DB::beginTransaction();
       try {
        $user_id = $request->header('id');
        InvoiceProduct::where('user_id', $user_id)->where('invoice_id', $id)->delete();
        Invoice::where('user_id', $user_id)->where('id', $id)->delete();

       DB::commit();

          return response()->json([
              'status' => 'success',
              'message' => 'Invoice deleted successfully',
          ]);
       } catch (Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => 'failed',
            'message' => 'Something went wrong, Please try again later',
        ]);
       }



    }//end method


}
