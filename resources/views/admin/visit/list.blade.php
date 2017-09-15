@extends('layouts.admin')

@section('title', '客户列表')

@section('style')
    @parent
@endsection

@section('breadcrumb')
    <li navValue="nav_1"><a href="#">业务员操作</a></li>
    <li navValue="nav_1_3"><a href="#">客户跟踪记录</a></li>
@endsection

@section('body')
<div class="row">
    <div class="col-md-12">
		<section class="panel">
            <div class="panel-body">
                <button type="button" class="btn btn-primary" onclick="location='{{ route('visit_add') }}'">添加回访记录</button>
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-success dropdown-toggle" type="button">选择客户 <span class="caret"></span></button>
                    <ul role="menu" class="dropdown-menu">
                        @foreach($customers as $customer)
                            <li><a href="{{ route('visit_search', ['keyword' => 'customer_'.$customer['id']]) }}">{{ $customer['name'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
                @if(can('manage'))
                    <div class="btn-group">
                        <button data-toggle="dropdown" class="btn btn-success dropdown-toggle" type="button">选择业务员 <span class="caret"></span></button>
                        <ul role="menu" class="dropdown-menu">
                            @foreach($salesmans as $salesman)
                                <li><a href="{{ route('visit_search', ['keyword' => 'salesman_'.$salesman['id']]) }}">{{ $salesman['name'] }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if(can('admin'))
                    <div class="btn-group">
                        <button data-toggle="dropdown" class="btn btn-success dropdown-toggle" type="button">选择分组 <span class="caret"></span></button>
                        <ul role="menu" class="dropdown-menu">
                            @foreach($groups as $group)
                                <li><a href="{{ route('visit_search', ['keyword' => 'group_'.$group['id']]) }}">{{ $group['name'] }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            <header class="panel-heading">
                客户列表
            </header>
            	<table class="table table-striped table-hover">
		            <thead>
		                <tr>
		                    <th>ID</th>
		                    <th>业务员</th>
                            <th>客户</th>
                            <th>记录</th>
                            <th>添加时间</th>
                            <th>操作</th>
		                </tr>
		            </thead>

		            <tbody id="target">
                        @foreach($lists as $list)
                        <tr>
                            <td>{{ $list['id'] }}</td>
                            <td>{{ $list['salesman_name'] }}</td>
                            <td>{{ $list['customer_name'] }}</td>
                            <td>{{ $list['record'] }}</td>
                            <td>{{ $list['created_at'] }}</td>
                            <td>
                                @if(can('manage'))
                                    <button class="btn btn-info" type="button" onclick="location='{{ route('visit_update', ['id' => $list['id'] ]) }}'">编辑</button>
                                    <button class="btn btn-danger" type="button" onclick="javascript:if(confirm('确实要删除吗?'))location='{{ route('visit_destroy', ['id' => $list['id'] ]) }}'">删除</button>
                                    @else 无权限
                                @endif
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
@endsection
