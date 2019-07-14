@extends('layouts.site_noHeader')
@section('seo')
	<meta name="keywords" content="{{ $keywords }}">
    <meta name="description" content="{{ $description }}">
@stop
@section('page_title'){{ trans('menu.worksPage') }}@stop

@section('body')
<section id="content">

	<div class="content-wrap">
		<div class="container clearfix">
			<!-- Top區塊-->
				<div class="col_full">

					<div class="heading-block center nobottomborder">
						<h2>{{ $item_top->title }}</h2>
						<span>{{ $item_top->content}}</span>
					</div>

					<div class="fslider" data-pagi="false" data-animation="fade">
						<div class="flexslider">
							<img src="{{ asset('images/'.$item_top->pic) }}" alt="{{ $item_top->alt }}"/>
						</div>
					</div>

				</div><!-- Top區塊結束-->

		</div>

		<div class="section nobottommargin">

			<div class="container clearfix">

				<div class="fancy-title title-center title-dotted-border topmargin">
					<h3>{{ trans('page.works') }}</h3>
				</div>

				@include('partials.medias')


			</div>

		</div>
	</div>
</section><!-- #content end -->
	<!-- Call To Action  -->
	@include('partials.action')
@stop