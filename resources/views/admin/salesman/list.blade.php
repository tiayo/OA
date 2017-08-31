@extends('layouts.admin')

@section('title', '业务员列表')

@section('style')
    @parent
@endsection

@section('breadcrumb')
    <li navValue="nav_0"><a href="#">管理员操作</a></li>
    <li navValue="nav_0_1"><a href="#">业务员列表</a></li>
@endsection

@section('body')
<div class="row">
    <div class="col-md-12">
		<section class="panel">
            <div class="panel-body">
                <form class="form-inline" id="salesman_form">
                    <div class="form-group">
                        <label class="sr-only" for="search"></label>
                        <input type="text" class="form-control" id="search" name="keyword"
                               value="{{ Request::get('keyword') }}" placeholder="输入姓名或邮箱" required>
                    </div>
                    <button type="submit" class="btn btn-primary" id="salesman_search">搜索</button>
                </form>
            <header class="panel-heading">
               	业务员列表
            </header>
            	<table class="table table-striped table-hover">
		            <thead>
		                <tr>
		                    <th>ID</th>
		                    <th>帐号</th>
		                    <th>邮箱</th>
                            <th>类型</th>
                            <th>创建时间</th>
							<th>操作</th>
		                </tr>
		            </thead>

		            <tbody id="target">
                        @foreach($salesman as $user)
                        <tr>
                            <td>{{ $user['id'] }}</td>
                            <td>{{ $user['name'] }}</td>
                            <td>{{ $user['email'] }}</td>
                            <td>
                                @if($user['type'] == 0)
                                    业务员
                                    @elseif($user['type'] == 1)
                                    管理员
                                    @elseif($user['type'] == 2)
                                    负责人
                                @endif
                            </td>
                            <td>{{ $user['created_at'] }}</td>
                            <td>
                                <button class="btn btn-info" type="button" onclick="location='{{ route('salesman_update', ['id' => $user['id'] ]) }}'">编辑</button>
                                <button class="btn btn-danger" type="button" onclick="javascript:if(confirm('确实要删除吗?'))location='{{ route('salesman_destroy', ['id' => $user['id'] ]) }}'">删除</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
		        </table>
                @if(!empty($page))
                    <ul class="pagination pull-left">

                    </ul>
                @endif
            </div>
    	</section>
    </div>
</div>
@endsection

@section('script')
    @parent
    @if(!empty($page))
        <script type="text/javascript">
            $(function(){

                $('#salesman_form').submit(function () {
                    var keyword = $('#search').val();

                    window.location='{{ route('salesman_search', ['page' => 1, 'keyword' => '']) }}/'+keyword;

                    return false;
                });

                $(".pagination").createPage({
                    totalPage:{{ $count }},
                    currPage:{{ $current }},
                    @if($sign == 'search')
                        url:"{{ route('salesman_search_simple') }}",
                        keyword:"{{ Request::route('keyword') }}",
                    @else
                        url:"{{ route('salesman_list_simple') }}",
                    @endif
                    backFn:function(p){
                        console.log("回调函数："+p);
                    }
                });
            });
        </script>
        @if($sign == 'search')
            <script src="{{ asset('style/js/pagingSearch.js') }}"></script>
            @else
            <script src="{{ asset('style/js/paging.js') }}"></script>
        @endif
    @endif
@endsection
