@extends("layout")
@section("content")
	<div>
		<ol class="breadcrumb">
			<li><a href="{{{URL::route('user.home')}}}">{{trans('messages.home')}}</a></li>
			<li><a href="{{ URL::route('test.index') }}">{{ Lang::choice('messages.test',2) }}</a></li>
			<li class="active">{{trans('messages.test-details')}}</li>
		</ol>
	</div>
	<?php $showWorkSheet = false;?>
	<div class="panel panel-primary">
		<div class="panel-heading ">
			<div class="container-fluid">
				<div class="row less-gutter">
					<div class="col-md-11">
						<span class="glyphicon glyphicon-cog"></span>{{trans('messages.test-details')}}

						@if($test->isCompleted() && $test->specimen->isAccepted())
							<div class="panel-btn">

								@if(empty($hideVerifyButton) && Auth::user()->can('verify_test_results') && (Auth::user()->id != $test->tested_by || Entrust::hasRole(Role::getAdminRole()->name)))
									<a class="btn btn-sm btn-success" href="{{ URL::route('test.verify', array($test->id)) }}">
										<span class="glyphicon glyphicon-thumbs-up"></span>
										{{trans('messages.verify')}}
									</a>
								@endif
							</div>
						@endif
						@if($test->isCompleted() ||$test->isVerified())
							<div class="panel-btn">
								@if(Auth::user()->can('view_reports'))
									<a class="btn btn-sm btn-default" href="{{ URL::to('patientreport/'.$test->visit->patient->id.'/'.$test->visit->id) }}">
										<span class="glyphicon glyphicon-eye-open"></span>
										{{trans('messages.view-report')}}
									</a>
								@endif
							</div>
						@endif
					</div>
					<div class="col-md-1">
						<a class="btn btn-sm btn-primary pull-right" href="#" onclick="window.history.back();return false;"
						   alt="{{trans('messages.back')}}" title="{{trans('messages.back')}}">
							<span class="glyphicon glyphicon-backward"></span></a>
					</div>
				</div>
			</div>
		</div> <!-- ./ panel-heading -->
		<div class="panel-body">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-6">
						<div class="display-details">
							<h3 class="view"><strong>{{ Lang::choice('messages.test-type',1) }}</strong>
								<?php
								$testName = '';
								if ($test->panel_id){
									$testName = PanelType::find(TestPanel::find($test->panel_id)->panel_type_id)->name;
								}

								?>
								{{ !empty($testName) ? $testName : $test->testType->name }}</h3>

							<p class="view"><strong>{{trans('messages.date-ordered')}}</strong>
								{{ $test->isExternal()?$test->external()->request_date:$test->time_created }}</p>
							<p class="view"><strong>{{trans('messages.lab-receipt-date')}}</strong>
								{{$test->time_created}}</p>
							<p class="view"><strong>{{trans('messages.test-status')}}</strong>
								{{trans('messages.'.$test->testStatus->name)}}</p>
							<p class="view"><strong>{{trans('messages.ward')}}</strong>
								{{$test->visit->ward_or_location or trans('messages.unknown') }}</p>
							<p class="view-striped"><strong>{{trans('messages.physician')}}</strong>
								{{$test->requested_by or trans('messages.unknown') }}</p>
							<p class="view-striped"><strong>{{trans('messages.request-origin')}}</strong>
								@if($test->specimen->isReferred() && $test->specimen->referral->status == Referral::REFERRED_IN)
									{{ trans("messages.in") }}
								@else
									{{ $test->visit->visit_type }}
								@endif</p>
							<p class="view-striped"><strong>{{trans('messages.registered-by')}}</strong>
								{{$test->createdBy->name or trans('messages.unknown') }}</p>
							<p class="view"><strong>{{trans('messages.tested-by')}}</strong>
								{{$test->testedBy->name or trans('messages.unknown')}}</p>
							@if($test->isVerified())
								<p class="view"><strong>{{trans('messages.verified-by')}}</strong>
									{{$test->verifiedBy->name or trans('messages.verification-pending')}}</p>
							@endif
							@if((!$test->specimen->isRejected()) && ($test->isVerified()))
								<!-- Not Rejected and (Verified or Completed)-->
								<p class="view-striped"><strong>{{trans('messages.turnaround-time')}}</strong>
									{{$test->getFormattedTurnaroundTime()}}</p>
							@endif
						</div>
					</div>
					<div class="col-md-6">
						<div class="panel panel-info">  <!-- Patient Details -->
							<div class="panel-heading">
								<h3 class="panel-title">{{trans("messages.patient-details")}}</h3>
							</div>
							<div class="panel-body">
								<div class="container-fluid">
									<div class="row">
										<div class="col-md-3">
											<p><strong>{{trans("messages.patient-number")}}</strong></p></div>
										<div class="col-md-9">
											{{$test->visit->patient->external_patient_number}}</div></div>
									<div class="row">
										<div class="col-md-3">
											<p><strong>{{ Lang::choice('messages.name',1) }}</strong></p></div>
										<div class="col-md-9">
											{{$test->visit->patient->name}}</div></div>
									<div class="row">
										<div class="col-md-3">
											<p><strong>{{trans("messages.age")}}</strong></p></div>
										<div class="col-md-9">
											{{$test->visit->patient->getAge('YY/MM')}}</div></div>
									<div class="row">
										<div class="col-md-3">
											<p><strong>{{trans("messages.gender")}}</strong></p></div>
										<div class="col-md-9">
											{{$test->visit->patient->gender==0?trans("messages.male"):trans("messages.female")}}
										</div></div>
								</div>
							</div> <!-- ./ panel-body -->
						</div> <!-- ./ panel -->
						<div class="panel panel-info"> <!-- Specimen Details -->
							<div class="panel-heading">
								<h3 class="panel-title">{{trans("messages.specimen-details")}}</h3>
							</div>
							<div class="panel-body">
								<div class="container-fluid">
									<div class="row">
										<div class="col-md-4">
											<p><strong>{{ Lang::choice('messages.specimen-type',1) }}</strong></p>
										</div>
										<div class="col-md-8">
											{{$test->specimen->specimenType->name or trans('messages.pending') }}
										</div>
									</div>
									<div class="row">
										<div class="col-md-4">
											<p><strong>{{trans('messages.specimen-number')}}</strong></p>
										</div>
										<div class="col-md-8">
											{{$test->getSpecimenId() }}
										</div>
									</div>
									<div class="row">
										<div class="col-md-4">
											<p><strong>{{trans('messages.specimen-status')}}</strong></p>
										</div>
										<div class="col-md-8">
											{{trans('messages.'.$test->specimen->specimenStatus->name) }}
										</div>
									</div>
									@if($test->specimen->isRejected())
										<div class="row">
											<div class="col-md-4">
												<p><strong>{{trans('messages.rejection-reason-title')}}</strong></p>
											</div>
											<div class="col-md-8">
												{{$test->specimen->rejectionReason->reason or trans('messages.pending') }}
											</div>
										</div>
										<div class="row">
											<div class="col-md-4">
												<p><strong>{{trans('messages.reject-explained-to')}}</strong></p>
											</div>
											<div class="col-md-8">
												{{$test->specimen->reject_explained_to or trans('messages.pending') }}
											</div>
										</div>
									@endif
									@if($test->specimen->isReferred())
										<br>
										<div class="row">
											<div class="col-md-4">
												<p><strong>{{trans("messages.specimen-referred-label")}}</strong></p>
											</div>
											<div class="col-md-8">
												@if($test->specimen->referral->status == Referral::REFERRED_IN)
													{{ trans("messages.in") }}
												@elseif($test->specimen->referral->status == Referral::REFERRED_OUT)
													{{ trans("messages.out") }}
												@endif
											</div>
										</div>
										<div class="row">
											<div class="col-md-4">
												<p><strong>{{Lang::choice("messages.facility", 1)}}</strong></p>
											</div>
											<div class="col-md-8">
												{{$test->specimen->referral->facility->name }}
											</div>
										</div>
										<div class="row">
											<div class="col-md-4">
												<p><strong>@if($test->specimen->referral->status == Referral::REFERRED_IN)
															{{ trans("messages.originating-from") }}
														@elseif($test->specimen->referral->status == Referral::REFERRED_OUT)
															{{ trans("messages.intended-reciepient") }}
														@endif</strong></p>
											</div>
											<div class="col-md-8">
												{{$test->specimen->referral->person }}
											</div>
										</div>
										<div class="row">
											<div class="col-md-4">
												<p><strong>{{trans("messages.contacts")}}</strong></p>
											</div>
											<div class="col-md-8">
												{{$test->specimen->referral->contacts }}
											</div>
										</div>
										<div class="row">
											<div class="col-md-4">
												<p><strong>@if($test->specimen->referral->status == Referral::REFERRED_IN)
															{{ trans("messages.recieved-by") }}
														@elseif($test->specimen->referral->status == Referral::REFERRED_OUT)
															{{ trans("messages.referred-by") }}
														@endif</strong></p>
											</div>
											<div class="col-md-8">
												{{ $test->specimen->referral->user->name }}
											</div>
										</div>
									@endif
								</div>
							</div>
						</div>

					</div>
				</div>
			</div> <!-- ./ container-fluid -->
			<!-- ./ panel -->
			<div class="panel panel-info">  <!-- Test Results -->
				<div class="panel-heading">
					<h3 class="panel-title">{{trans("messages.test-results")}}</h3>
				</div>
				<div class="panel-body">
					<div class="container-fluid">
						<?php
						if($test->panel_id){
							$tests = Test::where('panel_id', $test->panel_id)->get();
						}else{
							$tests = array($test);
						}
						?>


							@foreach($tests as $test)
								<?php
									if (count($test->testType->organisms) > 0){
										$showWorkSheet = true;
									}
								?>


											<div class="col-md-6">
												<table class="table table-bordered">
													<tbody>
													<tr>
														<th>{{ $test->testType->name }}</th>
														<th>
															@if($test->isCompleted() && Auth::user()->can('edit_test_results'))
																<a class="btn btn-sm btn-info pull-right" id="edit-{{$test->id}}-link"
																   href="{{ URL::route('test.edit', array($test->id)) }}"
																   title="{{trans('messages.edit-test-results')}}">
																	<span class="glyphicon glyphicon-edit"></span>
																	{{trans('messages.edit-test-results')}}
																</a>
															@endif
														</th>
													</tr>
													<tr>
														<th width="50%">{{ Lang::choice('messages.measure-type',1) }}</th>
														<th>{{ trans('messages.result-name')}}</th>
													</tr>
													@foreach($test->testResults as $result)
															<tr>
																<td>{{ Measure::find($result->measure_id)->name }}</td>
																<td>@if ($result->result)
																		{{$result->result}}
																		{{ Measure::find($result->measure_id)->unit }}
																	@else
																		&nbsp;
																	@endif
																</td>
															</tr>
													@endforeach

													@if($test->interpretation)
														<tr>
															<td>{{ trans('messages.test-remarks').'/'.trans('messages.interpretation') }}</td>
															<td>{{$test->interpretation}}</td>
														</tr>
													@endif

													</tbody>
												</table>
											</div>

							@endforeach
					</div>
				</div> <!-- ./ panel-body -->
			</div>  <!-- ./ panel -->
			@if($showWorkSheet == true)
				<div class="panel panel-success">  <!-- Patient Details -->
					<div class="panel-heading">
						<h3 class="panel-title">{{trans("messages.culture-worksheet")}}</h3>
					</div>
					<div class="panel-body">
						<p><strong>{{trans("messages.culture-work-up")}}</strong></p>
						<table class="table table-bordered">
							<thead>

							</thead>
							<tbody id="tbbody">
							<tr>
								<th width="15%">{{ trans('messages.date')}}</th>
								<th width="10%">{{ trans('messages.tech-initials')}}</th>
								<th>{{ trans('messages.observations-and-work-up')}}</th>
							</tr>
							@if(($observations = $test->culture) != null)
								@foreach($observations as $observation)
									<tr>
										<td>{{ $observation->created_at }}</td>
										<td>{{ User::find($observation->user_id)->name }}</td>
										<td>{{ $observation->observation }}</td>
									</tr>
								@endforeach
							@else
								<tr>
									<td colspan="3">{{ trans('messages.no-data-found') }}</td>
								</tr>
							@endif
							</tbody>
						</table>
						<p><strong>{{trans("messages.susceptibility-test-results")}}</strong></p>
						@foreach($tests as $test)
						<div class="row">
							@if(count($test->susceptibility)>0)
								@foreach($test->organisms() as $organism)
									<?php
										$organism = Organism::find($organism->organism_id);
									?>
									<div class="col-md-6">
										<table class="table table-bordered">
											<tbody>
											<tr>
												<th colspan="3">{{ $organism->name }}</th>
											</tr>
											<tr>
												<th width="50%">{{ Lang::choice('messages.drug',1) }}</th>
												<th>{{ trans('messages.zone-size')}}</th>
												<th>{{ trans('messages.interp')}}</th>
											</tr>
											@foreach(Susceptibility::drugs_search($test->id, $organism->id) as $drug)
												<?php
													$drug = Drug::find($drug->drug_id);
												?>
												@if($drugSusceptibility = Susceptibility::getDrugSusceptibility($test->id, $organism->id, $drug->id))
													<tr>
														<td>{{ $drug->name }}</td>
														<td>{{ $drugSusceptibility->zone!=null?$drugSusceptibility->zone:'' }}</td>
														<td>{{ $drugSusceptibility->interpretation!=null?$drugSusceptibility->interpretation:'' }}</td>
													</tr>
												@else
													<tr>
														<td>{{ $drug->name }}</td>
														<td></td>
														<td></td>
													</tr>
												@endif
											@endforeach
											</tbody>
										</table>
									</div>
								@endforeach
							@endif
						</div>
						@endforeach
					</div>
				</div> <!-- ./ panel-body -->
			@endif
		</div> <!-- ./ panel-body -->
	</div> <!-- ./ panel -->
@stop