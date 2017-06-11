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
    #fullscreen {float: none;}
    .fullscreen-container {
        text-align: center;
    }
</style>
@endsection

@section('content')
    <div class='name'>{{$event->name}}</div>
    <div class='address'>{{$event->address}}</div>
    <div id='countdown'>00:00:00</div>

    <div class='fullscreen-container'>
        <a class='fullscreen' id="fullscreen"></a>
    </div>

    <div class='note'>Don't close this page to keep your geo position up-to-date</div>

    <script>
        var noSleep = new NoSleep();

        var toggleEl = document.querySelector("#fullscreen");
        toggleEl.addEventListener('click', function() {

            var isInFullScreen = (document.fullscreenElement && document.fullscreenElement !== null) ||
                (document.webkitFullscreenElement && document.webkitFullscreenElement !== null) ||
                (document.mozFullScreenElement && document.mozFullScreenElement !== null) ||
                (document.msFullscreenElement && document.msFullscreenElement !== null);

            var docElm = document.documentElement;
            if (!isInFullScreen) {
                if (docElm.requestFullscreen) {
                    docElm.requestFullscreen();
                } else if (docElm.mozRequestFullScreen) {
                    docElm.mozRequestFullScreen();
                } else if (docElm.webkitRequestFullScreen) {
                    docElm.webkitRequestFullScreen();
                } else if (docElm.msRequestFullscreen) {
                    docElm.msRequestFullscreen();
                }
                $("#main").addClass("full");
                noSleep.enable();

            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
                $("#main").removeClass("full");
                noSleep.disable();
            }

        }, false);
    </script>

@endsection

@section('scripts')
<script>
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