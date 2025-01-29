<?php


namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use App\Models\CartItemsModel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message)
    {
        $response = [
            'success' => "true",
            'message' => $message,
            'data' => $result
        ];
        return response()->json($response, 200);
    }
    public function sendMessage($message)
    {
        $response = [
            'success' => "true",
            'message' => $message
        ];
        return response()->json($response, 200);
    }
    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => "false",
            'message' => $error,
        ];
        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }
        return response()->json($response, $code);
    }


    public function s3FetchFile($filekey)
    {
        if (Storage::disk('s3')->exists($filekey)) {
            // $url = Storage::disk('s3')->temporaryUrl($filekey, now()->addMinutes(10));
            //public url
            $url = Storage::disk('s3')->url($filekey);
            return (string)$url;
        }
        return false;
    }
    public function s3DeleteFile($filekey)
    {
        // $deleted = Storage::disk('s3')->delete($filekey);
        return Storage::disk('s3')->delete($filekey);
    }

    public function monthlyUsersChart()
    {
        $usersPerYear = DB::table('users')
            ->select(DB::raw('MONTHNAME(created_at) as x'), DB::raw('count(*) as y'), DB::raw('max(created_at) as createdAt'))
            ->where('user_type', 'Normal')
            ->whereBetween('created_at', [
                Carbon::now()->startOfYear(),
                Carbon::now()->endOfYear(),
            ])->groupBy('x')->orderBy('createdAt')->get();

        $months = $this->getAllMonths();
        $data = $this->fillMissingMonths($usersPerYear, $months);

        return $data;
    }

    public function monthlyVendorChart()
    {
        $usersPerYear = DB::table('users')
            ->select(DB::raw('MONTHNAME(created_at) as x'), DB::raw('count(*) as y'), DB::raw('max(created_at) as createdAt'))
            ->where('user_type', 'Vendor')
            ->whereBetween('created_at', [
                Carbon::now()->startOfYear(),
                Carbon::now()->endOfYear(),
            ])->groupBy('x')->orderBy('createdAt')->get();

        $months = $this->getAllMonths();
        $data = $this->fillMissingMonths($usersPerYear, $months);

        return $data;
    }

    private function getAllMonths()
    {
        return [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
    }
    private function fillMissingMonths($usersPerYear, $months)
    {
        $data = [];
        foreach ($months as $month) {
            $matchingRecord = $usersPerYear->firstWhere('x', $month);
            $count = $matchingRecord ? $matchingRecord->y : 0;
            $data[] = [
                'x' => $month,
                'y' => (int)$count
            ];
        }
        return $data;
    }

    public function monthlySelling()
    {
        $sellPerYear = DB::table('orders')
            ->select(DB::raw('MONTHNAME(created_at) as x'), DB::raw('sum(total) as y'), DB::raw('max(created_at) as createdAt'))
            ->where('order_status', 2)
            ->whereBetween('created_at', [
                Carbon::now()->startOfYear(),
                Carbon::now()->endOfYear(),
            ])->groupBy('x')->orderBy('createdAt')->get();

        $months = $this->getAllMonths();
        $data = $this->fillMissingMonths($sellPerYear, $months);

        return $data;
    }

    public function monthlySellingOfvendor($vendor_id)
    {
        // $sellPerYear = DB::table('orders')
        //     ->select(DB::raw('MONTHNAME(created_at) as x'), DB::raw('sum(total) as y'), DB::raw('max(created_at) as createdAt'))
        //     ->where('order_status', 2)
        //     ->whereBetween('created_at', [
        //         Carbon::now()->startOfYear(),
        //         Carbon::now()->endOfYear(),
        //     ])->groupBy('x')->orderBy('createdAt')->get();
        try {
            //code...
            $sellPerYear = CartItemsModel::join('orders', 'orders.id', '=', 'cart_items.order_id')
                // ->join('products', 'cart_items.product_id', '=', 'products.product_id')
                ->select(
                    DB::raw('MONTHNAME(cart_items.updated_at) as x'),
                    // DB::raw('SUM(cart_items.total) as y'),
                    DB::raw('SUM(cart_items.price + cart_items.shipping_charge) as y'),
                    DB::raw('MAX(cart_items.updated_at) as createdAt')
                )
                ->where('cart_items.checked', 1)
                ->where('cart_items.vendor_id', $vendor_id)
                ->whereBetween('orders.updated_at', [
                    Carbon::now()->startOfYear(),
                    Carbon::now()->endOfYear(),
                ])
                ->groupBy('x')
                ->orderBy('createdAt')
                ->get();
            $months = $this->getAllMonths();
            $data = $this->fillMissingMonths($sellPerYear, $months);
            return $data;
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError('SolvThisErrors', $th->getMessage());
        }
    }
    
    function sendNotification($data)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';

        $serverKey = "AAAAlA1mt_c:APA91bFRlO3IAv2MmAiD658oICfevFgeXjm-Kg2g8QVOrUSV8k8Svr_396-APLTnqCWiFJs2dqF63kIv9KyIS44pZWWF-N6h9NFLhNAkAu2ZR36GfNZR3Stv_sDegOsG41acp79IqtUz";
        $encodedData = json_encode($data);
        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        // Close connection
        curl_close($ch);
        return true;
    }
}
