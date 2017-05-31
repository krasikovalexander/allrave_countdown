@extends('layouts.app')

@section('styles')
<style>
    .event {
        background-color: white;
        margin: 10px;
        padding: 15px;
        width: 90vw;
        max-width: 800px;
        font-size: 1em;
        font-family: "Roboto", "Helvetica Neue", Helvetica, Arial, sans-serif;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.23), 0 3px 10px rgba(0, 0, 0, 0.16);
    }
    .event-name {
        font-weight: bold;
        font-size: 1.5em;
        border-bottom: 1px solid #777;
        margin-bottom: 5px;
    }
    .event-time {
        color: #ff3d00;
    }

    .event-driver {
        float: left;
        background-color: #383838;
        padding: 5px;
        margin: 5px;
        color: white;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.23), 0 3px 10px rgba(0, 0, 0, 0.16);
    }
    .event-driver-photo {
        width: 48px;
        height: 48px;
        background-size: cover;
        background-image: url(/images/avatar-man-no-text-grey.jpg);
        float: left;
        border-radius: 50%;
        border: 2px solid #333333;
    }
    .event-driver-name {
        font-weight: bold;
    }
    .event-driver-name, .event-driver-eta {
        display: inline;
        line-height: 48px;
        margin: 5px;
    }
    .event-driver-eta {
        background-color: #03a9f4;
        padding: 5px;
        border-radius: 6px;
        color: white;
        font-weight: bold;
        font-size:0.8em;
    }
    .clear {
        clear:both;
    }
    .flex-center {
        align-items: initial;
    }
    .content {
        margin-top:40px;
    }
</style>
@endsection

@section('content')
    @foreach($events as $event) 
        <div class="event">
            <div class='event-name'>
                {{$event->name}}
            </div>
            <div class='event-address'>
                {{$event->address}}
            </div>
            <div class='event-time'>
                {{$event->time}}
            </div>
            <div class='event-drivers'>
                @foreach($event->drivers as $driver)
                    <div class="event-driver">
                        <div class="event-driver-photo" @if ($driver->photo) style="background-image:url({{url($driver->photo)}})" @endif></div>
                        <div class="event-driver-name">
                            {{$driver->name}}
                        </div>
                        <div class="event-driver-eta">
                            10 min
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="clear"></div>
        </div>
    @endforeach
@endsection
