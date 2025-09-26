<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>ID Card Siswa</title>
    <style>
        /* Hapus semua referensi font, biarkan dompdf menggunakan default-nya */
        body {
            font-family: sans-serif; /* Pilihan paling aman */
        }
        .card-container {
            width: 323px;
            height: 204px;
            border: 1px solid black;
            padding: 10px;
        }
        .main-table {
            width: 100%;
        }
        .photo-td {
            width: 90px;
        }
        .student-photo {
            width: 85px;
            height: 105px;
            object-fit: cover;
        }
        .data-td {
            vertical-align: top;
            padding-left: 10px;
        }
        .school-name {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
        }
        .qr-code {
            width: 60px;
            height: 60px;
        }
    </style>
</head>
<body>

    <div class="card-container">
        <div class="school-name">SEKOLAH HARAPAN BANGSA</div>

        <table class="main-table">
            <tr>
                <td class="photo-td">
                    @if($studentPhotoBase64)
                        <img src="{{ $studentPhotoBase64 }}" class="student-photo" alt="Foto Siswa">
                    @endif
                </td>

                <td class="data-td">
                    <table>
                        <tr>
                            <td>Nama</td>
                            <td>:</td>
                            <td><b>{{ $student->name }}</b></td>
                        </tr>
                        <tr>
                            <td>NIS</td>
                            <td>:</td>
                            <td><b>{{ $student->nis }}</b></td>
                        </tr>
                        <tr>
                            <td>Kelas</td>
                            <td>:</td>
                            <td><b>{{ $student->class }}</b></td>
                        </tr>
                        <tr>
                            <td colspan="3" style="padding-top: 10px;">
                                <img src="{{ $qrCodeBase64 }}" class="qr-code" alt="QR Code">
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>