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
    $request->validate([
        'name' => 'required|unique:pots,name',
    ]);

    $name = trim($request->input('name'));
    if ($name === '') {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Tên chậu không được để trống.');
    }

    if (Pot::where('name', $name)->exists()) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Tên chậu đã tồn tại.');
    }

    // Tạo chậu mới
    $pot = Pot::create(['name' => $name]);

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
        $request->validate([
            'name' => 'required|unique:pots,name,' . $pot->id,
        ]);
        if (!$request->has('name') || trim($request->input('name')) === '') {
            return redirect()->back()->with('error', 'Tên chậu không được để trống.');
        }
        $pot->update($request->only('name'));

        return redirect()->route('admin.pot.index')->with('success', 'Cập nhật chậu thành công.');
    }

    public function destroy(Pot $pot)
    {
        $pot->delete();
        return redirect()->route('admin.pot.index')->with('success', 'Xóa chậu thành công.');
    }
}
