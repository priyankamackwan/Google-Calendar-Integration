@extends('layouts.app')
@section('title', isset($event) && $event != '' ? 'Update Event' : 'Create Event')
@section('content')

<div class="container">
    <div class="col d-flex justify-content-center">
        <div class="card shadow mb-4 col-md-6">
            <div class="card-head m-3">
                <h1 class="h3 mb-0 text-gray-800">{{ isset($event) && $event != '' ? 'Update Event' : 'Create Event' }}
                </h1>
            </div>
            <hr>

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <div class="card-body mb-4">
                <form action='@yield("editRoute", route("events.store"))' method="post" id="eventform">
                    @csrf
                    @yield('editContents')
                    <div class="form-group"><label for="name">Event Name</label>
                        <input class="form-control {{ $errors->has('name') ? 'error' : ''}}" type="text" name="name" id="name"
                            value="{{ old('name', $event->name ?? '') }}">
                    </div>
                    <div class="form-group"><label for="description">Description</label>
                        <input class="form-control {{ $errors->has('description') ? 'error' : ''}}" type="text" name="description" id="description"
                            value="{{ old('description', $event->description ?? '') }}">
                    </div>

                    <div class="form-group"><label for="startDateTime">Start Date</label>
                        <input class="form-control {{ $errors->has('startDateTime') ? 'error' : ''}}" type="datetime-local" name="startDateTime" id="startDateTime"
                            value="{{ old('startDateTime', $event->startDateTime ?? '') }}">
                    </div>

                    <div class="form-group"><label for="endDateTime">End Date</label>
                        <input class="form-control {{ $errors->has('endDateTime') ? 'error' : ''}}" type="datetime-local" name="endDateTime" id="endDateTime"
                            value="{{ old('endDateTime', $event->endDateTime ?? '') }}">
                    </div>

                    <div class="mt-3">
                        <button type="submit"
                            class="btn btn-primary">{{ isset($event) && $event != '' ? 'Update' : 'Create' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $('#eventform').validate({
        rules: {
            name: {
                required: true
            },
            startDateTime: {
                required: true
            },
            endDateTime: {
                required: true
            }
        },
        messages: {
            name: {
                required: "Name field is required"
            },
            startDateTime: {
                required: "Start Date field is required"
            },
            endDateTime: {
                required: "End Date field is required"
            }
        }
    });
</script>
@stop