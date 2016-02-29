@extends('Shared.Layouts.BlankSlate')


@section('blankslate-icon-class')
    ico-search
@stop

@section('blankslate-title')
    No Search Results
@stop

@section('blankslate-text')
    There was nothing found matching the term '{{isset($search['q']) ? $search['q'] : $q}}'
@stop

@section('blankslate-body')
    
@stop