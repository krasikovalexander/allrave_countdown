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
            text-shadow: 1px 1px #e5e5e5;
    }

    .event-time {
        float: right;
    }

    .event-time .date {
        font-size: 0.8em;
    }
    .event-time .time {
        color: #ff3d00;
    }
    .event-drivers {
        margin-top:15px;
    }
    .event-driver {
        float: left;
        background-color: #383838;
        padding: 3px 5px;
        margin: 5px;
        color: white;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.23), 0 3px 10px rgba(0, 0, 0, 0.16);
        border-radius: 30px;
    }
    
    .event-driver.start {
        background-color: #00e676;
    }

    .event-driver.stop {
        background-color: #d50000;
    }

    .event-driver.tracking {
        background-color: #00838f;
    }

    .event-driver-photo {
        width: 36px;
        height: 36px;
        background-size: cover;
        background-image: url(/images/avatar-man-no-text-grey.jpg);
        float: left;
        border-radius: 50%;
        border: 2px solid #333333;      
    }

    .event-driver.start .event-driver-photo, .event-driver.stop .event-driver-photo ,  .event-driver.tracking .event-driver-photo{
        background-image: url(/images/start.png);
        border: none;
        width: 80px;
        height: 30px;
        margin-top: 6px;
        border-radius: 0;
        background-size: 38px 16px;
        background-repeat: no-repeat;
        background-position: center 5px;
        cursor: pointer;
    }

    .event-driver.stop .event-driver-photo {
        background-image: url(/images/stop.png);
    }

    .event-driver.tracking .event-driver-photo {
        background-image: url(/images/tracking.png);
        border: none;
        background-position: center 1px;
        background-size: 24px 24px;
    }

    .event-driver.arrived {
        background-image: none;
        background-color: #ff3d00;
        font-weight: bold;
        font-size: 16px;
        padding: 8px 20px 8px 20px;
        cursor: pointer;
    }

    .event-driver-name {
        font-weight: bold;
        text-shadow: 1px 1px #101010;
    }
    .event-driver-name, .event-driver-eta {
        display: inline;
        line-height: 36px;
        margin: 5px;
    }
    .event-driver-eta, .event-driver-arrived {
        background-color: #ffffff;
        padding: 5px;
        border-radius: 10px;
        color: black;
        font-weight: bold;
        font-size: 0.8em;
        display: inline;
    }

    .event-driver-arrived {
        background-color: #ff3d00;
    }
    .clear {
        clear:both;
    }
    .flex-center {
        align-items: initial;
    }
    .content {
        margin-top:50px;
    }

    .form-control {
        color: black;
        font-weight: bold;
    }
    
</style>
@endsection

