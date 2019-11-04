@extends('layouts.edit-add-master')

@section('panel_top')
    @if(isset($dataTypeContent))
        <a class="btn btn-info btn-rounded" href="{{ url('/admin/medias/copy/' . $dataTypeContent->id ) }}">{{ trans('label.copy_'.$dataType->slug) }} </a>
        <a class="btn btn-danger btn-rounded" href="{{ url('/admin/medias/del/' . $dataTypeContent->id ) }}">{{ trans('label.delete_'.$dataType->slug) }} </a>
    @endif
    <a class="btn btn-success btn-rounded" href="{{ url('/admin/'.$dataType->slug) }}">{{ trans('label.back_'.$dataType->slug) }} </a>
@stop


@section('panel_l_1_title')
    <i class="voyager-list"></i> {{ __('generic.basic_field') }}
@stop

@section('panel_l_1_body')
    @include('easyweb2::partials.data_type_fields', [ 'dataType' => $dataType ,'dataTypeContent' => $dataTypeContent , 'isInclude' => true , 'fields' => ['id','title','title_link','subtitle'] ])
@stop

@section('panel_r_1_title')
   {{ __('generic.adv_field') }}

@stop

@section('panel_r_1_body')
    @include('easyweb2::partials.data_type_fields', [ 'dataType' => $dataType ,'dataTypeContent' => $dataTypeContent , 'isInclude' => true , 'fields' => ['enabled','sort','media_belongsto_cgy_relationship'] ])
@stop

@section('panel_r_2_title')
    
     {{ __('generic.btn_setting') }}
@stop

@section('panel_r_2_body')
    @include('easyweb2::partials.data_type_fields', [ 'dataType' => $dataType ,'dataTypeContent' => $dataTypeContent , 'isInclude' => true , 'fields' => ['l_link','l_icon','l_type','r_link','r_icon','r_type'] ])
@stop


@section('panel_l_2_title')
    <i class="voyager-images"></i>{{ __('generic.media') }}
@stop

@section('panel_l_2_body')
    @include('easyweb2::partials.data_type_fields', [ 'dataType' => $dataType ,'dataTypeContent' => $dataTypeContent , 'isInclude' => true , 'fields' => ['pics'] ])
@stop

@section('panel_l_3_title')
    <i class="voyager-treasure-open"></i>{{ __('generic.additional_field') }}
@stop

@section('panel_l_2_body')
    @include('easyweb2::partials.data_type_fields', [ 'dataType' => $dataType ,'dataTypeContent' => $dataTypeContent , 'isInclude' => false , 'fields' => ['title','cgy_id','title_link','subtitle','enabled','sort','l_link','l_icon','l_type','r_link','r_icon','r_type','pics'] ])
@stop