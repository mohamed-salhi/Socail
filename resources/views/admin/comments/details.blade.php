@extends('admin.master')

@section('title' , 'Admin | Home')

@section('css')

    @stop

@section('content')

    <div class="modal fade" id="edit-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('update') @lang('comment')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="form_edit" id="form_edit" enctype="multipart/form-data" action="{{ route('admin.comment.update')  }}"  method="POST">
                        @csrf
                        <input type="hidden" name="id" id="id"  class="form-control">
                        <div class="mb-2">
                            <label class="form-label">@lang("comment")</label>
                            <input id="edit_comment" placeholder="@lang('comment')"  name="comment" class="form-control" type="text">
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
                            <h5 class="mb-0">@lang('comments') {{ $user->name }}</h5>
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
                                <th>@lang('post')</th>
                                <th>@lang('user_post')</th>
                                <th>@lang('comment')</th>
                                <th>@lang('date')</th>
                                <th>@lang('time')</th>
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
                    url: "{{ route('admin.comment.getcomments') }}",
                    data:{
                        id:"{{ $user->uuid }}"
                    }
                },

                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "post",
                        name: "post",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "publisher",
                        name: "publisher",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "comment",
                        name: "comment",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "date",
                        name: "date",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "time",
                        name: "time",
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



        $(document).ready(function() {
            $(document).on('click', '.edit_btn', function(event) {
                $('input').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                event.preventDefault();
                var button = $(this)
                var id = button.data('id');
                $('#id').val(id);
                $('#edit_comment').val(button.data('comment'))
            });
        });






    </script>

@stop






