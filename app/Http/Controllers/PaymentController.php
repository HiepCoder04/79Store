<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\CheckoutController;

class PaymentController extends Controller{
 public function vnpay_payment(Request $request)
  {
    $data = $request->all();
    
    // Lưu thông tin đơn hàng và dữ liệu người dùng vào session để sử dụng sau khi quay về từ cổng thanh toán
    if(isset($data['name'])) {
      session(['checkout_data' => $data]);
    }
    
    $code_cart = rand(00, 9999);
    $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
    $vnp_Returnurl = url('/vnpay-callback'); // URL callback để xử lý kết quả từ VNPAY
    $vnp_TmnCode = "2EIW7I1X"; //Mã website tại VNPAY 
    $vnp_HashSecret = "8PLZB6CNPBBP44KUAE9RIA0TANIKIY6Q"; //Chuỗi bí mật

    $vnp_TxnRef = $code_cart; //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
    $vnp_OrderInfo = 'Thanh toán đơn hàng 79Store';
    $vnp_OrderType = 'billpayment';
    $vnp_Amount = $data['amount'] * 100;
    $vnp_Locale = 'vn';
    $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

    $inputData = array(
      "vnp_Version" => "2.1.0",
      "vnp_TmnCode" => $vnp_TmnCode,
      "vnp_Amount" => $vnp_Amount,
      "vnp_Command" => "pay",
      "vnp_CreateDate" => date('YmdHis'),
      "vnp_CurrCode" => "VND",
      "vnp_IpAddr" => $vnp_IpAddr,
      "vnp_Locale" => $vnp_Locale,
      "vnp_OrderInfo" => $vnp_OrderInfo,
      "vnp_OrderType" => $vnp_OrderType,
      "vnp_ReturnUrl" => $vnp_Returnurl,
      "vnp_TxnRef" => $vnp_TxnRef,
    );

    if (isset($vnp_BankCode) && $vnp_BankCode != "") {
      $inputData['vnp_BankCode'] = $vnp_BankCode;
    }
    if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
      $inputData['vnp_Bill_State'] = $vnp_Bill_State;
    }

    ksort($inputData);
    $query = "";
    $i = 0;
    $hashdata = "";
    foreach ($inputData as $key => $value) {
      if ($i == 1) {
        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
      } else {
        $hashdata .= urlencode($key) . "=" . urlencode($value);
        $i = 1;
      }
      $query .= urlencode($key) . "=" . urlencode($value) . '&';
    }

    $vnp_Url = $vnp_Url . "?" . $query;
    if (isset($vnp_HashSecret)) {
      $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret); //  
      $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
    }
    $returnData = array(
      'code' => '00',
      'message' => 'success',
      'data' => $vnp_Url
    );
    if (isset($_POST['redirect'])) {
      return redirect()->away($vnp_Url);
    } else {
      echo json_encode($returnData);
    }
  }

  /**
   * Xử lý callback từ VNPAY
   */
  public function vnpayCallback(Request $request)
  {
    $data = $request->all();
    
    // Kiểm tra xem giao dịch có thành công không
    if(isset($data['vnp_ResponseCode']) && $data['vnp_ResponseCode'] == '00') {
      // Giao dịch thành công
      if(session()->has('checkout_data')) {
        // Lấy dữ liệu đơn hàng từ session
        $checkoutData = session('checkout_data');
        
        // Thêm phương thức thanh toán là vnpay và đã thanh toán
        $checkoutData['payment_method'] = 'vnpay';
        $checkoutData['payment_status'] = 'paid'; // Đánh dấu đã thanh toán
        
        // Tạo đơn hàng
        $request = new Request($checkoutData);
        return app(CheckoutController::class)->store($request);
      } else {
        return redirect()->route('checkout.index')
          ->with('error', 'Không tìm thấy thông tin đơn hàng, vui lòng thử lại.');
      }
    } else {
      // Giao dịch thất bại
      return redirect()->route('checkout.index')
        ->with('error', 'Thanh toán không thành công. Vui lòng thử lại hoặc chọn phương thức thanh toán khác.');
    }
  }
}
