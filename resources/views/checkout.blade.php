@extends('layouts.master')
@extends('layouts.layout')

@section('content')
@include('sidebar.sidebar')
<div style="padding-top:30px;" class="container">
    <div class="row justify-content-center">
        <div class="container">
            <div class="card">
                <div class="card-header">{{ __('Checked Out Item') }}</div>
                <div class="card-body">
                    <table id="example" class="table table-hover table-sm nowrap" style="width:100%">
                        <thead class="thead-dark" style='text-align: center'>
                            <tr>
                                <th>No</th>
                                <th>@sortablelink('item_name', 'Item Name', ['filter' => 'active, visible'], ['class' => 'text text-light text-decoration-none'])</th>
                                <th>@sortablelink('item_code', 'Item_code', ['filter' => 'active, visible'], ['class' => 'text text-light text-decoration-none'])</th>
                                <th>@sortablelink('command', 'Command', ['filter' => 'active, visible'], ['class' => 'text text-light text-decoration-none'])</th>
                                <th>@sortablelink('status', 'Status', ['filter' => 'active, visible'], ['class' => 'text text-light text-decoration-none'])</th>
                                <th>Manage</th>
                            </tr>
                        </thead>
                        <tbody style='text-align: center'>
                            
                            @php ($i = 1)
                            @if($items->count())
                            @foreach($items as $key => $item)
                            <tr>
                                <td>{{$i++}}</td>
                                <td>{{$item['item_name']}}</td>
                                <td>{{$item['item_code']}}</td>
                                <td>{{$item['command']}}</td>
                                <td>
                                    @if($item['status']=='Available')
                                    <span class="badge badge-success"> {{$item['status']}}</span>
                                    @elseif($item['status']=='Unresolved')
                                    <span class="badge badge-warning" style='background-color:#F29A02; color:#fff;'> {{$item['status']}}</span>
                                    @elseif($item['status']=='Lost')
                                    <span class="badge badge-danger" style='width:3rem;'> {{$item['status']}}</span>
                                    @else
                                    <span class="badge badge-dark"> {{$item['status']}}</span>
                                    @endif
                                </td>
                                <td>
                                    <a class="btn btn-outline-info btn-sm" style="width:2rem; height:2rem; padding-right:0.3rem;" href="{{route('viewcheckout', $item->id)}}" ><i class="fa fa-info"></i></a>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>

                    {!! $items->appends(\Request::except('page'))->render() !!}

                    <style>
                        .w-5{
                            display:none;
                        }
                    </style>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
