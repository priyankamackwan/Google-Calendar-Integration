@extends('events.create')
@section("editRoute", route('events.update', $event->id))
@section('editContents')
@method('PUT')
@stop