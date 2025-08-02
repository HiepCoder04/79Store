<?php
namespace App\Http\Controllers\Admin;

use App\Models\Pot;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PotController extends Controller
{
    public function index()
    {
        $pots = Pot::all();
        return view('admin.pot.index', compact('pots'));
    }

    public function create()
    {
        return view('admin.pot.create');
    }

  public function store(Request $request)
{

    $name = trim($request->input('name'));
    $price = $request->input('price');
    // Kiểm tra xem tên chậu có để trống không
     if ($name === '') {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Tên chậu không được để trống.');
    }
    // Kiểm tra xem tên chậu đã tồn tại chưa
    if (Pot::where('name', $name)->exists()) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Tên chậu đã tồn tại.');
    }
    // Kiểm tra xem giá có hợp lệ không
    if (!is_numeric($price) || $price < 0) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Giá phải là một số hợp lệ lớn hơn hoặc bằng 0.');
    }


    // Tạo chậu mới
    $pot = Pot::create([
        'name' => $name,
        'price' => $price,
    ]);

    // Gắn chậu này cho tất cả các biến thể sản phẩm hiện có
    $variantIds = \App\Models\ProductVariant::pluck('id'); // Lấy ID tất cả biến thể

    foreach ($variantIds as $variantId) {
        DB::table('pot_product_variant')->insert([
            'pot_id' => $pot->id,
            'product_variant_id' => $variantId,
        ]);
    }

    return redirect()->route('admin.pot.index')->with('success', 'Thêm chậu và gắn cho các biến thể thành công.');
}


    public function edit(Pot $pot)
    {
        return view('admin.pot.edit', compact('pot'));
    }

    public function update(Request $request, Pot $pot)
    {
        $name = trim($request->input('name'));
        $price = $request->input('price');
         if ($name === '') {
        return redirect()->back()->withInput()->with('error', 'Tên chậu không được để trống.');
    }

    if (!is_numeric($price) || $price < 0) {
        return redirect()->back()->withInput()->with('error', 'Giá phải là một số hợp lệ lớn hơn hoặc bằng 0.');
    }
          $pot->update([
        'name' => $name,
        'price' => $price,
    ]);

        return redirect()->route('admin.pot.index')->with('success', 'Cập nhật chậu thành công.');
    }

   public function destroy($id)
{
    $pot = Pot::findOrFail($id);

    $pot->delete();

    return redirect()->route('admin.pot.index')->with('success', 'Xóa chậu thành công.');
}
}
