<?php

namespace App\Http\Controllers;

use App\Models\StockTransaction;
use App\Models\Inventory;
use App\Models\SparePart;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class StockTransactionController extends Controller
{
    public function inboundIndex()
    {
        // Mengambil riwayat transaksi masuk
        $transactions = StockTransaction::where('type', 'masuk')
            ->with(['sparePart', 'supplier'])
            ->latest()
            ->get();

        // Mengambil data untuk pilihan di dropdown form
        $parts = SparePart::all();
        $suppliers = Supplier::all();

        return view('admin-gudang-inboundstock', compact('transactions', 'parts', 'suppliers'));
    }

    // UC-02: Simpan Stok Masuk
    public function storeInbound(Request $request)
    {
        $request->validate([
            'spare_part_id' => 'required',
            'supplier_id' => 'required',
            'quantity' => 'required|integer|min:1',
            'reference' => 'required',
            'status' => 'required|in:Selesai,Pending,Batal', // Validasi status
        ]);

        DB::transaction(function () use ($request) {
            StockTransaction::create([
                'spare_part_id' => $request->spare_part_id,
                'user_id' => Auth::id(),
                'supplier_id' => $request->supplier_id,
                'type' => 'masuk',
                'quantity' => $request->quantity,
                'reference' => $request->reference,
                'status' => $request->status, // Mengambil dari input
                'notes' => $request->notes
            ]);

            // Stok hanya bertambah jika status langsung 'Selesai'
            if ($request->status === 'Selesai') {
                Inventory::where('spare_part_id', $request->spare_part_id)->increment('stock', $request->quantity);
            }
        });

        return redirect()->back()->with('success', 'Transaksi masuk berhasil dicatat.');
    }

    public function updateStatus(Request $request, $id)
    {
        $transaction = StockTransaction::findOrFail($id);
        $oldStatus = $transaction->status;
        $newStatus = $request->status;

        DB::transaction(function () use ($transaction, $oldStatus, $newStatus) {
            // Logika penyesuaian stok jika status berubah menjadi 'Selesai'
            if ($oldStatus !== 'Selesai' && $newStatus === 'Selesai') {
                Inventory::where('spare_part_id', $transaction->spare_part_id)->increment('stock', $transaction->quantity);
            } 
            // Logika penyesuaian stok jika status berubah dari 'Selesai' menjadi lainnya (Batal/Pending)
            elseif ($oldStatus === 'Selesai' && $newStatus !== 'Selesai') {
                Inventory::where('spare_part_id', $transaction->spare_part_id)->decrement('stock', $transaction->quantity);
            }

            $transaction->update(['status' => $newStatus]);
        });

        return redirect()->back()->with('success', 'Status transaksi diperbarui.');
    }

    // UC-03: Simpan Stok Keluar
    public function storeOutbound(Request $request)
    {
        $request->validate([
            'spare_part_id' => 'required',
            'quantity' => 'required|integer|min:1',
            'reference' => 'required',
        ]);

        $inventory = Inventory::where('spare_part_id', $request->spare_part_id)->first();
        if ($inventory->stock < $request->quantity) {
            return redirect()->back()->with('error', 'Stok tidak cukup!');
        }

        DB::transaction(function () use ($request) {
            StockTransaction::create([
                'spare_part_id' => $request->spare_part_id,
                'user_id' => Auth::id(),
                'type' => 'keluar',
                'quantity' => $request->quantity,
                'reference' => $request->reference,
                'status' => 'Selesai',
                'notes' => $request->notes
            ]);

            Inventory::where('spare_part_id', $request->spare_part_id)->decrement('stock', $request->quantity);
        });

        return redirect()->back()->with('success', 'Stok Keluar berhasil dicatat.');
    }

    public function outboundIndex()
{
    // Mengambil data item yang stoknya menipis untuk peringatan di halaman outbound (UC-07)
    $lowStockItems = \App\Models\Inventory::lowStock()->with('sparePart')->get();
    
    // Mengambil riwayat transaksi keluar (UC-03)
    $transactions = StockTransaction::where('type', 'keluar')
        ->with(['sparePart'])
        ->latest()
        ->get();

    // Pastikan nama view sesuai dengan file yang Anda miliki
    return view('admin-gudang-outbondstock', compact('lowStockItems', 'transactions'));
}
}
