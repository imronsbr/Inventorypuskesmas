<?php

namespace App\Http\Controllers;

use App\Models\MasterBarangModal;
use App\Exports\MasterBarangModalExport;
use App\Exports\MasterBarangModalTemplateExport;
use App\Imports\MasterBarangModalImport;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class MasterBarangModalController extends Controller
{
    public function index(Request $request)
    {
        // Determine if we need to include trashed records
        $includeTrashed = $request->filled('status') && $request->status === 'inactive';
        
        // Start with appropriate query
        if ($includeTrashed) {
            $query = MasterBarangModal::withTrashed();
        } else {
        $query = MasterBarangModal::query();
        }
        
        // Filter by status (active/inactive)
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('deleted_at');
            } elseif ($request->status === 'inactive') {
                $query->whereNotNull('deleted_at');
            }
        } else {
            // Default to show only active records
            $query->whereNull('deleted_at');
        }
        
        // Filter by kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        
        // Filter by status kondisi
        if ($request->filled('kondisi')) {
            if ($request->kondisi === 'tidak_ada_data') {
                $query->whereDoesntHave('detailBarangModals');
            } else {
                $query->whereHas('detailBarangModals', function($q) use ($request) {
                    $q->where('kondisi', $request->kondisi);
                });
            }
        }
        
        // Debug: Log the query for inactive records
        if ($request->filled('status') && $request->status === 'inactive') {
            \Log::info('Querying inactive records. Count: ' . $query->count());
        }
        
        // Search by nama_barang
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
        
        $barangs = $query->with(['detailBarangModals.ruang.puskesmas'])->paginate($perPage)->withQueryString();
        
        // Get unique categories for filter (include trashed records for inactive filter)
        if ($includeTrashed) {
            $kategoris = MasterBarangModal::withTrashed()->distinct()->pluck('kategori');
        } else {
        $kategoris = MasterBarangModal::distinct()->pluck('kategori');
        }
        
        // Get ruangs for ruang information
        $ruangs = \App\Models\Ruang::all();
        
        // Get puskesmas for puskesmas information
        $puskesmas = \App\Models\Puskesmas::all();
        
        return Inertia::render('Barang/MasterBarangModal/Index', [
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
            'ruangs' => $ruangs,
            'puskesmas' => $puskesmas,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_barang' => [
                'required',
                'string',
                'unique:master_barang_modals,kode_barang',
                'regex:/^\d{12}$/', // Hanya angka, tepat 12 digit
            ],
            'nama_barang' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'kategori' => 'required|string|max:100',
            'keterangan' => 'nullable|string',
        ], [
            'kode_barang.regex' => 'Kode barang harus berupa angka dengan tepat 12 digit.',
        ]);

        MasterBarangModal::create($validated);

        return redirect()->back()->with('success', 'Barang modal berhasil ditambahkan!');
    }

    public function update(Request $request, MasterBarangModal $masterBarangModal)
    {
        $validated = $request->validate([
            'kode_barang' => [
                'required',
                'string',
                'unique:master_barang_modals,kode_barang,' . $masterBarangModal->id,
                'regex:/^\d{12}$/', // Hanya angka, tepat 12 digit
            ],
            'nama_barang' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'kategori' => 'required|string|max:100',
            'keterangan' => 'nullable|string',
        ], [
            'kode_barang.regex' => 'Kode barang harus berupa angka dengan tepat 12 digit.',
        ]);

        $masterBarangModal->update($validated);

        return redirect()->back()->with('success', 'Barang modal berhasil diperbarui!');
    }

    public function destroy(MasterBarangModal $masterBarangModal)
    {
        $masterBarangModal->delete();

        return redirect()->back()->with('success', 'Barang modal berhasil dihapus!');
    }

    public function restore($id)
    {
        $masterBarangModal = MasterBarangModal::withTrashed()->findOrFail($id);
        $masterBarangModal->restore();

        // Kembalikan redirect dengan flash message
        return redirect()->back()->with('success', 'Barang modal berhasil dikembalikan!');
    }

    public function export()
    {
        $filename = 'master_barang_modal_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(new MasterBarangModalExport, $filename);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:2048'
        ]);

        try {
            \Log::info('Starting import process');
            $import = new MasterBarangModalImport();
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
            $export = new \App\Exports\MasterBarangModalTemplateExport();
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
                    'Content-Disposition' => 'attachment; filename="template_master_barang_modal.xlsx"',
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
