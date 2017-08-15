@extends('layouts.admin')

@section('title', '添加/管理业务员')

@section('style')
    @parent
@endsection

@section('breadcrumb')
    <li navValue="nav_1"><a href="#">业务员操作</a></li>
    <li navValue="nav_1_2"><a href="#">添加/管理客户</a></li>
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
                添加/更新客户
            </header>
            <div class="panel-body">
                <form id="form" class="form-horizontal adminex-form" method="post" action="{{ $url }}">
                    {{ csrf_field() }}
                    <input type="hidden" class="form-control" name="salesman_id" value="{{ Auth::id() }}" required>
                    <div class="form-group">
                        <label for="name" class="col-sm-2 col-sm-2 control-label">客户姓名</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="name" name="name" value="{{ $old_input['name'] }}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="phone" class="col-sm-2 col-sm-2 control-label">客户电话</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ $old_input['phone'] or null}}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="col-sm-2 col-sm-2 control-label">客户邮箱</label>
                        <div class="col-sm-3">
                            <input type="email" class="form-control" id="email" name="email" value="{{ $old_input['email'] or null}}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="company" class="col-sm-2 col-sm-2 control-label">客户公司</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="company" name="company" value="{{ $old_input['company'] or null}}">
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