@section('scripts')
<script>

    function startCountdown(event, mode, eta, lat, lng) {
        var jc = $.alert({
            theme: 'dark',
            icon: '',
            title: '',
            content: '<span class="fa fa-spinner fa-spin"></span> Please wait',
            buttons: {}
        });

        $.post('/start', {event: event, mode: mode, eta: eta, lat: lat, lng: lng, _token:  window.Laravel.csrfToken}, function(response){
            if (response.result) {
                window.location = response.url;
            } else {
                jc.close();
                $.alert({type: 'red', theme: 'dark', backgroundDismiss: true, title:'Error', content:response.error, autoClose:"ok|10000"});
            }
        }).fail(function(){
            jc.close();
            $.alert({type: 'red', theme: 'dark', backgroundDismiss: true, title:'Error', content:"Can't process request. Try again later.", autoClose:"ok|10000"});
        });
    }

    function showManual(event, userChoice = false) {
        var jc = $.confirm({
            theme: 'dark',
            title: 'Drive to event location',
            content: '' +
            '<form action="" class="formName">' +
            '<div class="form-group">' +
            (userChoice ? 'Set ETA manualy:' : '<span style="color:#ef5350;font-weight:bold">Geolocation is not available. Set ETA manualy:</span>')+
            '<br/><label for="h">Hours</label><input id="h" type="number" placeholder="Hours" class="hours form-control" name="hours" value="0"  min=0 max=24 step=1 required /><br/>' +
            '<label for="m">Minutes</label><input id="m" type="number" placeholder="Minutes" class="minutes form-control" name="minutes" value="0" min=0 max=60 step=1 required />' +
            '</div>' +
            '</form>',
            buttons: {
                formSubmit: {
                    text: 'START',
                    btnClass: 'btn-green',
                    action: function () {
                        var hours = this.$content.find('.hours').val();
                        var minutes = this.$content.find('.minutes').val();
                        var eta = parseInt(hours)*60+parseInt(minutes);
                        if(!eta){
                            $.alert({type: 'red', theme: 'dark', backgroundDismiss: true, title:'Error', content:'Provide a valid estimated time', autoClose:"ok|10000"});
                            return false;
                        }
                        startCountdown(event, 'manual', eta, null, null);
                    }
                },
                cancel: function () {
                    //close
                },
            },
            onContentReady: function () {
                var jc = this;
                this.$content.find('form').on('submit', function (e) {
                    e.preventDefault();
                    jc.$$formSubmit.trigger('click');
                });
            }
        });
    }

    function showAuto(event, lat, lng) {
        var jc = $.confirm({
            theme: 'dark',
            title: 'Drive to event location',
            content: '' +
            'Are you sure you want to start ride? ETA will be calculated based on your coordinates. <br/>Click <b>Manual</b> to set ETA explicitly.',
            buttons: {
                formSubmit: {
                    text: 'START',
                    btnClass: 'btn-green',
                    action: function () {
                        startCountdown(event, 'auto', null, lat, lng);
                    }
                },
                manual: {
                    text: 'MANUAL',
                    btnClass: 'btn-red',
                    action: function () {
                        jc.close();
                        showManual(event, true);
                    }
                },
                cancel: function () {
                    //close
                },
            }
        });
    }

    function toggle(btn, event, hasAddress) {
        if ($(btn).hasClass('start')) {
            if(window.location.protocol == "https:" && navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position){
                    if (!hasAddress) {
                        showManual(event); 
                    } else {
                        showAuto(event, position.coords.latitude, position.coords.longitude);
                    }
                }, function(error) {
                    showManual(event);
                });
            } else {
                showManual(event);
            }
        } else {

            var jc = $.confirm({
                theme: 'dark',
                title: 'Stop ride',
                content: '' +
                'Are you sure you want to stop this ride?',
                buttons: {
                    ok: {
                        text: 'STOP',
                        btnClass: 'btn-red',
                        action: function () {
                            window.location = "/cancel/"+event;
                        }
                    },
                    cancel: function () {
                        //close
                    },
                }
            });
            
        }
    }

    function arrived(event) {
        var jc = $.confirm({
            theme: 'dark',
            title: 'Finish ride',
            content: '' +
            'Are you sure?',
            buttons: {
                ok: {
                    text: 'Arrived',
                    btnClass: 'btn-red',
                    action: function () {
                        window.location = "/arrived/"+event;
                    }
                },
                cancel: function () {
                    //close
                },
            }
        });
    }

    function leave(event) {
        window.location = "/cancel/"+event;
    }

</script>
@endsection

@section('content')
    @foreach($events as $event) 
        <div class="event">
            <div class='event-name'>
                {{$event->name}}
                <div class='event-time'>
                    <span class="date">{{$event->time->format("j M")}}</span>,
                    <span class="time">{{$event->time->format("H:i")}}</span>
                </div>
            </div>
            <div class='event-address'>
                {{$event->address}}
            </div>

            <div class='event-drivers'>
                @if (!$event->me && !$event->arrived)
                    <div class="event-driver start" onclick="toggle(this, {{$event->id}}, {{$event->address ? 'true' : 'false'}})">
                        <div class="event-driver-photo"></div>
                    </div>
                @else 
                    @if (!$event->arrived)
                        <div class="event-driver stop" onclick="toggle(this, {{$event->id}})">
                            <div class="event-driver-photo"></div>
                        </div>
                        <div class="event-driver arrived" onclick="arrived({{$event->id}})">
                            Arrived
                        </div>
                        @if($event->mode == 'auto') 
                            <div class="event-driver tracking" onclick="window.location='{{url("tracking", ['event' => $event->id])}}'">
                                <div class="event-driver-photo"></div>
                            </div>
                        @endif
                    @else
                        <div class="event-driver arrived" onclick="leave({{$event->id}})">
                            Leave
                        </div>
                    @endif
                @endif
                @foreach($event->drivers as $driver)
                    @if ($driver->id !== auth()->id())
                    <div class="event-driver">
                        <div class="event-driver-photo" @if ($driver->photo) style="background-image:url({{url($driver->photo)}})" @endif></div>
                        <div class="event-driver-name">
                            {{$driver->name}}
                        </div>
                        @if (!$driver->pivot->arrived && $driver->pivot->eta !== null)
                         <div class="event-driver-eta">
                            {{$driver->eta}} min
                        </div>
                        @endif
                        @if ($driver->pivot->arrived)
                         <div class="event-driver-arrived">
                            Arrived
                        </div>
                        @endif
                    </div>
                    @endif
                @endforeach
            </div>
            <div class="clear"></div>
        </div>
    @endforeach
@endsection
