<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
    <META   HTTP-EQUIV="Pragma"   CONTENT="no-cache">
    <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no,width=device-width"   />
    <meta name="format-detection" content="telephone=no" />
    <meta name="app-mobile-web-app-capable"  content="yes" />
    <meta name="app-mobile-web-app-status-bar-style" content="black-translucent" />

    <title>精美的表格样式</title>
    <style type="text/css">
        <!--
        body,table{
            font-size:12px;
        }
        table{
            table-layout:fixed;
            empty-cells:show;
            border-collapse: collapse;
            margin:0 auto;
        }
        td{
            height:20px;
        }
        h1,h2,h3{
            font-size:12px;
            margin:0;
            padding:0;
        }

        .title { background: #FFF; border: 1px solid #9DB3C5; padding: 1px; width:90%;margin:20px auto; }
        .title h1 { line-height: 31px; text-align:center; background:#00a8c6; color: #FFF; }
        .title th, .title td { border: 1px solid #CAD9EA; padding: 5px; }

        .search{
            width:90%;
            margin:auto;
            margin-bottom:10px;
            border:none;
        }
        /*这个是借鉴一个论坛的样式*/
        table.t1{
            border:1px solid #cad9ea;
            color:#666;
        }
        table.t1 th {
            height:30px;
        }
        table.t1 td,table.t1 th{
            border:1px solid #cad9ea;
            padding:0 1em 0;
        }
        table.t1 tr.a1{
            background-color:#f5fafe;
        }

    </style>
    <script src="https://cdn.bootcss.com/jquery/2.1.1/jquery.min.js"></script>
    <script type="text/javascript">
        function deletePet(id){
            if(!confirm('确定删除吗')){
                return false;
            }

            $.ajax({
                url:"deletePet?pid="+id

            }).success(function(res){
                console.log(res);
                location.reload();
            })
        }

        function search(){
            var keywords = $('#keywords').val();
            location.href = location.origin + location.pathname + "?keywords=" + keywords;
        }
    </script>
</head>

<body>
<div class="title">
    <h1>华山医院PD患者临床详细信息表</h1>
</div>
<div class="search">
    <input type="text" id="keywords" value="{{$keywords}}">
    <button onclick="search()">查询</button>
</div>
<table width="90%" id="mytab"  border="1" class="t1">
    <thead>
    <th >#</th>
    <th >PET号</th>
    <th >内容</th>
    <th >创建时间</th>
    <th>诊断记录</th>
    <th >操作</th>
    </thead>
    @foreach($pets as $pet)
    <tr class="a1">
        <td>1</td>
        <td>{{$pet->pid}}</td>
        <td><div style="padding:5px;">{!! $pet->content !!}</div></td>
        <td>{{$pet->created_at}}</td>
        <td><a href="preview?pid={{$pet->pid}}">查看</a></td>
        <td><button onclick="deletePet({{$pet->pid}})">删除</button></td>
    </tr>
    @endforeach
</table>

</body>
</html>