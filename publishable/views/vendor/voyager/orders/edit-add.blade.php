@extends('layouts.edit-add-master')

@section('panel_top')
    @if(isset($dataTypeContent))
{{-- <a class="btn btn-info btn-rounded" href="{{ url('/admin/orders/copy/' . $dataTypeContent->id ) }}">{{ trans('label.copy_'.$dataType->slug) }} </a> --}}
{{-- <a class="btn btn-danger btn-rounded" href="{{ url('/admin/orders/del/' . $dataTypeContent->id ) }}">{{ trans('label.delete_'.$dataType->slug) }} </a> --}}
    @endif
    <a class="btn btn-success btn-rounded" href="{{ url('/admin/'.$dataType->slug) }}">{{ trans('label.back_'.$dataType->slug) }} </a>
@stop


@section('panel_l_1_title')
    <i class="voyager-character"></i> {{ __('generic.basic_field') }}
    {{-- <span class="panel-desc"> {{ __('voyager::post.title_sub') }}</span> --}}
@stop

@section('panel_l_1_body')
    @include('easyweb2::partials.data_type_fields', [ 'dataType' => $dataType ,'dataTypeContent' => $dataTypeContent , 'isInclude' => true , 'fields' => ['owner_id','receiver','receiverTitle','receiverMobile','receiverEmail','receiverAddress'] ])
@stop

@section('panel_l_2_title')
    {{ __('generic.content_field_big') }}
@stop

@section('panel_l_2_body')
    @include('easyweb2::partials.data_type_fields', [ 'dataType' => $dataType ,'dataTypeContent' => $dataTypeContent , 'isInclude' => true , 'fields' => ['message','reply_desc','couponCode'] ])
@stop

@section('panel_l_3_title')
    <i class="voyager-treasure-open"></i> {{ __('voyager::post.additional_fields') }}
@stop

@section('panel_l_3_body')
    @include('easyweb2::partials.data_type_fields', [ 'dataType' => $dataType ,'dataTypeContent' => $dataTypeContent , 'isInclude' => false , 'fields' => ['id','owner_id','receiver','receiverTitle','receiverMobile','receiverEmail','receiverAddress','message','couponCode','subtotal','shipCost','status','pay_type','trade_no','pay_at','pay_from','created_at','updated_at','pay_pre','pay_after','type','reply_desc'] ])
@stop

@section('panel_r_1_title')
    <i class="icon wb-clipboard"></i> {{ __('generic.adv_field') }}
@stop

@section('panel_r_1_body')
    @include('easyweb2::partials.data_type_fields', [ 'dataType' => $dataType ,'dataTypeContent' => $dataTypeContent , 'isInclude' => true , 'fields' => ['shipCost','subtotal','status','type'] ])
@stop

@section('panel_r_2_title')
    {{ __('generic.content_field_small') }}
@stop

@section('panel_r_2_body')
    @include('easyweb2::partials.data_type_fields', [ 'dataType' => $dataType ,'dataTypeContent' => $dataTypeContent , 'isInclude' => true , 'fields' => ['pay_type','trade_no','pay_at','pay_from','pay_pre','pay_after'] ])
@stop

@section('panel_r_3_all')

@stop

@section('javascript_child')
    <script>

    </script>
@stop