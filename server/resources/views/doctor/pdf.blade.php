<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>华山医院PD患者临床详细信息表</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <!-- Styles -->
    <style>
        html, body {
            margin:0;
            background:#555;
        }
        div{
            max-width:1000px;
            margin:auto;
        }
      img{
          width:100%;
          display: block;
          margin-bottom:10px;
      }
    </style>
</head>
<body>
<div>
    @foreach($imgs as $img)
        <img src="{{$img->img}}">
    @endforeach
</div>
</body>
</html>
