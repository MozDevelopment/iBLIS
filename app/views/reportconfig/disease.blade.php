@extends("layout")
@section("content")
<div>
	<ol class="breadcrumb">
	  <li><a href="{{{URL::route('user.home')}}}">{{ trans('messages.home') }}</a></li>
	  <li class="active">{{ Lang::choice('messages.disease',2) }}</li>
	</ol>
</div>
<div class="panel panel-primary">
	<div class="panel-heading ">
		<span class="glyphicon glyphicon-edit"></span>
		{{ Lang::choice('messages.disease',2) }}
	</div>
	{{ Form::open(array('route' => 'reportconfig.disease', 'id' => 'form-edit-disease')) }}
		<div class="panel-body disease-input">
			<div class="alert alert-danger error-div hidden">
				<ul><li>Please enter all fields</li></ul>
				@if($errors->all())
					{{ HTML::ul($errors->all()) }}
				@endif
			</div>
			@foreach($diseases as $disease)
			<div class="form-group">
				<div class="row">
					<div class="col-sm-5 col-md-3">
						<input class="form-control" name="disease[{{ $disease->id }}][disease]"
							type="text" value="{{ $disease->name }}">
					    <button class="close" aria-hidden="true" type="button" 
					        title="{{trans('messages.delete')}}">×</button>
					</div>
				</div>
            </div>
			@endforeach
		</div>
		<div class="panel-footer">
			<div class="form-group actions-row">
				<input class="hidden" name="from-form" type="text" value="from-form">
				{{ Form::button(
					'<span class="glyphicon glyphicon-save"></span> '.trans('messages.save'), 
					['class' => 'btn btn-primary', 'onclick' => 'authenticate("#form-edit-disease")']
				) }}
				{{ Form::button(trans('messages.cancel'), 
					['class' => 'btn btn-default', 'onclick' => 'javascript:history.go(-1)']
				) }}
				{{ Form::button(trans('messages.add-another'), 
					['class' => 'btn btn-default add-another-disease', 'data-new-disease' => '1']
				) }}
			</div>
		</div>
	{{ Form::close() }}
</div>
<div class="hidden addDiseaseLoader">
	<div class="form-group new">
		<div class="row">
			<div class="col-sm-5 col-md-3">
                <select class="form-control test-type" name=""> 
					<option value="0"></option>
					@foreach (TestType::all() as $testType)
					    <option value="{{ $testType->id }}">{{ $testType->name }}</option>
					@endforeach
            	</select>
			</div>
			<div class="col-sm-5 col-md-3">
				<input class="form-control disease" name="" type="text">
			    <button class="close" aria-hidden="true" type="button" 
			        title="{{trans('messages.delete')}}">×</button>
			</div>
		</div>
    </div>
</div>
@stop