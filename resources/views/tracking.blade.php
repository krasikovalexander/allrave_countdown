@extends('layouts.app')

@section('styles')
<style>
    #countdown {
        font-size: 80px;
        font-weight: bold;
        color: #ff3d00;
        text-align: center;
        font-family: sans-serif;
    }
    .note, .address, .name {
        padding: 50px;
        text-align: center;
        color: black;
        font-weight: normal;
    }
    .name {
        padding-bottom: 0px;
    }
    .address {
        padding-top: 10px;
        font-weight: bold;
    }
</style>
@endsection

@section('content')
    <div class='name'>{{$event->name}}</div>
    <div class='address'>{{$event->address}}</div>
    <div id='countdown'>00:00:00</div>
    <div class='note'>Don't close this page to keep your geo position up-to-date</div>
@endsection

@section('scripts')
<script>
    var noSleep = new NoSleep();

    function enableNoSleep() {
      noSleep.enable();
      document.removeEventListener('touchstart', enableNoSleep, false);
    }
    document.addEventListener('touchstart', enableNoSleep, false);

    function str_pad_left(string,pad,length) {
        return (new Array(length+1).join(pad)+string).slice(-length);
    }

    var time = {{$driver->pivot->eta ? $driver->pivot->eta : 0}}*60;
    var countdown = function() {
        time = Math.max(time-1, 0);
        var t = time;
        var hours = Math.floor(t / 3600);
        t = t - hours * 3600;
        var minutes = Math.floor(t / 60);
        var seconds = t - minutes * 60;

        $("#countdown").html(str_pad_left(hours,'0',2)+":"+str_pad_left(minutes,'0',2)+":"+str_pad_left(seconds,'0',2));
    };
    countdown();
    setInterval(countdown, 1000);

    var update = function() {
        if(window.location.protocol == "https:" && navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position){
                $.post('/update', {event: {{$event->id}}, lat: position.coords.latitude, lng: position.coords.longitude, _token:  window.Laravel.csrfToken}, function(response){
                    if (response.result) {
                        time = response.eta*60;
                    } else {
                        $.alert({type: 'red', theme: 'dark', backgroundDismiss: true, title:'Error', content:response.error, autoClose:"ok|10000"});
                    }
                }).fail(function(){
                    $.alert({type: 'red', theme: 'dark', backgroundDismiss: true, title:'Error', content:"Can't process request. Check internet connection.", autoClose:"ok|10000"});
                });
            }, function(error) {
                $.alert({type: 'red', theme: 'dark', backgroundDismiss: true, title:'Error', content:"Geolocation is not available.", autoClose:"ok|10000"});
            });
        } else {
            $.alert({type: 'red', theme: 'dark', backgroundDismiss: true, title:'Error', content:"Geolocation is not available.", autoClose:"ok|10000"});
        }
    }
    update();
    setInterval(update, 60*1000);
</script> 
@endsection