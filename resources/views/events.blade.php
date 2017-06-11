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
    function view(event){
        window.location = '/event/'+event; 
    }

    function setAddress(event, mode, address, lat, lng) {
        var jc = $.alert({
            theme: 'dark',
            icon: '',
            title: '',
            content: '<span class="fa fa-spinner fa-spin"></span> Please wait',
            buttons: {}
        });

        $.post('/address', {event: event, mode: mode, address: address, lat: lat, lng: lng, _token:  window.Laravel.csrfToken}, function(response){
            if (response.result) {
                window.location.reload();
            } else {
                jc.close();
                $.alert({type: 'red', theme: 'dark', backgroundDismiss: true, title:'Error', content:response.error, autoClose:"ok|10000"});
            }
        }).fail(function(){
            jc.close();
            $.alert({type: 'red', theme: 'dark', backgroundDismiss: true, title:'Error', content:"Can't process request. Try again later.", autoClose:"ok|10000"});
        });
    }

    function showManual(event, userChoice) {
        var jc = $.confirm({
            theme: 'dark',
            title: 'Set event address',
            content: '' +
            '<form action="" class="formName">' +
            '<div class="form-group">' +
            (userChoice ? 'Set address manualy:' : '<span style="color:#ef5350;font-weight:bold">Geolocation is not available. Set address manualy:</span>')+
            '<br/><input id="address" type="text" placeholder="Address" class="address form-control" name="address" required />' +
            '</div>' +
            '</form>',
            buttons: {
                formSubmit: {
                    text: 'SAVE',
                    btnClass: 'btn-green',
                    action: function () {
                        var address = this.$content.find('.address').val();
                        if(!address){
                            $.alert({type: 'red', theme: 'dark', backgroundDismiss: true, title:'Error', content:'Provide a valid address', autoClose:"ok|10000"});
                            return false;
                        }
                        setAddress(event, 'manual', address);
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
            title: 'Set event address',
            content: '' +
            'Are you sure you want to set event address? It will be detected by your current coordinates. <br/>Click <b>Manual</b> to set address explicitly.',
            buttons: {
                formSubmit: {
                    text: 'SAVE',
                    btnClass: 'btn-green',
                    action: function () {
                        setAddress(event, 'auto', null, lat, lng);
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


    function geocode(event) {
        if(window.location.protocol == "https:" && navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position){
                showAuto(event, position.coords.latitude, position.coords.longitude);
            }, function(error) {
                showManual(event);
            });
        } else {
            showManual(event);
        }
    }
</script> 
@endsection

@section('content')
    @foreach($events as $event) 
        <div class="event">
            <div class='event-name' onclick="view({{$event->id}})">
                {{$event->name}}
                <div class='event-time'>
                    <span class="date">{{$event->time->format("j M")}}</span>,
                    <span class="time">{{$event->time->format("H:i")}}</span>
                </div>
            </div>
            <div class='event-address'>
                <span  onclick="view({{$event->id}})">
                    {{$event->address}}
                </span>
                @if(!$event->address && auth()->id())
                    Address not specified <button type="button" onclick="geocode({{$event->id}})">Use current location</button>
                @endif
            </div>

            <div class='event-drivers' onclick="view({{$event->id}})">
                @foreach($event->drivers as $driver)
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
                @endforeach
            </div>
            <div class="clear"></div>
        </div>
    @endforeach
@endsection
