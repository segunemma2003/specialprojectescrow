@extends('user.layouts.master')
@section('breadcrumb')
    @include('user.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("user.dashboard"),
        ]
    ], 'active' => __("KYC Verification")])
@endsection
@section('content') 
@if ($basic_settings->kyc_verification == true && isset($kyc_fields) && $kyc_fields != null)
    <div class="body-wrapper">
        <div class="row mt-20 mb-20-none justify-content-center align-items-center">
            <div class="col-xl-7 col-lg-7 mb-20">
                <div class="custom-card mt-10"> 
                    <div class="card-body"> 
                        <h3 class="title">{{ __("KYC Verification") }} &nbsp; <span class="{{ auth()->user()->kycStringStatus->class }}">{{ auth()->user()->kycStringStatus->value }}</span></h3>
                        @if (auth()->user()->kyc_verified == global_const()::PENDING)
                            <div class="pending text--warning kyc-text">{{ __('Your KYC information is submited. Please wait for admin confirmation. When you are KYC verified you will show your submited information here') }}.</div> 
                        @elseif (auth()->user()->kyc_verified == global_const()::REJECTED)
                            <div class="unverified text--danger kyc-text d-flex align-items-center justify-content-between mb-4">
                                <div class="title text--warning">{{ __("Your KYC information is rejected.") }}</div> 
                            </div>
                            <div class="rejected">
                                <div class="rejected-reason">{{ auth()->user()->kyc->reject_reason ?? "" }}</div>
                            </div>
                        @elseif (auth()->user()->kyc_verified == global_const()::APPROVED)
                            <div class="approved text--success kyc-text">{{ __('Your KYC information is verified') }}</div>
                            <ul class="kyc-data">
                                @foreach (auth()->user()->kyc->data ?? [] as $item)
                                    <li>
                                        @if ($item->type == "file")
                                            @php
                                                $file_link = get_file_link("kyc-files",$item->value);
                                            @endphp
                                            <span class="kyc-title">{{ $item->label }}:</span>
                                            @if (its_image($item->value))
                                                <div class="kyc-image">
                                                    <img src="{{ $file_link }}" alt="{{ $item->label }}">
                                                </div>
                                            @else
                                                <span class="text--danger">
                                                    @php
                                                        $file_info = get_file_basename_ext_from_link($file_link);
                                                    @endphp
                                                    <a href="{{ setRoute('file.download',["kyc-files",$item->value]) }}" >
                                                        {{ Str::substr($file_info->base_name ?? "", 0 , 20 ) ."..." . $file_info->extension ?? "" }}
                                                    </a>
                                                </span>
                                            @endif
                                        @else
                                            <span class="kyc-title">{{ $item->label }}:</span>
                                            <span>{{ $item->value }}</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                        <p>{{ __("Please submit your KYC information with valid data.") }}</p>
                        <form action="{{ setRoute('user.authorize.kyc.submit') }}" class="account-form" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row ml-b-20">
    
                                @include('user.components.generate-kyc-fields',['fields' => $kyc_fields])
    
                                <div class="col-lg-12 form-group">
                                    <div class="forgot-item">
                                        <label>{{ __("Back to ") }}<a href="{{ setRoute('user.dashboard') }}" class="text--base">{{ __("Dashboard") }}</a></label>
                                    </div>
                                </div>
                                <div class="col-lg-12 form-group text-center">
                                    <button type="submit" class="btn--base w-100">{{ __("Submit") }}</button>
                                </div>
                            </div>
                        </form>
                        @endif
                    </div>
                </div>
            </div> 
        </div> 
    </div>
@endif
@endsection