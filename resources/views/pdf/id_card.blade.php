<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>ID Card Siswa Modern</title>
    <style>
        /* Import font modern dari Google Fonts */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

        /* Variabel warna agar mudah diganti */
        :root {
            --primary-color: #007BFF; /* Biru cerah */
            --secondary-color: #0056b3; /* Biru lebih gelap */
            --text-color-dark: #333333;
            --text-color-light: #757575;
            --card-bg: #FFFFFF;
            --shadow-color: rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: DejaVuSans, sans-serif;
            background-color: #f4f7f6; /* Latar belakang abu-abu agar kartu menonjol */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card-container {
            width: 338px; /* Sekitar 8.9cm */
            height: 213px; /* Sekitar 5.6cm */
            background-color: var(--card-bg);
            border-radius: 16px; /* Sudut melengkung */
            box-shadow: 0 8px 24px var(--shadow-color); /* Efek bayangan halus */
            padding: 20px;
            box-sizing: border-box;
            position: relative;
            overflow: hidden; /* Agar elemen dekoratif tidak keluar dari kartu */
            display: flex;
            flex-direction: column;
        }

        /* Elemen dekoratif di latar belakang */
        .card-container::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -80px;
            width: 200px;
            height: 200px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            opacity: 0.1;
        }

        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #eeeeee;
            padding-bottom: 10px;
        }

        .school-logo {
            width: 40px; /* Ukuran logo */
            height: 40px;
            margin-right: 12px;
        }
        
        .school-info .school-name {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-color-dark);
            margin: 0;
            line-height: 1.2;
        }
        
        .school-info .card-title {
            font-size: 10px;
            font-weight: 400;
            color: var(--text-color-light);
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-body {
            display: flex;
            flex-grow: 1;
            align-items: center;
        }

        .photo-section {
            padding-right: 15px;
        }

        .student-photo {
            width: 85px;
            height: 105px;
            object-fit: cover;
            border-radius: 12px; /* Foto dengan sudut melengkung */
            border: 3px solid var(--primary-color);
        }

        .info-section {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%; /* Agar QR code bisa di bawah */
            width: 100%;
        }

        .student-details .info-item {
            margin-bottom: 8px;
        }
        
        .student-details .label {
            font-size: 10px;
            color: var(--text-color-light);
            display: block;
        }
        
        .student-details .value {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-color-dark);
        }

        .qr-section {
            align-self: flex-end; /* Posisikan QR code di kanan bawah */
            margin-top: auto;
        }
        
        .qr-code {
            width: 55px;
            height: 55px;
        }
    </style>
</head>
<body>

    <div class="card-container">
        <header class="card-header">
            <img src="https://via.placeholder.com/100" alt="Logo Sekolah" class="school-logo">
            <div class="school-info">
                <p class="school-name">SEKOLAH HARAPAN BANGSA</p>
                <p class="card-title">KARTU TANDA PELAJAR</p>
            </div>
        </header>

        <main class="card-body">
            <div class="photo-section">
                @if($studentPhotoBase64)
                    <img src="{{ $studentPhotoBase64 }}" class="student-photo" alt="Foto Siswa">
                @endif
            </div>

            <div class="info-section">
                <div class="student-details">
                    <div class="info-item">
                        <span class="label">Nama Lengkap</span>
                        <span class="value">{{ $student->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">NIS</span>
                        <span class="value">{{ $student->nis }}</span>
                    </div>
                     <div class="info-item">
                        <span class="label">Kelas</span>
                        <span class="value">{{ $student->class }}</span>
                    </div>
                </div>

                <div class="qr-section">
                    <img src="{{ $qrCodeBase64 }}" class="qr-code" alt="QR Code">
                </div>
            </div>
        </main>
    </div>

</body>
</html>