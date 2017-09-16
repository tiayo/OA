@extends('layouts.admin')

@section('title', '业务员分组')

@section('style')
    @parent
@endsection

@section('breadcrumb')
    <li navValue="nav_0"><a href="#">管理员操作</a></li>
    <li navValue="nav_0_4"><a href="#">业务员分组</a></li>
@endsection

@section('body')
<div class="row">
    <div class="col-md-12">
		<section class="panel">
            <div class="panel-body">
                <form class="form-inline" id="group_form">
                    <button type="button" class="btn btn-primary list-inline" onclick="location='{{ route('group_add') }}'">添加分组</button>
                    <div class="form-group">
                        <label class="sr-only" for="search"></label>
                        <input type="text" class="form-control" id="search" name="keyword"
                               value="{{ Request::route('keyword') }}" placeholder="分组名" required>
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
		                    <th>分组名</th>
                            <th>管理员</th>
                            <th>创建时间</th>
							<th>操作</th>
		                </tr>
		            </thead>

		            <tbody id="target">
                        @foreach($groups as $group)
                        <tr>
                            <td>{{ $group['id'] }}</td>
                            <td>{{ $group['name'] }}</td>
                            <td>{{ $group['salesman_name'] }}</td>
                            <td>{{ $group['created_at'] }}</td>
                            <td>
                                <button class="btn btn-success" type="button" onclick="location='{{ route('customer_group', ['group' => $group['id'] ]) }}'">查看客户</button>
                                <button class="btn btn-info" type="button" onclick="location='{{ route('group_update', ['id' => $group['id'] ]) }}'">编辑</button>
                                <button class="btn btn-danger" type="button" onclick="javascript:if(confirm('确实要删除吗?'))location='{{ route('group_destroy', ['id' => $group['id'] ]) }}'">删除</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
		        </table>
                {{ $groups->links() }}
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

                    window.location='{{ route('group_search', ['keyword' => '']) }}/' + stripscript(keyword);

                    return false;
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
@endsection
