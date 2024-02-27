@extends('admin.master')

@section('title' , 'Admin | Home')

@section('content')



    <div class="profile-cover bg-dark"></div>

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="mb-0">{{ $user->name }} @lang('account') </h5>
                    <hr>
                    <div class="card shadow-none border">
                        <div class="card-header">
                            <h6 class="mb-0">@lang('USER_INFORMATION')</h6>
                        </div>
                        <div class="card-body">
                            <form class="row g-3">
                                <div class="col-6">
                                    <label class="form-label">@lang('name')</label>
                                    <input type="text" class="form-control" value="{{ $user->name }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">@lang('email')</label>
                                    <input type="text" class="form-control" value="{{ $user->email }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">@lang('date_of_birth')</label>
                                    <input type="date" class="form-control" value="{{ $user->dateOfBirth }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">@lang('created_at')</label>
                                    <input type="date" class="form-control" value="{{ $user->created_at->toDateString() }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">@lang('gender')</label>
                                    <input class="form-control" value="{{ $user->gender == 1 ? __('male') : __('female')  }}">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm border-0 overflow-hidden">
                <div class="card-body">
                    <div class="profile-avatar text-center">
                        <img src="{{ $user->image }}" class="rounded-circle shadow" width="120" height="120" alt="">
                    </div>
                    <div class="d-flex align-items-center justify-content-around mt-5 gap-3">
                        <div class="text-center">
                            <h4 class="mb-0">{{ count($user->followers) }}</h4>
                            <p class="mb-0 text-secondary">@lang('followers')</p>
                        </div>
                        <div class="text-center">
                            <h4 class="mb-0">{{ count($user->following) }}</h4>
                            <p class="mb-0 text-secondary">@lang('following')</p>
                        </div>
                        <div class="text-center">
                            <h4 class="mb-0">{{ count($user->posts) }}</h4>
                            <p class="mb-0 text-secondary">@lang('posts')</p>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <h4 class="mb-1">{{$user->name}}</h4>
                    </div>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-top">
                        Comments
                        <span class="badge bg-primary rounded-pill">{{ count($user->comments) }}</span>
                    </li>
                    @if($user->star == 1)
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-top">
                        Verification
                        <span class="badge bg-primary rounded-pill"><i style="color: white" class="lni lni-star-filled"></i></span>
                    </li>
                    @endif
                    @if($user->gender == 1)
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-top">
                            Gender
                            <span class="badge bg-info rounded-pill">@lang('male')</span>
                        </li>
                    @else
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-top">
                            Gender
                            <span style="background-color: #ff69b4;
                                    color: #fff;
                                    border-color: #ff69b4;" class="badge rounded-pill">@lang('female')</span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div><!--end row-->

@stop







