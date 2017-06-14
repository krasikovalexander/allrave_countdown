@extends('layouts.kiosk')

@section('styles')
    <style>
        #main {
            background-color: {{$event->main_bg_color ? $event->main_bg_color : 'white'}};
            @if($event->main_bg_image)
            background-image:url({{url($event->main_bg_image)}});
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
            @endif
        }
        .event-card .text {
           display: none;
        }

        .event-card {
            color: {{$event->area_text_color ? $event->area_text_color : 'black'}};
            background-color: {{$event->area_bg_color ? $event->area_bg_color : 'white'}};
            @if($event->area_bg_image)
            background-image:url({{url($event->area_bg_image)}});
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center center;
            @endif
            width: 80vw;
            padding: 20px;
            font-size: 40px;
            margin:auto;
            text-align: center;
        }

        #countdown {
            font-size: 160px;
            font-weight: bold;
            color: {{$event->area_timer_color ? $event->area_timer_color : '#ff3d00'}};
            text-align: center;
            font-family: sans-serif;
        }
        .arrived {
            color: {{$event->area_arrived_color ? $event->area_arrived_color : '#ff3d00'}};
            font-weight: bold;
            font-size: 100px;
            margin-bottom: 20px;
        }

        @media(max-height: 767px) {
            .event-card {
                font-size: 20px;
            }
            .arrived {
                font-size: 50px;
            }
            #countdown {
                font-size: 100px;
            }
        }
    </style>
@endsection

@section('scripts')
<script>
    

    function str_pad_left(string,pad,length) {
        return (new Array(length+1).join(pad)+string).slice(-length);
    }

    var time = null;
    var arrived = false;

    var countdown = function() {
        if (time !== null) {
            time = Math.max(time-1, 0);
            var t = time;
            var hours = Math.floor(t / 3600);
            t = t - hours * 3600;
            var minutes = Math.ceil(t / 60);
            var seconds = t - minutes * 60;

            $("#countdown").html(str_pad_left(hours,'0',2)+":"+str_pad_left(minutes,'0',2));

            $(".without-eta").hide()
            $(".with-eta").show();
        } else {
            $(".with-eta").hide()
            if (!arrived) {
                $(".without-eta").show();
            }
        }

        if (arrived) {
            $(".arrived").show();
        } else {
            $(".arrived").hide();
        }
    };
    countdown();
    setInterval(countdown, 1000);

    var update = function() {
        $.post('/arrivings', {event: {{$event->id}}, _token:  window.Laravel.csrfToken}, function(response){
            if (response.result) {
                time = response.eta === null ? null : response.eta*60;
                arrived = response.arrived;
            } else {
                $.alert({type: 'red', theme: 'dark', backgroundDismiss: true, title:'Error', content:response.error, autoClose:"ok|10000"});
            }
        }).fail(function(){
            $.alert({type: 'red', theme: 'dark', backgroundDismiss: true, title:'Error', content:"Can't process request. Check internet connection.", autoClose:"ok|10000"});
        });
    }
    update();
    setInterval(update, 5*1000);
</script>
@endsection

@section('content')
    <div class='event-card'>
        <div class="text without-eta">
            {!!$event->congratulations!!}
        </div>
        <div class="text arrived">
            Your shuttle has arrived!
        </div>
        <div class="text with-eta">
            Your next shuttle to <br/>
            <b>{{$event->name}}</b><br/>
            will arrive in
            <br/>
            <div class="eta" id="countdown"></div>
        </div>
@endsection