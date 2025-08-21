<?php

namespace App\Http\Controllers;

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
}
