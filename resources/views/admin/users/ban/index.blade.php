@extends('admin.master')

@section('title' , 'Admin | Home')

@section('css')

    @stop

@section('content')

    <div class="modal fade" id="add-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('update') @lang('user')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="form_add" id="form_add" enctype="multipart/form-data" action="{{ route('admin.user.store')  }}"  method="POST">
                        @csrf
                        <div class="mb-2 form-group">
                            <label class="form-label">@lang("name")</label>
                            <input  placeholder="@lang('name')"  name="name" class="form-control" type="text">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-2 form-group">
                            <label class="form-label">@lang("user_name") <small>(@lang('must_be_unique'))</small></label>
                            <input  placeholder="@lang('user_name')"  name="username" class="form-control" type="text">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-2 form-group">
                            <label class="form-label">@lang("email")</label>
                            <input  placeholder="@lang('email')"  name="email" class="form-control" type="text">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-2 form-group">
                            <label class="form-label">@lang("mobile")</label>
                            <input  placeholder="@lang('mobile')"  name="mobile" class="form-control" type="text">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-2 form-group">
                            <label class="form-label">@lang("gender")</label>
                            <select name="gender" class="form-control" type="text">
                                <option value="1">@lang("male")</option>
                                <option value="2">@lang("female")</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-2 form-group">
                            <label class="form-label">@lang("date_of_birth")</label>
                            <input  placeholder="@lang('date_of_birth')"  name="date_of_birth" class="form-control" type="date">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-2 form-group">
                            <label class="form-label">@lang("image")</label>
                            <input name="image" class="form-control" type="file">
                            <div class="invalid-feedback"></div>
                        </div>
                        <hr>
                        <div class="mb-2 form-group">
                            <label class="form-label">@lang("password")</label>
                            <input  placeholder="@lang('password')"  name="password" class="form-control" type="text">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang("close")</button>
                            <button type="submit" class="btn btn-info">@lang("save")</button>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="edit-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('update') @lang('user')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="form_edit" id="form_edit" enctype="multipart/form-data" action="{{ route('admin.user.update')  }}"  method="POST">
                        @csrf
                        <input type="hidden" name="id" id="id"  class="form-control">
                        <div class="mb-2 form-group">
                            <label class="form-label">@lang("name")</label>
                            <input id="edit_name" placeholder="@lang('user_name')"  name="name" class="form-control" type="text">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-2 form-group">
                            <label class="form-label">@lang("email")</label>
                            <input id="edit_email" placeholder="@lang('email')"  name="email" class="form-control" type="text">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-2 form-group">
                            <label class="form-label">@lang("mobile")</label>
                            <input id="edit_mobile" placeholder="@lang('mobile')"  name="mobile" class="form-control" type="text">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-2 form-group">
                            <label class="form-label">@lang("gender")</label>
                            <select id="edit_gender"   name="gender" class="form-control" type="text">
                                <option value="1">@lang("male")</option>
                                <option value="2">@lang("female")</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-2 form-group">
                            <label class="form-label">@lang("date_of_birth")</label>
                            <input id="edit_date_of_birth" placeholder="@lang('date_of_birth')"  name="date_of_birth" class="form-control" type="date">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-2 form-group">
                            <label class="form-label">@lang("image")</label>
                            <input name="image" class="form-control" type="file">
                            <div class="invalid-feedback"></div>
                        </div>
                        <hr>
                        <div class="mb-2 form-group">
                            <label class="form-label">@lang("password")</label>
                            <input  placeholder="@lang('password')"  name="password" class="form-control" type="text">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang("close")</button>
                            <button type="submit" class="btn btn-info">@lang("save")</button>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="show-followers" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('show') @lang('followers')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="list_followers">

                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="show-posts" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('show') @lang('followers')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="list_posts">

                  </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="tempo-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('update') @lang('user')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="form_tempo" id="form_tempo" enctype="multipart/form-data" action="{{ route('admin.user.temporary_ban')  }}"  method="POST">
                        @csrf
                        <input id="tempo_id" name="id" type="hidden">
                        <div class="mb-2 form-group">
                            <label class="form-label">@lang("number")</label>
                            <input id="days" placeholder="@lang('number')" name="days" class="form-control" type="number">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang("close")</button>
                            <button type="submit" class="btn btn-info">@lang("save")</button>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-12 col-lg-12 col-xl-12 d-flex">
            <div class="card radius-10 w-100">
                <div class="card-header bg-transparent">
                    <div class="row g-3 align-items-center">
                        <div class="col">
                            <h5 class="mb-0">@lang('user_ban')</h5>
                        </div>
                        <div class="col">
                            <div class="d-flex align-items-center justify-content-end gap-3 cursor-pointer">
                                <div class="dropdown">
                                    <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown" aria-expanded="false"><i class="bx bx-dots-horizontal-rounded font-22 text-option"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li data-bs-toggle="modal" data-bs-target="#add-modal"><a class="dropdown-item">@lang('add')</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table align-middle mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>@lang('user')</th>
                                <th>@lang('email')</th>
                                <th>@lang('mobile')</th>
                                <th>@lang('gender')</th>
                                <th>@lang('status')</th>
                                <th>@lang('date_of_birth')</th>
                                <th>@lang('created_at')</th>
                                <th>@lang('actions')</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
