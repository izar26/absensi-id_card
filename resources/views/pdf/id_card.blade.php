<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ID Cards Batch</title>
    <style>
        @page { margin: 10px; }
        body { font-family: sans-serif; font-size: 10px; margin: 0; padding: 0; }
        .page-break { page-break-after: always; }

        .card {
            width: 242.6px;  /* ukuran yang sama dengan per-student */
            height: 153px;
            border: 1px solid #000;
            margin: 5px auto;
            padding: 5px;
            box-sizing: border-box;
            display: flex;
            flex-direction: row;
        }

        .photo {
            width: 70px;
            height: 100%;
            border: 1px solid #000;
            margin-right: 5px;
            overflow: hidden;
        }
        .photo img {
            width: 100%;
            height: auto;
        }

        .info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .info .name { font-weight: bold; font-size: 12px; margin-bottom: 4px; }
        .info .field { margin-bottom: 2px; }
    </style>
</head>
<body>
    @foreach($students as $index => $student)
        <div class="card">
            <div class="photo">
                @if(!empty($student['photo']))
                    <img src="{{ public_path('storage/' . $student['photo']) }}" alt="Foto">
                @else
                    <img src="{{ public_path('images/default.png') }}" alt="Foto">
                @endif
            </div>
            <div class="info">
                <div class="name">{{ $student['name'] }}</div>
                <div class="field">NIS: {{ $student['nis'] }}</div>
                <div class="field">Kelas: {{ $student['class'] }}</div>
                @if(!empty($student['date_of_birth']))
                    <div class="field">Lahir: {{ $student['date_of_birth'] }}</div>
                @endif
                <div class="field">Status: {{ $student['status'] }}</div>
            </div>
        </div>

        @if(($index + 1) % 4 === 0)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>
