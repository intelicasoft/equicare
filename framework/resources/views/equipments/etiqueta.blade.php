@php
    $u_e_id =
        \App\QrGenerate::where('id', $equipment->qr_id)->first() != null
            ? \App\QrGenerate::where('id', $equipment->qr_id)->first()->uid
            : '';
@endphp

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etiqueta de Equipo</title>
    <style>
        @page {
            size: letter;
        }

        body {
            transform: rotate(90deg);
            font-family: Arial, sans-serif;
            font-size: 32px;
        }

        .container {
            border: 3px solid black;
            width: 1000px;
            height: 650px;
            padding: 10px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(2, 2, 2, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: solid 1px black;
        }

        .logoImage {
            width: 200px;
            height: 95px;
            border-radius: 15px;
        }

        .info-table {
            width: 100%;
            border-spacing: 30px;
        }

        .textos {
            vertical-align: top;
        }

        .qrImage img {
            width: 400px;
            height: 400px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
            <img class="logoImage" src="{{ asset('framework/public/images/logo.png') }}" alt="Logo">
        </div>

        <table class="info-table">
            <tr>
                <td class="textos">
                    <div><strong>Hospital:</strong> {{ $equipment->hospital->name }}</div>
                    <div><strong>Compañía:</strong> {{ $equipment->company }}</div>
                    <div><strong>Número de Serie:</strong> {{ $equipment->sr_no }}</div>
                    <div><strong>Modelo:</strong> {{ $equipment->models->name }}</div>
                    <div><strong>Call center:</strong> +52 331486 96 52</div>
                    <div><strong>Correo:</strong> serviciotecnico@ucinmedica.com</div>
                </td>
                <td class="qrImage">
                    <img src="{{ asset('uploads/qrcodes/qr_assign/' . $u_e_id . '.png') }}" alt="QR Code">
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
