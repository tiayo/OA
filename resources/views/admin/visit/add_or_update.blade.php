@extends('layouts.admin')

@section('title', '添加/管理业务员')

@section('style')
    @parent
@endsection

@section('breadcrumb')
    <li navValue="nav_1"><a href="#">业务员操作</a></li>
    <li navValue="nav_1_3"><a href="#">添加/管理客户跟踪记录</a></li>
@endsection

@section('body')
    <div class="col-md-12">

        <!--错误输出-->
        <div class="form-group">
            <div class="alert alert-danger fade in @if(!count($errors) > 0) hidden @endif" id="alert_error">
                <a href="#" class="close" data-dismiss="alert">×</a>
                <span>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </span>
            </div>
        </div>

        <section class="panel">
            <header class="panel-heading">
                添加/管理客户跟踪记录
            </header>
            <div class="panel-body">
                <form id="form" class="form-horizontal adminex-form" method="post" action="{{ $url }}">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="customer_id" class="col-sm-2 col-sm-2 control-label">客户</label>
                        <div class="col-sm-3">
                            <select class="form-control m-bot15" id="customer_id" @if($sign == 'add')name="customer_id" @else disabled @endif>
                                @if($sign == 'update')
                                    <option>{{ $old_input['customer_name'] }}</option>
                                    @elseif($sign == 'add')
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer['id'] }}">{{ $customer['name'] }}</option>
                                        @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="record" class="col-sm-2 col-sm-2 control-label">回访记录</label>
                        <div class="col-sm-3">
                            <textarea class="form-control" id="record" name="record">{{ $old_input['record'] or null}}</textarea>
                        </div>
                    </div>
                    <div class="group">
                        <div  class="col-sm-2 col-sm-2 control-label">
                            <button class="btn btn-success" type="submit"><i class="fa fa-cloud-upload"></i> 确认提交</button>
                        </div>
                    </div>

                </form>
            </div>
        </section>
    </div>
@endsection

@section('script')
    @parent
    <script>
        $(document).ready(function () {
            $('#password').bind('input propertychange', function() {
                $(this).attr('name', 'password')
            })
        })
    </script>
@endsection
