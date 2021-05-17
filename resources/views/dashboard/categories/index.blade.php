@extends('layouts.dashboard.app')

@section('content')
@section('title')
    الاقسام الاساسيه
@endsection
    <div class="content-wrapper">

        <section class="content-header">

            <h1>الاقسام الاساسيه</h1>

            <ol class="breadcrumb">
                <li><a href="{{route('dashboard.index')}}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a>
                </li>
                <li class="active"></i>الاقسام</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title" style="margin-bottom: 10px;">الاقسام
                        <small>{{ $cats->total() }}</small>
                    </h3>
                    <form action="{{ route('dashboard.cats.index') }}" method="get">
                        <div class="row">

                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" value="{{ request()->search }}"
                                       placeholder="@lang('site.search')">
                            </div>


                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary"><i
                                        lass="fa fa-search"></i>@lang('site.search')</button>
                                <a href="{{ route('dashboard.cats.create') }}" class="btn btn-primary"><i
                                    class="fa fa-plus"></i>@lang('site.add')</a>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="box-body">

                    @if($cats->count() > 0)
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>الاسم بالعربيه</th>
                                <th>الاسم بالانجليزيه</th>
                                <th>الصوره</th>
                                <th>الاقسام الفرعيه</th>
                                <th>@lang('site.action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($cats as $index => $cat)
                                <tr>
                                    <td>{{ $index +1 }}</td>
                                    <td>{{ $cat->category_name_ar }}</td>
                                    <td>{{ $cat->category_name_en }}</td>
                                    <td>
                                        <img src="{{ asset('uploads/category/'.$cat->img) }}" width="100" height="50" alt="" srcset="">
                                    </td>
                                    <td>
                                        <a href="{{ route('dashboard.cats.child.index' ,  $cat->id) }}" class="btn btn-warning btn-sm">الاقسام الفرعيه</a>
                                    </td>
                                    <td>

                                        @if(auth()->user()->hasPermission('update_categories'))
                                            <a class="btn btn-info btn-sm" title="تعديل القسم !"
                                                href="{{route('dashboard.cats.edit' , $cat->id)}}"><i
                                                        class="fa fa-edit"></i>@lang('site.edit')</a>
                                        @else
                                            <a class="btn btn-info btn-sm" href="#" disabled><i
                                                    class="fa fa-edit"></i>تعديل</a>
                                        @endif


                                        @if(auth()->user()->hasPermission('delete_categories'))
                                            <form method="post"
                                                    action="{{route('dashboard.cats.destroy' , $cat->id)}}"
                                                    style="display: inline-block">
                                                @csrf()
                                                @method('delete')
                                                <button type="submit" title="حذف القسم !" class="btn btn-danger btn-sm delete"><i
                                                        class="fa fa-trash"></i>@lang('site.delete')</button>
                                            </form>
                                        @else
                                            <button type="submit" class="btn btn-danger btn-sm" disabled><i
                                                        class="fa fa-trash"></i>حذف</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                        {{ $cats->appends(request()->query())->links() }}
                    @else
                        <h2>@lang('site.no_data_found')</h2>
                    @endif

                </div>
            </div>

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->

@endsection
