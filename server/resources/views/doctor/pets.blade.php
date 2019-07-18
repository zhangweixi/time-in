<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
    <META   HTTP-EQUIV="Pragma"   CONTENT="no-cache">
    <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no,width=device-width"   />
    <meta name="format-detection" content="telephone=no" />
    <meta name="app-mobile-web-app-capable"  content="yes" />
    <meta name="app-mobile-web-app-status-bar-style" content="black-translucent" />

    <title></title>
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


        #pull_right{
            text-align:center;
        }
        .pull-right {
            /*float: left!important;*/
        }
        .pagination {
            display: inline-block;
            padding-left: 0;
            margin: 20px 0;
            border-radius: 4px;
        }
        .pagination > li {
            display: inline;
        }
        .pagination > li > a,
        .pagination > li > span {
            position: relative;
            float: left;
            padding: 6px 12px;
            margin-left: -1px;
            line-height: 1.42857143;
            color: #428bca;
            text-decoration: none;
            background-color: #fff;
            border: 1px solid #ddd;
        }
        .pagination > li:first-child > a,
        .pagination > li:first-child > span {
            margin-left: 0;
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
        }
        .pagination > li:last-child > a,
        .pagination > li:last-child > span {
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
        }
        .pagination > li > a:hover,
        .pagination > li > span:hover,
        .pagination > li > a:focus,
        .pagination > li > span:focus {
            color: #2a6496;
            background-color: #eee;
            border-color: #ddd;
        }
        .pagination > .active > a,
        .pagination > .active > span,
        .pagination > .active > a:hover,
        .pagination > .active > span:hover,
        .pagination > .active > a:focus,
        .pagination > .active > span:focus {
            z-index: 2;
            color: #fff;
            cursor: default;
            background-color: #428bca;
            border-color: #428bca;
        }
        .pagination > .disabled > span,
        .pagination > .disabled > span:hover,
        .pagination > .disabled > span:focus,
        .pagination > .disabled > a,
        .pagination > .disabled > a:hover,
        .pagination > .disabled > a:focus {
            color: #777;
            cursor: not-allowed;
            background-color: #fff;
            border-color: #ddd;
        }
        .clear{
            clear: both;
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
    @foreach($pets as $key => $pet)
    <tr class="a1">
        <td>{{($pets->currentPage() -1) * 20 + $key+1}}</td>
        <td>{{$pet->pid}}</td>
        <td><div style="padding:5px;">{!! $pet->content !!}</div></td>
        <td>{{$pet->created_at}}</td>
        <td><a href="preview?pid={{$pet->pid}}">查看</a></td>
        <td><button onclick="deletePet({{$pet->pid}})">删除</button></td>
    </tr>
    @endforeach
</table>



<div id="pull_right">
    <div class="pull-right">
        {!! $pets->appends(['keywords' => $keywords])->render() !!}
    </div>
</div>

</body>
</html>