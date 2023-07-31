@extends('layouts.app')
@section('title', 'Calendar Events')
@section('content')
<div class="container">
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Events List</h1>
    <a href="{{route('events.create')}}" target="_blank" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fa fa-plus"></i> Create Event</a>
</div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="calendar_list" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Start Date & Time</th>
                            <th>End Date & Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
   $(() => {
        var table = $('#calendar_list').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{route('events.datatable')}}",
            columns: [
                {data: 'id', name: 'id', orderable: false},
                {data: 'name', name: 'name'},
                {data: 'description', name: 'description'},
                {data: 'startDateTime', name: 'startDateTime', orderable: true},
                {data: 'endDateTime', name: 'endDateTime'},
                {data: 'action', name: 'action', searchable: false},
            ]
        });

        $('#calendar_list').on('click', '.delete', function (e) 
        {
            e.preventDefault();
            var deleteurl = $(this).attr('href');
            var token = $("meta[name='csrf-token']").attr('content');
            
            bootbox.confirm({
                size: 'small',
                closeButton: false,
                message: "Are You Sure?",
                callback: function (confirm)
                {
                    if(confirm)
                    {
                        $.ajax({
                            url: deleteurl,
                            method: "DELETE",
                            data: { "_token": token },
                            success: function (result) {
                                if(result.success)
                                {
                                    location.reload();
                                }
                                else
                                {
                                    toastr.error('Something Went Wrong, Please Try Again!');
                                }
                            }
                        });
                    }
                }
            });
        });
    })
</script>

@stop