@section('js')
    <script>


            var table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                @if(\Illuminate\Support\Facades\App::getLocale() == 'ar')
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json',
                },
                @endif
                ajax: {
                    url: "{{ route('admin.user.getbanaccounts') }}",
                },

                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "user",
                        name: "user",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "email",
                        name: "email",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "mobile",
                        name: "mobile",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "gender",
                        name: "gender",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "status",
                        name: "status",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "dateOfBirth",
                        name: "dateOfBirth",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "dateOfSign",
                        name: "created_at",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "actions",
                        name: "actions",
                        orderable: false,
                        searchable: false
                    },
                ]
            });
            // follow
            $(document).ready(function (){
               $(document).on('click' , '.followers_btn' , function (event){
                   event.preventDefault();
                   var button = $(this)
                   var id = button.data('id');
                   var type = button.data('type');

                    $.ajax({
                       url: "{{ route('admin.user.getfollowers') }}" ,
                       method: "GET" ,
                       data:{
                          id: id ,
                           type: type
                       } ,
                        success: function (res){
                            $('.list_followers').html(res);
                        }
                    });
               });
           });
            // posts
            $(document).ready(function (){
                $(document).on('click' , '.posts_btn' , function (event){
                    event.preventDefault();
                    var button = $(this)
                    var id = button.data('id');

                    $.ajax({
                        url: "{{ route('admin.user.getposts') }}" ,
                        method: "GET" ,
                        data:{
                            id: id ,
                        } ,
                        success: function (res){
                            $('.list_posts').html(res);
                        }
                    });
                });
            });
            // veri
            $(document).ready(function (){
                $(document).on('click' , '.vri_btn' , function (event){
                    event.preventDefault();
                    var button = $(this)
                    var id = button.data('id');
                    var vri = button.data('vri');

                    $.ajax({
                        url: "{{ route('admin.user.account_verification') }}" ,
                        method: "POST" ,
                        data:{
                            "_token":"{{ csrf_token() }}",
                            "id": id ,
                            "vri" : vri
                        } ,
                        success: function (res){
                           toastr.success("@lang('operation_accomplished_successfully')")
                           table.draw();
                        }
                    });
                });
            });
            // ban
            $(document).ready(function (){
                $(document).on('click' , '.permanent_btn' , function (event){
                    event.preventDefault();
                    var button = $(this)
                    var id = button.data('id');
                    var ban = button.data('ban');

                    swal({
                        title: "Are you sure?",
                        text: "Do you really want to ban this item ?",
                        icon: "warning",
                        buttons: true ,
                        dangerMode: true,
                    }).then( (willDelete) => {
                        if (willDelete) {

                            $.ajax({
                                url: "{{ route('admin.user.ban') }}" ,
                                method: "POST" ,
                                data:{
                                    id: id ,
                                    ban:ban ,
                                    _token: "{{ csrf_token() }}"
                                } ,
                                success: function (res){
                                    toastr.success('@lang('operation_accomplished_successfully')');
                                    table.draw()
                                }
                            })
                        } else {
                            toastr.error('Canceled Deleted item');
                        }
                    } );
                });
            });
            // edit
            $(document).ready(function() {
                $(document).on('click', '.edit_btn', function(event) {
                    $('input').removeClass('is-invalid');
                    $('.invalid-feedback').text('');
                    event.preventDefault();
                    var button = $(this)
                    var id = button.data('id');
                    $('#id').val(id);
                    $('#edit_name').val(button.data('name'))
                    $('#edit_email').val(button.data('email'))
                    $('#edit_mobile').val(button.data('mobile'))
                    $('#edit_date_of_birth').val(button.data('date-of-birth'))
                    $('#edit_gender').val(button.data('gender'))
                });
            });
            // tempo
            $(document).ready(function() {
                $(document).on('click', '.tempo_btn', function(event) {
                    $('input').removeClass('is-invalid');
                    $('.invalid-feedback').text('');
                    event.preventDefault();
                    var button = $(this)
                    var id = button.data('id');
                    $('#tempo_id').val(id);
                });
            });
            // tempo submit
            $('#form_tempo').on('submit', function(event) {

                event.preventDefault();
                var data = new FormData(this);
                let url = $(this).attr('action');
                let method = $(this).attr('method');

                $('input').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                $.ajax({
                    type: method,
                    cache: false,
                    contentType: false,
                    processData: false,
                    url: url,
                    data: data,

                    success: function(result) {
                        $("#tempo-modal").modal('hide');
                        $('#form_tempo').trigger("reset");
                        toastr.success("@lang('operation_accomplished_successfully')");
                        table.draw()
                    },
                    error: function(data) {
                        if (data.status === 422) {
                            var response = data.responseJSON;
                            $.each(response.errors, function(key, value) {
                                var str = (key.split("."));
                                if (str[1] === '0') {
                                    key = str[0] + '[]';
                                }
                                $('[name="' + key + '"], [name="' + key + '[]"]').addClass(
                                    'is-invalid');
                                $('[name="' + key + '"], [name="' + key + '[]"]').closest(
                                    '.form-group').find('.invalid-feedback').html(value[0]);
                            });
                        } else {
                            console.log('ahmed');
                        }
                    }
                });
            })







    </script>

@stop






