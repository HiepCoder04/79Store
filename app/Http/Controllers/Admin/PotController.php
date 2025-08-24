<?php
namespace App\Http\Controllers\Admin;

use App\Models\Pot;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PotController extends Controller
{
    public function index(Request $request)
    {
 $query = Pot::query();

    // Lọc theo tên (q = keyword)
    if ($request->filled('q')) {
        $query->where('name', 'like', '%'.$request->q.'%');
    }

    // Lọc theo giá
    $min = $request->input('price_min');
    $max = $request->input('price_max');

    // ép về số nguyên
    if ($min !== null && $min !== '') $min = (int)$min;
    if ($max !== null && $max !== '') $max = (int)$max;

    // nếu nhập cả 2 và min > max thì hoán đổi
    if (is_int($min) && is_int($max) && $min > $max) {
        [$min, $max] = [$max, $min];
    }

    if (is_int($min)) $query->where('price', '>=', $min);
    if (is_int($max)) $query->where('price', '<=', $max);

    $pots = $query->latest()
        ->paginate(10)
        ->appends($request->query()); // giữ tham số khi phân trang

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
    $quantity = $request->input('quantity');
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
    // Kiểm tra xem số lượng có hợp lệ không
    if (!is_numeric($quantity) || intval($quantity) < 0) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Số lượng phải là số nguyên không âm.');
    }


    // Tạo chậu mới
    $pot = Pot::create([
        'name' => $name,
        'price' => $price,
        'quantity' => $quantity,
    ]);

    // Gắn chậu này cho tất cả các biến thể sản phẩm hiện có
    

    return redirect()->route('admin.pot.index')->with('success', 'Thêm chậu thành công.');
}


    public function edit(Pot $pot)
    {
        return view('admin.pot.edit', compact('pot'));
    }

    public function update(Request $request, Pot $pot)
    {
        $name = trim($request->input('name'));
        $price = $request->input('price');
        $quantity = $request->input('quantity');
        
         if ($name === '') {
        return redirect()->back()->withInput()->with('error', 'Tên chậu không được để trống.');
    }
    // Kiểm tra xem tên chậu đã tồn tại chưa
    if (Pot::where('name', $name)->where('id', '!=', $pot->id)->exists()) {
    return redirect()->back()->withInput()->with('error', 'Tên chậu đã tồn tại.');
}

    if (!is_numeric($price) || $price < 0) {
        return redirect()->back()->withInput()->with('error', 'Giá phải là một số hợp lệ lớn hơn hoặc bằng 0.');
    }
      // Kiểm tra xem số lượng có hợp lệ không
    if (!is_numeric($quantity) || intval($quantity) < 0) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Số lượng phải là số nguyên không âm.');
    }
          $pot->update([
        'name' => $name,
        'price' => $price,
        'quantity' => $quantity,
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
