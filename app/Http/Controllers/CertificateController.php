<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Services\CertificateService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function __construct(private CertificateService $certificates) {}

    /** Unduh sertifikat PDF: hanya siswa pemilik yang sudah menyelesaikan kelas. */
    public function download(Enrollment $enrollment): Response
    {
        Gate::authorize('downloadCertificate', $enrollment);

        return $this->certificates->render($enrollment)
            ->download($this->certificates->filename($enrollment));
    }

    /** Halaman verifikasi publik: memastikan keaslian sertifikat dari kode uniknya. */
    public function verify(string $code): View|Response
    {
        $enrollment = $this->certificates->findByCode($code);

        if (! $enrollment) {
            return response()->view('certificates.verify', ['certificate' => null, 'code' => $code], 404);
        }

        return view('certificates.verify', [
            'certificate' => $this->certificates->data($enrollment),
            'code' => $code,
        ]);
    }
}
