<?php

namespace App\Services;

use App\Models\Bonsai;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BonsaiCsvExportService
{
    public function downloadAll(): StreamedResponse
    {
        return $this->download('semua-bonsai', Bonsai::query());
    }

    public function downloadWinners(): StreamedResponse
    {
        return $this->download(
            'bonsai-pemenang',
            Bonsai::query()->where('status', 'Pemenang'),
        );
    }

    private function download(string $filename, Builder $query): StreamedResponse
    {
        $bonsais = $query
            ->with(['participant', 'bonsaiType'])
            ->orderBy('id')
            ->get();

        return response()->streamDownload(function () use ($bonsais): void {
            $output = fopen('php://output', 'w');

            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, [
                'Nama Peserta',
                'Email',
                'No. HP',
                'ID Bonsai',
                'Jenis Bonsai',
                'Ukuran',
                'Kelas',
                'Status',
                'Predikat',
                'Deskripsi',
                'Foto',
                'Tanggal Dibuat',
            ]);

            foreach ($bonsais as $bonsai) {
                fputcsv($output, [
                    $bonsai->participant?->name,
                    $bonsai->participant?->email,
                    $bonsai->participant?->no_hp,
                    $bonsai->bonsai_code,
                    $bonsai->bonsai_type ?: $bonsai->bonsaiType?->name,
                    $bonsai->size,
                    $bonsai->class,
                    $bonsai->status,
                    $bonsai->predicate,
                    $bonsai->description,
                    $bonsai->getPhotoMedia()?->file_name ?? $bonsai->photo,
                    $bonsai->created_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($output);
        }, $filename.'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
