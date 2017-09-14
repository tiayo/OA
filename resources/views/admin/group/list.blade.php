@extends('layouts.admin')

@section('title', '客户列表')

@section('style')
    @parent
@endsection

@section('breadcrumb')
    <li navValue="nav_1"><a href="#">业务员操作</a></li>
    <li navValue="nav_1_1"><a href="#">客户列表</a></li>
@endsection

@section('body')
<div class="row">
    <div class="col-md-12">
		<section class="panel">
            <div class="panel-body">
                <form class="form-inline" id="group_form">
                    <div class="form-group">
                        <label class="sr-only" for="search"></label>
                        <input type="text" class="form-control" id="search" name="keyword"
                               value="{{ Request::route('keyword') }}" placeholder="姓名、微信、手机、公司" required>
                    </div>
                    <button type="submit" class="btn btn-primary">搜索</button>
                </form>
            <header class="panel-heading">
                客户列表
            </header>
            	<table class="table table-striped table-hover">
		            <thead>
		                <tr>
		                    <th>ID</th>
		                    <th>姓名</th>
                            <th>业务员</th>
                            <th>微信</th>
                            <th>手机</th>
                            <th>公司</th>
                            <th>备注</th>
                            <th>添加时间</th>
							<th>操作</th>
		                </tr>
		            </thead>

		            <tbody id="target">
                        @foreach($groups as $group)
                        <tr>
                            <td>{{ $group['id'] }}</td>
                            <td>{{ $group['name'] }}</td>
                            <td>{{ $group['salesman_name'] }}</td>
                            <td>{{ $group['wx'] }}</td>
                            <td>{{ $group['phone'] }}</td>
                            <td>{{ $group['company'] }}</td>
                            <td>{{ $group['remark'] }}</td>
                            <td>{{ $group['created_at'] }}</td>
                            <td>
                                <button class="btn btn-info" type="button" onclick="location='{{ route('group_update', ['id' => $group['id'] ]) }}'">编辑</button>
                                <button class="btn btn-danger" type="button" onclick="javascript:if(confirm('确实要删除吗?'))location='{{ route('group_destroy', ['id' => $group['id'] ]) }}'">删除</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
		        </table>
                <ul class="pagination pull-left">

                </ul>
            </div>
    	</section>
    </div>
</div>
@endsection

@section('script')
    @parent
    <script type="text/javascript">
        $(function(){
            $(function(){

                $('#group_form').submit(function () {
                    var keyword = $('#search').val();

                    if (stripscript(keyword) == '') {
                        $('#search').val('');
                        return false;
                    }

                    window.location='{{ route('group_search', ['page' => 1, 'keyword' => '']) }}/' + stripscript(keyword);

                    return false;
                });

                $(".pagination").createPage({
                    totalPage:{{ $count }},
                    currPage:{{ $current }},
                    @if($sign == 'search')
                    url:"{{ route('group_search_simple') }}",
                    keyword:"{{ Request::route('keyword') }}",
                    @else
                    url:"{{ route('group_list_simple') }}",
                    @endif
                    backFn:function(p){
                        console.log("回调函数："+p);
                    }
                });
            });
        });

        function stripscript(s)
        {
            var pattern = new RegExp("[`~!@#$^&*()=|{}':;',\\[\\].<>/?~！@#￥……&*（）——|{}【】‘；：”“'。，、？]");
            var rs = "";
            for (var i = 0; i < s.length; i++) {
                rs = rs+s.substr(i, 1).replace(pattern, '');
            }
            return rs;
        }
    </script>
    @if($sign == 'search')
        <script src="{{ asset('style/js/pagingSearch.js') }}"></script>
    @else
        <script src="{{ asset('style/js/paging.js') }}"></script>
    @endif
@endsection
