<!DOCTYPE html>
<html>
<head>
    <title>Génesis</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        /* Estilos CSS para tu plantilla PDF */
        *{
            font-family: 'Arial, Helvetica, sans-serif';
            font-size: 12px;
        }
        html {
            margin: 30pt 0pt 0pt;
        }
        header{
            margin:0pt 50pt ;
        }
        .content{
            margin: 10pt 50pt;
        }
        table{
            font-size: 13px; 
            line-height: 12px;
        }
        table thead tr td{
            background-color: #6d0dff; color:#ffffff;
        }
        table tr td{
            padding: 5px;
        }
        table,
        table td,
        table th{
            border-color: #000000;
        }
        h1{
            font-size: 15px;
        }
        h2{
            font-size: 14px;
        }
        h3{
            font-size: 13px;
        }
        p{
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="content">
        <h1 style="text-align: center; color: #000000; font-size: 20px; font-weight: 700; margin-bottom: 30px;">GodAi</h1>
        <div style="display: block; margin-bottom: 20px;">
            <h2 style="font-size: 18px;">RESULTADO ASISTENTE CREATIVO</h2>
            {!! $asistenteCreativoGenerateContainer !!}
        </div>
    </div>
</body>
</html>