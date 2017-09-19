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
                <form class="form-inline" id="customer_form">
                    @if(can('admin'))
                        <div class="btn-group">
                            <button data-toggle="dropdown" class="btn btn-success dropdown-toggle" type="button">根据分组查看 <span class="caret"></span></button>
                            <ul role="menu" class="dropdown-menu">
                                @foreach($groups as $group)
                                    <li><a href="{{ route('customer_by_group', ['group' => $group['id'] ]) }}">{{ $group['name'] }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(can('manage'))
                        <div class="btn-group">
                            <button data-toggle="dropdown" class="btn btn-success dropdown-toggle" type="button">根据业务员查看 <span class="caret"></span></button>
                            <ul role="menu" class="dropdown-menu">
                                <li><a href="{{ route('customer_by_salesman', ['salesman' => Auth::id() ]) }}">我</a></li>
                                @foreach($salesmans as $salesman)
                                    <li><a href="{{ route('customer_by_salesman', ['salesman' => $salesman['id'] ]) }}">{{ $salesman['name'] }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

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
                        @foreach($customers as $customer)
                        <tr>
                            <td>{{ $customer['id'] }}</td>
                            <td>{{ $customer['name'] }}</td>
                            <td>{{ $customer['salesman_name'] }}</td>
                            <td>{{ $customer['wx'] }}</td>
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
                <div class="dataTables_info">当前共{{ $customers->total() }}个客户  当前显示第{{ $customers->currentPage() }}页</div>
                {{ $customers->links() }}
            </div>
    	</section>
    </div>
</div>
@endsection

@section('script')
    @parent
    {{--转换搜索链接--}}
    <script type="text/javascript">
        $(document).ready(function () {

            $('#customer_form').submit(function () {

                var keyword = $('#search').val();

                if (stripscript(keyword) == '') {
                    $('#search').val('');
                    return false;
                }

                window.location = '{{ route('customer_search', ['keyword' => '']) }}/' + stripscript(keyword);

                return false;
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
