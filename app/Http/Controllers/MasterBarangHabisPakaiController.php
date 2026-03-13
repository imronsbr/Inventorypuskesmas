<?php

namespace App\Http\Controllers;

use App\Models\MasterBarangHabisPakai;
use App\Exports\MasterBarangHabisPakaiExport;
use App\Exports\MasterBarangHabisPakaiTemplateExport;
use App\Imports\MasterBarangHabisPakaiImport;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class MasterBarangHabisPakaiController extends Controller
{
    public function index(Request $request)
    {
        // Start with query
        $query = MasterBarangHabisPakai::query();
        
        // Filter by kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'aktif') {
                $query->whereNull('deleted_at')->where(function($q) {
                    $q->whereHas('stokHabisPakais', function($subQ) {
                        $subQ->where('stok_akhir', '>', 0);
                    })->orWhereDoesntHave('stokHabisPakais');
                });
            } elseif ($request->status === 'tidak_aktif') {
                $query->whereNotNull('deleted_at');
            } elseif ($request->status === 'stok_kosong') {
                $query->whereHas('stokHabisPakais', function($q) {
                    $q->where('stok_akhir', '<=', 0);
                });
            } elseif ($request->status === 'pending_pengadaan') {
                $query->whereHas('pengadaanBarang', function($q) {
                    $q->whereHas('pengadaanBarang', function($subQ) {
                        $subQ->whereIn('status', [
                            'draft', 'submitted', 'approved_penjab', 'approved_pptk', 
                            'approved_perencana', 'approved_kepala_puskesmas'
                        ]);
                    });
                });
            } elseif ($request->status === 'pending_permintaan') {
                $query->whereHas('permintaanBarang', function($q) {
                    $q->whereIn('status', ['draft', 'submitted', 'approved_user', 'approved_penjab']);
                });
            }
        }
        
        // Search by nama_barang or kode_barang
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_barang', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_barang', 'like', '%' . $request->search . '%');
            });
        }
        
        // Sorting
        $sortBy = $request->get('sortBy', 'nama_barang');
        $sortDir = $request->get('sortDir', 'asc');
        $query->orderBy($sortBy, $sortDir);
        
        // Ensure perPage is a valid integer
        $perPage = (int) $request->get('perPage', 10);
        $perPage = max(1, min(100, $perPage)); // Ensure it's between 1 and 100
        
        $barangs = $query->paginate($perPage)->withQueryString();
        
        // Get unique categories for filter
        $kategoris = MasterBarangHabisPakai::distinct()->pluck('kategori');
        
        // Get unique satuan for filter
        $satuans = MasterBarangHabisPakai::distinct()->pluck('satuan');
        
        // Configuration options
        $config = [
            'satuanOptions' => [
                ['value' => 'Pcs', 'label' => 'Pcs'],
                ['value' => 'Box', 'label' => 'Box'],
                ['value' => 'Lusin', 'label' => 'Lusin'],
                ['value' => 'Kg', 'label' => 'Kg'],
                ['value' => 'Liter', 'label' => 'Liter'],
                ['value' => 'Rim', 'label' => 'Rim'],
                ['value' => 'Botol', 'label' => 'Botol'],
            ],
            'kategoriOptions' => [
                ['value' => 'ATK', 'label' => 'ATK'],
                ['value' => 'ART', 'label' => 'ART'],
                ['value' => 'Alat Makan', 'label' => 'Alat Makan'],
                ['value' => 'Alat Laboratorium', 'label' => 'Alat Laboratorium'],
                ['value' => 'Lainnya', 'label' => 'Lainnya'],
            ],
            'statusOptions' => [
                ['value' => 'aktif', 'label' => 'Aktif'],
                ['value' => 'tidak_aktif', 'label' => 'Tidak Aktif'],
                ['value' => 'stok_kosong', 'label' => 'Stok Kosong'],
                ['value' => 'pending_pengadaan', 'label' => 'Pending Pengadaan'],
                ['value' => 'pending_permintaan', 'label' => 'Pending Permintaan'],
            ],
            'perPageOptions' => [10, 25, 50, 100],
        ];
        
        return Inertia::render('Barang/MasterBarangHabisPakai/Index', [
            'barangs' => $barangs,
            'filters' => [
                'status' => $request->status,
                'kategori' => $request->kategori,
                'search' => $request->search,
                'sortBy' => $sortBy,
                'sortDir' => $sortDir,
                'perPage' => $request->get('perPage', 10),
            ],
            'kategoris' => $kategoris,
            'satuans' => $satuans,
            'config' => $config,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_barang' => 'required|string|unique:master_barang_habis_pakais,kode_barang',
            'nama_barang' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'kategori' => 'required|string|max:100',
            'keterangan' => 'nullable|string',
        ]);

        MasterBarangHabisPakai::create($validated);

        return redirect()->back()->with('success', 'Barang habis pakai berhasil ditambahkan!');
    }

    public function update(Request $request, MasterBarangHabisPakai $masterBarangHabisPakai)
    {
        $validated = $request->validate([
            'kode_barang' => 'required|string|unique:master_barang_habis_pakais,kode_barang,' . $masterBarangHabisPakai->id,
            'nama_barang' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'kategori' => 'required|string|max:100',
            'keterangan' => 'nullable|string',
        ]);

        $masterBarangHabisPakai->update($validated);

        return redirect()->back()->with('success', 'Barang habis pakai berhasil diperbarui!');
    }

    public function destroy(MasterBarangHabisPakai $masterBarangHabisPakai)
    {
        // Get validation result
        $validation = $masterBarangHabisPakai->canBeDeleted();
        
        if (!$validation['can_delete']) {
            $reasons = $masterBarangHabisPakai->getDeleteValidationMessage();
            $message = 'Barang tidak dapat dihapus: ' . implode(', ', $reasons);
            
            return redirect()->back()->with('error', $message);
        }
        
        $masterBarangHabisPakai->delete();
        return redirect()->back()->with('success', 'Barang habis pakai berhasil dihapus!');
    }

    public function restore($id)
    {
        $masterBarangHabisPakai = MasterBarangHabisPakai::withTrashed()->findOrFail($id);
        
        if ($masterBarangHabisPakai->trashed()) {
            $masterBarangHabisPakai->restore();
            return redirect()->back()->with('success', 'Barang habis pakai berhasil dipulihkan!');
        }
        
        return redirect()->back()->with('error', 'Barang tidak dalam status terhapus!');
    }

    public function forceDelete($id)
    {
        $masterBarangHabisPakai = MasterBarangHabisPakai::withTrashed()->findOrFail($id);
        
        // Double check validation even for force delete
        $validation = $masterBarangHabisPakai->canBeDeleted();
        
        if (!$validation['can_delete']) {
            $reasons = $masterBarangHabisPakai->getDeleteValidationMessage();
            $message = 'Barang tidak dapat dihapus permanen: ' . implode(', ', $reasons);
            
            return redirect()->back()->with('error', $message);
        }
        
        $masterBarangHabisPakai->forceDelete();
        return redirect()->back()->with('success', 'Barang habis pakai berhasil dihapus permanen!');
    }

    public function export()
    {
        $filename = 'master_barang_habis_pakai_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(new MasterBarangHabisPakaiExport, $filename);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:2048'
        ]);

        try {
            \Log::info('Starting import process for MasterBarangHabisPakai');
            $import = new MasterBarangHabisPakaiImport();
            Excel::import($import, $request->file('file'));
            
            \Log::info('Import completed successfully');
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diimpor!'
            ]);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            \Log::error('Validation exception: ' . $e->getMessage());
            $failures = $e->failures();
            $errors = [];
            
            foreach ($failures as $failure) {
                $errors[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode('; ', $errors)
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Import error: ' . $e->getMessage());
            \Log::error('Import error stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengimpor data: ' . $e->getMessage()
            ], 422);
        }
    }

    public function downloadTemplate()
    {
        try {
            // Create spreadsheet manually using PhpSpreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Get data from export class
            $export = new \App\Exports\MasterBarangHabisPakaiTemplateExport();
            $headings = $export->headings();
            $data = $export->array();
            
            // Add headers
            foreach ($headings as $index => $heading) {
                $column = chr(65 + $index); // A, B, C, etc.
                $sheet->setCellValue($column . '1', $heading);
            }
            
            // Set format kolom Kode Barang ke text untuk seluruh kolom A (A:A)
            $sheet->getStyle('A:A')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

            // Add data
            foreach ($data as $rowIndex => $row) {
                $rowNumber = $rowIndex + 2; // Start from row 2 (after headers)
                foreach ($row as $colIndex => $value) {
                    $column = chr(65 + $colIndex); // A, B, C, etc.
                    if ($colIndex === 0) {
                        // Kolom Kode Barang, set sebagai string
                        $sheet->setCellValueExplicit($column . $rowNumber, (string)$value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    } else {
                        $sheet->setCellValue($column . $rowNumber, $value);
                    }
                }
            }
            
            // Style headers
            $headerRange = 'A1:' . chr(65 + count($headings) - 1) . '1';
            $sheet->getStyle($headerRange)->getFont()->setBold(true);
            $sheet->getStyle($headerRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
            $sheet->getStyle($headerRange)->getFill()->getStartColor()->setRGB('E2E8F0');
            
            // Auto-size columns
            foreach (range('A', chr(65 + count($headings) - 1)) as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            
            // Create writer
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            
            // Create response
            $response = response()->stream(
                function () use ($writer) {
                    $writer->save('php://output');
                },
                200,
                [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => 'attachment; filename="template_master_barang_habis_pakai.xlsx"',
                    'X-Content-Type-Options' => 'nosniff',
                    'X-Frame-Options' => 'DENY',
                ]
            );
            
            return $response;
            
        } catch (\Throwable $e) {
            \Log::error('Download template error: ' . $e->getMessage());
            return response('Error: ' . $e->getMessage(), 500);
        }
    }
} 