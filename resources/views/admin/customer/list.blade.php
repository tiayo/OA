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
                <form class="form-inline" method="get" action="{{ route('customer_search_simple') }}">
                    <div class="form-group">
                        <label class="sr-only" for="search"></label>
                        <input type="text" class="form-control" id="search" name="keyword"
                               value="{{ Request::route('keyword') }}" placeholder="姓名、邮箱、手机、公司" required>
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
                            <th>手机</th>
                            <th>公司</th>
                            <th>备注</th>
                            <th>添加时间</th>
							<th>操作</th>
		                </tr>
		            </thead>

		            <tbody id="target">
                        @foreach($customers as $customer)
                        <tr>
                            <td>{{ $customer['id'] }}</td>
                            <td>{{ $customer['name'] }}</td>
                            <td>{{ $customer['phone'] }}</td>
                            <td>{{ $customer['company'] }}</td>
                            <td>{{ $customer['remark'] }}</td>
                            <td>{{ $customer['created_at'] }}</td>
                            <td>
                                <button class="btn btn-info" type="button" onclick="location='{{ route('customer_update', ['id' => $customer['id'] ]) }}'">编辑</button>
                                <button class="btn btn-danger" type="button" onclick="javascript:if(confirm('确实要删除吗?'))location='{{ route('customer_destroy', ['id' => $customer['id'] ]) }}'">删除</button>
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
            $(".pagination").createPage({
                totalPage:{{ $count }},
                currPage:{{ $current }},
                url:"{{ route('customer_list_simple') }}",
                backFn:function(p){
                    console.log("回调函数："+p);
                }
            });
        });
    </script>
    <script src="{{ asset('style/js/paging.js') }}"></script>
@endsection
