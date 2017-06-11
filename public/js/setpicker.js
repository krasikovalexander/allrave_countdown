    $('[name$="_color"]:input').each(function(){
        $(this).minicolors({
            animationSpeed: 50,
            animationEasing: 'swing',
            change: null,
            changeDelay: 0,
            control: 'hue',
            defaultValue: '',
            format: 'rgb',
            hide: null,
            hideSpeed: 100,
            inline: false,
            keywords: '',
            letterCase: 'lowercase',
            opacity: true,
            position: 'left',
            show: null,
            showSpeed: 100,
            theme: 'default',
            swatches: []
        });
    });
