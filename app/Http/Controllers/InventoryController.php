<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\SparePart;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function locationIndex()
    {
        // 1. Ambil semua data inventaris
        $locations = Inventory::with('sparePart')->get();

        // 2. Hitung Kapasitas per Rak (Berdasarkan Huruf Depan)
        // Kita buat fungsi pembantu agar kode lebih rapi
        $capacityA = $this->calculateRackCapacity('A');
        $capacityB = $this->calculateRackCapacity('B');
        $capacityC = $this->calculateRackCapacity('C');

        // 3. KIRIM VARIABEL KE VIEW (Pastikan namanya sama dengan di Blade)
        return view('admin-gudang-stocklocation', compact(
            'locations', 
            'capacityA', 
            'capacityB', 
            'capacityC'
        ));
    }

    /**
     * Fungsi pembantu untuk menghitung persentase isi rak
     */
    private function calculateRackCapacity($prefix)
    {
        // Hitung berapa banyak item yang ada di rak A, B, atau C
        $currentUsage = Inventory::where('location_rack', 'like', $prefix . '%')->count();
        
        // Tentukan batas maksimal item per zona (misal: 20 item)
        $maxCapacity = 1000000; 

        // Kembalikan dalam bentuk persentase (0-100)
        $percentage = ($currentUsage / $maxCapacity) * 100;
        
        return min(round($percentage), 100);
    }
}
