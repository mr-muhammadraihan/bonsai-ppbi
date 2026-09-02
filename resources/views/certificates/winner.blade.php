<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">

    <style>
        @font-face {
            font-family: 'Shelley Script';
            src: url('{{ public_path('fonts/ShelleyScript.ttf') }}') format('truetype');
        }

        @font-face {
            font-family: 'Garet';
            src: url('{{ public_path('fonts/Garet.ttf') }}') format('truetype');
        }

        @font-face {
            font-family: 'Alice';
            src: url('{{ public_path('fonts/Alice.ttf') }}') format('truetype');
        }

        @page {
            size: A4 landscape;
            margin: 0;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 297mm;
            height: 210mm;
        }

        .certificate {
            position: relative;
            width: 297mm;
            height: 210mm;

            background-image: url('{{ public_path('certificates/templates/winner.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .participant-name {
            position: absolute;

            left: 85mm;
            top: 62mm;

            width: 120mm;

            text-align: center;

            font-family: 'Shelley Script';
            font-size: 8mm;
            font-weight: bold;
            overflow-wrap: anywhere;
        }

        .bonsai-code {
            position: absolute;

            left: 130mm;
            top: 98mm;

            font-family: 'Garet';
            font-size: 5mm;
            font-weight: bold;
        }

        .size {
            position: absolute;

            left: 130mm;
            top: 105mm;

            font-family: 'Garet';
            font-size: 5mm;
            font-weight: bold;
        }

        .class {
            position: absolute;

            left: 130mm;
            top: 112mm;

            font-family: 'Garet';
            font-size: 5mm;
            font-weight: bold;
        }

        .predicate {
            position: absolute;

            left: 80mm;
            top: 85mm;

            width: 140mm;

            text-align: center;

            font-family: 'Alice';
            font-size: 8mm;
            font-weight: bold;
        }

        .bonsai-photo {
            position: absolute;

            left: 215mm;
            top: 65mm;

            width: 58mm;
            height: 68mm;

            object-fit: cover;
        }
    </style>
</head>

<body>

    <div class="certificate">

        {{-- NAMA --}}
        <div class="participant-name">
            {{ $bonsai->participant->name }}
        </div>

        {{-- ID BONSAI --}}
        <div class="bonsai-code">
            {{ $bonsai->bonsaiType->name }}
        </div>

        {{-- UKURAN --}}
        <div class="size">
            {{ $bonsai->size }}
        </div>

        {{-- KELAS --}}
        <div class="class">
            {{ $bonsai->class }}
        </div>

        {{-- PREDIKAT --}}
        <div class="predicate">
            {{ $bonsai->predicate }}
        </div>

        {{-- FOTO --}}
        <img class="bonsai-photo" src="{{ storage_path('app/public/' . $bonsai->photo) }}">

    </div>

</body>

</html>
