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
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal;
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
        a{color: #000000 !important;}
        .page-break {
    page-break-before: always;
}

    </style>
</head>
<body>
    <div class="content">
        <h1 style="text-align: center; color: #000000; font-size: 20px; font-weight: 700; margin-bottom: 30px;">GodAi</h1>
        
        <div style="display: block; margin-bottom: 20px;">
            <h2 style="font-size: 18px;">RESULTADO GÉNESIS</h2>
            <h3 style="font-size: 18px;">ESTRATEGIA</h3>
            {!! $genesis !!}
            {{-- <h3 style="font-size: 18px;">FUENTES</h3> --}}
            {!! $fuentesGenesis !!}
        </div>
        <div class="page-break"></div>
        <h3 style="font-size: 18px;">CREATIVIDAD</h3>
        <div style="display: block; margin-bottom: 10px;">
           {!! $construccionescenario !!}
           {{-- <h3 style="font-size: 18px;">FUENTES</h3> --}}
            {!! $fuentesEscenario !!}
        </div>
        <div style="display: block; margin-bottom: 20px;">
            <h2 style="font-size: 18px;">BAJADAS CREATIVAS</h2>
            {!! $creatividad !!}
        </div>
        <div style="display: block; margin-bottom: 20px;">
            <h2 style="font-size: 18px;">ESTRATEGIA DIGITAL</h2>
            {!! $estrategia !!}
        </div>
        <div style="display: block; margin-bottom: 20px;">
            <h2 style="font-size: 18px;">IDEAS DE CONTENIDO</h2>
            {!! $contenido !!}
        </div>
        {{-- <div style="display: block; margin-bottom: 20px;">
            <h2 style="font-size: 18px;">INNOVACIONES</h2>
            {!! $innovacion !!}
        </div> --}}
    </div>
</body>
</html>