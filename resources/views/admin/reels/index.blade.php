@extends('admin.master')

@section('title' , 'Admin | Home')
@section('content')
    {{-- Start Modals   --}}
    <div class="modal fade" id="edit-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('update') @lang('post')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="form_edit" id="form_edit" enctype="multipart/form-data" action="{{ route('admin.post.update')  }}"  method="POST">
                        @csrf
                        <input type="hidden" name="id" id="id"  class="form-control">
                        <div class="mb-2">
                            <label class="form-label">@lang("content")</label>
                            <input id="edit_content" placeholder="@lang('content')"  name="desc" class="form-control" type="text">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">@lang("image")</label>
                            <input name="image" class="form-control" type="file">
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
    <div class="modal fade" id="comments-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('show') @lang('comments')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="list_comments">

                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="modal fade" id="likes-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('show') @lang('likes')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="list_likes">

                    </div>
                </div>

            </div>
        </div>
    </div>
    {{-- End Modals --}}

    {{-- Start Content --}}
    <div class="row">
        <div class="col-12 col-lg-12 col-xl-12 d-flex">
            <div class="card radius-10 w-100">
                <div class="card-header bg-transparent">
                    <div class="row g-3 align-items-center">
                        <div class="col">
                            <h5 class="mb-0">@lang('users')</h5>
                        </div>
                        <div class="col">
                            <div class="d-flex align-items-center justify-content-end gap-3 cursor-pointer">
                                <div class="dropdown">
                                    <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown" aria-expanded="false"><i class="bx bx-dots-horizontal-rounded font-22 text-option"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item">@lang('add')</a>
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
                                <th>@lang('image')</th>
                                <th>@lang('post')</th>
                                <th>@lang('user')</th>
                                <th>@lang('comments')</th>
                                <th>@lang('likes')</th>
                                <th>@lang('actions')</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- End Content --}}
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
                    url: "{{ route('admin.post.getdata') }}",
                },

                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        "data": 'reels',
                        "name": 'reels',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "content",
                        name: "content",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "user",
                        name: "user",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "comment",
                        name: "comments",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "likes",
                        name: "likes",
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

            $(document).ready(function (){
                $(document).on('click' , '.comments_btn' , function (event){
                    event.preventDefault();
                    var button = $(this)
                    var id = button.data('id');

                    $.ajax({
                        url: "{{ route('admin.post.getcomments') }}" ,
                        method: "GET" ,
                        data:{
                            id: id
                        } ,
                        success: function (res){
                            $('.list_comments').html(res);
                        }
                    });
                });
            });

            $(document).ready(function (){
                $(document).on('click' , '.likes_btn' , function (event){
                    event.preventDefault();
                    var button = $(this)
                    var id = button.data('id');

                    $.ajax({
                        url: "{{ route('admin.post.getlikes') }}" ,
                        method: "GET" ,
                        data:{
                            id: id
                        } ,
                        success: function (res){
                            $('.list_likes').html(res);
                        }
                    });
                });
            });

            $(document).ready(function (){
                $(document).on('click' , '.closebtn' , function (event){
                    event.preventDefault();
                    var button = $(this)
                    swal({
                        title: "Are you sure?",
                        text: "Do you really want to delete this item ?",
                        icon: "warning",
                        buttons: true ,
                        dangerMode: true,
                    }).then( (willDelete) => {
                        if (willDelete) {
                            var id = button.data('id-comment');
                            var idpost = button.data('id-post');
                            $.ajax({
                                url: "{{ route('admin.post.deletecomment') }}" ,
                                method: "POST" ,
                                data:{
                                    id: id ,
                                    idpost: idpost ,
                                    _token: "{{ csrf_token() }}"
                                } ,
                                success: function (res){
                                    $('.list_comments').html(res);
                                }
                            });
                        } else {
                            toastr.error('Canceled Deleted item');
                        }
                    } );




                });
            });

            $(document).ready(function() {
            $(document).on('click', '.edit_btn', function(event) {
                $('input').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                event.preventDefault();
                var button = $(this)
                var id = button.data('uuid');
                $('#id').val(id);
                $('#edit_content').val(button.data('content'))
            });
        });







    </script>

@stop






